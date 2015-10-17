// ==================================
//   The base module for Flares
//   All page apps should extend off this module
// ==================================

var flaresBase = angular.module('flaresBase', []).config(function($locationProvider) {
    $locationProvider.html5Mode(false).hashPrefix('!');
});

// ==========================
// Services
// 1. flaresAPI
// 2. flaresLinkBuilder
flaresBase.factory('flaresAPI', function($http){
    function FlaresAPI(endpoint){
        this.endpoint = endpoint;
    };
    FlaresAPI.prototype.buildEndpoint = function(pathParts){
        pathParts = pathParts || [];
        var finalEndpoint = this.endpoint;
        if (typeof pathParts === 'object'){
            var trailing = pathParts.join('/');
            finalEndpoint += (trailing ? '/'+trailing : '');
        }
        // finalEndpoint += (id ? '/'+id : '');
        // finalEndpoint += (child ? '/'+child : '');
        // finalEndpoint += (childId ? '/'+childId : '');
        return finalEndpoint;
    }
    FlaresAPI.prototype.getAll = function(params){      // don't expect ID
        return $http.get(this.buildEndpoint(), params);
    };
    FlaresAPI.prototype.get = function(parts, params){
        return $http.get(this.buildEndpoint(parts), params);
    };
    FlaresAPI.prototype.post = function(data, params){      // don't expect ID
        return $http.post(this.buildEndpoint(), data, params);
    };
    FlaresAPI.prototype.put = function(parts, data, params){
        return $http.put(this.buildEndpoint(parts), data, params);
    };
    FlaresAPI.prototype.patch = function(parts, data, params){
        return $http.patch(this.buildEndpoint(parts), data, params);
    };
    FlaresAPI.prototype.delete = function(parts, params){
        return $http.delete(this.buildEndpoint(parts), params);
    };
    // Todo: add a FlaresAPI.prototype.remove alias of delete?
    
    // Todo: build objects then assign sub-resource accessors to them e.g. 
    //// var member = new FlaresAPI('api/member');
    //// member.addSubResource('picture'); 
    
    return {
        refData: new FlaresAPI('/api/refdata'),
        member: new FlaresAPI('/api/member'),
        activity: new FlaresAPI('/api/activity'),
    };
});
flaresBase.factory('flaresLinkBuilder', function() {
    function FlaresLinkBuilder(urlRoot){
        this.url = '/' + (urlRoot ? urlRoot : '');
        this.member = function(memberId){
            this.url += 'member/' + memberId;
            this.search = function(){
                this.url += 'search';
            };
            return this;
        };
        this.activity = function(actyId){
            this.url += 'activity/' + actyId;
            return this;
        };
        this.anonImage = function(){
            this.url += 'img/anon.png';
            return this;
        }
    }
    FlaresLinkBuilder.prototype.getLink = function(actyId){
        return this.url;
    };
    
    
    return {
        page: function(){
            return new FlaresLinkBuilder();
        },
        resource: function(){
            return new FlaresLinkBuilder('assets/');
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

flaresBase.controller('flaresViewEdit', function($scope, $http, $location, flaresAPI){
    
});

// =================
// Base directives
// 1. bsShowTab - for BS3 tabs
// 2. spreadsheetNav - WIP - for member onboarding editing

flaresBase.directive('bsShowTab', function($location){
    return { 
        link: function (scope, element, attr) {
            element.click(function(e) {
                e.preventDefault();
                $(element).tab('show');		// Show the BS3 tab
                
                if (scope.workflow){
                    scope.$apply(function(){
                        scope.workflow.path.tab = attr.ariaControls;
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
// flaresBase.directive('', function(){
    // return { 
        // link: function (scope, element, attr) {
            // element.click(function(e) {
                // e.preventDefault();
            // });
        // }
    // };
    
// });



// =================
// Base filters

flaresBase.filter('yesNo', function(){
	return function(input){
		return input && input !== '0' ? 'Yes' : 'No';
	}
}).filter('markBlanks', function(){
	return function(input){
		return input ? input : '--';
	}
});