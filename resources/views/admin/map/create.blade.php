@extends('layouts.app')

@section('content')
	<!-- Hidden from window -->
	<div class="hidden">
		<!-- Form for adding event info -->
		<div id="form">
			<form>
				<div class="form-group">
					<label for="name">Name</label>
					<input type="text" class="form-control" id="name" placeholder="something">
				</div>
				<div class="form-group">
					<label for="body">Body</label>
					<textarea name="body" id="body" cols="30" rows="4" class="form-control"></textarea>
				</div>
				<div class="form-group">
					<label for="address">Address</label>
					<input type="text" name="address" id="address" class="form-control">
				</div>
				<div>
					<label for="link">Link</label>
					<input type="text" name="link" id="link" class="form-control">
					<p class="help-block">Make sure there is a http:// or https:// in the link.</p>
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" id="live" checked> Live
					</label>
					<p class="help-block">Check this to make the event visible to the public.</p>
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
							<div id="map" style="min-height: 90vh; min-width: 100%;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script src="/js/adminMap.js"></script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBope1OFljyrx9BHNeaC9YJ3Oxx76i6XFY&callback=initMap"></script>
@endpush
