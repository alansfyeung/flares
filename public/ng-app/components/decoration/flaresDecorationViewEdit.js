var flaresApp = angular.module('flaresDecoration', ['flaresBase', 'flow']);

flaresApp.config(['flowFactoryProvider', '$httpProvider', 'flLaravelCsrfToken', function(flowFactoryProvider, $httpProvider, flLaravelCsrfToken){	

	function imageResizer(fileObj){	// fileObj is an instance of FlowFile
		console.log(fileObj);
		console.log('TODO ImageResizer: file size is ' + Math.floor(fileObj.file.size/1024) + ' KB');
	};

    let headers = {};
    
    // $httpProvider.defaults.xsrfCookieName should be XSRF-TOKEN
	headers[$httpProvider.defaults.xsrfHeaderName] = (function(cookieName){
		var c = document.cookie.split('; ');
		for (var i = 0; i < c.length; i++){
			var cookie = c[i].split('=');
			if (cookie[0] === cookieName){
			  return decodeURIComponent(cookie[1]);
			}
		}
    }($httpProvider.defaults.xsrfCookieName));

    // Must add X-CSRF-TOKEN as well in order to work with Laravel Passport laravel_token auth
    headers['X-CSRF-TOKEN'] = flLaravelCsrfToken;

    flowFactoryProvider.defaults = { 
		headers: headers,
		initFileFn: imageResizer,
		singleFile: true,
		allowDuplicateUploads: true,
    };

}]);

