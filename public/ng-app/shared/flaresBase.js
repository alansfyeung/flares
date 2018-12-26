(function () {
    'use strict';

    // ==================================
    //   The base module for Flares
    //   All page apps should extend off this module
    // ==================================

    var flaresBase = window.flaresBase || angular.module('flaresBase', ['ui.bootstrap']);

    // ==========================
    // Pre-run config
    // 1a. Our app uses ! mode rather than html5 history (which would muck with Laravel routing too much)
    // 1b. Create a $httpProvider interceptor, and whack the crsf token to the top of everything. This enables API auth. 
    // 2a. Cache some templates.
    
    flaresBase.config(['$locationProvider', '$httpProvider', function ($locationProvider, $httpProvider) {
        // ! mode
        $locationProvider.html5Mode(false).hashPrefix('!');
        // CRSF Token 
        registerApiTokenInterceptor($httpProvider);
    }]);

    flaresBase.run(['$http', '$templateCache', function ($http, $templateCache) {
        // Preload/precache some templates
        registerTemplate($http, $templateCache, 'template/modal/backdrop.html', '/ng-app/shared/uibModalBackdrop.html');
        registerTemplate($http, $templateCache, 'template/modal/window.html', '/ng-app/shared/uibModalWindow.html');
    }]);

    // =================
    // Register Constants
    // 1. Flares Resource Definitions - A source of truth for factories/services
    // =================

    flaresBase.constant('flResourceDefinitions', {
        refData: {
            apiBase: '/api/refdata',
            singular: 'refdata',
            plural: 'refdata'
        },
        member: {
            apiBase: '/api/member',
            singular: 'member',
            plural: 'members',
            nestedResources: ['posting', 'picture', 'status', 'decoration'],
            aliases: [{
                search: ['members', 'search']
            }]
        },
        activity: {
            apiBase: '/api/activity',
            singular: 'activity',
            plural: 'activities',
            nestedResources: ['roll', 'awol'],
            aliases: [{
                roll: ['activity', 'roll'],
                awol: ['activities', 'awol']
            }],
        },
        decoration: {
            apiBase: '/api/decoration',
            singular: 'decoration',
            plural: 'decorations',
            aliases: [{
                search: ['decorations', 'search']
            }]
        },
        approval: {
            apiBase: '/api/approval',
            singular: 'approval',
            plural: 'approvals',
            aliases: [{
                pending: ['approval', 'pending']
            }]
        },
        user: {
            apiBase: '/api/users',
            singular: 'user',
            plural: 'users',
        },
    });


    // =================
    // Base directives
    // 1. contextMenu - Deploys the $uibModal as a context menu
    // 2. displayMode - for View/Edit screens, toggles fields depending on 'mode' frag in path
    // 4. bsShowTab - for BS3 tabs
    // 5. spreadsheetNav - WIP - for member onboarding editing

    flaresBase.directive('contextMenu', function () {
        return {
            restrict: 'E',
            link: function (scope, element, attr) {
                // WIP
            }
        };
    });
    flaresBase.directive('displayMode', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attr) {
                var pathModeExpr = 'state.path.mode';
                // console.log('directiving', scope.$eval(pathModeExpr));
                if (scope.$eval(pathModeExpr) !== attr.displayMode) {
                    element.hide();
                }

                scope.$watch(pathModeExpr, function (newValue) {
                    if (newValue !== attr.displayMode) {
                        element.hide();
                        return;
                    }
                    element.show();
                });
            }
        };
    });
    flaresBase.directive('sidebarToggle', function () {
        // Warning: Deprecated
        console.warn('Directive SidebarToggle is deprecated; use ui.bootstrap.dropdown instead');
        return {
            restrict: 'A',
            link: function (scope, element, attr) {
                element.click(function (e) {
                    if (scope.state && !scope.state.hasOwnProperty('showSidebar')) {
                        scope.state.showSidebar = false;
                    }

                    // Toggle it
                    scope.state.showSidebar = !scope.state.showSidebar;

                    // Look for fl-content and fl-sidebar
                    if (scope.state.showSidebar) {
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
    flaresBase.directive('bsShowTab', function ($location) {
        return {
            link: function (scope, element, attr) {
                element.click(function (e) {
                    e.preventDefault();
                    $(element).tab('show');		// Show the BS3 tab

                    if (scope.state) {
                        scope.$apply(function () {
                            scope.state.path.tab = attr.ariaControls;
                        });
                    }
                });
            }
        };

    });
    flaresBase.directive('spreadsheetNav', function () {
        return {
            link: function (scope, element, attr) {
                element.keydown(function (e) {
                    // console.log(e.keyCode);
                });
            }
        };
    });


    // =================
    // Base filters

    flaresBase.filter('yesNo', function () {
        return function (input) {
            return input && input !== '0' ? 'Yes' : 'No';
        }
    });
    flaresBase.filter('markBlanks', function () {
        return function (input) {
            return input ? input : '--';
        }
    });
    flaresBase.filter('is', function () {
        return function (items, field) {
            var result = {};
            angular.forEach(items, function (value, key) {
                if (!value.hasOwnProperty(field)) {
                    result[key] = value;
                }
            });
            return result;
        };
    });

    // ==============
    // Run phase logic

    function registerTemplate($http, $templateCache, templateCacheName, url) {
        $http.get(url).then(function (response) {
            $templateCache.put(templateCacheName, response.data);
        });
    }

    function registerApiTokenInterceptor($httpProvider) {

        // 1a. Use httpProvider config to set the xrsf header info
        // Note: This assumes that Laravel already set the XSRF cookie for us.
        /*
        // PS. We can't use the cookie, because it's encrypted and \Laravel\Passport doesn't expect that.
        // Taylor Otwell makes it clear in comments that he wants X-CSRF because "it needs to be explicitly set" and therefore improves security or something.
        $httpProvider.defaults.xsrfCookieName = 'XSRF-TOKEN';
        $httpProvider.defaults.xsrfHeaderName = 'X-CSRF-TOKEN';         // for Laravel Passport
        */

        // 1b. Register an interceptor to manually create the XRSF headers
        $httpProvider.interceptors.push(function() {
            return {
                'request': function(config) {
                    let csrfToken = window.csrfToken || angular.element('meta[name="csrf-token"]').attr('content');
                    config.headers['X-CSRF-TOKEN'] = csrfToken;
                    return config;
                },
            }
        });

    }

}());