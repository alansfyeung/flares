var flaresApp = angular.module('flaresMemberViewEdit', ['flaresBase', 'flow']);

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

flaresApp.run(['$http', '$templateCache', function($http, $templateCache){
    $http.get('/app/components/member/memberDisplayPictureTemplate.html').then(function(response){
        $templateCache.put('memberDisplayPictureTemplate.html', response.data);
    });
}]);

flaresApp.controller('memberViewEditController', function($scope, $location, $controller, $uibModal, flAPI){
    
    // Add some base 
    var veController = this;
    angular.extend(veController, $controller('baseViewEditController', {$scope: $scope})); 
	$scope.state = Object.create(veController.state);        // inherit the proto
	$scope.state.isDischarge = function(){
		return this.path.mode === 'discharge';
	};
	$scope.state.isImageUploadable = function(){
		return this.isMemberLoaded && !$scope.member.deleted_at;
	};
	$scope.state.toggleMode = function(){
		this.path.mode = this.isView() ? 'edit' : 'view';
	};
    
	$scope.member = Object.create($scope.record);
	$scope.originalMember = Object.create($scope.originalRecord);
	
	$scope.dischargeContext = {		// viewmodel for the discharge screen
		effectiveDate: new Date(),
		isCustomRank: false,
		dischargeRank: 'REC'
	};
	$scope.formData = {
		sexes: ['M','F']
	}
	
	$scope.edit = function(){
		var sw = $scope.state;
		if (sw.isView()){
			// If in view mode, toggle to Edit mode
			sw.path.mode = 'edit';
			return;
		}
		if (sw.isEdit()){
			// Save the changes
			// send back to view mode
			updateMemberRecord();
			sw.path.mode = 'view';
		}
	};
	$scope.cancelEdit = function(){
		if ($scope.state.isLoaded){
			$scope.member = angular.extend(Object.create($scope.record), $scope.originalMember);
			$scope.state.path.mode = 'view';
			return;
		}
		console.warn('Cannot cancel - member record was never loaded');
	};
	
	$scope.activate = function(){
		var sw = $scope.state;
		if ($scope.member.regt_num){
			var payload = {
				member: {
					is_active: 1
				}
			};	
			sw.isAsync = true;
			// $http.patch('/api/member/'+$scope.member.regt_num, payload).then(function(response){
			flAPI('member').patch([$scope.member.regt_num], payload).then(function(response){
				console.log('Activation successful');
				retrieveMember();
				
			}, function(response){
				// Save failed. Why?
				alert('Warning: Couldn\'t activate this record. Check your connection.');
				console.warn('Error: member update', response);
				
			}).finally(function(){
				sw.isAsync = false;
				
			});
		}
	};
	
	$scope.confirmDischarge = function(){
		$scope.state.path.mode = 'discharge';
		$scope.state.path.tab = 'confirm';
	};
	$scope.cancelDischarge = function(){
		$scope.state.path.mode = 'view';
		$scope.state.path.tab = 'details';
	};
	$scope.discharge = function(){
		var sw = $scope.state;
		if (!sw.isDischarge()){
			$scope.confirmDischarge();
			return;
		}
		sw.isAsync = true;
		
		// $http.post('/api/member/'+$scope.member.regt_num+'/posting', {context: $scope.dischargeContext}).then(function(response){
		flAPI('member').postingFor($scope.member.regt_num).post({context: $scope.dischargeContext}).then(function(response){
			console.log('Success: Created discharge posting record');
			
			// $http.delete('/api/member/'+$scope.member.regt_num).then(function(response){
			flAPI('member').delete([$scope.member.regt_num]).then(function(response){
				retrieveMember();
				$scope.state.path.mode = 'view';		// Revert
				$scope.state.path.tab = 'details';
				
			}, function(response){
				console.warn('ERROR: Discharge process failed', response);
				alert('Error occurred during discharge process (2)');
				
			}).finally(function(){
				sw.isAsync = false;
				
			});
		}, function(response){
			console.warn('ERROR: Discharge posting record failed -- member was not discharged as a result', response);
			alert('Error occurred during discharge process (1)');
		});
		
	};
	
	$scope.permanentDelete = function(){
		var sw = $scope.state;
		if ($scope.member.regt_num && !$scope.member.is_active){
			sw.isAsync = true;
			// $http.delete('/api/member/'+$scope.member.regt_num, {params: { remove: 'permanent' }}).then(function(response){
			flAPI('member').delete([$scope.member.regt_num], {params: { remove: 'permanent' }}).then(function(response){
				$scope.member = {};  // Clear all traces of the old member
				sw.isMemberLoaded = false;
				retrieveMember();		// Then this should result in a "Member not found"
				
			}, function(response){
				console.warn('ERROR: Permanent delete process failed', response);
				alert('Error occurred during deletion process.');
				
			}).finally(function(){
				sw.isAsync = false;
				
			});
		}
	};
    
    $scope.displayPictureModal = function(){
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'memberDisplayPictureTemplate.html',
            controller: 'pictureModalController',
            scope: $scope,
            size: 'lg',
            resolve: {
                
            }
        });
        modalInstance.result.then(function(selectedItem){
        }, function(){
            // Cancellation
            console.log('Modal dismissed at: ' + new Date());
        });
    }
	
	
	// Read the url
    if (veController.loadWorkflowPath()){
        retrieveMember();
    }
	
	//==================
	// Fetch reference data for platoons and ranks
	
	// $http.get('/api/refdata').then(function(response){
	flAPI('refData').getAll().then(function(response){
		if (response.data.ranks){
			$scope.formData.ranks = response.data.ranks;
		}
	});
	
	
	//======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.state.isEdit()){
			var message = 'You are editing this member record, and will lose any unsaved changes.';
			return message;
		}
	};
		
	$scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
	
    
    // ====================
    // Function decs
    
	function retrieveMember(){
		if ($scope.state.path.id){
			// $http.get('/api/member/'+$scope.state.path.id, {params: {detail: 'high'}}).then(function(response){
			flAPI('member').get([$scope.state.path.id], {params: {detail: 'high'}}).then(function(response){
				// Process then store in VM
				processMemberRecord(response.data.member);
				$scope.state.isMemberLoaded = true;
				
			}, function(response){
				if (response.status == 404){
					$scope.member.errorNotFound = true;
				}
				else {
					$scope.member.errorServerSide = true;
				}
			});
		}
		else {
			console.warn('Member ID not specified');
		}
	};
	function processMemberRecord(member){
        veController.convertToDateObjects(['dob', 'idcard_expiry', 'created_at', 'updated_at', 'deleted_at'], member);
		$scope.member = member;
		$scope.originalMember = angular.extend(Object.create($scope.originalRecord), member);
	};
	function updateMemberRecord(){
		var hasChanges = false;
		var payload = {
			member: {}
		};	
		angular.forEach($scope.member, function(value, key){
			if ($scope.originalMember[key] !== value){
				// Value has changed
				hasChanges = true;
				payload.member[key] = value;
			}
		});
		if (hasChanges){
			// $http.patch('/api/member/'+$scope.member.regt_num, payload).then(function(response){
			flAPI('member').patch([$scope.member.regt_num], payload).then(function(response){
				console.log('Save successful');
				$scope.originalMember = angular.extend(Object.create($scope.originalRecord), $scope.member);
				
			}, function(response){
				// Save failed. Why?
				alert('Warning: Couldn\'t save this record. Check your connection.');
				console.warn('Error: member update', response);
			});
		}
	};
	
});

