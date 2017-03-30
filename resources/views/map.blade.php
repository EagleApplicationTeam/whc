<!-- Copyright Eagle Application Team 2017 -->
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

        .searchBarContainer {
            width: 40vw; 
            height: 50px; 
            position: absolute; 
            left: 50%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
            /*top: 20px; 
            left: 30%; */
            background-color: #fff; 
            z-index: 5; 
            border-radius: 5px; 
            padding: 10px;
            margin-top: 20px;
        }

        .searchBarInput {
            min-width: 100%; 
            min-height: 100%; 
            border: none; 
            outline: none;
        }

        .searchItem {
            min-width: 500px; 
            min-height: 20px; 
            background-color: #fff; 
            padding: 10px; 
            margin-left: -10px;
        }

        .searchItem:hover {
            background-color: #3097D1;
            color: #fff;
            cursor: pointer;
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
		<!-- Map Container -->
        <div id="app">
            <!-- Search Bar -->
            <div class="searchBarContainer">
                <input id="search" type="text" class="searchBarInput" placeholder="Search for events..." autocomplete="off">
                <div class="results">
                    
                </div>
            </div>
            <!-- Map -->
            <div id="map"></div>
        </div>
		<!-- Helper packages and functions -->
		<script src="/js/app.js"></script>
		<!-- Map logic script -->
		<script src="/js/map.js"></script>
		<!-- Google Maps API script -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&callback=initMap"></script>
	</body>
</html>