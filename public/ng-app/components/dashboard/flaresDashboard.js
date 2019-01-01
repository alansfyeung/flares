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
    retrievePendingApprovalList();

    $scope.selectApproval = function(approval){
        $window.location.href = flResource('approval')
            .setFragment([approval.dec_appr_id, 'edit'])
            .getLink();
    };    

    // ==================
    // Functions 

    function retrievePendingApprovalList() {
        flAPI('approval').getAll({
            params: { status: 'pending' },
        }).then(function (response) {
            $scope.state.approvalsLoaded = true;
            if (response.data && angular.isArray(response.data.approvals)) {
                // Use approvals object as-is, and invent a status name for it as well. 
                $scope.approvals = response.data.approvals;
                angular.forEach($scope.approvals, function(approval) {
                    approval.statusName = 'Pending';        // Hardcode to 'pending'.
                    approval.created_at = new Date(approval.created_at);
                    approval.updated_at = new Date(approval.updated_at);
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
