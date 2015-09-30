{{-- Add multiple members --}}
@extends('master')

@section('ng-app', 'flaresApp')
@section('ng-controller', 'memberAddController')
@section('title', 'Member Onboarding')

@section('heading')
<div class="page-header">
	<h1>Onboarding members <small>Stage @{{workflow.stage}} of 6</small></h1>
</div>
@endsection


@section('memberAdd-contextInfo')
<div class="alert alert-info">
	<strong><span class="glyphicon glyphicon-info-sign"></span> Context Info:</strong> Information here will be used to generate info such as Regimental Number, initial platoon posting, etc.
</div>
<form class="form-horizontal" name="contextForm" ng-submit="workflow.next()">
	<div class="row">
	
		<div class="col-sm-8">
			<div class="form-group">
				<label class="control-label col-sm-4">Default Type</label>
				<div class="col-sm-8">
					<select class="form-control" ng-model="onboardingContext.name">
						<option ng-repeat="type in formData.onboardingTypes" value="@{{type.id}}">@{{type.name}}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-8 checkbox">
					<label><input type="checkbox" ng-model="onboardingContext.hasOverrides" ng-true-value="true" ng-false-value="false"/> Override defaults</label>
				</div>
			</div>
			
			<div ng-show="onboardingContext.hasOverrides">			
				<hr>
				<div class="form-group">
					<label class="control-label col-sm-4">Year and Intake Cycle</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" ng-model="onboardingContext.thisYear" />
					</div>
					<div class="col-sm-4">
						<select class="form-control" ng-model="onboardingContext.thisCycle">
							<option ng-repeat="intake in formData.intakes" value="@{{intake.id}}">@{{intake.name}}</option>
						</select>
					</div>
				</div>			
				<div class="form-group">
					<label class="control-label col-sm-4">Initial Rank</label>
					<div class="col-sm-8">
						<select class="form-control" ng-model="onboardingContext.newRank" aria-describedby="descInitialRank">
							<option ng-repeat="rank in formData.ranks" value="@{{rank.abbr}}">@{{rank.name}}</option>
						</select>
						<span id="descInitialRank" class="help-block">All proceeding members will be onboarded at this rank. Leave blank if ranks will be mixed.</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Initial Posting</label>
					<div class="col-sm-8">
						<select class="form-control" ng-model="onboardingContext.newPosting" aria-describedby="descInitialPosting">
							<option ng-repeat="posting in formData.postings" value="@{{posting.abbr}}">@{{posting.name}}</option>
						</select> 
						<span id="descInitialPosting" class="help-block">All proceeding members will be onboarded into this platoon. For mixed-platoon groups, onboard people separately.</span>
					</div>
				</div>
				<!-- Note: We don't give an option for forums account creation -->
			</div>
			
			
		</div>
		
		<div class="col-sm-4">
			<div class="well">
				<h4>Defaults</h4>
				<p><strong>New Recruitment: </strong> Recruit, Member, 3PL</p>
				<p><strong>Transfer: </strong> Cadet, Member, 1PL</p>
			</div>
		</div>
	</div>
	
	<hr>
	<div class="text-right">
		<button type="submit" class="btn btn-primary">Continue</button>
	</div>
	
</form>
@endsection


@section('memberAdd-memberBasic')
<div class="alert alert-info">
	<strong><span class="glyphicon glyphicon-info-sign"></span> Spreadsheet:</strong> Now we'll create a basic account for each new member. Later we'll fill in additional details such as address, health info, etc.
</div>
<form class="form" name="memberBasicForm" ng-submit="workflow.submitNewRecords()" novalidate>
	<!-- Create a spreadsheet -->
	<table class="memberadd-spreadsheet">
		<thead>
			<tr>
				<th>Last Name</th>
				<th>Given Names</th>
				<th>Sex</th>
				<th>DOB</th>
				<th>School</th>
				<th>Member's email</th>
				<th>Parent's email</th>
			</tr>
		<thead>
		<tbody>
			<tr ng-repeat="member in newMembers">
				<td><input type="text" ng-model="member.data.last_name" placeholder="required" ng-disabled="member.isSaved" spreadsheet-nav required ></td>
				<td><input type="text" ng-model="member.data.first_name" placeholder="required" ng-disabled="member.isSaved" spreadsheet-nav required></td>
				<td><select ng-model="member.data.sex" ng-disabled="member.isSaved" spreadsheet-nav required><option ng-repeat="sex in formData.sexes" value="@{{sex}}">@{{sex}}</option></select></td>
				<td><input type="date" ng-model="member.data.dob" placeholder="optional" ng-disabled="member.isSaved" spreadsheet-nav></td>
				<td><input type="text" ng-model="member.data.school" placeholder="optional" ng-disabled="member.isSaved" spreadsheet-nav></td>
				<td><input type="email" ng-model="member.data.member_email" placeholder="optional" ng-disabled="member.isSaved" spreadsheet-nav></td>
				<td><input type="email" ng-model="member.data.parent_email" placeholder="optional" ng-disabled="member.isSaved" spreadsheet-nav></td>
			</tr>
		</tbody>
	</table>
	
	<hr/>
	
	<div ng-show="memberBasicForm.$submitted && memberBasicForm.$invalid" class="alert alert-block alert-warning">
		<span class="glyphicon glyphicon-warning-sign"></span>  @{{workflow.errorMessage}}
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<span>@{{newMembers.length}} new record@{{newMembers.length === 1 ? '' : 's'}}</span> &nbsp;&nbsp;
			<button type="button" class="btn btn-default" ng-click="addNewRecord()"><span class="glyphicon glyphicon-plus-sign"></span> Add more</button>			
			<button type="button" class="btn btn-default" ng-click="removeBlankRows()"><span class="glyphicon glyphicon-erase"></span> Remove blank rows</button>			
		</div>
		<div class="col-sm-6 text-right">
			<button type="button" class="btn btn-default" ng-click="workflow.prev()">Back</button>
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
</form>
@endsection


