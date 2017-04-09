@extends('layouts.app')

@section('content')
<div class="container">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">
				Utilities
			</div>
			<div class="panel-body">
				@if(session('links'))
					<table class="table">
						<tr>
							<th>Location Name</th>
							<th>Link</th>
							<th>Live</th>
						</tr>
						@foreach(session('links') as $event)
							<tr>
								<td>{{ $event->name }}</td>
								<td>{{ url('/location/' . $event->id) }}</td>
								<td>{{ $event->live ? 'Yes' : 'No' }}</td>
							</tr>
						@endforeach	
					</table>
				@endif
				<a href="/utility/links" class="btn btn-primary">Generate Location Links</a>
				<p class="help-block">This will generate a list of links for the locations in the WHC Map database. When one of these links is visited, the user will be taken straight to the location on the map and shown the details for that location.</p>
				<hr>
				<div class="form-group">
					<label for="address">Address</label>
					<input id="address" type="text" class="form-control" style="max-width: 70%;">
				</div>
				<div class="form-group">
					<p><strong id="result" class="hidden"></strong></p>
				</div>
				
				<div class="form-group">
					<button id="addressButton" href="/utility/generate" class="btn btn-primary">Generate Link</button>
					<p class="help-block">Input the address of a location and get a link that will take the user to the location on the map when they visit the link.</p>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&libraries=places"></script>
<script>
	$(document).ready(function() {
		$("#addressButton").click(function() {
			$(this).toggleClass("disabled").text("Generating...").blur();
			var address = $("#address").val();
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({'address' : address}, function(results, status) {
				if (status === "OK") {
					var url = "{{ url('/location') }}";
					url += "?lat=" + results[0].geometry.location.lat() + "&lng=" + results[0].geometry.location.lng();
					$("#result").removeClass("hidden").text(url);
				} else {
					alert("Could not find location for address.");
				}
				$("#addressButton").toggleClass("disabled").text("Generate Link");
			});
		});
	});
</script>
@endpush