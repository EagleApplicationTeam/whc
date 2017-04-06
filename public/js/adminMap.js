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

	// Add click event to button
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
		addMarkers(response.data, map);
	}).catch((error) => {
		// Show error message
		showErrorMessage("There was a problem retrieving the events.");
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
			draggable: true
		});

		// Set the id of the marker
		marker.id = events[i].id;
		marker.name = events[i].name;
		marker.body = events[i].body;
		marker.address = events[i].address;
		marker.link = events[i].link
		marker.live = events[i].live

		// Create info window structure
		var form = $("#form").clone();

		// Setup the form ids and actions
		form.find("form").attr("data-id",marker.id);
		form.find("#save").attr("onclick", "saveInfo("+marker.id+")");
		form.find("#delete").attr("onclick", "deleteEvent("+marker.id+")");

		// Set input values
		form.find("#name").attr("value",marker.name);
		form.find("#body").text(marker.body);
		form.find("#address").attr("value", marker.address);
		form.find("#link").attr("value", marker.link);

		if (marker.live !== 1) {
			form.find("#live").removeAttr("checked");
		}

		form.find("#directions").attr("id", "directions"+marker.id);
		form.find("#directions"+marker.id).attr("onclick", "redirectToDirections(" + marker.id + ")");

		// Create the data window
		var infoWindow = new google.maps.InfoWindow({
			content: form.html()
		});

		marker.infoWindow = infoWindow;

		// Attach a event listener to the marker so that the info window opens when clicked
		google.maps.event.addListener(marker, 'click', function() {
			// Open info window
        	this.infoWindow.open(map, this);
      	});

		// Call the position update method when drag ends
      	google.maps.event.addListener(marker, 'dragend', function() {
      		updatePosition(this.id, this.getPosition());
      	});

      	// Push the marker onto the array of markers
      	markers.push(marker);
	}
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
 * Function for adding a new marker
 */
function addNewMarker(position, map, response) {
	// Create marker
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		draggable: true
	});

	// Create info window
	var infoWindow = new google.maps.InfoWindow({
		content: response.data.name
	});

	// Add marker property data
	marker.id = response.data.id
	marker.name = response.data.name
	marker.body = response.data.body
	marker.address = response.data.address
	marker.link = response.data.link

	// Create info window structure
	var form = $("#form").clone();

	// Setup the form ids and actions
	form.find("form").attr("data-id",marker.id);
	form.find("#save").attr("onclick", "saveInfo("+marker.id+")");
	form.find("#delete").attr("onclick", "deleteEvent("+marker.id+")");

	// Set input values
	form.find("#name").attr("value",marker.name);
	form.find("#body").text(marker.body);
	form.find("#address").attr("value", marker.address);
	form.find("#link").attr("value", marker.link);

	// Create the data window
	var infoWindow = new google.maps.InfoWindow({
		content: form.html()
	});

	marker.infoWindow = infoWindow;

	// Add click event
	google.maps.event.addListener(marker, 'click', function() {
		// Open info window
    	this.infoWindow.open(map, this);  	
  	});

	// Add drag end event
  	google.maps.event.addListener(marker, 'dragend', function() {
  		// Update the position of the event
    	updatePosition(response.data.id, marker.getPosition());
  	});

  	// Push onto the markers array
  	markers.push(marker);
}

/*
 * Add new event to map
 */
function addEvent(map) {
	// Get center
	var center = map.getCenter();

	// Attempt request to create new event
	axios.post("/event", {
		lat: center.lat(),
		lng: center.lng()
	}).then((response) => {
		// Add the event data to a new marker
		addNewMarker(center, map, response);
	}).catch((error) => {
		// Show error message
		showErrorMessage("An error occurred while trying to add a new marker.");
	});
}

/*
 * Save event info
 */
function saveInfo(id) {
	// Get DOM of marker info window
	var form = $("form[data-id='" + id + "']");

	var name = form.find("#name").val();
	var body = form.find("#body").val();
	var address = form.find("#address").val();
	var link = form.find("#link").val();

	var checked = false

	if (form.find("#live").is(":checked")) {
		checked = true;
	}

	if (name != "") {
		// Setup save button loading state
		var saveButton = form.find("#save");
		saveButton.text("Saving...").toggleClass("disabled");

		// Attempt request to update the event's info
		axios.patch("/event/"+id, {
			name: name,
			body: body,
			address: address,
			link: link,
			live: checked
		}).then((response) => {
			// Toggle loaded state and reset after 2 seconds 
			saveButton.text("Saved!");
			setTimeout(function() {
				saveButton.toggleClass("disabled").text("Save");
			}, 2000);
		}).catch((error) => {
			// Show error message
			showErrorMessage("There was a problem updating the event information.");

			// Return button to normal state
			saveButton.toggleClass("disabled").text("Save");
		});
	}
}

/*
 * Delete an event
 */
function deleteEvent(id) {
	if (id != null) {
		// Get the event form DOM
		var form = $("form[data-id='" + id + "']");

		// Toggle loading state
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
					}
				}
			}, 2000);
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