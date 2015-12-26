/*************
 *  ECO BTstrap modal popups
 *  Uses BTstrap styles to insert a popup into the top right corner
 *  Alan Yeung - 2015
 *  xoxo
 *************/

// Alerts depend on CSS in bootstrap
// Alerts go to the top-right of the screen (fixed) in size sm+
// Alerts extend full screen width on mobile

(function($){
    
    var iconClasses = {
       'success': 'glyphicon-ok-circle',       
       'info': 'glyphicon-info-sign',       
       'warning': 'glyphicon-exclamation-sign',       
       'danger': 'glyphicon-remove-circle'
    };
    
    
    var Alert = function(options){
        
        if (!~Object.keys(iconClasses).indexOf(options.type)){
            options.type = 'info';
        }
        
        var extraClassNames = 'alert-' + options.type  + ' ' + options.classNames;
        var $alert = $('<div class="alert-popup"/>').addClass(extraClassNames);
        // if (typeof options.css === 'object'){
            // $alert.css(options.css);
        // }
        
        var $alertSymbol = $('<div class="alert-symbol">');
        $alertSymbol.append('<span class="glyphicon '+iconClasses[options.type]+'">');
        
        var $alertText = $('<div class="alert-text"/>');
        var $alertTextTitle = $('<strong>');
        $alertTextTitle.text(options.title);
        $alertText.append($alertTextTitle);
        
        var $alertTextBody = options.lineBreakAfterTitle ? $('<div>') : $('<span>');
        if (options.allowTextHtml){
            var htmlSafe = options.text.replace(/<(?!((?:\/\s*)?\b(?:br|p|b|u|strong|em|a)\b))([^>])+>/gi, '');
            $alertTextBody.html(htmlSafe);
        }
        else {
            $alertTextBody.text(' ' + options.text);
        }
        $alertText.append($alertTextBody);
        
        $alert.append($alertSymbol);
        $alert.append($alertText);
        
        var self = this;
        self.$parent = $(options.parent);
        self.$alert = $alert;
        self.appear();
        
        // Auto slide-out
        if (options.time){
            self.timer = setTimeout(function(){
                self.disappear();
            }, (options.time * 1000));
        }
        
        $alert.hover(function(){
            clearTimeout(self.timer);
        }, function(){
            self.timer = setTimeout(function(){
                self.disappear();
            }, (options.time * 2000));
        });
        
        $alert.on('click', function(){
            self.disappear();
        });
        
        return $alert;
    };
    
    Alert.DEFAULTS = {
        parent: 'body',
        type: 'info',
        title: 'Alert',
        text: '',
        time: 10,
        classNames: '',
        css: {},
        allowTextHtml: true,
        lineBreakAfterTitle: true
    };
    
    Alert.prototype.appear = function(){
        var $thisAlert = this.$alert;
        $thisAlert.hide().appendTo(this.$parent).fadeIn(500);      // or you could create some custom classes + transitions
    };
    Alert.prototype.disappear = function(){
        var $thisAlert = this.$alert;
        $thisAlert.addClass('slide-right');
        setTimeout(function(){
            $thisAlert.remove();
        }, 2000);
        
    };

    
    /* Plugin
     ========================= */
    
    function Plugin(options) { 
        // if (!data && options == 'destroy') return;
        var options = $.extend({}, Alert.DEFAULTS, typeof options == 'object' && options);
        var alert = new Alert(options);
        
        
        return alert;
    }
    
    
     // ALERTS NO CONFLICT
    // =====================

    
    var old = $.alert;
    $.alert = Plugin;
    $.alert.Constructor = Alert;
    $.alert.noConflict = function () {
        $.fn.alert = old;
        return this;
    }
    
    $(function(){
        $('[data-trigger=popup]').click(function(){
            $.alert($(this).data());
        });
        
    });
    

    
}(jQuery));