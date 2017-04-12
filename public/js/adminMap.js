// Array to keep track of the markers
var markers = [];

/*
 * Setup the map and get the events
 */
function initMap() {
	// Create the map
	var map = new google.maps.Map(document.getElementById('map'), {
		center: {lat: 34.5414014, lng: -112.4716222},
  		zoom: 9
	});

	// Get events
	getEvents(map);

	// Add click event to add marker button
	$("#addEvent").click(function() {
		// Attach the addEvent function to the button
		addEvent(map);

		// Blur the button so it looks nicer
		$(this).blur();
	});
}

/*
 * Gets the events from the database and adds them to the map
 */
function getEvents(map) {
	// AJAX request to server
	axios.get("/map/events").then((response) => {
		// Add the markers to the map
		for (var i = response.data.length - 1; i >= 0; i--) { 
			addMarker(map,response.data[i], null);
		}
	}).catch((error) => {
		// Show error message
		console.log(error);
		showErrorMessage("There was a problem retrieving the events.");
	});
}

/*
 * Function for adding a marker to the map
 */
function addMarker(map, event, position) {
	// If the position parameter is not null
	if (position != null) {
		// Create the marker and set the marker position
		var marker = new google.maps.Marker({
			position: position,
			map: map,
			draggable: true
		});
	}

	// Create the marker and set the marker position
	var marker = new google.maps.Marker({
		position: {lat: event.location.lat, lng: event.location.lng},
		map: map,
		draggable: true
	});

	// Set the attributes of the marker
	marker.id = event.id;
	marker.name = event.name;
	marker.body = event.body;
	marker.address = event.address;
	marker.link = event.link
	marker.live = event.live
	marker.priority = event.priority;

	// Create info window structure
	var form = $("#form").clone();

	// Setup the form ids and actions
	form.find("form").attr("data-id",marker.id);
	form.find("#save").attr("onclick", "saveInfo("+marker.id+")");
	form.find("#delete").attr("onclick", "deleteEvent("+marker.id+")");
	form.find("#address").attr("data-id", marker.id);

	// Set the form input values
	form.find("#name").attr("value",marker.name);
	form.find("#body").text(marker.body);
	form.find("#address").attr("value", marker.address);
	form.find("#link").attr("value", marker.link);

	// Make sure the live checkbox is in the right state
	if (marker.live !== 1) {
		form.find("#live").removeAttr("checked");
	}

	// Make sure the priority checkbox is in the right state
	if (marker.priority !== 1) {
		form.find("#priority").removeAttr("checked");
	}

	// Setup the directions button's attributes
	form.find("#directions").attr("id", "directions"+marker.id);
	form.find("#directions"+marker.id).attr("onclick", "redirectToDirections(" + marker.id + ")");

	// Create the info window and set the content equal to the form
	var infoWindow = new google.maps.InfoWindow({
		content: form.html()
	});

	// Assign the info window property to the newly created info window
	marker.infoWindow = infoWindow;

	// Attach a event listener to the marker so that the info window opens when clicked
	google.maps.event.addListener(marker, 'click', function() {
		// Close other info windows
		for (var i = markers.length - 1; i >= 0; i--) {
			if (markers[i].id !== this.id) {
				markers[i].infoWindow.close();
			}
		}

		// Open info window
    	this.infoWindow.open(map, this);
  	});

	// Call the position update method when drag ends
  	google.maps.event.addListener(marker, 'dragend', function() {
  		updatePosition(this.id, this.getPosition());
  	});

  	// Push the marker onto the global array of markers
  	markers.push(marker);
}

/*
 * Function for updating a marker position
 */
function updatePosition(id, position) {
	// Attempt request to update event location
	axios.patch("/event/"+id+"/location", {
		lat: position.lat(),
		lng: position.lng()
	}).then((response) => {
		// console.log(response);
		// Show success message TODO
	}).catch((error) => {
		// Show error message
		showErrorMessage("An error occured while trying to update the position of the marker.");
	});
}

/*
 * Add new event to map
 */
