var flaresBase = (function(){

    // ==================================
    //   The base module for Flares
    //   All page apps should extend off this module
    // ==================================

    var flaresBase = angular.module('flaresBase', ['ui.bootstrap']).config(function($locationProvider) {
        $locationProvider.html5Mode(false).hashPrefix('!');
    });

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


    // ==========================
    // Services
    // 1. flaresAPI
    // 2. flaresLinkBuilder
    flaresBase.factory('flaresAPI', function($http){
        function FlaresAPI(endpoint, subresources){     // expect subresources as array
            this.endpoint = endpoint;
            if (subresources instanceof Array){
                subresources.forEach(function(subresourceName){
                    // Create a "subresource" shortcut e.g. 
                    // FlaresAPI.member.postingFor('2065141').get();
                    this[subresourceName+'For'] = function(parentId){
                        var sub = Object.create(this);
                        sub.endpoint = [this.endpoint, parentId, subresourceName].join('/');
                        return sub;
                    };
                }, this);            
            }
        };
        FlaresAPI.prototype._buildEndpoint = function(suffixes){
            suffixes = suffixes || [];
            var trailing = '';
            if (suffixes instanceof Array){
                trailing = suffixes.join('/');
            }
            else {
                trailing = suffixes;
            }
            return this.endpoint + (trailing ? '/'+trailing : '');
        };
        FlaresAPI.prototype.getAll = function(params){      // don't expect ID
            return $http.get(this._buildEndpoint(), params);
        };
        FlaresAPI.prototype.get = function(parts, params){
            return $http.get(this._buildEndpoint(parts), params);
        };
        FlaresAPI.prototype.post = function(data, params){      // don't expect ID
            return $http.post(this._buildEndpoint(), data, params);
        };
        FlaresAPI.prototype.put = function(parts, data, params){
            return $http.put(this._buildEndpoint(parts), data, params);
        };
        FlaresAPI.prototype.patch = function(parts, data, params){
            return $http.patch(this._buildEndpoint(parts), data, params);
        };
        FlaresAPI.prototype.delete = function(parts, params){
            return $http.delete(this._buildEndpoint(parts), params);
        };
        // Todo: add a FlaresAPI.prototype.remove, alias of delete?
        
        return {
            refData: new FlaresAPI('/api/refdata'),
            member: new FlaresAPI('/api/member', ['posting', 'picture', 'status']),
            activity: new FlaresAPI('/api/activity', ['roll', 'awol'])
        };
    });
    flaresBase.factory('flaresLinkBuilder', function() {
        function FlaresLinkBuilder(urlRoot){
            this.url = (urlRoot ? urlRoot : '');
            this.frag = '';
        }
        FlaresLinkBuilder.prototype.member = function(){
            this.singular = 'member';
            this.plural = 'members';
            this.search = function(){
                this.addUrl('/search');
            };
            return this;
        };
        FlaresLinkBuilder.prototype.activity = function(activityId){
            this.singular = 'activity';
            this.plural = 'activities';
            if (activityId){
                this.addUrl(this.singular);
                this.addFragment(activityId);
            }
            return this;
        };
        FlaresLinkBuilder.prototype.anonImage = function(){            // wtf is this, it needs a refactor
            this.addUrl('img/anon.png');
            return this;
        };
        FlaresLinkBuilder.prototype.new = function(){
            this.addUrl([this.plural,'new']);
            return this;
        };
        FlaresLinkBuilder.prototype.roll = function(){
            this.addUrl([this.singular, 'roll']);
            return this;
        };
        FlaresLinkBuilder.prototype.addFragment = function(fragParts){     // expect an array or a string
            if (fragParts instanceof Array){
                this.frag = '#!/' + fragParts.join('/');
            }
            else if (typeof fragParts === 'string'){
                this.frag = '#/' + fragParts;
            }
            return this;
        };
        FlaresLinkBuilder.prototype.addUrl = function(urlParts){        // expect an array or a string
            if (urlParts instanceof Array){
                this.url += '/' + urlParts.join('/');
            }
            else if (typeof urlParts === 'string'){
                this.url += '/' + urlParts;
            }
            return this;
        };
        FlaresLinkBuilder.prototype.getLink = function(actyId){
            return this.url + this.frag;
        };
        
        return {
            page: function(){
                return new FlaresLinkBuilder('');
            },
            resource: function(){
                return new FlaresLinkBuilder('/assets');
            },
            raw: function(pathParts, queryStringParts, hashFragParts){
                pathParts = pathParts || [];
                queryStringParts = queryStringParts || [];
                hashFragParts = hashFragParts || [];            // expect hash frag to be separated by slashes
                var path = '';
                path = pathParts.join('/');
                if (queryStringParts.length > 0){
                    path += '?' + queryStringParts.join('&');
                }
                if (hashFragParts.length > 0){
                    path += '#' + queryStringParts.join('/');
                }
                return path;
            }
        };
    });

    // ==================
    // Base controllers
    // 1. ViewEdit controller base (for forms such as member, activity, etc)

    flaresBase.controller('baseViewEditController', function($scope, $http, $window, $location, flaresAPI){
        var veController = this;
        
        $scope.record = {};     // Expect this to be aliased in child instant.
        $scope.originalRecord = {};         // Expect this to be aliased in child instant.
        
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
        this.loadWorkflowPath = function(){
            // load parsed $location into state.path
            var pathParts = this.parseUrl();
            if (pathParts.id){
                this.state.isRequested = true;
                this.state.path.id = pathParts.id;
                this.state.path.mode = pathParts.mode ? pathParts.mode : 'view';
                
                var expectedTab = $("[bs-show-tab][aria-controls='" + pathParts.tab + "']");
                if (expectedTab.length > 0){
                    expectedTab.tab('show');
                    this.state.path.tab = pathParts.tab;
                }
                else {
                    this.state.path.tab = 'details';
                }
                
                // Change the state.path if $location is updated
                $scope.$on('$locationChangeSuccess', function(event) {
                    // This could be triggered by $watchCollection-state.path
                    veController.updateWorkflowPath();
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
        
        this.convertToDateObjects = function(dateFields, record){
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
        $scope.$watchCollection('state.path', function(){
    		veController.updateLocation();
    	});
        
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

return flaresBase;

}());