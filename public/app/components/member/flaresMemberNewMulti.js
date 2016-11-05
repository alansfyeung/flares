// ==================================
//   flaresMemberNew.js
//   Add new members
// ==================================

var flaresApp = angular.module('flaresMemberNew', ['flaresBase']);

flaresApp.controller('newMultiController', function($scope, flAPI){
	//======================
	// Vars which are related to overall onboarding process
	$scope.onboardingContext = {
		hasOverrides: false,
		name: 'newRecruitment',				
		thisYear: (new Date()).getFullYear(),
		thisCycle: '1',
		newRank: 'CDTREC',
		newPosting: 'MBR',
		newPlatoon: '3PL',
	};
	
	// Tracks the flow of screens
	$scope.workflow = {
		stage: 1,
		allNewMembersSaved: false,
		detailedMember: null,			// When at the detailed stage
		isWithForumsAccounts: null
	};
	$scope.workflow.prev = function(){
		$scope.workflow.stage--;
		$scope.memberBasicForm.$setSubmitted(false);
	};
	$scope.workflow.next = function(){
		$scope.workflow.stage++;
		
		if ($scope.workflow.stage > 3 && angular.equals({}, $scope.workflow.detailedMember)){
			$scope.workflow.setDetailedMember(0, function(memberObject){
				return memberObject.isSaved;
			});
		}
	};
	
	$scope.workflow.setDetailedMember = function(regtNum, fnComparator){
		var fnComparator = fnComparator || function(memberObject){		
			// The default comparator function checks for regimental number matches
			return memberObject.regtNum === regtNum;
		};
		
		for (var i = 0; i < $scope.newMembers.length; i++){
			if (fnComparator($scope.newMembers[i])){
				$scope.workflow.detailedMember = $scope.newMembers[i];
				return;
			}
		}
	};
	
    // This data should be extracted from reference service
	$scope.formData = {
		onboardingTypes: [],
		sexes: [],
		intakes: [],
		postings: [],
		ranks: []
	}
	
	// All new members are input here
	$scope.newMembers = [];
	
	// =====================================
	// $scope.newMembers = [
		// {
			// isSaved: true,
			// regtNum: '20611223F',
			// data: {
				// last_name: 'Nguyen',
				// first_name: 'Jocelyn',
				// dob: new Date('2000-10-15'),
				// sex: 'F',
				// school: 'Kingsford High School',
				// member_email: 'jnguyen@student.highschool.edu',
				// parent_email: 'Karennguyen@pharma.com'
			// }
		// },
		// {
			// isSaved: true,
			// regtNum: '20610011',
			// data: {
				// last_name: 'Smith',
				// first_name: 'Sam',
				// dob: new Date('2002-11-15'),
				// sex: 'M',
				// school: 'Dolan High School',
				// member_email: 'smithsam@student.dolanhighschool.edu',
				// parent_email: 'jacksmith@kkpharma.com'
			// }
		// },
		// {
			// isSaved: true,
			// regtNum: '20656011',
			// data: {
				// last_name: 'Kebab',
				// first_name: 'Kris',
				// dob: new Date('1999-01-03'),
				// sex: 'M',
				// school: 'Lakemba High School',
				// member_email: 'kk@student.lakemba-highschool.edu',
				// parent_email: 'shady@slim.com'
			// }
		// }
		
	// ];
	
	// =====================================
	
	
	angular.extend($scope, {
		addNewRecord: function(){
			var blankRecord = {
				isSaved: false,
				isUpdated: false,
				lastPersistTime: null,
				data: {
					last_name: '',
					first_name: '',
					dob: new Date('2000-01-01'),
					sex: '',
					school: '',
					member_email: '',
					parent_email: ''
				}
			};
			$scope.newMembers.push(blankRecord);
		},
		removeBlankRows: function(){
			var removeNextBlankRow = function(){
				var i = 1;
				while (i < $scope.newMembers.length){
					var nm = $scope.newMembers[i];
					if (!(nm.data.last_name || nm.data.first_name)){
						$scope.newMembers.splice(i, 1);
						removeNextBlankRow();
						return;
					}
					i++;
				}
			};
			
			removeNextBlankRow();
		},
		newMembersSaved: function(){
			var count = 0;
			angular.forEach($scope.newMembers, function(newMember){
				if (newMember.isSaved){
					count++;
				}
			});
			return count;
		}
	});
	
	
	//======================
	// Workflow Screen navigation
	$scope.workflow.submitNewRecords = function(){
		// Validation
		if($scope.memberBasicForm.$invalid){
			$scope.workflow.errorMessage = 'Resolve validation errors (Are required fields are filled and emails are correctly formatted?)';
			return false;
		}
		
		// Submission
		var numResolved = 0;
		var checkAllResolved = function(){
			return numResolved === $scope.newMembers.length;
		};
		
		angular.forEach($scope.newMembers, function(newMember, newMemberIndex){
			if (newMember.isSaved){		// Don't double save
				numResolved++;	
			}
			else {
				var payload = {
					context: $scope.onboardingContext,
					member: newMember.data
				};
				
				flAPI('member').post(payload).then(function(response){
					if (response.data.error){
						console.warn(response.data.error);
						return;
					}
					
					newMember.lastPersistTime = (new Date()).toTimeString();
					if (response.data.recordId){
						newMember.regtNum = response.data.recordId;	
						newMember.isSaved = true;
					}
					
					numResolved++;
					if (checkAllResolved()){
						$scope.workflow.allNewMembersSaved = true;
					}
					
				}, function(response){
					console.warn('Error: member add', response);
				});
			}
		});
		
		if (checkAllResolved()){
			$scope.workflow.allNewMembersSaved = true;
		}
		
		$scope.workflow.next();		// Asynchronous
	};
	
	$scope.workflow.confirmNewRecords = function(){
		// sets the is_active flag on all saved records
		// angular.forEach($scope.newMembers, function(newMember, newMemberIndex){
			// if (newMember.isSaved){
				// flAPI('member').patch([newMember.regtNum], {
					// member: { is_active: '1' }
				// });
			// }
		// });
        
        var unsaved = [];
        angular.forEach($scope.newMembers, function(member){
            if (!member.isSaved){
                unsaved.push(member);
            }
        });
        if (unsaved.length > 0){
            var names = unsaved.reduce(function(prev, curr){
                return prev + ', ' + [curr.data.first_name, curr.data.last_name].join(' ');
            });
            if (!confirm('The following invalid/incomplete member records will be discarded if you continue: ' + names)){
                return;
            }
        }
		
		$scope.workflow.next();
	};
	
	$scope.workflow.submitDetailedRecord = function(){
		var sw = $scope.workflow;
		if (!sw.detailedMember.regtNum){
			console.warn('No detailedMember is selected');
			return false;
		}
        
		var payload = {
			context: $scope.onboardingContext,
			member: sw.detailedMember.data
		};
		
		// Need IIFE to update the correct member reference on promise fulfill
		(function(detailedMember){
			flAPI('member').patch([detailedMember.regtNum], payload).then(function(response){				
				if (response.data.recordId){
                    
                    // Detailed save succeeded, so let's activate them
                    flAPI('member').patch([detailedMember.regtNum], {
                        member: { is_active: '1' }
                    });
                    
					detailedMember.lastPersistTime = (new Date()).toTimeString();
					detailedMember.isUpdated = true;	
					console.log('Updated:', detailedMember);
				}
			}, function(response){
				console.warn('Error: member add', response);
			});
			
		}(sw.detailedMember));
		
	};
	
	$scope.workflow.nextDetailedRecord = function(){
		// save the current one
		this.submitDetailedRecord();
		
		// Advance to the next person
		// TODO: Logic to select the next person on that list.
		
	};
	
	
	//==================
	// Fetch reference data for platoons and ranks
	
	flAPI('refData').getAll().then(function(response){
        // Auto-extract
        var extract = ['postings', 'ranks', 'sexes', 'onboardingTypes', 'intakes'];
        angular.forEach(extract, function(key){
            if (response.data.hasOwnProperty(key)){
                $scope.formData[key] = response.data[key];
            }
        });
	});
	
	//===================
	// Add a few records to start with
	var numDefaultRecordsToShow = 1;
	for (var i=0; i<numDefaultRecordsToShow; i++){
		$scope.addNewRecord();
	}

	//======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.workflow.stage > 1){
			if ($scope.workflow.stage < 4){
				var message = 'You will lose any unsaved member details.';
				return message;
			}
			if ($scope.workflow.stage < 6){
				var message = 'Although members are saved, the onboarding process is not yet complete.';
				return message;
			}
		}
	};
	
	$scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
	

	
});


// ==============================
// For the detailed editing screen,
// activate the member based on the clicked link

flaresApp.directive('detailedMember', function($parse){
	return {
		link: function (scope, element, attr) {
			scope.$watch('workflow.detailedMember.regtNum', function(value){
				// Toggle the activeness on the listgroup element
				if (attr.detailedMember === value){
					$(element).addClass('active');
				}
				else {
					$(element).removeClass('active');
				}
			});
			element.click(function(e) {
				e.preventDefault();
				if (attr.detailedMember){
					scope.$apply(function(){
						scope.workflow.setDetailedMember(attr.detailedMember);
						// $parse(attr.detailedMember).call();
					});
				}
			});
		}
	};	
});