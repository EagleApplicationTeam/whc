// Markers array
var markers = [];

/*
 * Called when the API script is fully loaded
 */
function initMap() {
	// Create the map
   	var map = new google.maps.Map(document.getElementById('map'), {
      	center: {lat: 34.5414014, lng: -112.4716222},
      	zoom: 9
    });

   	// Get the events from the database
    getEvents(map);

    /*
     * Search Bar Initialization
     */
    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);

    // $("#pac-input").keypress(function(e) {
    // 	if (e.which === 13) {
    // 		alert("return");
    // 	}
    // });

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
      searchBox.setBounds(map.getBounds());
    });

    var searchMarkers = [];

    var geocoder = new google.maps.Geocoder;

    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener('places_changed', function() {
      	var places = searchBox.getPlaces();

      	if (places.length == 0) {
        	return;
      	}

      	// Clear out the old markers.
      	searchMarkers.forEach(function(marker) {
        	marker.setMap(null);
      	});

      	searchMarkers = [];

	    // For each place, get the icon, name and location.
	    var bounds = new google.maps.LatLngBounds();
	    places.forEach(function(place) {
	    	// If the place does not contain a location
	        if (!place.geometry) {
	          	console.log("Returned place contains no geometry");
	          	return;
	        }

	        // Create a marker for the place.
	        var marker = new google.maps.Marker({
	          	map: map,
	          	title: place.name,
	          	position: place.geometry.location
	        });

	        // Instantiate InfoWindow object
	        var infoWindow = new google.maps.InfoWindow();

	        var placeId = place.place_id;

	        // Get the place information from the place ID
	        geocoder.geocode({'placeId' : placeId}, function(results, status) {
	        	if (status !== "OK") {
	        		alert("Error");
	        		return;
	        	}

	        	map.setZoom(17);
	        	map.setCenter(results[0].geometry.location);

	        	marker.setPlace({
	        		placeId: placeId,
	        		location: results[0].geometry.location
	        	});

	        	marker.setVisible(true);

	        	infoWindow.setContent(results[0].formatted_address);
	        	infoWindow.open(map, marker);
	        });
	        
	        searchMarkers.push(marker);

	        if (place.geometry.viewport) {
	          	// Only geocodes have viewport.
	          	bounds.union(place.geometry.viewport);
	        } else {
	          	bounds.extend(place.geometry.location);
	        }
      	});

      	map.fitBounds(bounds);
    });
}

/*
 * Gets the events from the database and adds them to the map
 */
function getEvents(map) {
	// AJAX request to server
	axios.get("/events").then((response) => {
		// Add the markers to the map
		addMarkers(response.data, map);
	}).catch((error) => {
		// Log the error
		console.log(error)
		alert("There was a problem.");
	});
}

/*
 * Adds the events to the map
 */
function addMarkers(events, map) {
	// Loop through the events
	for (var i = events.length - 1; i >= 0; i--) {
		// Create the marker and set the marker position
		var marker = new google.maps.Marker({
			position: {lat: events[i].location.lat, lng: events[i].location.lng},
			map: map,
		});

		// Set marker properties
		marker.id = events[i].id
		marker.name = events[i].name
		marker.body = events[i].body
		marker.address = events[i].address
		marker.link = events[i].link

		// Build the form out
		var form = $("#infoWindow").clone();

		form.find("#name").text(marker.name);
		form.find("#body").text(marker.body);
		form.find("#address").text(marker.address);
		form.find("#link").attr("href", marker.link);

		form.find(".directions").attr("id", "directions"+marker.id);
		form.find("#directions"+marker.id).attr("onclick", "redirectToDirections(" + marker.id + ")");

		// Create the data window
		var infoWindow = new google.maps.InfoWindow();
		infoWindow.setContent(form.html());

		marker.infoWindow = infoWindow;

		// Attach a event listener to the marker so that the info window opens when clicked
		google.maps.event.addListener(marker, 'click', function() {
			this.label.close();
			// Open the info window
        	this.infoWindow.open(map, this);
      	});

		// Create label for marker with marker's name
		var markerLabel = new google.maps.InfoWindow({
			closeBoxURL: "",
			content: marker.name
		});

		marker.label = markerLabel;

		// Open the label
		marker.label.open(map, marker);

		// Attach event listener to the infowindow so that when it is closed, the label reopens
      	google.maps.event.addListener(infoWindow, 'closeclick', function() {
      		marker.label.open(map, marker);
      	});

		// Push marker onto markers array
      	markers.push(marker);
	}

	// If event is specified on page load
	if(typeof goToEvent === "function") {
		goToEvent(markers,map);
	}

	if(typeof goToLocation === "function") {
		goToLocation(map);
	}
}

/*
 * Moves the map center to event location
 */
function searchItemSelected(id,map) {
	// Loop through events
	for (var i = markers.length - 1; i >= 0; i--) {
		var marker = markers[i];
		if (id === marker.id) {
			// Pan to marker
     		map.panTo(marker.getPosition());

     		// Reset search bar
     		$(".searchItem").remove();
     		$("#search").val("");

     		// Open marker info window
     		marker.infoWindow.open(map, marker);

     		break;
		}
	}
}

/*
 * Function for redirecting the user to google maps with the desired directions
 */
function redirectToDirections(id) {
	// Toggle Loading state
	$("#directions"+id).text("Please wait...").toggleClass("disabled");

	// Find marker
	var des;
	for (var i = markers.length - 1; i >= 0; i--) {
		if (markers[i].id === id) {
			des = markers[i].getPosition()
			break;
		}
	}

	// Try to get location
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(data) {
			if (data.coords) {
				// Build URL with coordinates from origin and destination
				var url = "https://www.google.com/maps?saddr=" + data.coords.latitude + "," + data.coords.longitude + "&daddr=" + des.lat() + "," + des.lng();
				$("#directions"+id).html("Directions <span class='glyphicon glyphicon-log-out'></span>").toggleClass("disabled");
				window.location = url;
			}
		// Show error message
		}, function() {
			alert("Geolocation is disabled. Try enabling location services for the browser and try again.");
			$("#directions"+id).html("Directions <span class='glyphicon glyphicon-log-out'></span>").toggleClass("disabled");
			window.location = url;
		});
	} else {
		alert("Geolocation is not supported by this browser.");
		$("#directions"+id).html("Directions <span class='glyphicon glyphicon-log-out'></span>").toggleClass("disabled");
		window.location = url;
	}
}
