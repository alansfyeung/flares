@extends('master')

@section('heading')
<h1>Login</h1>
@endsection

@section('content')
<div class="row">
	<div class="col-sm-6">
		<form class="form" method="POST" action="/auth/login">
		    {!! csrf_field() !!}

		    <div class="form-group">
		        <input type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="Forums user name">
		    </div>

		    <div class="form-group">
		        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
		    </div>

		    <div class="form-group">
		        <button type="submit" class="btn btn-primary pull-right">Login</button>
		        <div class="checkbox">
		        	<label><input type="checkbox" name="remember"> Remember Me</label>
		        </div>
		    </div>

		</form>
	</div>
</div>

@endsection