<!DOCTYPE html>
<html lang="en" ng-app="@yield('ng-app')">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    @section('favicons')
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/assets/icons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/icons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/icons/favicon-16x16.png">
    <link rel="manifest" href="/assets/icons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/assets/icons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    @show

    <title>@yield('title') | FLARES</title>

	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/css/flares-bs-extn.css" rel="stylesheet">
	<link href="/assets/css/flares.css" rel="stylesheet">
	<link href="/assets/css/alerts.css" rel="stylesheet">
    
</head>
<body flow-prevent-drop ng-cloak>
	@section('header')
	<header>
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapsible">
						<span class="sr-only">Toggle navigation</span>
						<span class="glyphicon glyphicon-menu-hamburger"></span>
					</button>
					<a class="navbar-brand" href="/">
						<img alt="206 FLARES" src="/assets/img/flareslogo.png" style="height: 20px;">
					</a>
				</div>
				@yield('navbar-sections')
			</div>
		</nav>
	</header>
	@show
    
    @section('main')
    <div id="main" class="flares-main" ng-controller="@yield('ng-controller')">
        <div class="page-header">
            <div class="container">
            @yield('heading')				
            </div>
        </div>
        <div class="container">
            @yield('banner')
            @yield('alerts')
            @yield('content')
        </div>
    </div>
    @show
	
	@section('footer')
	<footer>
		<div class="container">
			<h6>FLARES Falcon Leave Automated REporting System &copy; 2015 Alan Yeung, 206 Army Cadet Unit</h6>
		</div>
	</footer>
	@show
    
    {{-- Core js --}}
    <script src="/assets/js/jquery-1.11.3.min.js"></script>
	<script src="/assets/js/angular.min.js"></script>
	<script src="/assets/js/ui-bootstrap-0.14.2.min.js"></script>
	<script src="/assets/js/bootstrap.min.js"></script>

    {{-- Application js --}}
	<script src="/app/shared/flaresBase.js"></script>
	<script src="/app/shared/flaresBase-controllers.js"></script>
	<script src="/app/shared/flaresBase-services.js"></script>
    
    {{-- Plugin/add-on/vendor js --}}
	<script src="/assets/js/notification-popups.js"></script>
	@yield('ng-script')
	
</body>
</html>
