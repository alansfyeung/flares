// ===============
// Flares Dashboard

var memberSearchApp = angular.module('flaresDashboard', ['flaresBase']);
memberSearchApp.controller('dashboardController', function($scope, $http){
	
	$scope.stats = {
		members: {}
	};
	
	$http.get('/api/dashboard').then(function(response){
		$scope.stats = response.data;
	});
	
});
	