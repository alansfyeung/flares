{{-- Display a single member --}}
@extends('master')

@section('ng-app', 'flaresApp')
@section('ng-controller', 'memberController')
@section('title', 'Member View')

@section('heading')
<div class="alert alert-warning" ng-cloak ng-show="member.errorNotFound">
	<strong>Member Lookup failed:</strong> The user with Regt# @{{member.regtNum}} couldn't be found.
</div>
<div class="alert alert-danger" ng-cloak ng-show="member.errorServerSide">
	<strong>Member Lookup failed:</strong> There was a server-side error and this record could not be retrieved
</div>

<div class="page-header" ng-if="member">

	<!-- EDIT BUTTON -->
	<div style="float: right">
		<button class="btn btn-default" ng-class="{'btn-success': workflow.isEdit()}" ng-click="edit()"><span class="glyphicon" ng-class="{'glyphicon-pencil': workflow.isView(), 'glyphicon-floppy-disk': workflow.isEdit()}"></span> @{{workflow.isEdit() ? 'Save Details' : 'Edit Details'}}</button>
		<button class="btn btn-default" ng-show="workflow.isEdit()" ng-click="cancelEdit()">Cancel</button>
	</div>
	
	<h1>@{{member.last_name}}, @{{member.first_name}} <small>&ndash; @{{member.regt_num}}</small></h1>
</div>
@endsection


@section('memberDisplay')
<div class="hidden-xs">
	<div class="row">
		<div class="col-sm-2"> 
			<dl>
				<dt>Member Status</dt>
				<dd><span class="label" ng-class="{'label-success': member.is_active, 'label-warning': !member.is_active}">@{{member.is_active ? 'Active member' : 'Inactive member'}}</span></dd>
			</dl>
		</div>
		<div class="col-sm-2"> 
			<dl>
				<dt>Enrolled Date</dt>
				<dd>(TBA)</dd>
			</dl>
		</div>
		<div class="col-sm-2"> 
			<dl>
				<dt>All documents loaded</dt>
				<dd>@{{member.is_fully_enrolled | yesNo}}</dd>
			</dl>
		</div>
		<div class="col-sm-3"> 
			<p>(Progress bar)</p>
		</div>
	</div>
	<hr>
</div>

