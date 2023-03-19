// ===============
// Flares Dashboard

var flaresDashboard = angular.module('flaresDashboard', ['flaresBase']);
flaresDashboard.controller('dashboardController', function ($scope, $window, flAPI, flResource) {

    $scope.state = {
        approvalsLoaded: false,
        approvalsRemaining: false,
        activityLogLoaded: false,
        activityLogCounter: 0,
    };

    var activityLogPerLoad = 20;
    
    $scope.stats = {};       // Need to initialize?
    $scope.activityLog = [];
    $scope.approvals = [];
    
    retrieveDashboardStats();
    retrieveActivityLog();
    retrievePendingApprovalList();

    $scope.goToSingleApproval = function(approval){
        $window.location.href = flResource('approval')
            .setFragment([approval.dec_appr_id, 'edit'])
            .getLink();
    };
    $scope.openSingleApprovalInWindow = function(approval, event) {
        if (event) {
            event.stopPropagation();
        }
        $window.open(
            flResource('approval')
                .setQuery({no_header: 1})
                .setFragment([approval.dec_appr_id, 'edit'])
                .getLink(), 
            'Approve Decoration Request', 
            'width=800, height=600'
        );
    };
    $scope.selectLog = function(logType, logId) {
        switch(logType) {
            case 'APPR':
                $window.location.href = flResource('approval')
                    .setFragment([logId, 'view'])
                    .getLink();
                break;
            case 'MBR':
                $window.location.href = flResource('member')
                    .setFragment([logId, 'view', 'details'])
                    .getLink();
                break;
        }
    }
    $scope.loadMoreLog = retrieveActivityLog;

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

    function retrieveDashboardStats() {
        flAPI('dashboard').getAll().then(function (resp) {
            $scope.stats = resp.data;
        });
    }

    function retrieveActivityLog() {
        flAPI('dashboard').get(['log'], {
            params: {
                offset: $scope.state.activityLogCounter,
                limit: activityLogPerLoad,
            },
        }).then(function (resp) {
            if (resp.data && angular.isArray(resp.data)) {
                let newLogData = resp.data;
                angular.forEach(newLogData, function(value){
                    value.log_date = new Date(value.log_date);
                });
                $scope.activityLog = $scope.activityLog.concat(newLogData);
                $scope.state.activityLogLoaded = true;
                $scope.state.activityLogCounter += newLogData.length;
            }
            else {
                console.warn('No data available in response', resp);
            }
        });
    }

});