@section('memberAdd-memberConfirm')
<div class="alert alert-info">
	<strong><span class="glyphicon glyphicon-info-sign"></span> Generated Results:</strong> Review the generated member records. Click Confirm to discard any invalid records, or Back to edit any of the invalid records.
</div>
<div class="row">
	<div class="col-sm-9">
		<table class="table">
			<thead>
				<tr>
					<th>Regimental Number</th>
					<th>Last Name</th>
					<th>Given Names</th>
					<th>Save result</th>
				</tr>
			<thead>
			<tbody>
				<tr ng-repeat="member in newMembers">
					<td>@{{member.regtNum}}</td>
					<td>@{{member.data.last_name}}</td>
					<td>@{{member.data.first_name}}</td>
					<td><span class="label" ng-class="{'label-success': member.isSaved && member.lastPersistTime, 'label-danger': !member.isSaved && member.lastPersistTime, 'label-warning': !member.lastPersistTime}">@{{member.lastPersistTime ? (member.isSaved ? 'OK' : 'NOT OK') : 'Pending'}}</span></td>
				</tr>
			</tbody>
		</table>	
	</div>
	<div class="col-sm-3">
		<div class="well">
			@{{newMembers.length}} total new member@{{newMembers.length === 1 ? '' : 's'}}
		</div>
	</div>	
</div>

<hr>
<div class="text-right">
	<button type="button" class="btn btn-default" ng-click="workflow.prev()">Back</button>
	<button type="button" class="btn btn-primary" ng-click="workflow.confirmNewRecords()" ng-disabled="!workflow.allNewMembersSaved">Confirm</button>
</div>
@endsection


@section('memberAdd-memberDetails')
<div class="alert alert-info">
	<strong><span class="glyphicon glyphicon-info-sign"></span> Member Detail entry:</strong> Add detailed information for each new member
