// ==========================
// Services
// flAPI (formerly flaresAPI)
// ==========================

var flaresBase = window.flaresBase || angular.module('flaresBase', ['ui.bootstrap']);

flaresBase.factory('flAPI', function($http){
    /**
     * The FlaresAPI constructor
     * @param endpoint string Path to the resource endpoint
     * @param subresources string[] Names of known subresources required
     */
    function FlaresAPI(endpoint, subresources){     
        this.endpoint = endpoint;
        if (subresources instanceof Array){
            subresources.forEach(function(subresourceName){
                // Create a "subresource" shortcut e.g. 
                // FlaresAPI.member.postingFor('2065141').get();
                this[subresourceName+'For'] = function(parentId){
                    if (!parentId) console.warn('[FlaresAPI] ID must be specified');
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
    
    var factory = function(className){
        if (className === 'refData'){
            return new FlaresAPI('/api/refdata');
        }
        if (className === 'member'){
            return new FlaresAPI('/api/member', ['posting', 'picture', 'status']);
        }
        if (className === 'activity'){
            return new FlaresAPI('/api/activity', ['roll', 'awol']);
        }
        return new FlaresAPI();
    }
    
    return factory;
});