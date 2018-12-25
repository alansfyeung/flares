{{-- Login page --}}
@extends('layouts.template')

@section('ng-app', 'flaresLogin')
@section('ng-controller', 'loginController')
@section('title', 'Login')

@push('scripts')
<script src="/ng-app/components/auth/flaresLogin.js"></script>
@endpush

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
                <form class="form" method="POST" action="{{ url('/login') }}">
                    <fieldset>
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="FLARES username">
                            @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
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

            {{-- Explainer about Forum SSO --}}
            <div role="tabpanel" class="tab-pane" id="forumsso">
                <div class="alert alert-info">Instead of using a password, you can log into FLARES via the <a href="{{ env('FL_HELP_FORUM_SSO_LINK') }}">forums (single-sign on)</a>. </div>
            </div>
        </div>
	</div>
</div>
@endsection