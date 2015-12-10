// ===========================================
//  flaresActivityOverview.js
//  Activity Overview screen
//  -- Divides into upcoming & archived activities
//  -- Upcoming is also grouped into sections    
// ===========================================

var flaresApp = angular.module('flaresActivityOverview', ['flaresBase']);
flaresApp.run(function($templateCache){
    $templateCache.put('activityContextMenuTemplate.html', '<div class="modal-header"><h4 class="modal-title">{{activity.type}} {{activity.name}}</h4><h5 class="modal-subtitle">{{activity.dateTopLine()}} {{activity.dateBottomLine()}}</h5></div> \
        <div class="modal-body"><a class="btn btn-block" ng-repeat="menuItem in bodyButtons" ng-class="menuItem.classNames" ng-click="parseClick(menuItem.click)">{{menuItem.label}}</a></div> \
    <div class="modal-footer"><a class="btn btn-block" ng-repeat="cancelItem in footerButtons" ng-class="cancelItem.classNames" ng-click="cancel()">{{cancelItem.label}}</a></div>');
    $templateCache.put('activityOverviewFilterTemplate.html', '<div class="modal-header"><h4 class="modal-title">Filter categories</h4></div> \
        <div class="modal-body"><a class="btn btn-block" ng-repeat="menuItem in bodyButtons" ng-class="menuItem.classNames" ng-click="parseClick(menuItem.click)">{{menuItem.label}}</a></div> \
    <div class="modal-footer"><a class="btn btn-block" ng-repeat="cancelItem in footerButtons" ng-class="cancelItem.classNames" ng-click="cancel()">{{cancelItem.label}}</a></div>');
    
    
});

