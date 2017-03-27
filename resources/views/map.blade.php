<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>WHC Map</title>
	<style>
        html {
            min-height: 100%;
            min-width: 100%;
        }

        body {
            min-height: 100%;
            min-width: 100%;
            padding: 0;
            margin: 0;
        }

        #map {
            min-height: 100vh;
            min-width: 1000px;
        }
    </style>
    <link rel="stylesheet" href="/css/app.css">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>

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

	<div id="map"></div>
	<script src="/js/app.js"></script>
	<script src="/js/map.js"></script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&callback=initMap"></script>
</body>
</html>