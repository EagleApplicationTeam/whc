function initMap() {
   	var  map = new google.maps.Map(document.getElementById('map'), {
      	center: {lat: 34.5414014, lng: -112.4716222},
      	zoom: 9
    });

    getEvents(map);
}

function getEvents(map) {
	axios.get("/events").then((response) => {
		addMarkers(response.data, map);
	}).catch((error) => {
		console.log(error)
		alert("There was a problem.");
	});
}

function addMarkers(events, map) {
	for (var i = events.length - 1; i >= 0; i--) {
		var marker = new google.maps.Marker({
			position: {lat: events[i].location.lat, lng: events[i].location.lng},
			map: map
		});

		var infoWindow = new google.maps.InfoWindow({
			content: $("#form").html()
		});

		google.maps.event.addListener(marker, 'click', function() {
        	infoWindow.open(map, marker);
      	});
	}
}