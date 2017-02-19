var flaresApp = angular.module('flaresDecoration', ['flaresBase', 'flow']);

flaresApp.config(['flowFactoryProvider', '$httpProvider', function(flowFactoryProvider, $httpProvider){	

	function imageResizer(fileObj){	// fileObj is an instance of FlowFile
		console.log(fileObj);
		console.log('TODO ImageResizer: file size is ' + Math.floor(fileObj.file.size/1024) + ' KB');
	};

	// $httpProvider.defaults.xsrfCookieName should be XSRF-TOKEN
	flowFactoryProvider.defaults = { 
		headers: {},
		initFileFn: imageResizer,
		singleFile: true,
		allowDuplicateUploads: true,
	};
	flowFactoryProvider.defaults.headers[$httpProvider.defaults.xsrfHeaderName] = (function(cookieName){
		var c = document.cookie.split('; ');
		for (var i = 0; i < c.length; i++){
			var cookie = c[i].split('=');
			if (cookie[0] === cookieName){
			  return decodeURIComponent(cookie[1]);
			}
		}
	}($httpProvider.defaults.xsrfCookieName));
	
}]);

flaresApp.controller('decorationViewEditController', function($scope, $location, $controller, $q, $uibModal, flAPI, flResource){

    // Add some base - unzip base controller's stuff into this controller
    angular.extend(this, $controller('resourceController', {$scope: $scope})); 
    
    var c = this;
    c.extendConfig({
        unloadWarning: 'You are editing this decoration record, and will lose any unsaved changes.'
    });
    
    
    $scope.state = Object.create(c.state);        // inherit the proto
    $scope.forms = {};
	$scope.dec = Object.create($scope.record);
	$scope.shadowDec = Object.create($scope.record);

    $scope.state.isDecorationLoaded = false;
    
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
    
	// Read the url
    if (c.loadWorkflowPath()){
        retrieveDecoration()
            .then(function(){
                // Nothing so far
            });
    }

    //==================
    // Fetch reference data for decorations
    // ONLY AFTER decoration has been initially retrieved
    // so that $scope.dec would already exist
    //==================
    
    flAPI('refData').get('decorationTiers').then(function(response){
        $scope.formData.decorationTiers = response.data || [];
    });
    flAPI('decoration').getAll().then(function(response){
        $scope.formData.existingDecorations = response.data.decorations;
    });
    
    
    $scope.formData = {
        decorationTiers: []
    }
    
    $scope.$watch('dec.data.parent_id', function(newValue){
        retrieveDecorationRelationship(newValue).then(function(parentInfo){
            angular.extend($scope.dec, parentInfo);
        });
    });

    
    // ====================
    // Function decs
    
	function retrieveDecoration(){
		if ($scope.state.path.id){
			return flAPI('decoration').get([$scope.state.path.id])
                .then(function(response){
                    // Process then store in VM
                    var dec = response.data.decoration;
                    processDecoration(dec);         // by reference
                    if (dec.parent_id){
                        // Retrieve the relationships
                        return retrieveDecorationRelationship(dec.parent_id).then(function(parentInfo){
                            return angular.extend({
                                id: dec.dec_id,
                                data: dec
                            }, parentInfo);
                        });
                    }
                    return {
                        id: dec.dec_id,
                        data: dec
                    };
                })
                .then(function(scopeDec){
                    $scope.dec = scopeDec;
                    $scope.shadowDec = angular.copy($scope.dec);
                    $scope.state.isDecorationLoaded = true;
                })
                .catch(function(response){
                    if (response.status == 404){
                        $scope.dec.errorNotFound = true;
                    }
                    else {
                        $scope.dec.errorServerSide = true;
                    }
                });
		}
		else {
			console.warn('Dec ID not specified');
            return $q.reject('Dec ID not specified');
		}
	}
    function retrieveDecorationRelationship(parentId){
        // Get the parent, also the surrounding siblings if any
        return flAPI('decoration').get([parentId])
            .then(function(response){
                var dec = response.data.decoration;
                processDecoration(dec);
                return {
                    parentDecoration: dec
                }
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
                if (!angular.equals($scope.shadowDec[key], value)){
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
            
			// reloadMemberImage();
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
			//reloadMemberImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
		}, function(response){
			console.warn('ERROR: Last picture could not be rewound');
			$scope.state.errorMessage = 'Failed to rewind picture';
		});
	};
	$scope.deleteAll = function(){
		flAPI('decoration').delete([$scope.dec.id, 'picture'], {params: { remove: 'all' }}).then(function(response){
			//reloadMemberImage();
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
        reloadMemberImage();
        updateUploaderDestination();
	});
        
    // If the modal uploads a new pic, make sure all other pictureControllers update
    $scope.$on('flares::displayPictureChanged', function(){
        if ($scope.dec.id){
            reloadMemberImage();            
        }
    });
    
	
	// ===========================
    // Function decs
    
    function reloadMemberImage(){
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
            $scope.$flow.opts.target = '/api/decoration/'+$scope.dec.id+'/badge/new';
            // $scope.$flow.opts.target = flAPI('decoration').sub('badge/new', decID).url();
            console.log('Updated uploader target %s', $scope.$flow.opts.target);
            $scope.uploader.hasUploadTarget = true;
        }
        else {
            $scope.uploader.hasUploadTarget = false;
        }
    }
	 
});
