var flaresApp = angular.module('flaresMemberViewEdit', ['flaresBase', 'flow']);

flaresApp.controller('memberController', function($scope, $http, $location, flaresAPI){
	
	$scope.member = {};
	$scope.originalMember = {};
	
	$scope.dischargeContext = {		// viewmodel for the discharge screen
		effectiveDate: new Date(),
		isCustomRank: false,
		dischargeRank: 'REC'
	};
	$scope.formData = {
		sexes: ['M','F']
	}
	$scope.workflow = {
		path: {
			id: 0,
			mode: 'view',		// by default
			tab: 'details'
		},
		isMemberRequested: false,
		isMemberLoaded: false,
		isAsync: false
	};
	$scope.workflow.isView = function(){
		return this.path.mode === 'view';
	};
	$scope.workflow.isEdit = function(){
		return this.path.mode === 'edit';
	};
	$scope.workflow.isDischarge = function(){
		return this.path.mode === 'discharge';
	};
	$scope.workflow.isImageUploadable = function(){
		return this.isMemberLoaded && !$scope.member.deleted_at;
	};
	$scope.workflow.toggleMode = function(){
		this.path.mode = this.isView() ? 'edit' : 'view';
	};
	
	$scope.edit = function(){
		var sw = $scope.workflow;
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
		if ($scope.workflow.isMemberLoaded){
			$scope.member = angular.extend({}, $scope.originalMember);
			$scope.workflow.path.mode = 'view';
			return;
		}
		console.warn('Cannot cancel - member record was never loaded');
	};
	
	$scope.activate = function(){
		var sw = $scope.workflow;
		if ($scope.member.regt_num){
			var payload = {
				member: {
					is_active: 1
				}
			};	
			sw.isAsync = true;
			// $http.patch('/api/member/'+$scope.member.regt_num, payload).then(function(response){
			flaresAPI.member.patch([$scope.member.regt_num], payload).then(function(response){
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
		$scope.workflow.path.mode = 'discharge';
		$scope.workflow.path.tab = 'confirm';
	};
	$scope.cancelDischarge = function(){
		$scope.workflow.path.mode = 'view';
		$scope.workflow.path.tab = 'details';
	};
	$scope.discharge = function(){
		var sw = $scope.workflow;
		if (!sw.isDischarge()){
			$scope.confirmDischarge();
			return;
		}
		sw.isAsync = true;
		
		// $http.post('/api/member/'+$scope.member.regt_num+'/posting', {context: $scope.dischargeContext}).then(function(response){
		flaresAPI.member.post([$scope.member.regt_num, 'posting'], {context: $scope.dischargeContext}).then(function(response){
			console.log('Success: Created discharge record');
			
			// $http.delete('/api/member/'+$scope.member.regt_num).then(function(response){
			flaresAPI.member.delete([$scope.member.regt_num]).then(function(response){
				retrieveMember();
				$scope.workflow.path.mode = 'view';		// Revert
				$scope.workflow.path.tab = 'details';
				
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
		var sw = $scope.workflow;
		if ($scope.member.regt_num && !$scope.member.is_active){
			sw.isAsync = true;
			// $http.delete('/api/member/'+$scope.member.regt_num, {params: { remove: 'permanent' }}).then(function(response){
			flaresAPI.member.delete([$scope.member.regt_num], {params: { remove: 'permanent' }}).then(function(response){
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
	
	
	$scope.$watchCollection('workflow.path', function(){
		// Change the URL path if workflow details are updated (e.g. tab click)
		updatePath();
	});
	
	// Read the url
	// get rid of any leading slash
	var path = $location.path();
	var pathFrags = (path.indexOf('/') === 0 ? path.substring(1) : path).split('/');		
	if (pathFrags.length > 0 && pathFrags[0].length > 0){
		$scope.workflow.isMemberRequested = true;
		$scope.workflow.path.id = pathFrags[0];
		$scope.workflow.path.mode = pathFrags[1] ? pathFrags[1] : 'view';
		$scope.workflow.path.tab = pathFrags[2] ? pathFrags[2] : 'details';
		retrieveMember();
	}
	
	
	//==================
	// Fetch reference data for platoons and ranks
	
	// $http.get('/api/refdata').then(function(response){
	flaresAPI.refData.getAll().then(function(response){
		if (response.data.ranks){
			$scope.formData.ranks = response.data.ranks;
		}
	});
	
	
	//======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.workflow.isEdit()){
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
		if ($scope.workflow.path.id){
			// $http.get('/api/member/'+$scope.workflow.path.id, {params: {detail: 'high'}}).then(function(response){
			flaresAPI.member.get([$scope.workflow.path.id], {params: {detail: 'high'}}).then(function(response){
				// Process then store in VM
				processMemberRecord(response.data);
				$scope.workflow.isMemberLoaded = true;
				
				// activate the correct tab
				$("[bs-show-tab][aria-controls='" + $scope.workflow.path.tab + "']").tab('show');
				
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
		// Convert dates to JS objects
		angular.forEach(['dob', 'idcard_expiry', 'created_at', 'updated_at', 'deleted_at'], function(datePropKey){
			if (this[datePropKey]){
				var timestamp = Date.parse(this[datePropKey]);
				if (!isNaN(timestamp)){
					this[datePropKey] = new Date(this[datePropKey]);
				}
				else {
					this[datePropKey] = null;
				}
			}	
		}, member);
		
		$scope.member = member;
		$scope.originalMember = angular.extend({}, member);
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
			flaresAPI.member.patch([$scope.member.regt_num], payload).then(function(response){
				console.log('Save successful');
				$scope.originalMember = angular.extend({}, $scope.member);
				
			}, function(response){
				// Save failed. Why?
				alert('Warning: Couldn\'t save this record. Check your connection.');
				console.warn('Error: member update', response);
			});
		}
	};
	
});

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
	
}])

flaresApp.controller('pictureController', function($scope, $http, $timeout, flaresAPI, flaresLinkBuilder){
	
	var maxImageSize = 1024 * 1024;		// 1MB max file size
	var maxImageSizeDesc = '1MB';
	var defaultImage = flaresLinkBuilder.resource().anonImage().getLink();
	
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
		ready: function(){
			return $scope.member.regt_num && $scope.workflow.isImageUploadable();
		}
	};
	
	$scope.uploadStart = function(){
		$scope.uploader.uploading = true;
	};
	$scope.uploadFinish = function(){
		if ($scope.$flow.files.length > 0){			// If any upload took place
			$scope.memberImage.resetToDefault();		// Revert it to the default
			reloadMemberImage();
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
		flaresAPI.member.delete([$scope.member.regt_num, 'picture']).then(function(response){
			reloadMemberImage();
		}, function(response){
			console.warn('ERROR: Last picture could not be rewound');
			alert('Failed to rewind picture');
		});
	};
	$scope.deleteAll = function(){
		// $http.delete('/api/member/'+$scope.member.regt_num+'/picture', {params: { remove: 'all' }}).then(function(response){
		flaresAPI.member.delete([$scope.member.regt_num, 'picture'], {params: { remove: 'all' }}).then(function(response){
			reloadMemberImage();
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
		if ($scope.member.regt_num){
			// Attempt to reload the member image
			reloadMemberImage();
			// Update the uploader destination
			$scope.$flow.opts.target = '/api/member/'+$scope.member.regt_num+'/picture/new';
			console.log('Updated uploader target', $scope.$flow.opts.target);
		}
	});
	
	// ===========================
    // Function decs
    
    function reloadMemberImage(){
		// var memberPictureRequestUrl = '/api/member/'+$scope.member.regt_num+'/picture';
		// $http.get(memberPictureRequestUrl+'/exists').then(function(response){
		flaresAPI.member.get([$scope.member.regt_num, 'picture', 'exists']).then(function(response){
			if (response.status === 200){
				if (response.data.exists){
					var cacheDefeater = +Date.now();
                    // Todo: replace the below with a more sturdy flaresLinkBuilder solution
					$scope.memberImage.url = flaresLinkBuilder.raw(['api', 'member', $scope.member.regt_num, 'picture'], [cacheDefeater]);
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
	};	
	
    
});

// ==========================================
// Specific directives for View/Edit screens

flaresApp.directive('displayMode', function(){
	return { 
		restrict: 'A',
		link: function (scope, element, attr) {
			var expr = 'workflow.path.mode';
			// console.log('directiving', scope.$eval(expr));
			if (scope.$eval(expr) !== attr.displayMode){
				element.hide();
			}
			
			scope.$watch(expr, function(newValue){
				if (newValue !== attr.displayMode){
					element.hide();
					return;
				}
				element.show();
			});
		}
	};
});
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