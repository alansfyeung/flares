(function(){
    'use strict';
    // =========================================
    // Services
    // flResource (formerly flaresLinkBuilder)
    // =========================================

    var flaresBase = angular.module('flaresBase');

    flaresBase.factory('flResource', ['flResourceDefinitions', 'flApiPathRoot', function(flResourceDefinitions, flApiPathRoot){
        
        function FlaresLinkBuilder(opts){
            opts = opts || {};
            this.urlPrefix = flApiPathRoot + (opts.prefix || '');
            this.singular = opts.singular || '';
            this.plural = opts.plural || '';
            this.frag = '';
            
            // By default, return the "one" endpoint
            return this.single();
            
        }
        
        // URL to create a new resource
        FlaresLinkBuilder.prototype.new = function(){
            this.setUrl([this.plural,'new']);
            return this;
        };
        // URL to retrieve a single resource
        FlaresLinkBuilder.prototype.single = function(subpath){
            var pathParts = [this.singular];
            if (subpath && angular.isString(subpath)){
                pathParts.push(subpath);
            }
            this.setUrl(pathParts);
            return this;  
        };
        FlaresLinkBuilder.prototype.retrieve = function(){
            console.warn('FlaresLinkBuilder.retrieve() is marked for deprecation. Use .single() instead.');
            return this.single();
        };
        
        // URL to retrieve a list of resources
        FlaresLinkBuilder.prototype.many = function(){
            this.setUrl([this.plural]);
            return this;
        };
        FlaresLinkBuilder.prototype.overview = function(){
            console.warn('FlaresLinkBuilder.overview() is marked for deprecation. Use .many() instead.');
            return this.many();
        };
        
        FlaresLinkBuilder.prototype.setFragment = function(fragParts){     // expect an array or a string
            var fragPrefix = '#!/';
            var fragSep = '/';
            if (angular.isArray(fragParts)){
                this.frag = fragPrefix + fragParts.join(fragSep);
            }
            else if (angular.isString(fragParts)){
                this.frag = fragPrefix + fragParts;
            }
            return this;
        };
        FlaresLinkBuilder.prototype.hash = function(fragParts){
            console.warn('FlaresLinkBuilder.hash() is marked for deprecation. Use .setFragment() instead.');
            return this.setFragment(fragParts);
        };
        FlaresLinkBuilder.prototype.addFragment = function(fragParts){
            console.warn('FlaresLinkBuilder.addFragment() is marked for deprecation. Use .setFragment() instead.');
            return this.setFragment(fragParts);
        };
        
        FlaresLinkBuilder.prototype.setUrl = function(urlParts){        // expect an array or a string
            if (angular.isArray(urlParts)){
                this.url = this.urlPrefix + '/' + urlParts.join('/');
            }
            else if (arguments.length > 0){
                this.url = this.urlPrefix;
                angular.forEach(arguments, function(value, key){
                    if (angular.isString(value)){
                        this.url += '/' + urlParts;
                    }
                }, this);
            }
            return this;
        };
        
        FlaresLinkBuilder.prototype.getLink = function(){
            return this.url + this.frag;
        };
        FlaresLinkBuilder.prototype.build = FlaresLinkBuilder.prototype.getLink;
        
        FlaresLinkBuilder.prototype.raw = function(pathParts, queryStringParts, hashFragParts, opts){
            pathParts = pathParts || [];
            queryStringParts = queryStringParts || [];
            hashFragParts = hashFragParts || [];            // expect hash frag to be separated by slashes
            opts = angular.extend({
                absolutePath: true,
                usePrefix: true,
            }, opts || {});
            var path = pathParts.join('/');
            if (queryStringParts.length > 0){
                path += '?' + queryStringParts.join('&');
            }
            if (hashFragParts.length > 0){
                path += '#' + queryStringParts.join('/');
            }
            // Apply defaults: absolute path
            if (opts.absolutePath){
                if (opts.usePrefix){
                    path = this.urlPrefix + ((path.charAt(0) !== '/') ? '/' : '') + path;
                }
                else {
                    path = '/' + path;          // Prefix at the top
                }
            }
            return path;
        };
        
        var factory = function(resource){
            if (resource === 'asset'){
                return new FlaresLinkBuilder({ prefix: '/assets' });
            }
            
            var flrd = flResourceDefinitions;
            if (flrd.hasOwnProperty(resource)){
                var flb = new FlaresLinkBuilder({
                    singular: flrd[resource].singular,
                    plural: flrd[resource].plural
                });
                
                // Find the aliases hash, build up a resource map this way.
                if (flrd[resource].hasOwnProperty('aliases')){
                    angular.forEach(flrd[resource].aliases, function(aliasParts, aliasName){
                        (function(aliasParts){
                            flb[aliasName] = function(){
                                this.setUrl(aliasParts);
                            }     
                        }(aliasParts));
                    });
                }
                
                return flb;
            }
            
            // Default
            return new FlaresLinkBuilder();
        };
        
        return factory;

    }]);
    
}());
