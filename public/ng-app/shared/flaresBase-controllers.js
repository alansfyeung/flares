(function () {
    // ==================================
    //   The base module for Flares
    //   All page apps should extend off this module
    // ==================================

    var flaresBase = angular.module('flaresBase');

    // ==================
    // Base controllers
    // 1. Resource controller base (forms for resources such as member, activity, etc)

    flaresBase.controller('viewEditController', function ($scope, $http, $window, $location, flAPI) {
        var viewEditController = this;

        $scope.record = {};     // Expect this to be aliased in child instance.
        $scope.originalRecord = angular.copy($scope.record);         // Expect this to be aliased in child instance.

        // This should be the prototype for child controller state objects
        this.state = new (function () {
            this.isRequested = false;
            this.isLoaded = false;
            this.isAsync = false;
            this.reloadOnIdChange = true;
            this.path = {
                id: 0,
                mode: 'view',		// by default
            };
            this.isView = function () {
                return this.path.mode === 'view';
            };
            this.isEdit = function () {
                return this.path.mode === 'edit';
            };
            this.setPath = function (newPath) {
                this.path = newPath;
                Object.getPrototypeOf(this).path = newPath;
            };
            this.setReloadOnIdChange = function (newSetting) {
                this.reloadOnIdChange = newSetting;
                Object.getPrototypeOf(this).reloadOnIdChange = newSetting;
            }
        });

        $scope.config = {};
        $scope.config.unloadWarning = 'WARNING: Your changes might not be saved.';
        $scope.config.hasMode = true;
        $scope.config.hasTab = true;

        this.extendConfig = function (newConfig) {
            $scope.config = angular.extend($scope.config, newConfig);
        };

        this.parseUrl = function () {
            // Read the $location
            // get rid of any leading slash
            var path = $location.path();
            var pathFrags = (path.indexOf('/') === 0 ? path.substring(1) : path).split('/');
            return {
                id: pathFrags[0] ? pathFrags[0] : null,
                mode: pathFrags[1] ? pathFrags[1] : null,
                tab: (pathFrags[2] && !isFinite(parseInt(pathFrags[2]))) ? pathFrags[2] : null,
                subId: (pathFrags[2] && isFinite(parseInt(pathFrags[2]))) ? parseInt(pathFrags[2]) : null,
            };
        };
        this.loadWorkflowPath = function (defaultMode, defaultTab, defaultSubId) {
            defaultMode = defaultMode || 'view';
            defaultTab = defaultTab || 'details';
            defaultSubId = defaultSubId || 0;
            // load parsed $location into state.path
            var pathParts = this.parseUrl();
            if (pathParts.id) {
                this.state.isRequested = true;
                this.state.path.id = pathParts.id;
                this.state.path.mode = pathParts.mode ? pathParts.mode : defaultMode;

                // Check tab exists, and do not show a non-existent tab
                if (pathParts.tab) {
                    if (hasTab(pathParts.tab)) {
                        getTabElement(pathParts.tab).tab('show');
                        this.state.path.tab = pathParts.tab;
                    }
                    else {
                        this.state.path.tab = defaultTab;
                    }
                }

                if (pathParts.subId) {
                    this.state.path.subId = pathParts.subId;
                }

                // Change the state.path if $location is updated
                $scope.$on('$locationChangeSuccess', function (event) {
                    // This could be triggered by $watchCollection-state.path
                    viewEditController.updateWorkflowPath();
                });

                this.state.isLoaded = true;
                return true;
            }
            return false;
        };

        this.updateWorkflowPath = function () {           // called after $location change
            var path = this.state.path;
            var pathParts = this.parseUrl();

            if (path.id !== pathParts.id && this.state.reloadOnIdChange) {
                // If the ID changed, gotta reload the page.. bye
                $window.location.reload();
            }
            // ensure the mode and tab matches the currently display
            if (pathParts.mode && path.mode !== pathParts.mode) {
                this.state.path.mode = pathParts.mode;     // note: circular triggers updateLocation
            }
            // try to activate the correct tab
            if (pathParts.tab && path.tab !== pathParts.tab) {
                if (hasTab(pathParts.tab)) {
                    getTabElement(pathParts.tab).tab('show');
                    path.tab = pathParts.tab;
                }
            }
        };
        this.updateLocation = function () {               // called after state.path change
            var path = this.state.path;
            if (path.id) {
                if (path.tab) {
                    if (hasTab(path.tab)) {
                        getTabElement(path.tab).tab('show');    // try to activate the correct tab
                    }
                    $location.path([path.id, path.mode, path.tab].join('/'));
                }
                else if (path.subId) {
                    $location.path([path.id, path.mode, path.subId].join('/'));
                }
                else if (path.mode) {
                    $location.path([path.id, path.mode].join('/'));
                }
            }
            else {
                console.warn('Update location called but no id found in path', path);
            }
        };

        this.toggleMode = function () {
            var sw = this.state;
            if (sw.isView()) {
                return sw.path.mode = 'edit';
            }
            if (sw.isEdit()) {
                return sw.path.mode = 'view';
            }

        };

        //==============================================
        // Any family-wide utilities can live here
        //==============================================

        this.util = {};
        this.util.convertToDateObjects = function (dateFields, record) {
            angular.forEach(dateFields, function (datePropKey) {
                if (this[datePropKey]) {
                    var timestamp = Date.parse(this[datePropKey]);
                    if (!isNaN(timestamp)) {
                        this[datePropKey] = new Date(this[datePropKey]);
                    }
                    else {
                        this[datePropKey] = null;
                    }
                }
            }, record);
        };

        // Change the URL path if state path details are updated (e.g. clicked on tab)
        $scope.$watch('state.path', function () {
            viewEditController.updateLocation();
        }, true);


        //==========================
        // Save-your-change niceties
        //==========================

        window.onbeforeunload = function (event) {
            if ($scope.state.isEdit()) {
                return viewEditController.unloadWarning || 'NO WARNING SET';
            }
        };

        $scope.$on('$destroy', function () {
            delete window.onbeforeunload;
        });

        function getTabElement(tabName) {
            return angular.element("[bs-show-tab][aria-controls='" + tabName + "']");
        }
        function hasTab(tabName) {
            return getTabElement(tabName).length > 0;
        }


    });

}());