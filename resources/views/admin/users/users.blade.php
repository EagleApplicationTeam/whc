@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					Users
				</div>
				<div class="panel-body">
					<table class="table">
						<tr>
							<th>Name</th>
							<th>Verfied</th>
							<th>Actions</th>
						</tr>
						{{-- Loop through each user --}}
						@foreach($users as $user)
							<tr>
								<td>{{ $user->name }}</td>
								<td>{{ $user->verified ? "Yes" : "No" }}</td>
								<td>
									@if($user->id != 1 && $user->id != Auth::user()->id)

									<form action="/users/{{$user->id}}" style="display: inline;" method="POST">
										@if($user->verified)
											<button class="btn btn-warning">Revoke <span class="glyphicon glyphicon-lock"></span></button>
										@else
											<button class="btn btn-success">Verify <span class="glyphicon glyphicon-ok"></span></button>
										@endif
										{{ method_field("PATCH") }}
										{{ csrf_field() }}
									</form>

									<form action="/users/{{$user->id}}" style="display: inline;" method="POST">
										<button class="btn btn-danger">Remove <span class="glyphicon glyphicon-remove"></span></button>
										{{ method_field("DELETE") }}
										{{ csrf_field() }}
									</form>
									
									@endif
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection