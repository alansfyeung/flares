<!DOCTYPE html>
<html lang="en" ng-app="@yield('ng-app')">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partial.favicons')
    <title>@yield('title') | FLARES</title>
	<link href="/assets/css/app.css" rel="stylesheet">
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
			<h6>FLARES Falcon Leave Automated REporting System &copy; 2015–2016 Alan Yeung, 206 Army Cadet Unit</h6>
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
