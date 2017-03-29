@extends('layouts.app')

@section("content")
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					Password
				</div>
				<div class="panel-body" style="max-width: 400px;">
					<form action="/account/psswd" method="post">
						<div class="form-group{{ $errors->has('old') ? ' has-error' : '' }}">
							<label for="old">Old Password</label>
							<div class="input-group">
								<input id="input1" name="old" type="password" class="form-control">
								<span class="input-group-btn">
							        <button class="btn btn-default show" data-index="1" type="button"><span id="show1" class="glyphicon glyphicon-eye-open"></span></button>
							    </span>
							</div>	

							@if ($errors->has('old'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
						</div>
						<div class="form-group{{ $errors->has('new') ? ' has-error' : '' }}">
							<label for="new">New Password</label>
							<div class="input-group">
								<input id="input2" name="new" type="password" class="form-control">
								<span class="input-group-btn">
							        <button class="btn btn-default show" data-index="2" type="button"><span id="show2" class="glyphicon glyphicon-eye-open"></span></button>
							    </span>
							</div>
							
							@if ($errors->has('new'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('new') }}</strong>
                                </span>
                            @endif
						</div>
						<div class="form-group{{ $errors->has('new_confirmation') ? ' has-error' : '' }}">
							<label for="confirm">Confirm New Password</label>
							<div class="input-group">
								<input id="input3" name="new_confirmation" type="password" class="form-control">
								<span class="input-group-btn">
							        <button class="btn btn-default show" data-index="3" type="button"><span id="show3" class="glyphicon glyphicon-eye-open"></span></button>
							    </span>
							</div>
							
							@if ($errors->has('new_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('new_confirmation') }}</strong>
                                </span>
                            @endif
						</div>
						<div class="form-group">
							<button class="btn btn-warning">
								Change Password <span class="glyphicon glyphicon-lock"></span>
							</button>
						</div>

						{{ csrf_field() }}
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	$(document).ready(function() {
		// Event listener for showing password values
		$(".show").click(function() {
			var index = $(this).data("index");
			var element = $("#input"+index);
			var type = element.attr("type");
			if (type === "password") {
				element.attr("type","text");
			} else {
				element.attr("type","password");
			}
			$("#show"+index).toggleClass("glyphicon-eye-close glyphicon-eye-open");
			$(this).blur();
		});
	});
</script>
@endpush