flaresApp.controller('pictureController', function($scope, $rootScope, $http, $timeout, flAPI, flResource){
    
	var maxImageSize = 1024 * 1024;		// 1MB max file size
	var maxImageSizeDesc = '1MB';
	var defaultImage = flResource('resource').addUrl('img/anon.png').getLink();
	
	$scope.memberImage = {
		url: defaultImage,
		isDefault: true,
		count: 0
	};
	$scope.memberImage.resetToDefault = function(){
		this.url = defaultImage;
		this.isDefault = true;
	};
		
	$scope.uploader = {
		uploading: false,
		dropzone: false,
        hasUploadTarget: false,
		ready: function(){
			return $scope.uploader.hasUploadTarget && $scope.state.isImageUploadable();
		}
	};
	
	$scope.uploadStart = function(){
		$scope.uploader.uploading = true;
	};
	$scope.uploadFinish = function(){
		if ($scope.$flow.files.length > 0){			// If any upload took place
			$scope.memberImage.resetToDefault();		// Revert it to the default
            
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
		// $http.delete('/api/member/'+$scope.member.regt_num+'/picture').then(function(response){
		flAPI('member').delete([$scope.member.regt_num, 'picture']).then(function(response){
			//reloadMemberImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
		}, function(response){
			console.warn('ERROR: Last picture could not be rewound');
			alert('Failed to rewind picture');
		});
	};
	$scope.deleteAll = function(){
		// $http.delete('/api/member/'+$scope.member.regt_num+'/picture', {params: { remove: 'all' }}).then(function(response){
		flAPI('member').delete([$scope.member.regt_num, 'picture'], {params: { remove: 'all' }}).then(function(response){
			//reloadMemberImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
		}, function(response){
			console.warn('ERROR: Picture could not be deleted');
			alert('Failed to delete picture');
		});
	};
	
	$scope.$on('flow::fileAdded', function (event, $flow, flowFile) {
		if (flowFile.size > maxImageSize){
			console.warn('Image is oversize: ', flowFile.size);
			alert('Your image is too big; the maximum upload size is ' + maxImageSizeDesc);
			event.preventDefault();  //prevent file from uploading
		}
	});
    
    
    $scope.$watch('member.regt_num', function(newValue){
        reloadMemberImage();
        updateUploaderDestination();
	});
    
    // If the modal uploads a new pic, make sure all other pictureControllers update
    $scope.$on('flares::displayPictureChanged', function(){
        reloadMemberImage();
    });
    
	
	// ===========================
    // Function decs
    
    function reloadMemberImage(){
		// var memberPictureRequestUrl = '/api/member/'+$scope.member.regt_num+'/picture';
		// $http.get(memberPictureRequestUrl+'/exists').then(function(response){
        if ($scope.member.regt_num){
            flAPI('member').get([$scope.member.regt_num, 'picture', 'exists']).then(function(response){
                if (response.status === 200){
                    if (response.data.exists){
                        var cacheDefeater = +Date.now();
                        // Todo: replace the below with a more sturdy flResource solution
                        $scope.memberImage.url = flResource().raw(['api', 'member', $scope.member.regt_num, 'picture'], [cacheDefeater]);
                        $scope.memberImage.isDefault = false;
                    }
                    else {
                        $scope.memberImage.resetToDefault();
                    }
                    $scope.memberImage.count = response.data.count;
                }
            }, function(response){
                console.warn('WARN: Image not found for '+$scope.member.regt_num, response.status);
                $scope.memberImage.resetToDefault();
            });
        }
	};
    function updateUploaderDestination(){
        if ($scope.$flow && $scope.member.regt_num){
            $scope.$flow.opts.target = '/api/member/'+$scope.member.regt_num+'/picture/new';
            console.log('Updated uploader target %s', $scope.$flow.opts.target);
            $scope.uploader.hasUploadTarget = true;
        }
        else {
            $scope.uploader.hasUploadTarget = false;
        }
    }
	
    
});

flaresApp.controller('pictureModalController', function($scope, $modalInstance){
    $scope.closeModal = function(){
         $modalInstance.dismiss('cancel');
    };
});

// ==========================================
// Specific directives for View/Edit screens

flaresApp.directive('memberStatus', function(){
	return {
		link: function(scope, element, attr){
			scope.$watchGroup(['member.is_active', 'member.deleted_at'], function(){
				if (!scope.member.is_active){
					element.removeClass().addClass('label label-danger');
					element.text('Inactive');
				}
				else if (scope.member.deleted_at){
					element.removeClass().addClass('label label-warning');
					element.text('Discharged');
				}
				else {
					element.removeClass().addClass('label label-success');
					element.text('Active');
				}
				// '<span class="label" ng-class="{'label-success': member.is_active, 'label-danger': !member.is_active}">';				
			});
		}
	};
});
flaresApp.directive('hmpStatus', function(){
	return {
		link: function(scope, element, attr){
			scope.$watch('member.is_med_hmp', function(){
				if (!!+scope.member.is_med_hmp){		// Expect is_hmp to either be '0' or '1'
					element.removeClass().addClass('label label-default');
					element.text('HMP');
				}
				else {
					element.removeClass().text('');
				}
			});
		}
	};
});
flaresApp.directive('allergyStatus', function(){
	return {
		link: function(scope, element, attr){
			scope.$watch('member.is_med_lifethreat', function(){
				if (!!+scope.member.is_med_lifethreat){		// Expect is_hmp to either be '0' or '1'
					element.removeClass().addClass('label label-danger');
					element.text('Life threatening');
				}
				else {
					element.removeClass().text('');
				}
			});
		}
	};
});