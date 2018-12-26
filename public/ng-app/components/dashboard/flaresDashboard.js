// ===============
// Flares Dashboard

var flaresDashboard = angular.module('flaresDashboard', ['flaresBase']);
flaresDashboard.controller('dashboardController', function ($scope, $window, flAPI, flResource) {

    $scope.state = {
        approvalsLoaded: false,
        approvalsRemaining: false,
        activityLoaded: false,
    };

    $scope.stats = {};       // Need to initialize?
    $scope.approvals = [];
    
    retrieveDashboardData();
    retrieveApprovalList();

    $scope.selectApproval = function(approval){
        $window.location.href = flResource('approval')
            .setFragment([$scope.activeMember.regt_num, 'view', 'details'])
            .getLink();
    };    

    // ==================
    // Functions 

    function retrieveApprovalList() {
        flAPI('approval').get(['pending']).then(function (resp) {
            $scope.state.approvalsLoaded = true;
            if (response.data && angular.isArray(response.data.approvals)) {
                // Use approvals object as-is, and invent a status name for it as well. 
                $scope.approvals = response.data.approvals;
                angular.forEach($scope.approvals, function(approval) {
                    approval.statusName = 'Pending';        // Hardcode to 'pending'.
                });
                $scope.state.approvalsRemaining = $scope.approvals.length > 0;
            }
            else {
                $scope.state.approvalsRemaining = false;
            }
        });
    }

    function retrieveDashboardData() {
        flAPI('dashboard').getAll().then(function (resp) {
            $scope.stats = resp.data;
        });
        flAPI('dashboard').get(['activity']).then(function (resp) {
            $scope.activity = resp.data;
            // Data is not going to be homogenous though. 
            // TBC
        });
    }

});
