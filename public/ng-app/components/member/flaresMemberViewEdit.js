var flaresApp = angular.module('flaresMemberViewEdit', ['flaresBase', 'flow']);

flaresApp.config(['flowFactoryProvider', '$httpProvider', function (flowFactoryProvider, $httpProvider) {

    function imageResizer(fileObj) {	// fileObj is an instance of FlowFile
        console.log(fileObj);
        console.log('TODO ImageResizer: file size is ' + Math.floor(fileObj.file.size / 1024) + ' KB');
    };

    // $httpProvider.defaults.xsrfCookieName should be XSRF-TOKEN
    flowFactoryProvider.defaults = {
        headers: {},
        initFileFn: imageResizer,
        singleFile: true,
        allowDuplicateUploads: true,
    };
    flowFactoryProvider.defaults.headers[$httpProvider.defaults.xsrfHeaderName] = (function (cookieName) {
        var c = document.cookie.split('; ');
        for (var i = 0; i < c.length; i++) {
            var cookie = c[i].split('=');
            if (cookie[0] === cookieName) {
                return decodeURIComponent(cookie[1]);
            }
        }
    }($httpProvider.defaults.xsrfCookieName));

}]);

flaresApp.run(['$http', '$templateCache', function ($http, $templateCache) {
    $http.get('ng-app/components/member/memberDisplayPictureTemplate.html').then(function (response) {
        $templateCache.put('memberDisplayPictureTemplate.html', response.data);
    });
}]);

