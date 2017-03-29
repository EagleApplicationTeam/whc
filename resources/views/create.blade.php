@extends('layouts.app')

@section('content')
	<!-- Hidden from window -->
	<div class="hidden">
		<!-- Form for adding event info -->
		<form id="form">
			<div class="form-group">
				<label for="exampleInputEmail1">Name</label>
				<input type="times" class="form-control" id="exampleInputEmail1" placeholder="Event Name">
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
			<div onclick="save(1)" class="saveButton btn btn-success">Save</div>
			<div id="delete" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal">Delete</div>
		</form>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div id="errorMessage" class="alert alert-danger hidden" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button><span class="glyphicon glyphicon-exclamation-sign"></span><strong> Whoops!</strong><span id="errorMessageText"></span>
				</div>
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
			addEvent(map);
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
				draggable: true
			});

			// Set the id of the marker
			marker.id = events[i].id;

			// Create the data window
			var infoWindow = new google.maps.InfoWindow({
				content: $("#form").html()
			});

			// Attach a event listener to the marker so that the info window opens when clicked
			google.maps.event.addListener(marker, 'click', function() {
	        	infoWindow.open(map, marker);
	      	});

			// Call the position update method when drag ends
	      	google.maps.event.addListener(marker, 'dragend', function() {
	      		updatePosition(this.id, this.getPosition());
	      	});
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
			console.log(response);
		}).catch((error) => {
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

		// Add click event
		google.maps.event.addListener(marker, 'click', function() {
        	infoWindow.open(map, marker);
      	});

		// Add drag end event
      	google.maps.event.addListener(marker, 'dragend', function() {
        	updatePosition(response.data.id, marker.getPosition());
      	});
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
			addNewMarker(center, map, response);
			console.log(response.data);
		}).catch((error) => {
			showErrorMessage("An error occurred while trying to add a new marker.");
		});
	}

	/*
	 * Show error message
	 */
	function showErrorMessage(message) {
		$("#errorMessage").removeClass("hidden");
		$("#errorMessageText").text(message);
	}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&callback=initMap"></script>
@endpush
