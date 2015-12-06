// =============================
//    flaresActivityNew.js
//    Add a new activity record
// =============================

var flaresApp = angular.module('flaresActivityNew', ['flaresBase']);

flaresApp.controller('activityAddController', function($scope, $http, flaresAPI, flaresLinkBuilder){
    
    $scope.newActivity = {
        isSaved: false,
        isUpdated: false,
        lastPersistTime: null,
        data: {
            type: '',
            name: '',
            start_date: new Date(),
            end_date: new Date(),
            desc: '',
            is_half_day: 1,
            is_parade_night: 0
        }
    };
    
    $scope.formData = {};
    $scope.workflow = {
        isSubmitAttempted: false,
        validation: {
            hasErrors: false,
            message: ''
        }
    };
    $scope.workflow.setName = function(newName){
        $scope.newActivity.data.name = newName;
    };
    $scope.workflow.setValidation = function(hasErrors, message){
        this.validation.hasErrors = hasErrors;
        this.validation.message = message;
    };
    
    
    var validateActivityForm = function(){
        var d = $scope.newActivity.data;
        if (!(d.name && d.type)){
            $scope.workflow.setValidation(true, 'Y u do dis');
            return false;
        }
        $scope.workflow.setValidation(false, '');
        return true;
    };
    var createActivity = function(){
        return new Promise(function(fulfill, reject){
            var payload = {
                activity: $scope.newActivity.data
            };
            
            // $http.post('/api/activity', payload).then(function(response){
            flaresAPI.activity.post(payload).then(function(response){
                if (response.data.error && response.data.error.code){
                    console.warn(response.data.error); 
                    return;
                }
                if (response.data.recordId){
                    $scope.newActivity.actyId = response.data.recordId;	
                    $scope.newActivity.isSaved = true;
                }
                fulfill(response.data);
                
            }, function(response){
                console.warn('Error: activity add', response);
                reject();
                
            });
            
        });
    };
    
    $scope.submitThenAddRoll = function(){
        $scope.workflow.isSubmitAttempted = true;
        if (validateActivityForm()){
            createActivity().then(function(data){
                // Take us to the activity's view
                // window.location.href = $('[name=menu.activity.overview]').attr('href');
                // console.log(flaresLinkBuilderflaresLinkBuilder.page().activity(data.recordId).getLink());
                window.location.href = flaresLinkBuilder.page().activity(data.recordId).getLink();
            });
        }
        return false;
    };
    $scope.submitOnly = function(){
        $scope.workflow.isSubmitAttempted = true;
        if (validateActivityForm()){
            createActivity().then(function(){
                // Return to the activity overview
                window.location.href = $('[name=menu\\.activity\\.overview]').attr('href');
            });
        }
        return false;
    };
    
    
    
    
    //==================
	// Fetch reference data for activityTypes and activityNamePresets
    
    flaresAPI.refData.get(['activity']).then(function(response){
		if (response.data.types){
			$scope.formData.activityTypes = response.data.types;
		}
        if (response.data.presets){
			$scope.formData.namePresets = response.data.presets;
		}
	});
    
});