function addEvent(map) {
	// Get map center
	var center = map.getCenter();

	// Attempt request to create new event
	axios.post("/event", {
		lat: center.lat(),
		lng: center.lng()
	}).then((response) => {
		// Add the location data to a new marker
		addMarker(map, response.data, center);
	}).catch((error) => {
		// Show error message
		showErrorMessage("An error occurred while trying to add a new marker.");
	});
}

/*
 * Save location info
 */
function saveInfo(id) {
	// Get DOM of marker info window with ID
	var form = $("form[data-id='" + id + "']");

	// Get all values from form fields
	var name = form.find("#name").val();
	var body = form.find("#body").val();
	var address = form.find("#address").val();
	var link = form.find("#link").val();

	// Get values from check boxes
	var checked = false, priority = false;

	if (form.find("#live").is(":checked")) {
		checked = true;
	}

	if (form.find("#priority").is(":checked")) {
		priority = true;
	}

	// Make sure the name is not empty
	if (name != "") {
		// Setup save button loading state
		var saveButton = form.find("#save");
		saveButton.text("Saving...").toggleClass("disabled");

		// Attempt request to update the location's info
		axios.patch("/event/"+id, {
			name: name,
			body: body,
			address: address,
			link: link,
			live: checked,
			priority: priority
		}).then((response) => {
			// Toggle loaded state
			saveButton.text("Saved!");
			// Attempt to geolocate the position of the location from the address
			tryGeo(id, address);
			// Return to normal state after 2 seconds
			setTimeout(function() {
				saveButton.toggleClass("disabled").text("Save");
			}, 2000);
		}).catch((error) => {
			// Show error message
			showErrorMessage("There was a problem updating the event information.");
			console.log(error);
			// Return button to normal state
			saveButton.toggleClass("disabled").text("Save");
		});
	}
}

/*
 * Delete a location
 */
function deleteEvent(id) {
	if (id != null) {
		// Get the event form DOM
		var form = $("form[data-id='" + id + "']");

		// Toggle loading state of button
		var deleteButton = form.find("#delete");
		deleteButton.text("Deleting...").toggleClass("disabled");

		// Attempt request to delete event
		axios.delete("/event/"+id).then((response) => {
			deleteButton.text("Deleted!");

			// After 2 seconds remove the marker
			setTimeout(function() {
				// Loop through the markers array
				for (var i = markers.length - 1; i >= 0; i--) {
					// Marker id matches specified id, remove the marker
					if (markers[i].id === id) {
						markers[i].setMap(null);
						break;
					}
				}
			}, 1500);
		}).catch((error) => {
			// Return to normal state
			deleteButton.text("Delete").toggleClass("disabled");

			// Show error message
			showErrorMessage("There was a problem deleting the event.");
		});
	}
}

/*
 * Show error message
 */
function showErrorMessage(message) {
	// Setup html elements with message
	if (message != null || message != '') {
		var html = '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-exclamation-sign"></span><strong> Whoops!</strong><span> ' + message + '</span></div>';
		// Present the alert
		$("#messageContainer").html(html);
	}
}

/*
 * Geocode address of marker
 */
function tryGeo(id, address) {
	// Loop through local markers
	for (var i = markers.length - 1; i >= 0; i--) {
		var marker = markers[i];
		// If marker is found
		if (marker.id === id) {
			// Get map
			var map = marker.getMap();

			// Check if address is null
			if (address === null || address == "") {
				alert("Could not geocode address");
				return;
			}

			// Instantiate geocoder
			var geocoder = new google.maps.Geocoder();

			// Try to geocode address
			geocoder.geocode({'address' : address}, function(results, status) {
				if (status === "OK") {
					// Get postition
					var position = results[0].geometry.location;

					// Set position of marker
					marker.setPosition(position);

					// Set map center to geolocated position
					map.setCenter(position);

					// Reset all marker infowindows and reopen specified infowindow
					for (var j = markers.length - 1; j >= 0; j--) {
						markers[j].infoWindow.close()

						if (markers[j].id === id) {
							markers[j].infoWindow.open(map, marker);
						}
					}

					// Send API request to update location
					updatePosition(id, position);
				} else {
					showErrorMessage("Unable to locate address.");
				}
			});

			break;
		}
	}
}