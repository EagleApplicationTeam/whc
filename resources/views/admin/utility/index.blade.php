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
				<p class="help-block">Clicking this button will generate a list of links for locations. When one of these links is visited, the user will be taken straight to the location on the map.</p>
			</div>
		</div>
	</div>
</div>
@endsection