flaresApp.controller('decorationViewEditController', function($scope, $window, $controller, flAPI, flResource){

    // Add some base - unzip base controller's stuff into this controller
    angular.extend(this, $controller('viewEditController', {$scope: $scope})); 

    var c = this;
    c.extendConfig({
        unloadWarning: 'You are editing this decoration record, and will lose any unsaved changes.'
    });
    
    $scope.forms = {};
    
	$scope.dec = Object.create($scope.record);
	$scope.shadowDec = angular.copy($scope.dec);

    $scope.relatedDec = [];     // 

    $scope.state = Object.create(c.state);      // inherit the proto
    $scope.state.setReloadOnIdChange(false);          // No! we have stuff that listens then grabs the new data
    $scope.state.isDecorationLoaded = false;
    $scope.state.areRelatedDecorationsLoaded = false;
    $scope.state.lowerUrl = undefined;
    $scope.state.higherUrl = undefined;
    
    $scope.formData = {
        decorationTiers: []
    };

    $scope.edit = function () {
        if ($scope.state.isView()) {
            $scope.beginEdit();     // If in view mode, toggle to Edit mode
        }
        else if ($scope.state.isEdit()) {
            $scope.finishEdit();       // Save the changes and send back to view mode
        }
    };
    $scope.beginEdit = function(){
        $scope.state.path.mode = 'edit';
        return false;
    }
    $scope.finishEdit = function(){
        $scope.state.path.mode = 'view';
        updateDecoration();
        return false;
    }
	$scope.cancelEdit = function(){
		if ($scope.state.isLoaded){
			$scope.dec = angular.copy($scope.shadowDec);
			$scope.state.path.mode = 'view';
			return;
		}
		console.warn('Cannot cancel - dec record was never loaded');
	};
    
    $scope.navToDecoration = navToDecoration;
    $scope.delete = deleteDecoration;

	// Read the url
    if (c.loadWorkflowPath()){
        $scope.$watch('state.path.id', function(newValue){
            if (newValue){
                retrieveDecoration($scope.state.path.id).then(function(scopeDec){
                    $scope.dec = scopeDec;
                    $scope.shadowDec = angular.copy($scope.dec);
                    $scope.state.isDecorationLoaded = true;
                }).catch(function(err){
                    console.warn(err);
                });
            }
            else {
                console.warn('Decoration ID not specified');
            }
        });
    }

    //==================
    // Fetch reference data for decorations, need this for choosing a parent
    //==================
    
    flAPI('refData').get('decorationTiers').then(function(response){
        $scope.formData.decorationTiers = response.data || [];
        flAPI('decoration').getAll().then(function(response){
            $scope.formData.existingDecorations = response.data.decorations;
        });
    });
    
    $scope.$watch('dec.data.parent_id', function(newValue){
        if (newValue) {
            console.log('dec.data.parent_id newvalue is ', newValue);
            retrieveDecorationRelationship(newValue).then(function(parentInfo){
                angular.extend($scope.dec, parentInfo);
            });
        }
    });

    $scope.$watch('dec.lowerId', function(newValue){
        if (newValue){
            $scope.state.lowerUrl = flResource('decoration').single().setFragment([newValue, 'view', 'details']).getLink();
            $scope.state.lowerHashUri = [newValue, 'view', 'details'].join('/');
        }
        else {
            $scope.state.lowerUrl = null;
            $scope.state.lowerHashUri = null;
        }
    });
    $scope.$watch('dec.higherId', function(newValue){
        if (newValue){
            $scope.state.higherUrl = flResource('decoration').single().setFragment([newValue, 'view', 'details']).getLink();
        }
        else {
            $scope.state.higherUrl = null;
        }
    });

    
    // ====================
    // Function decs
    
	function retrieveDecoration(decorationId){
        if (decorationId) {
            return flAPI('decoration').get([decorationId]).then(function(response){
                // Process then store in VM
                var decData = response.data.decoration;
                var lowerDec = response.data.lowerDecoration;
                var higherDec = response.data.higherDecoration;
                processDecoration(decData);         // by reference
                var decorationBase = {
                    id: decData.dec_id,
                    lowerId: lowerDec && lowerDec.dec_id,
                    higherId: higherDec && higherDec.dec_id,
                    data: decData,
                };
                // Retrieve the whole family of decorations here
                var parentId = decData.parent_id ? decData.parent_id : decData.dec_id;
                return retrieveDecorationRelationship(parentId, decData.dec_id).then(function(relations){
                    if (relations.familyDecorations && relations.familyDecorations.length > 1) {
                        $scope.state.areRelatedDecorationsLoaded = true;        // There must have been more
                    }
                    return angular.extend(decorationBase, relations);
                }).catch(function(error){
                    console.error('Relationships could not be retrieved', error);
                    return decorationBase;
                });
            }).catch(function(response){
                if (response.status == 404){
                    $scope.dec.errorNotFound = true;
                }
                else {
                    $scope.dec.errorServerSide = true;
                }
            });
        }
	}
    function retrieveDecorationRelationship(parentId, decId){
        // Get the parent, also the surrounding siblings if any
        return flAPI('decoration').get([parentId]).then(function(response){
            var parentDec = response.data.decoration;
            return flAPI('decoration').get([parentId, 'children']).then(function(response){
                var childDecs = response.data.decorations;
                var familyDecs = [ parentDec ].concat(childDecs);
                angular.forEach(familyDecs, function(dec){
                    processDecoration(dec);
                })
                return {
                    parentDecoration: parentId != decId ? parentDec : null,
                    familyDecorations: familyDecs,
                };
            });
        }); 
    }
	function processDecoration(dec){
        c.util.convertToDateObjects(['date_commence', 'date_conclude', 'created_at', 'updated_at', 'deleted_at'], dec);
	}
	function updateDecoration(){
		var hasChanges = $scope.forms.decorationDetails && $scope.forms.decorationDetails.$dirty;
		if (hasChanges){
            var payload = {
                decoration: {}
            };
            angular.forEach($scope.dec.data, function(value, key){
                // Dirty only 
                if (!angular.equals($scope.shadowDec.data[key], value)){
                    payload.decoration[key] = value;
                }
            });
			flAPI('decoration').patch([$scope.dec.id], payload).then(function(response){
				$scope.state.successMessage = 'Save successful';
				$scope.shadowDec = angular.copy($scope.dec);
				
			}, function(response){
				$scope.state.errorMessage = "Warning: Couldn't save this record";
				console.warn('Error: dec update', response);
			});
		}
        else {
            console.warn('Nothing was saved');
            console.log('THe form object was %O', $scope.forms.decorationDetails);
        }
	}
    function deleteDecoration(){
        if (!confirm('Are you sure you want to delete this decoration? This action cannot be undone.')){
            return false;
        }
        $scope.state.deletionFailed = false;
        flAPI('decoration').delete([$scope.dec.id]).then(function(){
            // Gotta bail back to the index screen
            // delete window.onbeforeunload;
			$scope.state.path.mode = 'view';
            $window.location.href = flResource('decoration').many().getLink();
        }).catch(function(){
            $scope.state.deletionFailed = true;
        });
    }

    function navToDecoration(decorationId){
        if (decorationId){
            $scope.state.path.id = decorationId;
            $scope.state.path.mode = 'view';
        }
    }
    
    // End function decs
    //======================
	
});

