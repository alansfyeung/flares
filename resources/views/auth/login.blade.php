{{-- Login page --}}
@extends('layouts.template')

@section('title', 'Login')

@section('navbar-sections')
{{-- Intentionally empty in order to hide navbar --}}
@endsection

@section('heading')
<h1>Login</h1>
@endsection

@section('content')
<div class="row">
	<div class="col-sm-6 col-sm-push-3">

        <h1 class="text-muted">Sign in to FLARES.</h1>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#passwd" aria-controls="passwd" role="tab" data-toggle="tab">Password</a></li>
            <li role="presentation"><a href="#forumsso" aria-controls="forumsso" role="tab" data-toggle="tab">Forums SSO</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="passwd">
                <form class="form auth-login-form" method="POST" action="{{ url('/login') }}">
                    <fieldset>
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="FLARES username" autocomplete="username">
                            @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="current-password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </fieldset>
                    <div class="form-group">
                        <div class="checkbox col-sm-6">
                            <label><input type="checkbox" name="remember"> Remember Me</label>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Login</button>
                        <input type="hidden" name="loginType" value="fallback"/>  {{-- Otherwise to be set at submit-time by the ng controller --}}
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>

            {{-- Explainer about Forum SSO --}}
            <div role="tabpanel" class="tab-pane" id="forumsso">
                <div class="alert alert-info">
                    <p>Instead of using a password, you can log into FLARES via the forums (single-sign on), if your
                        forums account is enabled as an admin user.</p>
                </div>
                <div class="text-right">
                    <a class="btn btn-default" href="{{ env('FL_HELP_FORUM_SSO_LINK') }}">Click here to access the forums</a>
                </div> 
            </div>
        </div>
	</div>
</div>
@endsection