flaresApp.controller('memberViewEditController', function ($scope, $rootScope, $window, $controller, $q, $uibModal, flAPI, flResource) {

    // Add some base 
    angular.extend(this, $controller('viewEditController', { $scope: $scope }));

    var c = this;
    c.extendConfig({
        unloadWarning: 'You are editing this member record, and will lose any unsaved changes.'
    });

    $scope.state = Object.create(c.state);        // inherit the proto
    $scope.state.isDischarge = function () {
        return this.path.mode === 'discharge';
    };
    $scope.state.isImageUploadable = function () {
        return this.isMemberLoaded && !$scope.member.data.deleted_at;
    };

    $scope.member = Object.create($scope.record);
    $scope.originalMember = Object.create($scope.record);

    $scope.dischargeContext = {		// viewmodel for the discharge screen
        effectiveDate: new Date(),
        isCustomRank: false,
        dischargeRank: 'REC'
    };

    $scope.formData = {
        sexes: ['M', 'F']
    };

    $scope.nav = {
        assignAward: null
    };

    $scope.shiftKeyPressed = false;

    $scope.edit = function () {
        var sw = $scope.state;
        if (sw.isView()) {
            // If in view mode, toggle to Edit mode
            sw.path.mode = 'edit';
            return;
        }
        if (sw.isEdit()) {
            // Save the changes
            // send back to view mode
            updateMemberRecord();
            sw.path.mode = 'view';
        }
    };
    $scope.cancelEdit = function () {
        if ($scope.state.isLoaded) {
            $scope.member.data = angular.copy($scope.originalMember.data);
            $scope.state.path.mode = 'view';
            return;
        }
        console.warn('Cannot cancel - member record was never loaded');
    };

    $scope.activate = function () {
        var sw = $scope.state;
        if ($scope.member.regtNum) {
            var payload = {
                member: {
                    is_enrolled: 1
                }
            };
            sw.isAsync = true;
            // $http.patch('/api/member/'+$scope.member.regt_num, payload).then(function(response){
            flAPI('member').patch([$scope.member.regtNum], payload).then(function (response) {
                console.log('Activation successful');
                retrieveMember();

            }, function (response) {
                // Save failed. Why?
                alert('Warning: Couldn\'t activate this record. Check your connection.');
                console.warn('Error: member update', response);

            }).finally(function () {
                sw.isAsync = false;

            });
        }
    };

    $scope.startDischarge = function () {
        $scope.state.path.mode = 'discharge';
        $scope.state.path.tab = 'confirm';
    };
    $scope.cancelDischarge = function () {
        $scope.state.path.mode = 'view';
        $scope.state.path.tab = 'details';
    };
    $scope.discharge = function () {
        var sw = $scope.state;
        if (!sw.isDischarge()) {
            $scope.startDischarge();
            return;
        }
        sw.isAsync = true;

        flAPI('member').postingFor($scope.member.regtNum).post({ context: $scope.dischargeContext }).then(function (response) {
            console.log('Success: Created discharge posting record');

            flAPI('member').delete([$scope.member.regtNum]).then(function (response) {
                retrieveMember();
                $scope.state.path.mode = 'view';		// Revert
                $scope.state.path.tab = 'details';

            }, function (response) {
                console.warn('ERROR: Discharge process failed', response);
                alert('Error occurred during discharge process (2)');

            }).finally(function () {
                sw.isAsync = false;

            });
        }, function (response) {
            console.warn('ERROR: Discharge posting record failed -- member was not discharged as a result', response);
            alert('Error occurred during discharge process (1)');
        });

    };

    $scope.permanentDelete = function () {
        var sw = $scope.state;
        if ($scope.member.regtNum && !$scope.member.data.is_enrolled) {
            sw.isAsync = true;
            flAPI('member').delete([$scope.member.regtNum], { params: { remove: 'permanent' } }).then(function (response) {
                delete $scope.member;  // Clear all traces of the old member
                sw.isMemberLoaded = false;
                retrieveMember();		// Then this should result in a "Member not found"

            }, function (response) {
                console.warn('ERROR: Permanent delete process failed', response);
                alert('Error occurred during deletion process.');

            }).finally(function () {
                sw.isAsync = false;

            });
        }
    };

    $scope.displayPictureModal = function () {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'memberDisplayPictureTemplate.html',
            controller: 'pictureModalController',
            scope: $scope,
            size: 'lg',
            resolve: {

            }
        });
        modalInstance.result.then(function (selectedItem) {
        }, function () {
            // Cancellation
            console.log('Modal dismissed at: ' + new Date());
        });
    };

    $scope.removeAward = function (award) {
        if (confirm('Are you sure you want to delete this award?')) {
            award.isDeleting = true;
            flAPI('member').nested('decoration', $scope.member.regtNum).delete(award.id).then(function () {
                var deletedIndex = $scope.member.awards.indexOf(award);
                $scope.member.awards.splice(deletedIndex, 1);
            }).catch(function (err) {
                award.isDeleting = false;
                console.warn(err);
            });
        }
        else {
            return false;
        }
    };

    // Catch keyup/down for shift event
    registerShiftKeyListener();

    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // Read the url and GO
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    if (c.loadWorkflowPath()) {
        retrieveMember()
            .then(function () {
                $scope.nav.assignAward = flResource('member')
                    .single('decorations/new')
                    .setFragment([$scope.member.regtNum])
                    .getLink();
            })
            .catch(function (err) {
                console.log(err);
            });
    }

    //==================
    // Fetch reference data for platoons and ranks
    //==================

    // $http.get('/api/refdata').then(function(response){
    flAPI('refData').getAll().then(function (response) {
        if (response.data.ranks) {
            $scope.formData.ranks = response.data.ranks;
        }
    });

    // ====================
    // Function decs
    // ====================

    function retrieveMember() {
        var regtNum = $scope.state.path.id;
        if (regtNum) {
            return $q(function (resolve, reject) {
                // $http.get('/api/member/'+$scope.state.path.id, {params: {detail: 'high'}}).then(function(response){
                flAPI('member').get([regtNum], { params: { detail: 'high' } }).then(function (response) {
                    // Process then store in VM
                    var responseData = response.data.member;
                    processMemberRecord(responseData);        // by ref
                    $scope.member = {
                        regtNum: responseData.regt_num,
                        data: responseData
                    };
                    $scope.originalMember = angular.copy($scope.member);

                    flAPI('member').nested('decoration', regtNum).getAll().then(function (response) {
                        var awards = [];
                        angular.forEach(response.data.decorations, function (decorationData) {
                            var award = { data: decorationData, id: decorationData.awd_id };
                            processMemberDecorationRecord(award.data);
                            award.url = flResource().raw(['media', 'decoration', award.data.dec_id, 'badge']);
                            this.push(award);
                        }, awards);
                        $scope.member.awards = awards;
                        $scope.originalMember.awards = awards;
                    });

                    $scope.state.isMemberLoaded = true;
                    resolve(responseData);

                }, function (response) {
                    $scope.member[response.status == 404 ? 'errorNotFound' : 'errorServerSide'] = true;
                    reject(response);
                });
            });
        }
        else {
            console.warn('Member ID not specified');
            return $q.reject('No member ID found in path');
        }
    }

    function processMemberRecord(member) {
        c.util.convertToDateObjects(['dob', 'idcard_expiry', 'created_at', 'updated_at', 'deleted_at'], member);
    }

    function processMemberDecorationRecord(memberDecoration) {
        c.util.convertToDateObjects(['date', 'updated_at', 'deleted_at'], memberDecoration);
        c.util.convertToDateObjects(['date_commence', 'date_conclude', 'updated_at'], memberDecoration.decoration);
    }

    function updateMemberRecord() {
        var hasChanges = false;
        var payload = { member: {} };
        angular.forEach($scope.member.data, function (value, key) {
            if (angular.isObject(value)) {
                // Todo: Figure out deep object change detection
                return;
            }
            // if (~[].indexOf(key)){
            // // No timestamps
            // return;
            // }
            if ($scope.originalMember.data[key] !== value) {
                // Value has changed
                hasChanges = true;
                payload.member[key] = value;
            }
        });
        // console.log(payload);
        // return;
        if (hasChanges) {
            flAPI('member').patch([$scope.member.regtNum], payload).then(function (response) {
                console.log('Save successful');
                $scope.originalMember = angular.copy($scope.member);

            }, function (response) {
                // Save failed. Why?
                alert('Warning: Couldn\'t save this record. Check your connection.');
                console.warn('Error: member update', response);
            });
        }
    }

    function registerShiftKeyListener() {
        var doc = angular.element(document);
        doc.on('keydown', listenShiftKey);
        doc.on('keyup', listenShiftKey);
    }
    function deregisterShiftKeyListener() {
        var doc = angular.element(document);
        doc.off('keydown', listenShiftKey);
        doc.off('keyup', listenShiftKey);
    }

    function listenShiftKey(event) {
        if ($scope.shiftKeyPressed !== event.shiftKey) {
            $scope.$apply(function () {
                $scope.shiftKeyPressed = event.shiftKey;
                // console.log(event.shiftKey);
            });
        }
    }

});

