<!DOCTYPE html>
<html lang="en" ng-app="@yield('ng-app')">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title') | FLARES</title>
	
	<!-- <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'> -->
	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/css/flares.css" rel="stylesheet">

	<script src="/assets/js/jquery-1.11.3.min.js"></script>
	<script src="/assets/js/angular.min.js"></script>
	<script src="/assets/js/ui-bootstrap-0.14.2.min.js"></script>
	<script src="/assets/js/bootstrap.min.js"></script>
</head>
<body ng-controller="@yield('ng-controller')" flow-prevent-drop>
	@section('header')
	<header>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapsible">
						<span class="sr-only">Toggle navigation</span>
						<span class="glyphicon glyphicon-menu-hamburger"></span>
					</button>
					<a class="navbar-brand" href="/">
						<img alt="206 FLARES" src="/assets/img/flareslogo.png" style="height: 20px;">
					</a>
				</div>
				
				<div class="collapse navbar-collapse" id="navbar-collapsible">
					<ul class="nav navbar-nav">
						<li class="dropdown">
						  <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Members <span class="caret"></span></a>
						  <ul class="dropdown-menu">
							<li><a href="/members" name="menu.member.search">Search</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="/members/new" name="menu.member.new">New Onboarding</a></li>
							<li><a href="/members/mass" name="menu.member.massactions">Mass Actions</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="/members/stats" name="menu.member.stats">Statistics</a></li>
							<li><a href="/members/reports" name="menu.member.reporting">Reporting</a></li>
						  </ul>
						</li>
						<li class="dropdown">
						  <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Activity <span class="caret"></span></a>
						  <ul class="dropdown-menu">
							<li><a href="/activities" name="menu.activity.overview">Overview</a></li>
							<li><a href="/activities/search" name="menu.activity.search">Search</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="/activities/new" name="menu.activity.new">New Activity</a></li>
							<li role="separator" class="divider"></li>
							<!-- <li><a href="/activity/roll" name="menu.activity.roll">Mark Roll</a></li> -->
							<li><a href="/activity/awol" name="menu.activity.awol">Check AWOLs</a></li>
						  </ul>
						</li>
						<li class="dropdown">
						  <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Awards <span class="caret"></span></a>
						  <ul class="dropdown-menu">
							<li><a href="#">Mark Roll</a></li>
							<li><a href="#">Review Roll</a></li>
						  </ul>
						</li>
						<li class="dropdown">
						  <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
						  <ul class="dropdown-menu">
							<li><a href="#">System Users</a></li>
							<li><a href="#">Audit</a></li>
						  </ul>
						</li>
						<li class="dropdown">
						  <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Payments <span class="caret"></span></a>
						  <ul class="dropdown-menu">
							<li><a href="#">System Users</a></li>
							<li><a href="#">Audit</a></li>
						  </ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>
        @yield('heading')
	</header>
	@show
	
	
	<div id="main" class="container-fluid">
		@yield('banner')
		@yield('alerts')
		@yield('content')
	</div>
	
	@section('footer')
	<footer>
		<div class="container-fluid">
			<h6>FLARES Falcon Leave Automated REporting System &copy; 2015 Alan Yeung, 206 Army Cadet Unit</h6>
		</div>
	</footer>
	@show

	<script src="/app/shared/flaresBase.js"></script>
	@yield('ng-script')
	
</body>
</html>