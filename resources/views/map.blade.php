<!-- Copyright Eagle Application Team 2017 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>WHC Map</title>
	<!-- Styles so that the map takes up the entire window -->
	<style>
        html {
            min-height: 100vh;
            min-width: 100vw;
        }

        body {
            min-height: 100vh;
            min-width: 100vw;
            padding: 0;
            margin: 0;
        }

        #map {
            min-height: 100vh;
            min-width: 100vw;
        }

        .searchBarContainer {
            width: 50vw; 
            height: 35px; 
            position: absolute; 
            right: 5%;
            /*-webkit-transform: translateX(-50%);
            transform: translateX(-50%);*/
            /*top: 20px; 
            left: 30%; */
            background-color: #fff; 
            z-index: 5; 
            border-radius: 5px; 
            padding: 5px;
            margin-top: 12px;
            overflow: hidden;
        }

        .searchBarInput {
            min-width: 100%; 
            max-height: 55px; 
            border: none; 
            outline: none;
            margin-bottom: 7px;
        }

        .searchItem {
            min-width: 50vw; 
            min-height: 20px; 
            background-color: #fff; 
            padding: 10px;
            -webkit-transform: translateX(-5px);
            transform: translateX(-5px);
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
        <div class="hidden">
            <div id="infoWindow">
                <h3 id="name"></h3>
                <p id="body"></p>
                <h4 id="address"></h4>
                <h4><a id="link" href="#" target="_blank">Website</a></h4>
                <div class='btn btn-primary directions' onclick="redirectToDirections()">Directions <span class='glyphicon glyphicon-log-out'></span></div>
            </div>
        </div>
		<!-- Map Container -->
        <div id="app">
            <!-- Search Bar -->
            <div class="searchBarContainer">
                <input id="search" type="text" class="searchBarInput" placeholder="Search" autocomplete="off">
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