flaresApp.controller('pictureController', function ($scope, $rootScope, $http, $timeout, flAPI, flResource) {

    var maxImageSize = 1024 * 1024;		// 1MB max file size
    var maxImageSizeDesc = '1MB';
    var defaultImage = flResource().raw(['assets', 'img', 'anon.png']);

    $scope.memberImage = {
        url: defaultImage,
        isDefault: true,
        count: 0
    };
    $scope.memberImage.resetToDefault = function () {
        this.url = defaultImage;
        this.isDefault = true;
    };

    $scope.uploader = {
        uploading: false,
        dropzone: false,
        hasUploadTarget: false,
        ready: function () {
            return $scope.uploader.hasUploadTarget && $scope.state.isImageUploadable();
        }
    };

    $scope.uploadStart = function () {
        $scope.uploader.uploading = true;
    };
    $scope.uploadFinish = function () {
        if ($scope.$flow.files.length > 0) {			// If any upload took place
            $scope.memberImage.resetToDefault();		// Revert it to the default

            // reloadMemberImage();
            $rootScope.$broadcast('flares::displayPictureChanged');

            $timeout(function () {
                // Allow the upload success message to flash
                $scope.uploader.uploading = false;
                $scope.$flow.cancel();			// Clear out the files array
            }, 4000);
        }
        else {
            $scope.uploader.uploading = false;
        }
    };

    $scope.deleteLast = function () {
        // $http.delete('/api/member/'+$scope.member.regt_num+'/picture').then(function(response){
        flAPI('member').delete([$scope.member.regtNum, 'picture']).then(function (response) {
            //reloadMemberImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
        }, function (response) {
            console.warn('ERROR: Last picture could not be rewound');
            alert('Failed to rewind picture');
        });
    };
    $scope.deleteAll = function () {
        // $http.delete('/api/member/'+$scope.member.regt_num+'/picture', {params: { remove: 'all' }}).then(function(response){
        flAPI('member').delete([$scope.member.regtNum, 'picture'], { params: { remove: 'all' } }).then(function (response) {
            //reloadMemberImage();
            $rootScope.$broadcast('flares::displayPictureChanged');
        }, function (response) {
            console.warn('ERROR: Picture could not be deleted');
            alert('Failed to delete picture');
        });
    };

    $scope.$on('flow::fileAdded', function (event, $flow, flowFile) {
        if (flowFile.size > maxImageSize) {
            console.warn('Image is oversize: ', flowFile.size);
            alert('Your image is too big; the maximum upload size is ' + maxImageSizeDesc);
            event.preventDefault();  //prevent file from uploading
        }
    });


    $scope.$watch('member.regtNum', function (newValue) {
        reloadMemberImage();
        updateUploaderDestination();
    });

    // If the modal uploads a new pic, make sure all other pictureControllers update
    $scope.$on('flares::displayPictureChanged', function () {
        reloadMemberImage();
    });


    // ===========================
    // Function decs

    function reloadMemberImage() {
        // var memberPictureRequestUrl = '/api/member/'+$scope.member.regt_num+'/picture';
        // $http.get(memberPictureRequestUrl+'/exists').then(function(response){
        if ($scope.member.regtNum) {
            flAPI('member').get([$scope.member.regtNum, 'picture']).then(function (response) {
                if (response.status === 200) {
                    if (response.data.exists) {
                        var cacheDefeater = +Date.now();
                        // Todo: replace the below with a more sturdy flResource solution
                        // $scope.memberImage.url = flResource().raw(['media', 'member', $scope.member.regt_num, 'picture'], [cacheDefeater]);
                        $scope.memberImage.url = response.data.url;
                        $scope.memberImage.isDefault = false;
                    }
                    else {
                        $scope.memberImage.resetToDefault();
                    }
                    $scope.memberImage.count = response.data.count;
                }
            }, function (response) {
                console.warn('WARN: Image not found for ' + $scope.member.regtNum, response.status);
                $scope.memberImage.resetToDefault();
            });
        }
    };
    function updateUploaderDestination() {
        if ($scope.$flow && $scope.member.regtNum) {
            $scope.$flow.opts.target = '/api/member/' + $scope.member.regtNum + '/picture/new';
            console.log('Updated uploader target %s', $scope.$flow.opts.target);
            $scope.uploader.hasUploadTarget = true;
        }
        else {
            $scope.uploader.hasUploadTarget = false;
        }
    }


});

