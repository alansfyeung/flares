var flaresApp = angular.module('flaresDecoration', ['flaresBase', 'flow']);

flaresApp.run(['$http', '$templateCache', function($http, $templateCache){
    $http.get('ng-app/components/decoration/decorationContextMenuTemplate.html').then(function(response){
        $templateCache.put('decorationContextMenuTemplate.html', response.data);
    });
    $http.get('ng-app/components/decoration/decorationAccordionGroupTemplate.html').then(function(response){
        $templateCache.put('decorationAccordionGroupTemplate.html', response.data);
    });
}]);

flaresApp.controller('indexController', function($scope, $window, $location, $controller, $uibModal, flAPI, flResource){
    
    $scope.state = {
        loading: true
    };
    
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
            // $scope.decorations = resp.data.decorations;
            var decorations = resp.data.decorations;
            // Chunk up according to tier
            flAPI('refData').get('decorationTiers').then(function(response){
                var decorationTiers = response.data || [];
                var decorationTierMap = {};
                angular.forEach(decorationTiers, function(decorationTier, index){
                    // new array ready to fill with decorations
                    decorationTier.decorations = [];
                    this[decorationTier.tier] = index;
                }, decorationTierMap);
                angular.forEach(decorations, function(decoration){
                    // Map decorations into their tiers
                    if (decorationTierMap[decoration.tier] !== undefined){
                        var decorationTierIndex = decorationTierMap[decoration.tier];
                        decorationTiers[decorationTierIndex].decorations.push(decoration);
                    }
                });
                $scope.decorations = decorationTiers;
                $scope.state.loading = false;
            });
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


flaresApp.controller('decorationContextMenuController', function ($scope, $parse, $window, $modalInstance, flAPI, flResource, context){
    
    $scope.dec = context;
    
    $scope.bodyActions = [{
        label: 'Edit decoration',
        // classNames: ['btn-primary'],
        action: decorationDeepLink.bind(null, 'edit', 'details')
    }, {
        label: 'Assign to member',
        // classNames: ['btn-default'],
        action: decorationDeepLink.bind(null, 'edit', 'assign')
    }, {
        label: 'View public page ',
        // classNames: ['btn-default'],
        action: function(){
            if ($scope.dec.shortcode){
                $window.open(flResource().raw(['public', 'decorations', $scope.dec.shortcode]), 'Preview Award', 'width=800, height=600');
                $modalInstance.close();
            }
            // $window.location.href = flResource('decoration').retrieve().addFragment(frag).getLink();
        }
    }];
    /*
    $scope.bodyButtons = [{
        label: 'Delete decoration',
        classNames: ['btn-danger'],
        action: function(){
            if (!confirm("Are you sure you want to delete '"+$scope.dec.name+"'? This cannot be undone.")) return false;
            flAPI('decoration').delete([$scope.dec.dec_id]).then(function(){
                // Todo: splice
                angular.forEach($scope.$parent.decorations, function(tier){
                    var found = tier.decorations.indexOf($scope.dec);
                    if (~found){
                        tier.decorations.splice(found, 1);
                    }
                });
                $modalInstance.close();
            }).catch(function(err){
                alert('The decoration could not be deleted. This is possibly because members have been awarded this decoration, and those awards need to be deleted first. See the console for more details.');
                console.warn(err);
                $modalInstance.close();
            });
        }
    }];
    */
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
