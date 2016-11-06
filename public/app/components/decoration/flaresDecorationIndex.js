var flaresApp = angular.module('flaresDecoration', ['flaresBase', 'flow']);

flaresApp.run(['$http', '$templateCache', function($http, $templateCache){
    $http.get('/app/components/decoration/decorationContextMenuTemplate.html').then(function(response){
        $templateCache.put('decorationContextMenuTemplate.html', response.data);
    });
}]);

flaresApp.controller('indexController', function($scope, $location, $controller, $uibModal, flAPI, flResource){
    
    $scope.gotoCreateNew = flResource('decoration').new().getLink();

    $scope.decorations = [];
    $scope.selectedDecoration = {};
    
    $scope.selectDecoration = function(dec){
        $scope.selectedDecoration = dec;
        console.log($scope.selectedDecoration);
        openContextMenu();
    };
    
    $scope.badgeSrc = function(dec){
        var decId = dec;
        if (angular.isObject(dec) && dec.dec_id){
            decId = dec.dec_id;
        }
        var link = flAPI('decoration').sub('badge', decId).url();
        console.log(link);
        return link;
    }
    
    flAPI('decoration').getAll().then(function(resp){
        
        if (resp.data && angular.isArray(resp.data.decorations)){
            $scope.decorations = resp.data.decorations;
        }
    }).catch(function(error){
        console.warn(error);
    });    
    
    //===================
    // Functions
    //===================
    
    function openContextMenu(){
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'decorationContextMenuTemplate.html',
            controller: 'decorationContextMenuController',
            scope: $scope,
            size: 'sm',
            resolve: {
                context: function(){
                    return $scope.selectedDecoration
                }
            }
        });

        modalInstance.result.then(function(selectedItem){
            // Item clicked
            $scope.selected = selectedItem;
        });
    }

});


flaresApp.controller('decorationContextMenuController', function ($scope, $parse, $window, $modalInstance, flResource, context){
    
    $scope.dec = context;
    
    $scope.bodyButtons = [{
        label: 'View decoration',
        classNames: ['btn-primary'],
        action: decorationDeepLink.bind(null, 'view', 'details')
    }, {
        label: 'Assign to member',
        classNames: ['btn-success'],
        action: decorationDeepLink.bind(null, 'edit', 'assign')
    }, {
        label: 'Edit decoration',
        classNames: ['btn-default'],
        action: decorationDeepLink.bind(null, 'edit', 'details')
    }, {
        label: 'View assigned',
        classNames: ['btn-default'],
        action: decorationDeepLink.bind(null, 'view', 'assign')
    }];
    $scope.footerButtons = [{
        label: 'Cancel',
        classNames: ['btn-default']
    }];
    
    $scope.cancel = function(){
        $modalInstance.dismiss('cancel');
    };
    
    // $scope.ok = function () {
        // $modalInstance.close($scope.selected.item);
    // };
    
    function decorationDeepLink(mode, tab){
        mode = mode || 'view';
        tab = tab || 'details';
        var frag = [$scope.dec.dec_id, mode, tab];
        $window.location.href = flResource('decoration').retrieve().addFragment(frag).getLink();
    }
    
});