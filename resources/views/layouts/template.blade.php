<!DOCTYPE html>
@if(View::hasSection('ng-app'))
<html lang="en" ng-app="@yield('ng-app')" class="env-{{ config('app.env', 'local') }}">
@else
<html lang="en" class="env-{{ config('app.env', 'local') }}">
@endif
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicons')
    <base href="{{url('/')}}/" data-href="{{url('/')}}">
    <title>@yield('title') | FLARES</title>
	<link href="assets/css/app.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{{ asset('assets/icons/favicon.ico') }}}">
</head>
<body flow-prevent-drop>
	@section('header')
	<header>
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
                <div class="navbar-header">
                    @section('navbar-mobile-toggle')
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapsible">
						<span class="sr-only">Toggle navigation</span>
						<span class="glyphicon glyphicon-menu-hamburger"></span>
                    </button>
                    @show
					<a class="navbar-brand" href="{{url('/')}}">
						<img alt="206 FLARES" src="{{{ asset('/assets/img/flareslogo.png') }}}" style="height: 20px;">
					</a>
				</div>
                @section('navbar-sections')
                    @include('partials.navbar')
                @show
			</div>
		</nav>
	</header>
	@show
    
    @section('main')
    @if(View::hasSection('ng-controller'))
    <div id="main" class="flares-main" ng-controller="@yield('ng-controller')" ng-cloak>
    @else
    <div id="main" class="flares-main">
    @endif
        <div class="page-header">
            <div class="container">
            @yield('heading')				
            </div>
        </div>
        <div class="container">
            {{-- Banners are hero units below the nav but above other content --}}
            @yield('banner')
            
            {{-- Render notifications or alerts --}}
            @yield('alerts')
            
            {{-- The main screen functionality --}}
            @yield('content')
        </div>
    </div>
    @show
	
	@section('footer')
	<footer>
		<div class="container">
			<h6>{{ $copyright or 'FLARES Falcon Leave Administration and Reporting System &copy; A. Yeung' }}</h6>
		</div>
	</footer>
	@show
    
    {{-- Print API token into the page --}}
    {{-- Todo: With OAuth2 --}}
    
    {{-- Core js --}}
    <script src="assets/js/jquery-1.11.3.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>

    {{-- Angular app js, if required --}}
    @section('ng-scripts')
    <script src="assets/js/angular.min.js"></script>
    <script src="assets/js/ui-bootstrap-0.14.2.min.js"></script>
    @show
    @stack('ng-scripts')

    {{-- Plugin/add-on/vendor js --}}
	<script src="assets/js/notification-popups.js"></script>
	@stack('vendor-scripts')
    
    {{-- Other custom js --}}
	@stack('scripts')
    
</body>
</html>