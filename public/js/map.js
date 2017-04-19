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

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
      searchBox.setBounds(map.getBounds());
    });

    var searchMarkers = [];

    // Instantiate new geocoder
    var geocoder = new google.maps.Geocoder;

    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener('places_changed', function() {
      	var places = searchBox.getPlaces();

      	// If places returns no results
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

	        console.log(place);

	        // Create a marker for the place.
	        var marker = new google.maps.Marker({
	          	map: map,
	          	place: {
	          		placeId: place.id,
	          		location: place.geometry.location
	          	}   	
	        });

	        // Setup the map zoom and position
	        map.setZoom(18);
			map.setCenter(place.geometry.location);

	        // Instantiate InfoWindow object
	        var infoWindow = new google.maps.InfoWindow();

	        var content = "<p>" + place.name + "<p>";
	        if (place.opening_hours.open_now) {
	        	content += "<strong>Open</strong>"
	        } else {
	        	content += "<strong>Closed</strong>"
	        }

	        // Set the content of the info window and open it
	        infoWindow.setContent(content);
	        infoWindow.open(map, marker);
	        
	        // Push onto the search markers array
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
		alert("There was a problem retrieving the locations.");
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
		marker.priority = events[i].priority;

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
		marker.infoWindow.id = marker.id;

		// Attach a event listener to the marker so that the info window opens when clicked
		google.maps.event.addListener(marker, 'click', function() {
			var tM = this;
			// Loop through markers and close other info windows
			for (var i = markers.length - 1; i >= 0; i--) {
				if (markers[i].id != tM.id) {
					markers[i].infoWindow.close();
					if (markers[i].priority) {
						markers[i].label.open(map, markers[i]);
					}
				}
			}

			// Open the info window
        	this.infoWindow.open(map, this);
        	this.infoWindow.setZIndex(1000);

        	// Close marker label
        	this.label.close();
      	});

      	// Create label for marker with marker's name
		var markerLabel = new google.maps.InfoWindow({
			content: marker.name,
			disableAutoPan: true
		});

		// Set the label attribute to the marker label object
		marker.label = markerLabel;

		// Attach event listener to the infowindow so that when it is closed, the label reopens
      	google.maps.event.addListener(marker.infoWindow, 'closeclick', function() {
      		for (var i = markers.length - 1; i >= 0; i--) {
      			if (map.getZoom() >= 16 && !markers[i].priority) {
       				markers[i].label.open(map, markers[i])
      			} else if (map.getZoom() < 16 && markers[i].priority) {
      				markers[i].label.open(map, markers[i])
      			}
      		}
      	});

      	// If the marker is a priority
      	if (marker.priority) {
			// Open the label
			marker.label.open(map, marker);
      	}

		// Push marker onto markers array
      	markers.push(marker);
	}

	// If event is specified on page load
	if(typeof goToEvent === "function") {
		goToEvent(markers,map);
	}

	// If location is specified on page load
	if(typeof goToLocation === "function") {
		goToLocation(map);
	}

	google.maps.event.addListener(map, 'zoom_changed', function() {
		var zoom = this.getZoom();
		// If zoom level is 16, open the non priority markers
    	if (zoom === 16) {
    		for (var i = markers.length - 1; i >= 0; i--) {
	    		if (!markers[i].priority) {
	    			markers[i].label.open(map, markers[i]);
	    		}
	    	}
	    // If zoom level is less than 16, close non-priority labels
    	} else if (zoom < 16) {
    		for (var j = markers.length - 1; j >= 0; j--) {
    			if (!markers[j].priority) {
    				markers[j].label.close();
    			}
    		}
    	}
    });
}

/*
 * Function for redirecting the user to google maps with the desired directions
 */
function redirectToDirections(id) {
	// Toggle Loading state
	$("#directions"+id).text("Please wait...").toggleClass("disabled");

	// Find specified marker
	var des;
	for (var i = markers.length - 1; i >= 0; i--) {
		// If marker id matches supplied id
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
		}, function() {
			// Show error message
			alert("Geolocation is disabled. Try enabling location services for the browser and try again.");
			$("#directions"+id).html("Directions <span class='glyphicon glyphicon-log-out'></span>").toggleClass("disabled");
			window.location = url;
		});
	} else {
		// Show error message
		alert("Geolocation is not supported by this browser.");
		$("#directions"+id).html("Directions <span class='glyphicon glyphicon-log-out'></span>").toggleClass("disabled");
		window.location = url;
	}
}
