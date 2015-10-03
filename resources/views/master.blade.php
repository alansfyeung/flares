<!DOCTYPE html>
<html lang="en" ng-app="@yield('ng-app')">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title') | FLARES</title>
	
	<!-- <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'> -->
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/flares.css" rel="stylesheet">

	<script src="/js/jquery-1.11.3.min.js"></script>
	<script src="/js/angular.min.js"></script>
	<script src="/js/angular-route.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>
</head>
<body flow-prevent-drop>
	@section('header')
	<header>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="/">
						<img alt="206 FLARES" src="/img/flareslogo.png" style="height: 20px;">
					</a>
				</div>
			  <ul class="nav navbar-nav">
				<li class="dropdown">
				  <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Members <span class="caret"></span></a>
				  <ul class="dropdown-menu">
					<li><a href="/members">Search</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/members/new">New Onboarding</a></li>
					<li><a href="/members/mass">Mass Actions</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/members/stats">Statistics</a></li>
					<li><a href="/members/reports">Reporting</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Activity <span class="caret"></span></a>
				  <ul class="dropdown-menu">
					<li><a href="/activity/roll">Mark Roll</a></li>
					<li><a href="#">Check AWOLs</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">Parade State</a></li>
					<li><a href="#">View the Roll</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">Add new activity</a></li>
					<li><a href="#">Manage activities</a></li>
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
		</nav>
	</header>
	@show
	
	
	<div id="main" class="container-fluid" ng-controller="@yield('ng-controller')">
		@yield('banner')
		@yield('heading')
		@yield('content')
	</div>
	
	@section('footer')
	<footer>
		<div class="container-fluid">
			<h6>FLARES Falcon Leave Automated REporting System &copy; 2015 Alan Yeung, 206 Army Cadet Unit</h6>
		</div>
	</footer>
	@show

	<script>
	var flaresBase = angular.module('flaresBase', []).config(function($locationProvider) {
		$locationProvider.html5Mode(false).hashPrefix('!');
	});
	
	flaresBase.factory('memberApiService', function($http){
		return function() {
			
		};
	});
	
	flaresBase.directive('bsShowTab', function($location){
        return { 
            link: function (scope, element, attr) {
                element.click(function(e) {
                    e.preventDefault();
                    $(element).tab('show');		// Show the BS3 tab
					
					if (scope.workflow){
						scope.$apply(function(){
							scope.workflow.path.tab = attr.ariaControls;
						});
					}
                });
            }
        };
		
	});
	flaresBase.directive('spreadsheetNav', function(){
		return {
			link: function(scope, element, attr){
				element.keydown(function(e){
					// console.log(e.keyCode);
				});
			}
		};
	});
	flaresBase.directive('dropdownToggle', function(){
        return { 
            link: function (scope, element, attr) {
                element.click(function(e) {
                    e.preventDefault();
                });
            }
        };
		
	});
	</script>
	
	@yield('ng-script')
	
</body>
</html>