flaresApp.factory('activityCategoriser', function(){    
    var categoryDefinitions = [
        {
            name: 'Earlier this month',
            nameKey: 'earlier_this_month',
            isArchive: false,
            comparator: function(theirDate){
                // Determine if in the next logical week
                var today = new Date();
                
                // This month
                var thisMonthNum = today.getMonth();
                var thisMonthYear = today.getFullYear();
                var thisMonthFirstDayTS = (new Date()).getTime();
                
                if (theirDate.getTime() >= (new Date(thisMonthYear, thisMonthNum, 1)).getTime() && theirDate.getTime() < today.getTime()){
                    return true;
                }
                return false;
            }
        },
        {
            name: 'This week',
            nameKey: 'this_week',
            isArchive: false,
            comparator: function(theirDate){
                // Determine if in same logical week
                var today = new Date();
                var msDiff = today.getTime() - theirDate.getTime();
                var dayDiff = Math.ceil(msDiff / (1000 * 60 * 60 * 24));
                if (dayDiff < 7){
                    if (today.getDay() - dayDiff >= 1 && today.getDay() - dayDiff <= 7){      // testing if weekday within 1-7 (this monday to this sunday)
                        return true;
                    }
                }
                return false;
            }
        },
        {
            name: 'Upcoming this month',
            nameKey: 'upcoming_this_month',
            isArchive: false,
            comparator: function(theirDate){
                // Determine if it's within this month, using the unix ts
                var today = new Date();
                
                // Next month
                var nextMonthNum = today.getMonth()+1;
                var nextMonthYear = today.getFullYear();
                if (nextMonthNum > 11){
                    nextMonthNum -= 12;
                    nextMonthYear++;
                }
                var nextMonthFirstDayTS = (new Date(nextMonthYear, nextMonthNum, 1)).getTime();
                if (theirDate.getTime() >= today.getTime() && theirDate.getTime() < nextMonthFirstDayTS){
                    return true;
                }
                return false;
            }
        },
        {
            name: 'Next month',
            nameKey: 'next_month',
            isArchive: false,
            comparator: function(theirDate){
                // Determine if it's within this month, using the unix ts
                var today = new Date();
                
                // Next month
                var nextMonthNum = today.getMonth()+1;
                var nextMonthYear = today.getFullYear();
                if (nextMonthNum > 11){
                    nextMonthNum -= 12;
                    nextMonthYear++;
                }
                
                // Two months time
                var twoMonthsNum = today.getMonth()+2;
                var twoMonthsYear = today.getFullYear();
                if (twoMonthsNum > 11){
                    twoMonthsNum -= 12;
                    twoMonthsYear++;
                }
                
                var nextMonthFirstDayTS = (new Date(nextMonthYear, nextMonthNum, 1)).getTime();
                var twoMonthsFirstDayTS = (new Date(twoMonthsYear, twoMonthsNum, 1)).getTime();
                if (theirDate.getTime() >= nextMonthFirstDayTS && theirDate.getTime() < twoMonthsFirstDayTS){
                    return true;
                }
                return false;
            }
        },
        {
            name: 'Future',
            nameKey: 'future',
            isArchive: false,
            comparator: function(theirDate){
                // Anything 3+ months onwards
                var today = new Date();
                
                // Two months time
                var twoMonthsNum = today.getMonth()+2;
                var twoMonthsYear = today.getFullYear();
                if (twoMonthsNum > 11){
                    twoMonthsNum -= 12;
                    twoMonthsYear++;
                }
                
                var twoMonthsFirstDayTS = (new Date(twoMonthsYear, twoMonthsNum, 1)).getTime();
                if (theirDate.getTime() >= twoMonthsFirstDayTS){
                    return true;
                }
                return false;
            }
        },
        {
            name: 'Last week',
            nameKey: 'last_week',
            isArchive: true,
            comparator: function(theirDate){
                var today = new Date();
            }
        },
        {
            name: 'Before last week',
            nameKey: 'before_last_week',
            isArchive: true,
            comparator: function(date){
                // if ()
            }
        },
        {
            name: 'Past',
            nameKey: 'past',
            isArchive: true,
            comparator: function(date){
                // if ()
            }
        }
        
    ];

    function sortUpcoming(activities){        // expect activities to be an array of flat activity records
        var categorised = [];
        var summary = { total: 0 };
        
        // Create blank arrays for each upcoming definition
        // for (var x in categoryDefinitions){
            // var cd = categoryDefinitions[x];
            // if (!cd.isArchive){
                // categorised[cd.nameKey] = {name: cd.nameKey, activities: []};
            // }
        // }
        
        // Loop through activities, assign to the relevant section
        for (var i in categoryDefinitions){
            var cd = categoryDefinitions[i];
            if (!cd.isArchive){     // we want the non-archived i.e. the upcomings
                var inThisCategory = [];
                for (var j in activities){
                    var activity = activities[j];
                    if (!cd.isArchive){                        
                        if (cd.comparator(activity.start_date)){
                            inThisCategory.push(activity);
                            summary.total++;
                        }
                    }
                }
                categorised.push({name: cd.name, activities: inThisCategory});
            }
        }
        
        // Object.getPrototypeOf(categorised).summary = summary;
        categorised.summary = summary;
        return categorised;
    }
    
    function sortArchived(activities){
        
    }
    
    return {
        sortUpcoming: sortUpcoming
    };
});

