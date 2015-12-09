{{-- Add multiple members --}}
@extends('master')

@section('ng-app', 'flaresMemberNew')
@section('ng-controller', 'memberAddController')
@section('title', 'Member Onboarding')

@section('heading')
<h1>Onboarding members <small>Stage @{{workflow.stage}} of 6</small></h1>
@endsection


@section('memberAdd-contextInfo')
<div class="alert alert-info">
	<strong><span class="glyphicon glyphicon-info-sign"></span> Context Info:</strong> Information here will be used to generate info such as Regimental Number, initial platoon posting, etc.
</div>
<form class="form-horizontal" name="contextForm" ng-submit="workflow.next()">
	<div class="row">
	
		<div class="col-sm-6">
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
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Continue</button>
            </div>
		</div>
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
<script src="/app/components/member/flaresMemberNew.js"></script>
@endsection