<div ng-show="member">
	<div class="row">
		<div class="col-sm-3 col-sm-push-9">
			<h4>Profile picture</h4>
			<div class="thumbnail"><!-- Member image and quick links -->
				<img ng-src="@{{member.photo_url}}" alt="@{{member.last_name}}" class="img-thumbnail memberview-image">
				<div class="thumbnail">
					<ul class="list-inline">
						<li><a href="#">Edit image</a> | <a href="#">Remove image</a></li>
					</ul>				
				</div>
			</div>
			<h4>Actions</h4>
			<div class="list-group">
				<a href="#" class="list-group-item">Record Leave</a>
				<a href="#" class="list-group-item">Assign award</a>
				<a href="#" class="list-group-item">Promote</a>
				<a href="#" class="list-group-item">Change posting</a>
			</div>
			<h4>Record info</h4>
			<dl>
				<dt>Date created</dt>
				<dd>@{{member.created_at | date:'medium'}}</dd>
				<dt>Last Updated</dt>
				<dd>@{{member.updated_at | date:'medium'}}</dd>
			<dl>
		</div>
	
		<div class="col-sm-9 col-sm-pull-3">
		
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a bs-show-tab href="#details" aria-controls="details" role="tab">Details</a></li>
				<li role="presentation"><a bs-show-tab href="#healthmed" aria-controls="healthmed" role="tab">Health &amp; Med</a></li>
				<li role="presentation"><a bs-show-tab href="#iddocs" aria-controls="iddocs" role="tab">Docs &amp; ID</a></li>
				<li role="presentation"><a bs-show-tab href="#postings" aria-controls="postings" role="tab">Postings</a></li>
				<li role="presentation"><a bs-show-tab href="#attendance" aria-controls="attendance" role="tab">Attendance</a></li>
				<li role="presentation"><a bs-show-tab href="#payments" aria-controls="payments" role="tab">Payments</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="details">
					<section>
					
						<div class="row">
							<div class="col-sm-6">
								<h3>Personal Details</h3>
								<table class="table member-record-view">
									<tr>
										<td>Family Name</td>
										<td display-mode="view">@{{member.last_name | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.last_name"></td>
									</tr>
									<tr>
										<td>Given Name</td>
										<td display-mode="view">@{{member.last_name | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.first_name"></td>
									</tr>
									<tr>
										<td>Sex</td>
										<td display-mode="view">@{{member.sex | markBlanks}}</td>
										<td display-mode="edit">
											<select ng-model="member.sex">
												<option ng-repeat="sex in formData.sexes" value="@{{sex}}">@{{sex}}</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>DOB</td>
										<td display-mode="view">@{{member.dob | date}}</td>
										<td display-mode="edit"><input type="date" ng-model="member.dob"></td>
									</tr>
									<tr>
										<td>School</td>
										<td display-mode="view">@{{member.school | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.school"></td>
									</tr>
									<tr>
										<td>Street Address</td>
										<td display-mode="view">
											@{{member.street_addr | markBlanks}}<br>
											@{{member.suburb}}<br>
											@{{member.state}} @{{member.postcode}} 
										</td>
										<td display-mode="edit">
											<input type="text" ng-model="member.street_addr" placeholder="e.g. 270 Miller St">
											<input type="text" ng-model="member.suburb" placeholder="e.g. North Sydney">										
											<select class="form-control" ng-model="member.state">
												<option ng-repeat="state in ['NSW', 'ACT', 'QLD', 'VIC', 'SA', 'TAS', 'WA', 'NT']" value="@{{state}}">@{{state}}</option>
											</select> 
											<input type="text" ng-model="member.postcode" placeholder="e.g. 2065">
										</td>
									</tr>
								</table>						
							</div>
							<div class="col-sm-6">
								<h3>Contact Details</h3>
								<table class="table member-record-view">
									<tr>
										<td>Mobile (member)</td>
										<td display-mode="view">@{{member.member_mobile | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.member_mobile"></td>
									</tr>
									<tr>
										<td>Email (member)</td>
										<td display-mode="view">@{{member.member_email | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.member_email"></td>
									</tr>
								</table>
								
								<h3>Parental Details</h3>
								<table class="table member-record-view">
									<tr>
										<td>Preferred contact method</td>
										<td display-mode="view">@{{member.parent_preferred_comm | markBlanks}}</td>
										<td display-mode="edit">
											<select class="form-control" ng-model="member.parent_preferred_comm">
												<option ng-repeat="comm in ['Email', 'MobilePhone', 'HomePhone']" value="@{{comm}}">@{{comm}}</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Parent mobile</td>
										<td display-mode="view">@{{member.parent_mobile | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.parent_mobile"></td>
									</tr>
									<tr>
										<td>Parent email</td>
										<td display-mode="view">@{{member.parent_email | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.parent_email"></td>
									</tr>
									<tr>
										<td>Home phone</td>
										<td display-mode="view">@{{member.home_phone | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.home_phone"></td>
									</tr>
									<tr>
										<td>Special custody arrangement</td>
										<td display-mode="view">@{{member.parent_custodial | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.parent_custodial"></td>
									</tr>
								</table>
							</div>
						</div>
					</section>
				
					<section>
						<hr>
						<div class="row">
							<div class="col-sm-6">
								<h3>Member Details</h3>
								<table class="table member-record-view">
									<tr>
										<td>Regimental Number</td>
										<td>@{{member.regt_num | markBlanks}}</td>
									</tr>
									<tr>
										<td>Role Classification</td>
										<td>@{{member.role_class | markBlanks}}</td>
									</tr>
									<tr>
										<td>Years of Service</td>
										<td>@{{member.role_class | markBlanks}}</td>
									</tr>
									<tr>
										<td>Current Rank</td>
										<td>-- TBA</td>
									</tr>
									<tr>
										<td>Current Posting</td>
										<td>-- TBA</td>
									</tr>
									<tr>
										<td>Forums Username</td>
										<td>@{{member.forums_username | markBlanks}}</td>
									</tr>
									<tr>
										<td>COMS Username</td>
										<td>@{{member.coms_username | markBlanks}} @{{member.coms_id}}</td>
									</tr>
								</table>	
							</div>
							<div class="col-sm-6">
								<h3>Unit Qualifications</h3>
								<table class="table member-record-view">
									<tr>
										<td>Maroon Beret Award</td>
										<td>@{{member.is_qual_mb | yesNo}}</td>
									</tr>
									<tr>
										<td>Silver 303 Award</td>
										<td>@{{member.is_qual_s303 | yesNo}}</td>
									</tr>
									<tr>
										<td>Gold Falcon Award</td>
										<td>@{{member.is_qual_gf | yesNo}}</td>
									</tr>
								</table>
							</div>
						</div>
					</section>
					
				</div>
				
				<div role="tabpanel" class="tab-pane" id="healthmed">
					<section>
						<h3>Health and Medical</h3>
						<p>
							<span class="label label-default" ng-class="{'label-warning': member.is_med_hmp}">Requires HMP: @{{member.is_med_hmp | yesNo}}</span>
							<span class="label label-default" ng-class="{'label-danger': member.is_med_lifethreat}">Life threatening: @{{member.is_med_lifethreat | yesNo}}</span>
						</p>
						<table class="table member-record-view">
							<tr>
								<td>Allergies</td>
								<td>@{{member.med_allergies | markBlanks}}</td>
							</tr>
							<tr>
								<td>Medical Conditions</td>
								<td>@{{member.med_cond | markBlanks}}</td>
							</tr>
							<tr>
								<td>Special Dietary Requirements (SDR)</td>
								<td>@{{member.sdr | markBlanks}}</td>
							</tr>
							<tr>
								<td>Medical Conditions</td>
								<td>@{{member.med_cond | markBlanks}}</td>
							</tr>
						</table>
					</section>
				</div>
				
				<div role="tabpanel" class="tab-pane" id="iddocs">
					<section>
						<h3>Documents</h3>
						<p>Work in progress</p>
					</section>
					
					<section>
						<h3>ID Card</h3>
						<table class="table member-record-view">
							<tr>
								<td>Has been printed?</td>
								<td>@{{member.is_idcard_printed | yesNo}}</td>
							</tr>
							<tr>
								<td>Expiry Date</td>
								<td>@{{member.idcard_expiry | markBlanks}}</td>
							</tr>
							<tr>
								<td>Returned to Bn</td>
								<td>@{{member.idcard_at_bn | yesNo}}</td>
							</tr>
							<tr>
								<td>Serial Number</td>
								<td>@{{member.idcard_serial_num | markBlanks}}</td>
							</tr>
							<tr>
								<td>Remarks</td>
								<td>@{{member.idcard_remarks | markBlanks}}</td>
							</tr>
						</table>
					</section>
				</div>
				<div role="tabpanel" class="tab-pane" id="postings">
					<h3>Postings</h3>
					<p>Work in progress</p>
				</div>
				<div role="tabpanel" class="tab-pane" id="attendance">
					<h3>Attendance</h3>
					<p>Work in progress</p>
				</div>
				<div role="tabpanel" class="tab-pane" id="payments">
					<h3>History</h3>
					<p>Work in progress</p>
				</div>
			</div>		
			
		</div>
		
	</div>
	
</div>
@endsection

@section('content')
	@yield('memberDisplay')
@endsection


@section('ng-script')
<script>

var flaresApp = angular.module('flaresApp', ['flaresBase']);
flaresApp.controller('memberController', function($scope, $http, $location){
	
	$scope.member = {};
	$scope.originalMember = {};
	$scope.formData = {
		sexes: ['M','F'],
	}
	$scope.workflow = {
		path: {
			id: 0,
			mode: 'view',		// by default
			tab: 'details'			
		},
		isMemberLoaded: false;
	};
	$scope.workflow.isView = function(){
		return this.path.mode === 'view';
	};
	$scope.workflow.isEdit = function(){
		return this.path.mode === 'edit';
	};
	$scope.workflow.toggleMode = function(){
		this.path.mode = this.isView() ? 'edit' : 'view';
	};
	
	var updatePath = function(){
		var swp = $scope.workflow.path;
		var newPath = $location.path([swp.id, swp.mode, swp.tab].join('/'));
	};
	var processMemberRecord = function(member){
		if (!member.photo_url){
			member.photo_url = '/img/anon.png';
		}
		if (member.dob){
			var dob = Date.parse(member.dob);
			if (!isNaN(dob)){
				member.dob = new Date(dob);
				// member.dob = member.dob;
			}
			else {
				member.dob = null;
			}
		}
		$scope.member = member;
		$scope.originalMember = angular.extend({}, member);
	};
	var updateMemberRecord = function(){
		console.log('saving');
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
			return;
		}
		console.warn('Cannot cancel - member record was never loaded');
	};
	
	$scope.$watchCollection('workflow.path', function(){
		// Change the URL path if workflow details are updated (e.g. tab click)
		updatePath();
	});
	
	$scope.$watchCollection('member', function(newCollection){
		// If member record has changed
		console.log('newcollection is', newCollection);
		if ($scope.workflow.isMemberLoaded){
			// Store changed props
			// $scope.memberChangedProps.push();
		}
	});
	
	
	// Read the url
	// get rid of any leading slash
	var path = $location.path();
	var pathFrags = (path.indexOf('/') === 0 ? path.substring(1) : path).split('/');		
	$scope.workflow.path.id = pathFrags[0];
	$scope.workflow.path.mode = pathFrags[1] ? pathFrags[1] : 'view';
	$scope.workflow.path.tab = pathFrags[2] ? pathFrags[2] : 'details';
	
	// Retrieve this member
	if ($scope.workflow.path.id){
		$http.get('/api/member/'+$scope.workflow.path.id).then(function(response){
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
	
	
	
});

// flaresApp.controller('memberEditController', function($scope, $routeParams, $location){	
	// console.log('Edit', $routeParams);
// });

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
				console.log('memberView directiving watchChange', newValue);
				if (newValue !== attr.displayMode){
					element.hide();
					return;
				}
				element.show();
			});
		}
	};
});

// ==================
// Custom Filters for Member View/Edit
flaresApp.filter('yesNo', function(){
	return function(input){
		return input ? 'Yes' : 'No';
	}
}).filter('markBlanks', function(){
	return function(input){
		return input ? input : '--';
	}
}).filter('date', function(){
	return function(input){
		return input ? input : '--';
	}
});


</script>
@endsection