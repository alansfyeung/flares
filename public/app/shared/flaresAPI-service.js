(function(){
    'use strict';
    // ==========================
    // Services
    // flAPI (formerly flaresAPI)
    // ==========================

    var flaresBase = angular.module('flaresBase');

    flaresBase.factory('flAPI', ['$http', '$filter', 'flResourceDefinitions', function($http, $filter, flResourceDefinitions){
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
            data = flattenPayloadDates(data);
            return $http.post(buildEndpoint.call(this), data, params);
        };
        FlaresAPI.prototype.put = function(parts, data, params){
            data = flattenPayloadDates(data);
            return $http.put(buildEndpoint.call(this, parts), data, params);
        };
        FlaresAPI.prototype.patch = function(parts, data, params){
            data = flattenPayloadDates(data);
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
        
        /**
         * Flatton dates to a simpler format for Laravel/Carbon
         * If ISO timestamps are sent, then it returns the following error:
         * `{code: 0, reason: "Unexpected data found.↵Trailing data"}`
         */
        function flattenPayloadDates(payload){            
            // Need to flatten dates... thanks Laravel/Carbon... (sarcasm)
            return iteratePayload(payload);
            function iteratePayload(dataObject){
                for (var key in dataObject){
                    if (dataObject.hasOwnProperty(key)){
                        if (dataObject[key] instanceof Date){
                            dataObject[key] = $filter('date')(dataObject[key], "yyyy-MM-dd");
                        }
                        else if (typeof dataObject[key] === 'object' && dataObject[key].constructor === 'object'){
                            // Recursively map dates
                            dataObject[key] = iteratePayload(dataObject[key])
                        }
                    }
                }
                return dataObject;
            }
        }
        
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