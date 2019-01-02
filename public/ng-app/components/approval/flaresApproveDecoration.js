var flaresApp = angular.module('flaresDecorationApproval', ['flaresBase']);

flaresApp.controller('memberApproveDecorationController', function ($scope, $filter, $controller, flAPI, flResource) {

    // Extend this controller with viewEditController
    angular.extend(this, $controller('viewEditController', { $scope: $scope }));

    var c = this;
    c.extendConfig({
        'unloadWarning': 'You are editing this decoration record, and will lose any unsaved changes.'
    });

    $scope.state = Object.create(c.state);        // inherit the proto
    $scope.state.showDecorationDropdownList = false;
    $scope.formData = {
        approvalDecision: null,     // Start with neither
        months: [
            { name: 'Jan', value: 0 },
            { name: 'Feb', value: 1 },
            { name: 'Mar', value: 2 },
            { name: 'Apr', value: 3 },
            { name: 'May', value: 4 },
            { name: 'Jun', value: 5 },
            { name: 'Jul', value: 6 },
            { name: 'Aug', value: 7 },
            { name: 'Sep', value: 8 },
            { name: 'Oct', value: 9 },
            { name: 'Nov', value: 10 },
            { name: 'Dec', value: 11 }
        ],
        awardDate: {
            month: 0,
            year: 1975,
        },
    };

	$scope.appr = new Approval();
	$scope.shadowAppr = angular.copy($scope.appr);
    $scope.member = {};
    $scope.memberPictureUrl = '';

    var memberPictureDefaultUrl, decorationDefaultBadgeUrl;

    // 1. Load ref data
    // 2. Load approval data

    flAPI('refData').getAll().then(function (response) {
        if (response.data.misc) {
            var found1 = response.data.misc.find(function (misc) { return misc.name === 'PROFILE_UNKNOWN_IMAGE_PATH' });
            if (found1) {
                memberPictureDefaultUrl = found1.value;
            }
            var found2 = response.data.misc.find(function (misc) { return misc.name === 'BADGE_UNKNOWN_IMAGE_PATH' });
            if (found2) {
                decorationDefaultBadgeUrl = found2.value;
                // console.log(decorationDefaultBadgeUrl);
            }
        }
    }).then(function () {
        // Read the url and load the approval. 
        if (c.loadWorkflowPath()) {
            if ($scope.state.path.id) {
                retrieveApproval($scope.state.path.id).then(function (approvalIds) {
                    $scope.shadowAppr = angular.copy($scope.appr);
                    $scope.state.isApprovalLoaded = true;

                    if (approvalIds.dec_id) {
                        retrieveApprovalDecoration(approvalIds.dec_id);
                    }
                    if (approvalIds.regt_num) {
                        retrieveMember(approvalIds.regt_num).then(function () {
                            $scope.state.isMemberLoaded = true;
                        });
                    }
                    if (approvalIds.user_id) {
                        retrieveApproverUser(approvalIds.user_id);   // Indicates that it has already been decisioned...
                    }
                }).catch(function (err) {
                    console.warn(err);
                });
            }
            else {
                console.warn('Decoration ID not specified');
            }
        }
    });

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
			$scope.member.data = angular.copy($scope.originalMember.data);
			$scope.state.path.mode = 'view';
			return;
		}
		console.warn('Cannot cancel - member record was never loaded');
	};

    $scope.submit = saveApprovalDecision;

    $scope.approveAnother = function () {

        // TODO: Take them to approval list screen

    };

    $scope.cancelHref = function () {
        return flResource().getLink();      // This should take back to the dashboard
    };

    $scope.$watch('formData.approvalDecision', function (newVal) {
        if ($scope.appr) {
            switch (newVal) {
                case 'yes':
                    $scope.appr.data.is_approved = 1;    
                    break;
                case 'no':
                    $scope.appr.data.is_approved = 0;
                    break;
            }
        }
    });

    $scope.$watch('formData.awardDate.month', function (newVal) {
        if ($scope.appr) {
            $scope.appr.setDateMonth(newVal);
        }
    });

    $scope.$watch('formData.awardDate.year', function (newVal) {
        if ($scope.appr) {
            // Range of OK is 1975 –> (this year + 5)
            if (newVal >= 1975 && newVal <= ((new Date).getFullYear() + 5)) {
                $scope.appr.setDateYear(newVal);
            }
        }
    });

    //==================
    // Fetch reference data for decorations
    //==================

    flAPI('refData').get('decorationTiers').then(function (response) {
        if (response.data.length) {
            var tiers = response.data;
            angular.forEach(tiers, function (tier, index, tiers) {
                tiers[index].tierName = tier.tier + ': ' + tier.tierName;
            });
            $scope.formData.decorationTiers = tiers;
            // $scope.selectedTier = $scope.formData.decorationTiers[0];
        }
    });

    // ====================
    // Function decs
    // ====================

    function retrieveApproval(approvalId) {
        return flAPI('approval')
            .get([approvalId])
            .then(function (response) {
                // Process then store in VM
                if (response.data) {
                    if (response.data.approval) {
                        var appr = response.data.approval;
                        c.util.convertToDateObjects(['date', 'decision_date', 'created_at', 'updated_at'], appr);
                        $scope.appr.id = appr.dec_appr_id;
                        $scope.appr.isDecided = (appr.is_approved != null);
                        $scope.appr.isApproved = Boolean(Number(appr.is_approved));     // Convert from 0 and 1
                        $scope.appr.decisionDate = appr.decision_date;
                        $scope.appr.justification = appr.justification;
                        $scope.appr.submittedDate = appr.created_at;
                        $scope.formData.awardDate.month = appr.date.getMonth();
                        $scope.formData.awardDate.year = appr.date.getFullYear();
                    }
                    if (response.data.approver) {
                        var appv = response.data.approver;
                        $scope.appr.decisionedBy = appv;
                    }
                    if (response.data.requested_decoration) {
                        var dec = response.data.requested_decoration;
                        c.util.convertToDateObjects(['date_commence', 'date_conclude', 'updated_at'], dec);
                        $scope.appr.requestedDecoration = dec;
                        $scope.appr.requestedDecorationBadgeUrl = flResource().raw(['/media', 'decoration', dec.dec_id, 'badge']);
                    }
                    if (response.data.requester) {
                        var requester = response.data.requester;
                        $scope.member = requester;
                    }
                    if (response.data.approval && response.data.approval.request_comment) {
                        var comment = response.data.approval.request_comment;
                        $scope.appr.requestComment = comment;
                    }
                }

                return {
                    dec_appr_id: appr.dec_appr_id,
                    dec_id: dec ? undefined : appr.dec_id,
                    regt_num: requester ? undefined : appr.regt_num,
                    user_id: appv ? undefined : appr.user_id,           // Don't include if approver data already processed.
                };
            })
            .catch(function (response) {
                if (response.status == 404) {
                    $scope.appr.errorNotFound = true;
                }
                else {
                    $scope.appr.errorServerSide = true;
                }
            });
    }

    function retrieveMember(memberId) {
        return flAPI('member').get([memberId]).then(function (response) {

            if (response.data && response.data.member) {
                $scope.member = response.data.member;
                return response.data.member;
            }
            else {
                throw new Error('BadRequest.jpg');
            }

        }, function (response) {
            if (response.status == 404) {
                $scope.member.errorNotFound = true;
            }
            else {
                $scope.member.errorServerSide = true;
            }
        });
    };

    function retrieveApprovalDecoration(decorationId) {
        flAPI('decoration').get([decorationId]).then(function (response) {
            if (response.data && response.data.decoration) {
                var dec = response.data.decoration;
                c.util.convertToDateObjects(['date_commence', 'date_conclude', 'updated_at'], dec);
                $scope.appr.requestedDecoration = dec;
                $scope.appr.requestedDecorationBadgeUrl = flResource().raw(['/media', 'decoration', dec.dec_id, 'badge']);
                return dec;
            }
            else {
                console.error('Failed to get decoration from API: ' + decorationId);
            }
        }, function (response) {
            console.error(response);
        });
    }

    function retrieveApproverUser(userId) {
        flAPI('user').get([userId]).then(function (response) {
            if (response.data && response.data.user) {
                $scope.appr.decisionedBy = response.data.user;
                return response.data.user;
            }
            else {
                console.error('Failed to get user from API: ' + userId);
            }
        }, function (response) {
            console.error(response);
        });
    }

    function saveApprovalDecision() {

        // Do not bother saving if already decided.
        if ($scope.appr.isDecided) {
            console.warn('Cannot save since it is already decisioned');
            return;
        }

        // Validate that a decision was selected.
        $scope.appr.validationError = null;
        if ($scope.appr.data.is_approved == null) {
            console.warn('Validation: Decision not selected');
            $scope.appr.validationError = 'You have not selected an approval decision';
            return;
        }
        if ($scope.appr.data.is_approved == 0) {
            var justification = $scope.appr.data.justification;
            if (!angular.isString(justification) || justification.replace(/\s+/g, '') === '') {
                console.warn('Validation: Justification was empty for a declined decision');
                $scope.appr.validationError = 'You must provide a justification for a declined decision';
                return;
            }
        }
        
        // If all validations passed, then go ahead.
        var apprId = $scope.appr.id;
        var payload = {
            approval: angular.extend({}, $scope.appr.data, {
                date: $filter('date')($scope.appr.data.date, 'yyyy-MM-dd'),
            }),
        };

        flAPI('approval').patch(apprId, payload).then(function (response) {
            $scope.appr.saved = true;
            $scope.appr.isDecided = true;
            retrieveApproval(apprId);       // reload it anyway
            setTimeout(function () {
                $scope.$apply(function () {
                    $scope.state.isSaving = false;
                    $scope.state.path.mode = 'view';
                    angular.element('#viewApprovalDecision').focus();
                });
            }, 300);
        }).catch(function (errorResponse) {
            console.error(errorResponse);
            $scope.state.isSaving = false;
            $scope.appr.saveError = true;
        });
        
    }

    //======================
    // Classes (View models)
    //======================

    function Approval() {
        this.id = null;     // must be set
        this.saved = false;
        this.saveError = false;
        this.saveDuplicateError = false;
        this.validationError = null;    // Populate with string message if required
        this.requestedDecoration = null;
        this.requestedDecorationBadgeUrl = null;
        this.requestComment = null;
        this.isDecided = false;     // If set to true, then it will prevent editing
        this.isApproved = null;         // Populate from response data
        this.justification = null;      // "
        this.decisionedBy = null;       // "
        this.decisionDate = null;       // " 
        this.submittedDate = null;      // " 
        this.data = {
            justification: '',
            is_approved: null,   // 1, 0 or null  // This value can be dirty, as opposed to the other isApproved which represents the persisted result.
            date: new Date(),       // Requested award date post-approval
        };
        this.setDateMonth = function (month) {
            // Always set to first day of the month
            this.data.date.setDate(1);
            this.data.date.setMonth(month);
        };
        this.setDateYear = function (year) {
            this.data.date.setFullYear(year);
        };
    }

    //======================
    // End Classes
    //======================

});