flaresApp.controller('pictureModalController', function ($scope, $modalInstance) {
    $scope.closeModal = function () {
        $modalInstance.dismiss('cancel');
    };
});

// ==========================================
// Specific directives for View/Edit screens

flaresApp.directive('memberStatus', function () {
    return {
        link: function (scope, element, attr) {
            scope.$watchGroup(['member.data.is_enrolled', 'member.data.deleted_at'], function () {
                var smd = scope.member.data;
                if (!smd.is_enrolled) {
                    element.removeClass().addClass('label label-danger');
                    element.text('Inactive');
                }
                else if (smd.deleted_at) {
                    element.removeClass().addClass('label label-warning');
                    element.text('Discharged');
                }
                else {
                    element.removeClass().addClass('label label-success');
                    element.text('Active');
                }
            });
        }
    };
});
flaresApp.directive('hmpStatus', function () {
    return {
        link: function (scope, element, attr) {
            scope.$watch('member.data.is_med_hmp', function () {
                var smd = scope.member.data;
                if (!!+smd.is_med_hmp) {		// Expect is_hmp to either be '0' or '1'
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
flaresApp.directive('allergyStatus', function () {
    return {
        link: function (scope, element, attr) {
            scope.$watch('member.data.is_med_lifethreat', function () {
                var smd = scope.member.data;
                if (!!+smd.is_med_lifethreat) {		// Expect is_hmp to either be '0' or '1'
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