</div>
<div class="row">
	<div class="col-sm-3">
		<h4>Select a member</h4>
		<div class="list-group">
			<a href="#" class="list-group-item" detailed-member="@{{member.regtNum}}" ng-repeat="member in newMembers | filter: { isSaved: true }"><strong>@{{member.regtNum}}</strong> @{{member.data.last_name}}, @{{member.data.first_name}}</a>
		</div>
	</div>
	<div class="col-sm-6">
		<form class="form-horizontal" ng-submit="workflow.submitDetailedRecord()" ng-show="workflow.detailedMember.regtNum">
			<h3>Personal particulars</h3>
			<div class="form-group">
				<label class="control-label col-sm-3">Regt Num</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.regtNum" readonly />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Last Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.last_name" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">First Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.first_name" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Sex</label>
				<div class="col-sm-3">
					<select class="form-control" ng-model="workflow.detailedMember.data.sex"><option ng-repeat="sex in formData.sexes" value="@{{sex}}">@{{sex}}</option></select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Date of Birth</label>
				<div class="col-sm-9">
					<input type="date" class="form-control" ng-model="workflow.detailedMember.data.dob" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">School</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.school" />
				</div>
			</div>
			
			<hr>
			<h3>Contact Details</h3>
			<div class="form-group">
				<label class="control-label col-sm-3">Street Address</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.street_addr" placeholder="e.g. 55/512 Help St"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Suburb</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.suburb" placeholder="e.g. Chatswood"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">State</label>
				<div class="col-sm-3">
					<select class="form-control" ng-model="workflow.detailedMember.data.state">
						<option ng-repeat="state in ['NSW', 'ACT', 'QLD', 'VIC', 'SA', 'TAS', 'WA', 'NT']" value="@{{state}}">@{{state}}</option>
					</select> 
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Postcode</label>
				<div class="col-sm-3">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.postcode" placeholder="e.g. 2000"/>			
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Member's mobile</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.member_mobile" placeholder="e.g. 0400 123 456"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Member's email address</label>
				<div class="col-sm-9">
					<input type="email" class="form-control" ng-model="workflow.detailedMember.data.member_email" placeholder="e.g. jimmy.russell@highschool.edu.au"/>
				</div>
			</div>
			
			<hr>
			<h3>Parent Details</h3>
			<div class="form-group">
				<label class="control-label col-sm-3">Parent's email</label>
				<div class="col-sm-6">
					<input type="email" class="form-control" ng-model="workflow.detailedMember.data.parent_email" placeholder="e.g. KenRussell@amazingoffice.com.au"/>
				</div>
				<label class="radio-inline col-sm-3"><input type="radio" ng-model="workflow.detailedMember.data.parent_preferred_comm" value="Email"> Preferred?</label>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Parent's mobile</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.parent_mobile" placeholder="e.g. 0400 234 567"/>
				</div>
				<label class="radio-inline col-sm-3"><input type="radio" ng-model="workflow.detailedMember.data.parent_preferred_comm" value="MobilePhone"> Preferred?</label>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Home phone</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.home_phone" placeholder="e.g. 02 9478 9012"/>
				</div>
				<label class="radio-inline col-sm-3"><input type="radio" ng-model="workflow.detailedMember.data.parent_preferred_comm" value="HomePhone"> Preferred?</label>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Parent type</label>
				<div class="col-sm-9">
					<select class="form-control" ng-model="workflow.detailedMember.data.parent_type">
						<option ng-repeat="parentType in ['Parent', 'Guardian', 'Grandparent', 'Sibling', 'Relative', 'School Administrator', 'Other']" value="@{{parentType}}">@{{parentType}}</option>
					</select> 
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Custodial arrangement</label>
				<div class="col-sm-9">
					<input type="email" class="form-control" ng-model="workflow.detailedMember.data.parent_custodial" placeholder="e.g. Full custody to mother"/>
					<span id="helpBlockParentCustodial" class="help-block">Only add information if there is a special child custody arrangement (such as court-enforced)</span>
				</div>
			</div>

			<hr>
			<h3>Health</h3>
			<div class="form-group">
				<label class="control-label col-sm-3">Allergies</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.med_allergies" placeholder="e.g. Peanuts, bee stings"/>
					<span id="helpBlockMedAllergies" class="help-block">Enter as comma-separated values</span>
				</div>
				<label class="checkbox-inline col-sm-3"><input type="checkbox" ng-model="workflow.detailedMember.data.is_med_lifethreat" ng-true-value="1" ng-false-value="0"> Any life threatening?</label>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Special Dietary Requirements</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.sdr" placeholder="e.g. Vegetarian, No gluten"/>
					<span id="helpBlockSdr" class="help-block">Enter as comma-separated values</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Medical conditions</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" ng-model="workflow.detailedMember.data.med_cond" placeholder="e.g. Diabetic"/>
					<span id="helpBlockMedAllergies" class="help-block">Enter as comma-separated values</span>
				</div>
				<label class="checkbox-inline col-sm-3"><input type="checkbox" ng-model="workflow.detailedMember.data.is_med_hmp" ng-true-value="1" ng-false-value="0"> Requires HMP?</label>
			</div>

			<hr>
			<div class="row">
				<div class="col-sm-offset-3 col-sm-9">
					<p>
						<button type="submit" class="btn btn-primary" ng-disabled="!workflow.detailedMember.regtNum"><span class="glyphicon glyphicon-floppy-disk"></span> Save changes</button>
					</p>
					<p ng-show="workflow.detailedMember.isUpdated" title="Last updated at @{{workflow.detailedMember.lastPersistTime}}"><span class="glyphicon glyphicon-floppy-saved"></span> Saved</p>
					<!-- <button type="button" class="btn btn-default" ng-click="workflow.nextDetailedRecord()">Next member</button> -->
					<p></p>
				</div>
			</div>
		</form>
	</div>
	<div class="col-sm-3">
		<div class="well" ng-show="workflow.detailedMember.regtNum">
			<h4>@{{workflow.detailedMember.regtNum}}</h4>
			<p>@{{workflow.detailedMember.data.last_name}}, @{{workflow.detailedMember.data.first_name}}</p>
			<p><button type="button" class="btn btn-primary" ng-click="workflow.submitDetailedRecord()" ng-disabled="!workflow.detailedMember.regtNum"><span class="glyphicon glyphicon-floppy-disk"></span> Save changes</button><p>
			<p ng-show="workflow.detailedMember.isUpdated" title="Last updated at @{{workflow.detailedMember.lastPersistTime}}"><span class="glyphicon glyphicon-floppy-saved"></span> Saved</p>
		</div>
	</div>
	