flaresApp.controller('pictureController', function($scope, $rootScope, $http, $timeout, flAPI, flResource){
    
	var maxImageSize = 1024 * 1024;		// 1MB max file size
	var maxImageSizeDesc = '1MB';
	
	$scope.image = {
		url: '',
		isLoaded: false
	};
	$scope.image.resetToDefault = function(){
		this.url = '';
		this.isLoaded = true;
	};
		
	$scope.uploader = {
		uploading: false,
		dropzone: false,
        hasUploadTarget: false,
		ready: function(){
			return $scope.uploader.hasUploadTarget;
		}
	};
	
	$scope.uploadStart = function(){
		$scope.uploader.uploading = true;
	};
	$scope.uploadFinish = function(){
		if ($scope.$flow.files.length > 0){			// If any upload took place
			$scope.image.resetToDefault();		// Revert it to the default
            
			// reloadDecorationImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
            
			$timeout(function(){
				// Allow the upload success message to flash
				$scope.uploader.uploading = false;
				$scope.$flow.cancel();			// Clear out the files array
			}, 4000);
		}
		else {
			$scope.uploader.uploading = false;
		}
	};
	
	$scope.deleteLast = function(){
		flAPI('decoration').delete([$scope.dec.id, 'picture']).then(function(response){
			//reloadDecorationImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
		}, function(response){
			console.warn('ERROR: Last picture could not be rewound');
			$scope.state.errorMessage = 'Failed to rewind picture';
		});
	};
	$scope.deleteAll = function(){
		flAPI('decoration').delete([$scope.dec.id, 'picture'], {params: { remove: 'all' }}).then(function(response){
			//reloadDecorationImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
		}, function(response){
			console.warn('ERROR: Picture could not be deleted');
			$scope.state.errorMessage = 'Failed to delete picture';
		});
	};
	
	$scope.$on('flow::fileAdded', function (event, $flow, flowFile) {
		if (flowFile.size > maxImageSize){
			console.warn('Image is oversize: ', flowFile.size);
			$scope.state.errorMessage = 'Your image is too big; the maximum upload size is ' + maxImageSizeDesc;
			event.preventDefault();  //prevent file from uploading
		}
	});
    
    
    $scope.$watch('dec.id', function(newValue){
        reloadDecorationImage();
        updateUploaderDestination();
	});
        
    // If the modal uploads a new pic, make sure all other pictureControllers update
    $scope.$on('flares::displayPictureChanged', function(){
        if ($scope.dec.id){
            reloadDecorationImage();            
        }
    });
    
	
	// ===========================
    // Function decs
    
    function reloadDecorationImage(){
		// var memberPictureRequestUrl = '/api/member/'+$scope.member.regt_num+'/picture';
		// $http.get(memberPictureRequestUrl+'/exists').then(function(response){
        var decID = $scope.dec.id;
        if (decID){
            flAPI('decoration').nested('badge', decID).getAll().then(function(response){
                if (response.status === 200){
                    if (response.data.exists){
                        var cacheDefeater = +Date.now();
                        // Todo: replace the below with a more sturdy flResource solution
                        $scope.image.url = flResource().raw(['media', 'decoration', decID, 'badge'], [cacheDefeater]);
                        $scope.image.isLoaded = true;
                    }
                    else {
                        $scope.image.resetToDefault();
                    }
                    $scope.image.count = response.data.count;
                }
            }, function(response){
                console.warn('WARN: Image not found for '+decID, response.status);
                $scope.image.resetToDefault();
            });
        }
	}
    
    function updateUploaderDestination(){
        var decID = $scope.dec.id;
        if ($scope.$flow && decID){
            $scope.$flow.opts.target = flResource().raw(['/api', 'decoration', $scope.dec.id, 'badge', 'new']);
            console.debug('Updated uploader target %s', $scope.$flow.opts.target);
            $scope.uploader.hasUploadTarget = true;
        }
        else {
            $scope.uploader.hasUploadTarget = false;
        }
    }
	 
});
