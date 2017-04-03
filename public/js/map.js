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

    // Attach query function to search input
    $("#search").on("input",function() {
    	// Remove the search items DOM
    	$(".searchItem").remove();
    	// Get query string
    	var query = $("#search").val();
    	// If query string is greater than 0, perform the search
    	if (query.length != 0) {
    		queryEvents(query, map);
    	}
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
			map: map
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
		var infoWindow = new google.maps.InfoWindow({
			content: form.html()
		});

		marker.infoWindow = infoWindow;

		// Attach a event listener to the marker so that the info window opens when clicked
		google.maps.event.addListener(marker, 'click', function() {
			// Open the info window
        	this.infoWindow.open(map, this);
      	});

		// Push marker onto markers array
      	markers.push(marker);
	}
}

/*
 * Perform search on events
 */
function queryEvents(query, map) {
	// Loop through markers array
	for (var i = markers.length - 1; i >= 0; i--) {
		var name = markers[i].name;
		var lname = name.toLowerCase();
		query = query.toLowerCase();
		// If event name contains query substring
		if (lname.includes(query)) {
			// Append event to results element
 			$(".results").append("<div class='searchItem' data-id='" + markers[i].id + "'><strong>" + name + "</strong></div>");
		}
	}

	// Attach click event to search item
	$(".searchItem").click(function() {
		var id = $(this).data("id");
		// Move map center to event
		searchItemSelected(id, map);
	});
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

	console.log(des);

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
			alert("An error occured while trying to determine location.");
			$("#directions"+id).html("Directions <span class='glyphicon glyphicon-log-out'></span>").toggleClass("disabled");
				window.location = url;
		});
	} else {
		alert("Location not supported");
		$("#directions"+id).html("Directions <span class='glyphicon glyphicon-log-out'></span>").toggleClass("disabled");
				window.location = url;
	}
}