</div>
<hr/>
<div class="text-right">
	<button type="button" class="btn btn-default" ng-click="workflow.next()">Next stage</button>
</div>	

@endsection


@section('memberAdd-forums')
<h3>Forums accounts</h3>
<div ng-show="!workflow.isWithForumsAccounts">
	<p>
		Generate forums accounts? 
		<button type="button" class="btn btn-primary" ng-click="alert('Coming Soon')">Yes</button>
		<button type="button" class="btn btn-default" ng-click="workflow.next()">Skip</button>
	</p>
</div>
<div ng-show="workflow.isWithForumsAccounts">
	
</div>
@endsection


@section('memberAdd-complete')
<div class="row">
	<div class="col-sm-9">
		<h2>Complete</h2>
		<div class="alert alert-success">@{{newMembersSaved()}} new member@{{newMembersSaved() === 1 ? ' has' : 's have'}}  been stored into FLARES.</div>
		<table class="table">
			<thead>
				<tr>
					<th>Regimental Number</th>
					<th>Name</th>
				</tr>
			<thead>
			<tbody>
				<tr ng-repeat="member in newMembers">
					<td><a href="/member/#!/@{{member.regtNum}}/view" target="_blank">@{{member.regtNum}}</a></td>
					<td>@{{member.data.last_name}}, @{{member.data.first_name}}</td>
				</tr>
			</tbody>
		</table>
		
		<hr>
		<div class="text-right">
			<a href="/" class="btn btn-success">Return to dashboard</a>
		</div>
		
		
	</div>
	<div class="col-sm-3">
		<div class="well">
			TBA - more info
		</div>
	</div>
</div>
@endsection


@section('content')
<div class="hidden-xs">
	<section ng-cloak ng-show="workflow.stage === 1">@yield('memberAdd-contextInfo')</section>
	<section ng-cloak ng-show="workflow.stage === 2">@yield('memberAdd-memberBasic')</section>
	<section ng-cloak ng-show="workflow.stage === 3">@yield('memberAdd-memberConfirm')</section>
	<section ng-cloak ng-show="workflow.stage === 4">@yield('memberAdd-memberDetails')</section>
	<section ng-cloak ng-show="workflow.stage === 5">@yield('memberAdd-forums')</section>
	<section ng-cloak ng-show="workflow.stage === 6">@yield('memberAdd-complete')</section>
</div>
<div class="visible-xs-block">
	<div class="alert alert-warning">
		<span class="glyphicon glyphicon-warning-sign"></span> Please use a desktop device for the Member Onboarding process
	</div>
</div>

@endsection



@section('ng-script')
<script>

var flaresApp = angular.module('flaresApp', ['flaresBase']);
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
flaresApp.controller('memberAddController', function($scope, $http){
	//======================
	// Vars which are related to overall onboarding process
	$scope.onboardingContext = {
		hasOverrides: false,
		name: 'newRecruitment',				
		thisYear: (new Date()).getFullYear(),
		thisCycle: '1',
		newRank: 'REC',
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
	
	$scope.formData = {
		onboardingTypes: [		// newRecruitment, newTransfer, newVolunteerStaff, newAdultCadetStaff
			{id: 'newRecruitment', name: 'New Recruitment'},
			{id: 'newTransfer', name: 'New Transfer'},
			{id: 'newVolunteerStaff', name: 'Volunteer Staff member'},
			{id: 'newAdultCadetStaff', name: 'Adult Staff member'}
		],
		sexes: ['M', 'F'],
		intakes: [
			{ id: '1', name: '1st Trg Cycle' },
			{ id: '2', name: '2nd Trg Cycle' }
		],
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
					dob: '',
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
				
				$http.post('/api/member', payload).then(function(response){
					console.log(response.data);		// Debug
					
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
		angular.forEach($scope.newMembers, function(newMember, newMemberIndex){
			if (newMember.isSaved){
				$http.patch('/api/member/'+newMember.regtNum, {
					member: {
						is_active: '1'
					}
				});
			}
		});
		
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
		
		// IIFE to update the correct member reference on promise fulfill
		(function(detailedMember){
			
			$http.patch('/api/member/'+detailedMember.regtNum, payload).then(function(response){
				console.log(response.data);		// Debug
				
				if (response.data.recordId){
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
	
	$http.get('/api/refdata').then(function(response){
		if (response.data.postings){
			$scope.formData.postings = response.data.postings;
		}
		if (response.data.ranks){
			$scope.formData.ranks = response.data.ranks;
		}
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


</script>
@endsection