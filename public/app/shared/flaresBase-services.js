// ==================================
//   The base module for Flares
//   All page apps should extend off this module
// ==================================

var flaresBase = window.flaresBase || angular.module('flaresBase', ['ui.bootstrap']);

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
flaresBase.factory('flaresLinkBuilder', function() {
    
    function FlaresLinkBuilder(urlRoot){
        this.url = (urlRoot ? urlRoot : '');
        this.frag = '';
    }
    FlaresLinkBuilder.prototype.raw = function(pathParts, queryStringParts, hashFragParts){
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
    };
    FlaresLinkBuilder.prototype.new = function(){
        this.addUrl([this.plural,'new']);
        return this;
    };
    FlaresLinkBuilder.prototype.retrieve = function(){
        this.addUrl([this.singular]);
        return this;  
    };
    FlaresLinkBuilder.prototype.overview = function(){
        this.addUrl([this.plural]);
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
    FlaresLinkBuilder.prototype.hash = FlaresLinkBuilder.prototype.addFragment;     // alias
    FlaresLinkBuilder.prototype.addUrl = function(urlParts){        // expect an array or a string
        if (urlParts instanceof Array){
            this.url += '/' + urlParts.join('/');
        }
        else if (typeof urlParts === 'string'){
            this.url += '/' + urlParts;
        }
        return this;
    };
    FlaresLinkBuilder.prototype.getLink = function(){
        return this.url + this.frag;
    };
    
    var factory = function(className){
        if (className === 'resource'){
            return new FlaresLinkBuilder('/assets');
        }      
        
        var flb = new FlaresLinkBuilder('');  
        if (className === 'member'){
            flb.singular = 'member';
            flb.plural = 'members';
            flb.search = function(){
                this.addUrl([this.plural, 'search']);
                return this;
            };
            return flb;
        }
        if (className === 'activity'){
            flb.singular = 'activity';
            flb.plural = 'activities';
            flb.roll = function(){
                this.addUrl([this.singular, 'roll']);
                return this;
            };
            return flb;
        }
        return flb;
    };
    
    return factory;

});