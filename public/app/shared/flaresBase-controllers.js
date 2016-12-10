(function(){
    // ==================================
    //   The base module for Flares
    //   All page apps should extend off this module
    // ==================================

    var flaresBase = angular.module('flaresBase');

    // ==================
    // Base controllers
    // 1. Resource controller base (forms for resources such as member, activity, etc)

    flaresBase.controller('resourceController', function($scope, $http, $window, $location, flAPI){
        var resourceController = this;
        
        // this.loadInto = function(childController, callback){
            // angular.extend(childController, resourceController);
            // $scope.state = Object.create(resourceController.state);
            // (callback || function(){})();       // invoke the callback or whatever
        // };
        
        $scope.record = {};     // Expect this to be aliased in child instance.
        $scope.originalRecord = {};         // Expect this to be aliased in child instance.
        
        // This should be the prototype for child controller state objects
        this.state = new (function(){
            this.isRequested = false;
            this.isLoaded = false;
            this.isAsync = false;
            
            this.path = {
                id: 0,
                mode: 'view',		// by default
                tab: 'details'
            };
            this.isView = function(){ 
                return this.path.mode === 'view';
            };
            this.isEdit = function(){
                return this.path.mode === 'edit';
            };
        });
        
        $scope.config = {};
        $scope.config.unloadWarning = 'WARNING: Your changes might not be saved.';
        $scope.config.hasMode = true;
        $scope.config.hasTab = true;
        
        this.extendConfig = function(newConfig){
            $scope.config = angular.extend($scope.config, newConfig);
        };
        
        this.parseUrl = function(){
            // Read the $location
            // get rid of any leading slash
            var path = $location.path();
            var pathFrags = (path.indexOf('/') === 0 ? path.substring(1) : path).split('/');
            return {
                id: pathFrags[0] ? pathFrags[0] : null,
                mode: pathFrags[1] ? pathFrags[1] : null,
                tab: pathFrags[2] ? pathFrags[2] : null,
            };
        };
        this.loadWorkflowPath = function(defaultMode, defaultTab){
            defaultMode = defaultMode || 'view';
            defaultTab = defaultTab || 'details';
            
            // load parsed $location into state.path
            var pathParts = this.parseUrl();
            if (pathParts.id){
                this.state.isRequested = true;
                this.state.path.id = pathParts.id;
                this.state.path.mode = pathParts.mode ? pathParts.mode : defaultMode;
                
                var expectedTab = $("[bs-show-tab][aria-controls='" + pathParts.tab + "']");
                if (expectedTab.length > 0){
                    expectedTab.tab('show');
                    this.state.path.tab = pathParts.tab;
                }
                else {
                    this.state.path.tab = defaultTab;
                }
                
                // Change the state.path if $location is updated
                $scope.$on('$locationChangeSuccess', function(event) {
                    // This could be triggered by $watchCollection-state.path
                    resourceController.updateWorkflowPath();
                });
                
                this.state.isLoaded = true;
                return true;
            }
            return false;
        };
        
        this.updateWorkflowPath = function(){           // called after $location change
            var wp = this.state.path;
            var pathParts = this.parseUrl();
            if (wp.id !== pathParts.id){
                // If the ID changed, gotta reload the page.. bye
                $window.location.reload();
            }
            // ensure the mode and tab matches the currently display
            if (pathParts.mode && wp.mode !== pathParts.mode){
                this.state.path.mode = pathParts.mode;     // note: circular triggers updateLocation
            }
            if (pathParts.tab && wp.tab !== pathParts.tab){
                // try to activate the correct tab
                var expectedTab = $("[bs-show-tab][aria-controls='" + pathParts.tab + "']");
                if (expectedTab.length > 0){
                    expectedTab.tab('show');
                    wp.tab = pathParts.tab;
                }
            }
        };
        this.updateLocation = function(){               // called after state.path change
            var wp = this.state.path;
            if (wp.id){
                // try to activate the correct tab
                $("[bs-show-tab][aria-controls='" + wp.tab + "']").tab('show');
                // Ensure the URL matches the path
                $location.path([wp.id, wp.mode, wp.tab].join('/'));            
            }
        };
        
        this.toggleMode = function(){
            var sw = this.state;
            if (sw.isView()){
                return sw.path.mode = 'edit';
            }
            if (sw.isEdit()){
                return sw.path.mode = 'view';
            }
            
        };
        
        //==============================================
        // Any family-wide utilities can live here
        //==============================================
        
        this.util = {};
        this.util.convertToDateObjects = function(dateFields, record){
            angular.forEach(dateFields, function(datePropKey){
                if (this[datePropKey]){
                    var timestamp = Date.parse(this[datePropKey]);
                    if (!isNaN(timestamp)){
                        this[datePropKey] = new Date(this[datePropKey]);
                    }
                    else {
                        this[datePropKey] = null;
                    }
                }	
            }, record);
        };
        
        // Change the URL path if state path details are updated (e.g. clicked on tab)
        $scope.$watch('state.path', function(){
            resourceController.updateLocation();
        }, true);
        
        
        //==========================
        // Save-your-change niceties
        //==========================
        
        window.onbeforeunload = function(event){
            if ($scope.state.isEdit()){
                return resourceController.unloadWarning || 'NO WARNING SET';
            }
        };
            
        $scope.$on('$destroy', function() {
            delete window.onbeforeunload;
        });
        
        
    }); 
    
}());