flaresApp.controller('activityOverviewController', function($scope, $filter, $window, $location, $controller, $uibModal, flaresAPI, flaresLinkBuilder, activityCategoriser){
    
    var veController = $controller('baseViewEditController', {$scope: $scope});
    
    $scope.upcoming = [];
    $scope.archived = [];
    $scope.selectedActivity = {};
    
    $scope.state = {
        mode: '',
        isRequested: false,
        isLoaded: false,
        isAsync: false
    };
    
    $scope.selectActivity = function(activity){
        $scope.selectedActivity = activity;
        openContextMenu();
    };

    $scope.goToNewActivity = function(){
        $window.location.href = flaresLinkBuilder('activity').new().getLink();
    };
    
    function openContextMenu(){
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'activityContextMenuTemplate.html',
            controller: 'activityContextMenuController',
            scope: $scope,
            size: 'sm',
            resolve: {
                context: function(){
                    return $scope.selectedActivity;
                }
            }
        });

        modalInstance.result.then(function(){
            // Item clicked
        }, function(){
            // Cancellation
        });
    }

    
    // ===========
    // Load
    
    $scope.state.mode = parseUrl().mode || 'upcoming';
    $scope.$watch('state.mode', function(){
        $location.path($scope.state.mode || 'upcoming');
    });
    
    
    flaresAPI('activity').getAll().then(function(response){
        // categories the respose data into upcoming
        if (typeof response === 'object'){
            var activities = response.data.activities.map(function(currentValue, index, array){
                return new Activity(currentValue);
            });
            angular.forEach(activities, function(activity){
                veController.convertToDateObjects(['start_date', 'end_date', 'created_at', 'updated_at'], activity);
            });
            
            $scope.upcoming = activityCategoriser.sortUpcoming(activities); 
console.log($scope.upcoming);            
        }
    });
    
    
    
    // ====================
    // Function decs
    
    function parseUrl(){
        var path = $location.path();
        var pathFrags = (path.indexOf('/') === 0 ? path.substring(1) : path).split('/');
        return {
            mode: pathFrags[0] ? pathFrags[0] : null
        };
    };
    
    
    
    function Activity(data){
        angular.extend(this, data);
    }
    
    
    Activity.prototype = {
        dateTopLine: function(){
            if (this.is_half_day || this.start_date.getTime() === this.end_date.getTime()){
                return $filter('date')(this.start_date, 'EEE dd MMM yyyy');
            }
            return $filter("date")(this.start_date, 'EEE dd MMM yyyy') + ' – ';
        },
        dateBottomLine: function(){
            if (this.is_half_day){
                return '+ Half day';
            }
            if (this.start_date.getTime() === this.end_date.getTime()){
                return '++ Full day';
            }
            
            var dayDiff = Math.floor((this.end_date.getTime() - this.start_date.getTime()) / (1000*60*60*24)) + 1;
            return $filter("date")(this.end_date, 'EEE dd MMM yyyy') + ' (' + dayDiff + ' days)';
        }
    };
    
});

flaresApp.controller('activityContextMenuController', function ($scope, $parse, $filter, $window, $modalInstance, flaresLinkBuilder, context){
    
    $scope.activity = context;
    // $scope.activity.dateInfo = function(){
        // if ($scope.activity.is_half_day){
            // return $filter('date')($scope.activity.start_date) + ' — half day';
        // }
        // if ($scope.activity.start_date.getTime() === $scope.activity.end_date.getTime()){
            // return $filter("date")($scope.activity.start_date) + ' — full day';
        // }
        // return $filter("date")($scope.activity.start_date) + ' – ' + $filter("date")($scope.activity.end_date);
    // };
    
    $scope.bodyButtons = [{
        label: 'View activity',
        classNames: ['btn-default'],
        click: 'viewActivity'
    }, {
        label: 'Mark roll',
        classNames: ['btn-success'],
        click: 'markRoll'
    }];
    $scope.footerButtons = [{
        label: 'Close',
        classNames: ['btn-default']
    }];
    
    var clickActions = {
        viewActivity: function(){
            var frag = [$scope.activity.acty_id, 'view', 'details'];
            $window.location.href = flaresLinkBuilder('activity', frag).getLink();
            // Or if you want to return a value to the parent controller,
            // $modalInstance.close();
        },
        editActivity: function(){
            var frag = [$scope.activity.acty_id, 'edit', 'details'];
            $window.location.href = flaresLinkBuilder('activity', frag).getLink();
        },
        editRoll: function(){
            var frag = [$scope.activity.acty_id, 'edit', 'rollbuilder'];
            $window.location.href = flaresLinkBuilder('activity', frag).getLink();
        },
        markRoll: function(){
            var frag = [$scope.activity.acty_id];
            $window.location.href = flaresLinkBuilder('activity', frag).roll().getLink();
        }
    };
    
    $scope.parseClick = function(actionName){
        // ($parse(expr)($scope));          // i think this is way too confusing
        var func = $parse(actionName + '()');
        func(clickActions);
    };
    
    $scope.cancel = function(){
        $modalInstance.dismiss('cancel');
    };
    // $scope.ok = function () {
        // $modalInstance.close($scope.selected.item);
    // };
});