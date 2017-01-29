(function(){
    'use strict';
    // ==========================
    // Services
    // flAPI (formerly flaresAPI)
    // ==========================

    var flaresBase = angular.module('flaresBase');

    flaresBase.factory('flAPI', ['$http', 'flResourceDefinitions', function($http, flResourceDefinitions){
        /**
         * The FlaresAPI constructor
         * @param endpoint string Path to the resource endpoint
         * @param subresources string[] Names of known subresources required
         */
        function FlaresAPI(endpoint, subresources){     
            this.endpoint = endpoint;
            if (angular.isArray(subresources)){                
                subresources.forEach(function(subresourceName){
                    // Create a "subresource" shortcut e.g. 
                    // FlaresAPI.member.postingFor('2065141').get();
                    this[subresourceName+'For'] = function(parentId){
                        console.warn('resourceFor notation is deprecated and will be removed');
                        if (!parentId) console.warn('[FlaresAPI] ID must be specified');
                        var sub = Object.create(this);
                        sub.endpoint = [this.endpoint, parentId, subresourceName].join('/');
                        return sub;
                    };
                }, this);            
            }
        };
        FlaresAPI.prototype.getAll = function(params){      // don't expect ID
            return $http.get(buildEndpoint.call(this), params);
        };
        FlaresAPI.prototype.get = function(parts, params){
            return $http.get(buildEndpoint.call(this, parts), params);
        };
        FlaresAPI.prototype.post = function(data, params){      // don't expect ID
            return $http.post(buildEndpoint.call(this), data, params);
        };
        FlaresAPI.prototype.put = function(parts, data, params){
            return $http.put(buildEndpoint.call(this, parts), data, params);
        };
        FlaresAPI.prototype.patch = function(parts, data, params){
            return $http.patch(buildEndpoint.call(this, parts), data, params);
        };
        FlaresAPI.prototype.delete = function(parts, params){
            return $http.delete(buildEndpoint.call(this, parts), params);
        };
        // Todo: add a FlaresAPI.prototype.remove, alias of delete?
        
        FlaresAPI.prototype.nested = function(subresourceName, parentId){
            if (!parentId) throw '[flAPI] ID must be specified';
            var sub = Object.create(this);
            sub.endpoint = [this.endpoint, parentId, subresourceName].join('/');
            return sub;
        };
        FlaresAPI.prototype.url = function(parts){
            return buildEndpoint.call(this, parts);
        };
        
        function buildEndpoint(suffixes){
            if (suffixes){
                if (angular.isArray(suffixes)){
                    return this.endpoint + '/' + suffixes.join('/');
                }
                return this.endpoint + '/' + suffixes;
            }
            return this.endpoint;
        };
        
        var factory = function(resource){
            
            // Read the ngConstant and derive necessary data!
            var flrd = flResourceDefinitions;
            if (flrd.hasOwnProperty(resource)){
                return new FlaresAPI(flrd[resource].apiBase, flrd[resource].nestedResources || []);
            }
            
            switch (resource){
                // case 'refData':
                    // return new FlaresAPI('/api/refdata');
                // case 'member':
                    // return new FlaresAPI('/api/member', ['posting', 'picture', 'status']);
                // case 'activity':
                    // return new FlaresAPI('/api/activity', ['roll', 'awol']);
                default:
                    return new FlaresAPI();                
            }
        }
        
        return factory;
    }]);

}());