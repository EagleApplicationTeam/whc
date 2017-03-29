@extends('layouts.app')

@section('content')
	<!-- Hidden from window -->
	<div class="hidden">
		<!-- Form for adding event info -->
		<div id="form">
			<form>
				<div class="form-group">
					<label for="exampleInputEmail1">Name</label>
					<input type="text" class="form-control" id="name" placeholder="something">
				</div>
				<div class="form-group">
					<label for="exampleInputPassword1">Start Date</label>
					<input type="date" class="form-control" id="exampleInputPassword1">
				</div>
				<div class="form-group">
					<label for="exampleInputPassword1">End Date</label>
					<input type="date" class="form-control" id="exampleInputPassword1">
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox"> Live
					</label>
				</div>
				<div id="save" class="saveButton btn btn-success">Save</div>
				<div id="delete" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal">Delete</div>
			</form>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2" id="messageContainer">
				
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="col-md-2" style="text-align: center;">
							<button class="btn btn-primary btn-block" id="addEvent"><span class="glyphicon glyphicon-plus"></span> Add Event</button>
						</div>
						<div class="col-md-10">
							<div id="map" style="min-height: 550px; min-width: 100%;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script>
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
		axios.get("/events").then((response) => {
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

			// Attach a event listener to the marker so that the info window opens when clicked
			google.maps.event.addListener(marker, 'click', function() {
				// Create info window structure
				var form = $("#form").clone();

				// Setup the form ids and actions
				form.find("form").attr("data-id",this.id);
				form.find("#save").attr("onclick", "saveInfo("+this.id+")");
				form.find("#delete").attr("onclick", "deleteEvent("+this.id+")");

				// Set input values
				form.find("#name").attr("value",this.name);

				// Create the data window
				var infoWindow = new google.maps.InfoWindow({
					content: form.html()
				});

				// Open info window
	        	infoWindow.open(map, this);
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

		// Add click event
		google.maps.event.addListener(marker, 'click', function() {
			// Create info window structure
			var form = $("#form").clone();

			// Setup the form ids and actions
			form.find("form").attr("data-id",this.id);
			form.find("#save").attr("onclick", "saveInfo("+this.id+")");
			form.find("#delete").attr("onclick", "deleteEvent("+this.id+")");

			// Set input values
			form.find("#name").attr("value",this.name);

			// Create the data window
			var infoWindow = new google.maps.InfoWindow({
				content: form.html()
			});

			// Open info window
        	infoWindow.open(map, this);  	
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

	function saveInfo(id) {
		// Get DOM of marker info window
		var form = $("form[data-id='" + id + "']");

		// Setup save button loading state
		var saveButton = form.find("#save");
		saveButton.text("Saving...").toggleClass("disabled");

		// Attempt request to update the event's info
		axios.patch("/event/"+id, {
			name: form.find("#name").val()
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

	/*
	 * Delete an event
	 */
	function deleteEvent(id) {
		// Get the event form DOM
		var form = $("form[data-id='" + id + "']");

		// Toggle loading state
		var deleteButton = form.find("#delete");
		deleteButton.text("Deleting...").toggleClass("disabled");

		// Attempt request to delete event
		axios.delete("/event/"+id).then((response) => {
			deleteButton.text("Deleted!")

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

	/*
	 * Show error message
	 */
	function showErrorMessage(message) {
		// Setup html elements with message
		var html = '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-exclamation-sign"></span><strong> Whoops!</strong><span> ' + message + '</span></div>'
		// Present the alert
		$("#messageContainer").html(html);
		
	}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&callback=initMap"></script>
@endpush
