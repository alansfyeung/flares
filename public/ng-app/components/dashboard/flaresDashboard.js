// ===============
// Flares Dashboard

var flaresDashboard = angular.module('flaresDashboard', ['flaresBase']);
flaresDashboard.controller('dashboardController', function($scope, $http){
	
	$scope.stats = {
		members: {}
	};
	
	$http.get('/api/dashboard').then(function(response){
		$scope.stats = response.data;
	});
	
});
	