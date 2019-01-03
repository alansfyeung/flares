'use strict';
(function(w){
    /**
     * Place all of your helper scripts here. 
     * To add to the window scope, please explicitly set w.functionName = xxxx
     */ 

    w.openDecorationDetails = function openDecorationDetails(event, decorationDetailsUrl){
        event.preventDefault();
        window.open(decorationDetailsUrl, 'FlaresDecorationDescription', 'width=800, height=600'); 
        return false;
    };
    
}(window));