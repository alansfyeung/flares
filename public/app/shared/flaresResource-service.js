// ==========================
// Services
// flResource (formerly flaresLinkBuilder)
// ==========================

var flaresBase = window.flaresBase || angular.module('flaresBase', ['ui.bootstrap']);

flaresBase.factory('flResource', function() {
    
    function FlaresLinkBuilder(urlRoot){
        this.url = (urlRoot ? urlRoot : '');
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
            flb.awol = function(){
                this.addUrl([this.plural, 'awol']);
                return this;
            };
            return flb;
        }
        return flb;
    };
    
    return factory;

});