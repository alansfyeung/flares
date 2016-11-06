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

flaresApp.controller('decorationViewEditController', function($scope, $location, $controller, $uibModal, flAPI){

    // Add some base - unzip base controller's stuff into this controller
    var decorationViewEditController = this;
    angular.extend(decorationViewEditController, $controller('resourceController', {$scope: $scope})); 
    
    $scope.state = Object.create(decorationViewEditController.state);        // inherit the proto
	$scope.dec = Object.create($scope.record);
	$scope.shadow = Object.create($scope.originalRecord);

    $scope.beginEdit = function(){
        $scope.state.path.mode = 'edit';
    }
    $scope.finishEdit = function(){
        $scope.state.path.mode = 'view';
        updateDecoration();
    }
	$scope.cancelEdit = function(){
		if ($scope.state.isLoaded){
			$scope.dec = angular.extend(Object.create($scope.record), $scope.shadow);
			$scope.state.path.mode = 'view';
			return;
		}
		console.warn('Cannot cancel - dec record was never loaded');
	};
    
	// Read the url
    if (decorationViewEditController.loadWorkflowPath()){
        retrieveDecoration();
    }

    
    $scope.formData = {
        tiers: {
            A: 'A',
            B: 'B',
            C: 'C',
            D: 'D',
            E: 'E',
        }
    }
    
    // ====================
    // Function decs
    
	function retrieveDecoration(){
		if ($scope.state.path.id){
			flAPI('decoration').get([$scope.state.path.id]).then(function(response){
				// Process then store in VM
				processDecoration(response.data.decoration);
			}, function(response){
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
		}
	};
	function processDecoration(dec){
        decorationViewEditController.convertToDateObjects(['date_commence', 'date_conclude', 'created_at', 'updated_at', 'deleted_at'], dec);
		$scope.dec = dec;
		$scope.shadow = angular.extend(Object.create($scope.originalRecord), dec);
	};
	function updateDecoration(){
		var hasChanges = $scope.decorationDetails.$dirty;
		if (hasChanges){
            var payload = {
                decoration: {}
            };
            angular.forEach($scope.dec, function(value, key){
                if (!angular.equals($scope.shadow[key], value)){
                    payload.decoration[key] = value;
                }
            });
			flAPI('decoration').patch([$scope.dec.dec_id], payload).then(function(response){
				$scope.state.successMessage = 'Save successful';
				$scope.shadow = angular.extend(Object.create($scope.originalRecord), $scope.dec);
				
			}, function(response){
				$scope.state.errorMessage = "Warning: Couldn't save this record";
				console.warn('Error: dec update', response);
			});
		}
	};
    
    
    //======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.state.isEdit()){
			var message = 'You are editing this decoration record, and will lose any unsaved changes.';
			return message;
		}
	};
	$scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});

	
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
		flAPI('decoration').delete([$scope.dec.dec_id, 'picture']).then(function(response){
			//reloadMemberImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
		}, function(response){
			console.warn('ERROR: Last picture could not be rewound');
			$scope.state.errorMessage = 'Failed to rewind picture';
		});
	};
	$scope.deleteAll = function(){
		flAPI('decoration').delete([$scope.dec.dec_id, 'picture'], {params: { remove: 'all' }}).then(function(response){
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
    
    
    $scope.$watch('dec.dec_id', function(newValue){
        reloadMemberImage();
        updateUploaderDestination();
	});
    
    // If the modal uploads a new pic, make sure all other pictureControllers update
    $scope.$on('flares::displayPictureChanged', function(){
        if ($scope.dec.dec_id){
            reloadMemberImage();            
        }
    });
    
	
	// ===========================
    // Function decs
    
    function reloadMemberImage(){
		// var memberPictureRequestUrl = '/api/member/'+$scope.member.regt_num+'/picture';
		// $http.get(memberPictureRequestUrl+'/exists').then(function(response){
        var decID = $scope.dec.dec_id;
        flAPI('decoration').get([decID, 'badge', 'exists']).then(function(response){
            if (response.status === 200){
                if (response.data.exists){
                    var cacheDefeater = +Date.now();
                    // Todo: replace the below with a more sturdy flResource solution
                    $scope.image.url = '/api/decoration/' + decID + '/badge?' + cacheDefeater;
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
    
    function updateUploaderDestination(){
        var decID = $scope.dec.dec_id;
        if ($scope.$flow && decID){
            $scope.$flow.opts.target = '/api/decoration/'+$scope.dec.dec_id+'/badge/new';
            // $scope.$flow.opts.target = flAPI('decoration').sub('badge/new', decID).url();
            console.log('Updated uploader target %s', $scope.$flow.opts.target);
            $scope.uploader.hasUploadTarget = true;
        }
        else {
            $scope.uploader.hasUploadTarget = false;
        }
    }
	 
});