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
            background-color: #fff; 
            z-index: 5; 
            border-radius: 5px; 
            padding-top: 5px;
            margin-top: 11px;
        }

        .searchBarInput {
            min-width: 100%;
            max-width: 45vw; 
            max-height: 55px; 
            border: none; 
            outline: none;
            margin-bottom: 2px;
        }

        .pac-card {
            margin: 10px 10px 0 0;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            background-color: #fff;
            font-family: Roboto;
          }

          #pac-container {
            padding-bottom: 12px;
            margin-right: 0px;
          }

          .pac-controls {
            display: inline-block;
            padding: 5px 11px;
          }

          .pac-controls label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
          }

          #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 0 0 0;
            text-overflow: ellipsis;
            width: 95%;
            border: none;
            outline: none;
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
                <input id="pac-input" type="text" placeholder="Search" autocomplete="off">
                <div class="results">
                    
                </div>
            </div>
            <!-- Map -->
            <div id="map"></div>
        </div>
		<!-- Helper packages and functions -->
		<script src="/js/app.js"></script>
         @if(Session::has('event'))
            <!-- Script that will take map to route event -->
            <script>
                // When an event object is passed to the view, go to it
                function goToEvent(markersArray, map) {
                    // Get the id of the event
                    var id = {{ session('event')->id }};
                    // Loop through the markers and find right one
                    for (var i = markersArray.length - 1; i >= 0; i--) {
                        var marker = markersArray[i];
                        if (marker.id == id) {
                            // Set the position of the map on the marker
                            map.setCenter(marker.getPosition());
                            // Set the zoom of the map
                            map.setZoom(17);

                            // Open the info window
                            marker.infoWindow.open(map, marker);
                        }
                    }
                }
            </script>
        @endif

        @if(Session::has('location'))
        <script>
            function goToLocation(map) {
                // Setup variables
                var lat = {{ session('location')['lat'] }};
                var lng = {{ session('location')['lng'] }};

                // Make new marker
                // var marker = new google.maps.Marker({
                //     map: map,
                //     position: {
                //         lat: lat,
                //         lng: lng
                //     }
                // });

                // Set map center
                map.setCenter({
                    lat: lat,
                    lng: lng
                });

                // Set map zoom
                map.setZoom(16);
            }
        </script>
        @endif
		<!-- Map logic script -->
		<script src="/js/map.js"></script>
		<!-- Google Maps API script -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&callback=initMap&libraries=places"></script>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-96839075-1', 'auto');
          ga('send', 'pageview');
        </script>
	</body>
</html>