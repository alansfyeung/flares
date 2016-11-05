// ==========================
// Services
// flResource (formerly flaresLinkBuilder)
// ==========================

var flaresBase = window.flaresBase || angular.module('flaresBase', ['ui.bootstrap']);

flaresBase.factory('flResource', ['flResourceDefinitions', function(flResourceDefinitions){
    
    function FlaresLinkBuilder(opts){
        opts = opts || {};
        this.url = opts.prefix || '';
        this.singular = opts.singular || '';
        this.plural = opts.plural || '';
        this.frag = '';
    }
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
    
    FlaresLinkBuilder.prototype.build = function(){
        return this.url + this.frag;
    };
    FlaresLinkBuilder.prototype.getLink = FlaresLinkBuilder.prototype.build;
    
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
    
    var factory = function(resource){
        if (resource === 'asset'){
            return new FlaresLinkBuilder({ prefix: '/assets' });
        }
        
        var flrd = flResourceDefinitions;
        if (flrd.hasOwnProperty(resource)){
            
            console.log(flrd[resource]);
            
            var flb = new FlaresLinkBuilder({
                singular: flrd[resource].singular,
                plural: flrd[resource].plural
            });
            
            if (flrd[resource].hasOwnProperty('aliases')){
                angular.forEach(flrd[resource].aliases, function(aliasParts, aliasName){
                    (function(aliasParts){
                        flb[aliasName] = function(){
                            this.addUrl(aliasParts);
                        }     
                    }(aliasParts));
                });
            }
            
            console.log(flb);
            
            return flb;
        }

        // if (resource === 'member'){
            // flb.search = function(){
                // this.addUrl([this.plural, 'search']);
                // return this;
            // };
            // return flb;
        // }
        
        // if (resource === 'activity'){
            // flb.roll = function(){
                // this.addUrl([this.singular, 'roll']);
                // return this;
            // };
            // flb.awol = function(){
                // this.addUrl([this.plural, 'awol']);
                // return this;
            // };
            // return flb;
        // }
        
        // if (resource === 'decoration'){
            // flb.search = function(){
                // this.addUrl([this.plural, 'search']);
                // return this;
            // };
            // return flb;
        // }
        
        // Default
        return new FlaresLinkBuilder();
    };
    
    return factory;

}]);