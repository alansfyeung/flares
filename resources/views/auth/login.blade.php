{{--
Login page
--}}
@extends('template')

@section('ng-app', 'flaresLogin')
@section('ng-controller', 'loginController')
@section('title', 'Login')


@section('heading')
<h1>Login</h1>
@endsection

@section('content')
<div class="row">
	<div class="col-sm-6 col-sm-push-3">
		<form class="form" method="POST" action="/auth/login">
                    

            <fieldset ng-show="loginType == 'fallback'">
            
                <div class="form-group">
                    <h1 class="text-muted">Sign in to FLARES.</h1>
                </div>
            
                @foreach ($errors->all() as $error)
                <div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign"></span> {{ $error }}</div>
                @endforeach

                <div class="form-group">
                    <a class="btn btn-block btn-primary" ng-click="">Sign in using 206 forums</a>
                </div>
                
                <span class="login-hr-device"></span>
            
                <div class="form-group">
                    <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Flares username">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                </div>
            </fieldset>
            
		    <div class="form-group">
                <input type="hidden" name="loginType" value="fallback"/>  {{-- Otherwise to be set at submit-time by the ng controller --}}
		        <button type="submit" class="btn btn-primary pull-right">Login</button>
		        <div class="checkbox">
		        	<label><input type="checkbox" name="remember"> Remember Me</label>
		        </div>
		    </div>
            
            {!! csrf_field() !!}

		</form>
	</div>
</div>
@endsection

@section('ng-script')
<script src="/app/components/auth/flaresLogin.js"></script>
@endsection
