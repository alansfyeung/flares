var flaresApp = angular.module('flaresDecoration', ['flaresBase', 'flow']);

flaresApp.run(['$http', '$templateCache', function($http, $templateCache){
    $http.get('/app/components/decoration/decorationContextMenuTemplate.html').then(function(response){
        $templateCache.put('decorationContextMenuTemplate.html', response.data);
    });
}]);

flaresApp.controller('indexController', function($scope, $window, $location, $controller, $uibModal, flAPI, flResource){
    
    $scope.gotoCreateNew = flResource('decoration').new().getLink();

    $scope.decorations = [];
    $scope.selectedDecoration = {};
    
    $scope.selectDecoration = function(dec){
        $scope.selectedDecoration = dec;
        console.log(dec);
        $window.location.href = flResource('decoration')
            .setFragment([$scope.selectedDecoration.dec_id, 'view', 'details'])
            .getLink();
    };    
    $scope.selectDecorationContext = function(dec){
        $scope.selectedDecoration = dec;
        openContextMenu();
    };
    
    $scope.badgeSrc = function(dec){
        var decId = dec;
        if (angular.isObject(dec) && dec.dec_id){
            decId = dec.dec_id;
        }
        return flResource().raw(['media', 'decoration', decId, 'badge']);
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
        label: 'Edit decoration',
        classNames: ['btn-primary'],
        action: decorationDeepLink.bind(null, 'edit', 'details')
    }, {
        label: 'View public page',
        classNames: ['btn-success'],
        action: function(){
            if ($scope.dec.shortcode){
                $window.open(flResource().raw(['public', 'decoration', $scope.dec.shortcode]), 'Preview Award', 'width=800, height=600');
                $modalInstance.close();
            }
            // $window.location.href = flResource('decoration').retrieve().addFragment(frag).getLink();
        }
    }, {
        label: 'Assign to member',
        classNames: ['btn-default'],
        action: decorationDeepLink.bind(null, 'edit', 'assign')
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
