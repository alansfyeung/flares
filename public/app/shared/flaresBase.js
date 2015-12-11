// ==================================
//   The base module for Flares
//   All page apps should extend off this module
// ==================================

var flaresBase = window.flaresBase || angular.module('flaresBase', ['ui.bootstrap']);

// ==========================
// Pre-run config
flaresBase.run(function($templateCache){
    // UIB modal backdrop template
    $templateCache.put('template/modal/backdrop.html', '<div uib-modal-animation-class="fade"  modal-in-class="in" ng-style="{\'z-index\': 1040 + (index && 1 || 0) + index*10}"></div>');
    // UIB modal window template
    $templateCache.put('template/modal/window.html', '<div modal-render="{{$isRendered}}" tabindex="-1 role="dialog" class="modal" uib-modal-animation-class="fade" modal-in-class="in" ng-style="{\'z-index\': 1050 + index*10, display: \'block\'}"> \
        <div class="modal-dialog" ng-class="size ? \'modal-\' + size : \'\'"><div class="modal-content" uib-modal-transclude></div></div> \
    </div>');
});
flaresBase.config(function($locationProvider) {
    $locationProvider.html5Mode(false).hashPrefix('!');
});

// =================
// Base directives
// 1. contextMenu - Deploys the $uibModal as a context menu
// 2. displayMode - for View/Edit screens, toggles fields depending on 'mode' frag in path
// 4. bsShowTab - for BS3 tabs
// 5. spreadsheetNav - WIP - for member onboarding editing

flaresBase.directive('contextMenu', function(){
    return { 
        restrict: 'E',
        link: function (scope, element, attr){
            // WIP
        }
    };
});
flaresBase.directive('displayMode', function(){
    return { 
        restrict: 'A',
        link: function (scope, element, attr) {
            var expr = 'state.path.mode';
            // console.log('directiving', scope.$eval(expr));
            if (scope.$eval(expr) !== attr.displayMode){
                element.hide();
            }
            
            scope.$watch(expr, function(newValue){
                if (newValue !== attr.displayMode){
                    element.hide();
                    return;
                }
                element.show();
            });
        }
    };
});
flaresBase.directive('sidebarToggle', function(){
    return {
        restrict: 'A',
        link: function(scope, element, attr){
            element.click(function(e){
                if (scope.state && !scope.state.hasOwnProperty('showSidebar')){
                    scope.state.showSidebar = false;
                }

                // Toggle it
                scope.state.showSidebar = ! scope.state.showSidebar;
                
                // Look for fl-content and fl-sidebar
                if (scope.state.showSidebar){
                    angular.element('.fl-content').removeClass('col-sm-12').addClass('col-sm-9').addClass('col-sm-pull-3');
                    angular.element('.fl-sidebar').removeClass('hidden');
                }
                else {      // keep it hidden
                    angular.element('.fl-sidebar').addClass('hidden');
                    angular.element('.fl-content').removeClass('col-sm-9').removeClass('col-sm-pull-3').addClass('col-sm-12');
                }
            });
        }
    };
});
flaresBase.directive('bsShowTab', function($location){
    return { 
        link: function (scope, element, attr) {
            element.click(function(e) {
                e.preventDefault();
                $(element).tab('show');		// Show the BS3 tab
                
                if (scope.state){
                    scope.$apply(function(){
                        scope.state.path.tab = attr.ariaControls;
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


// =================
// Base filters

flaresBase.filter('yesNo', function(){
    return function(input){
        return input && input !== '0' ? 'Yes' : 'No';
    }
});
flaresBase.filter('markBlanks', function(){
    return function(input){
        return input ? input : '--';
    }
});
flaresBase.filter('is', function() {
    return function(items, field) {
        var result = {};
        angular.forEach(items, function(value, key) {
            if (!value.hasOwnProperty(field)) {
                result[key] = value;
            }
        });
        return result;
    };
});