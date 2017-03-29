<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>WHC Map</title>
	<!-- Styles so that the map takes up the entire window -->
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
    <!-- Bootstrap styles -->
    <link rel="stylesheet" href="/css/app.css">
    <!-- Add CSRF Token variable to window object -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
	<body>
		<!-- Map element -->
		<div id="map"></div>
		<!-- Helper packages and functions -->
		<script src="/js/app.js"></script>
		<!-- Map logic script -->
		<script src="/js/map.js"></script>
		<!-- Google Maps API script -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&callback=initMap"></script>
	</body>
</html>