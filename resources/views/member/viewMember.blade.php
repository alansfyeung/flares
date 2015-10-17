{{-- Display a single member --}}
@extends('master')

@section('ng-app', 'flaresMemberViewEdit')
@section('ng-controller', 'memberController')
@section('title', 'Member View')

@section('heading')
<!-- page main header -->
<div class="page-header container-fluid" ng-cloak ng-show="member.regt_num">

	<!-- EDIT BUTTON -->
	<aside class="title-actions pull-right" ng-show="!(member.deleted_at || workflow.isDischarge())">
		<button class="btn btn-default" ng-class="{'btn-success': workflow.isEdit()}" ng-click="edit()"><span class="glyphicon" ng-class="{'glyphicon-pencil': workflow.isView(), 'glyphicon-floppy-disk': workflow.isEdit()}"></span> @{{workflow.isEdit() ? 'Save Details' : 'Edit Details'}}</button>
		<button class="btn btn-default" ng-show="workflow.isEdit()" ng-click="cancelEdit()">Cancel</button>
	</aside>
	
	<h1>@{{member.last_name}}, @{{member.first_name}} &nbsp;<small style="display: inline-block">&diams; @{{member.regt_num}}</small></h1>
</div>
@endsection

@section('alerts')
<!-- Loading failure warnings -->
<div class="alert alert-info" ng-cloak ng-show="!workflow.isMemberRequested">
	<strong>No Member ID specified:</strong> Please go back and request the member record again
</div>
<div class="alert alert-warning" ng-cloak ng-show="member.errorNotFound">
	<strong>Member Lookup failed:</strong> The user with Regt# &diams;@{{workflow.path.id}} couldn't be found.
</div>
<div class="alert alert-danger" ng-cloak ng-show="member.errorServerSide">
	<strong>Member Lookup failed:</strong> There was a server-side error and this record could not be retrieved
</div>

<!-- Inactive and discharged warnings -->
<div class="alert alert-danger" ng-cloak ng-if="workflow.isMemberLoaded && !member.is_active">
	<h4>Incomplete Member Record</h4>
	<p>This record wasn't completely filled during the enrolment process. Perhaps it was cancelled or no longer required. </p>
	<p>
		<button type="button" class="btn btn-danger" ng-click="permanentDelete()" ng-disabled="workflow.isAsync">Delete this record, it's not needed</button>
		<button type="button" class="btn btn-default" ng-click="activate()" ng-disabled="workflow.isAsync">Activate member</button>
	</p>
</div>
<div class="alert alert-warning" ng-cloak ng-if="workflow.isMemberLoaded && member.deleted_at">
	<h4>Discharged Member</h4>
	<p>This member has been discharged so this record cannot be edited.</p>
</div>
@endsection


@section('dischargeDisplay')
<div ng-show="member.regt_num && workflow.isDischarge()">
	<div class="row">
		<form class="form-horizontal col-sm-6">
			<h3>Discharge member</h3>
			
			<div class="form-group">
				<label class="control-label col-sm-3">Discharge Date</label>
				<div class="col-sm-9">
					<input type="date" class="form-control" ng-model="dischargeContext.effectiveDate"/>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3">Discharge with different rank</label>
				<div class="col-sm-9">
					<div class="checkbox">
						<label><input type="checkbox" ng-model="dischargeContext.isCustomRank" aria-label="Discharge with different rank"> Tick to select a different terminating rank</label>
					</div>
				</div>
			</div>
			
			<div class="form-group" ng-show="dischargeContext.isCustomRank">
				<label class="control-label col-sm-3">Terminating rank</label>
				<div class="col-sm-9">
					<select class="form-control" ng-model="dischargeContext.dischargeRank">
						<option ng-repeat="rank in formData.ranks" value="@{{rank.abbr}}">@{{rank.name}}</option>
					</select>
				</div>
			</div>
			
			<div class="alert alert-info" ng-show="workflow.isAsync">
				<span class="glyphicon glyphicon-info-sign"></span> Working on your request.
			</div>
			
			<div class="form-group">
				<div class="col-sm-9 col-sm-push-3">
					<button type="button" class="btn btn-warning" ng-click="discharge()" ng-disabled="workflow.isAsync">Continue with Discharge</button>
					<button type="button" class="btn btn-default" ng-click="cancelDischarge()" ng-disabled="workflow.isAsync">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection

@section('memberDisplay')
<div ng-show="member.regt_num && !workflow.isDischarge()">

	<!-- Member quick statuses row -->
	<section class="member-quickstatus">
		<div class="row hidden-xs">
			<div class="col-sm-2"> 
				<dl>
					<dt>Member Status</dt>
					<dd><span member-status></span></dd>
					<dd><span hmp-status></span></dd>
					<dd><span allergy-status></span></dd>
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
			<div class="col-sm-6"> 
				<p>(Progress bar)</p>
			</div>
		</div>
	</section>
	
	<!-- Member info tabs & panel -->
	<div class="row">
		<div class="col-sm-3 col-sm-push-9">
		
			<!-- Display Picture -->
			<section ng-controller="pictureController" flow-init flow-files-submitted="$flow.upload()" flow-file-success="$file.msg = $message">
				
				<h4>Profile picture</h4>
				<div class="thumbnail" flow-drag-enter="uploader.dropzone = true" flow-drag-leave="uploader.dropzone = false" flow-drop flow-drop-enabled="uploader.ready()" ng-class="{'uploader-drop-zone': uploader.dropzone, 'uploader-not-ready': !uploader.ready()}"><!-- Member image and quick links -->
				
					<img ng-src="@{{memberImage.url}}" alt="@{{member.last_name}}" class="image-rounded memberview-image" ng-show="!uploader.uploading">
					
					<div class="text-center" ng-repeat="file in $flow.files" ng-show="uploader.uploading">
						<h3 ng-show="file.isUploading()">Uploading</h3>
						<h3 class="text-success" ng-show="file.isComplete()"><span class="glyphicon glyphicon-ok-sign"></span> Successful</h3>
						<div class="thumbnail">
							<img flow-img="file" />
							<div class="caption">@{{file.name}} (@{{Math.floor(file.size/1024)}} KB)</div>
						</div>
						<div class="progress progress-striped" ng-class="{active: file.isUploading()}">
							<div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" ng-style="{width: (file.progress() * 100) + '%'}" ng-class="{'progress-bar-success': file.isComplete()}">
							<span class="sr-only">1% Complete</span>
							</div>
						</div>
					</div>
					
					<div class="caption">
						<div class="text-center" ng-show="uploader.ready() && !uploader.uploading" flow-upload-started="uploadStart()" flow-complete="uploadFinish()">
							<em>Drag/Drop or </em>
							<div class="btn-group">
								<span class="btn btn-default" flow-btn>Upload File</span> 
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="@{{memberImage.url}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Download</a></li>
									<li><a ng-click="deleteLast()"><span class="glyphicon glyphicon-step-backward"></span> Rewind (@{{memberImage.count}})</a></li>
									<li><a ng-click="deleteAll()"><span class="text-danger"><span class="glyphicon glyphicon-ban-circle"></span> Delete all</span></a></li>
								</ul>
							</div>
						</div>
		
					</div>
				</div>
			</section>
			
			<section>
				<h4>Actions</h4>
				<!-- For fully active members -->
				<div class="list-group" ng-show="member.is_active && !member.deleted_at">
					<a href="#" class="list-group-item">Record Leave</a>
					<a href="#" class="list-group-item">Assign award</a>
					<a href="#" class="list-group-item">Promote</a>
					<a href="#" class="list-group-item">Change posting</a>
					<button type="button" class="list-group-item list-group-item-warning" ng-click="confirmDischarge()">Discharge</button>
				</div>
				<!-- For inactive members -->
				<div class="list-group" ng-show="!member.is_active">
					<button type="button" class="list-group-item" ng-click="">Activate</button>
					<button type="button" class="list-group-item list-group-item-danger" ng-click="">Remove permanently</button>
				</div>
				<!-- For discharged members -->
				<div class="list-group" ng-show="member.deleted_at">
					<button type="button" class="list-group-item" ng-click="">Reactivate --- (WIP)</button>
				</div>
			</section>
			
			<h4>Record audit info</h4>
			<dl ng-show="member.deleted_at">
				<dt>Date marked discharged</dt>
				<dd>@{{member.deleted_at | date:'medium'}}</dd>
			</dl>
			<dl>
				<dt>Date created</dt>
				<dd>@{{member.created_at | date:'medium'}}</dd>
				<dt>Last updated</dt>
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
								<h3>Activity Details</h3>
								<table class="table record-view">
									<tr>
										<td>Name</td>
										<td display-mode="view">@{{activity.last_name | markBlanks}}</td>
										<td display-mode="edit"><input type="text" ng-model="member.last_name"></td>
									</tr>
									<tr>
										<td>Given Name</td>
										<td display-mode="view">@{{member.first_name | markBlanks}}</td>
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
										<td display-mode="edit"><input type="date" ng-model="member.dob" placeholder="yyyy-MM-dd"></td>
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
								<p><em>Member details can be edited through the Actions menu</em></p>
								<table class="table member-record-view">
									<tr>
										<td>Regimental Number</td>
										<td>@{{member.regt_num | markBlanks}}</td>
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
								<p><em>Use Mass Actions to update Forums and COMS usernames</em></p>
							</div>
							<div class="col-sm-6">
								<h3>Unit Qualifications</h3>
								<p><em>To update qualifications, go to Assign Awards.</em></p>
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
							<span class="label label-default" ng-class="{'label-warning': !!+member.is_med_hmp }">Requires HMP: @{{member.is_med_hmp | yesNo}}</span>
							<span class="label label-default" ng-class="{'label-danger': !!+member.is_med_lifethreat}">Life threatening: @{{member.is_med_lifethreat | yesNo}}</span>
						</p>
						<table class="table member-record-view">
							<tr display-mode="edit">
								<td>Requires HMP</td>
								<td>
									<label class="radio-inline"><input type="radio" ng-model="member.is_med_hmp" value="1"> Yes</label>
									<label class="radio-inline"><input type="radio" ng-model="member.is_med_hmp" value="0"> No</label>
								</td>
							</tr>
							<tr display-mode="edit">
								<td>Allergies life threatening?</td>
								<td>
									<label class="radio-inline"><input type="radio" ng-model="member.is_med_lifethreat" value="1"> Yes</label>
									<label class="radio-inline"><input type="radio" ng-model="member.is_med_lifethreat" value="0"> No</label>
								</td>
							</tr>
							<tr>
								<td>Allergies</td>
								<td display-mode="view">@{{member.med_allergies | markBlanks}}</td>
								<td display-mode="edit"><input type="text" ng-model="member.med_allergies"></td>
							</tr>
							<tr>
								<td>Medical Conditions</td>
								<td display-mode="view">@{{member.med_cond | markBlanks}}</td>
								<td display-mode="edit"><input type="text" ng-model="member.med_cond"></td>
							</tr>
							<tr>
								<td>Special Dietary Requirements (SDR)</td>
								<td display-mode="view">@{{member.sdr | markBlanks}}</td>
								<td display-mode="edit"><input type="text" ng-model="member.sdr"></td>
							</tr>
						</table>
					</section>
				</div>
				
				<div role="tabpanel" class="tab-pane" id="iddocs">
					<section>
						<div display-mode="edit" class="pull-right">
							<label class="checkbox-inline"><input type="checkbox" ng-model="member.is_fully_enrolled" ng-true-value="1" ng-false-value="0"> All enrolment documents uploaded?</label>
						</div>
						<h3>Documents</h3>
						<p>Work in progress</p>
					</section>
					
					<section>
						<h3>ID Card</h3>
						<table class="table member-record-view">
							<tr>
								<td>Has been printed?</td>
								<td display-mode="view">@{{member.is_idcard_printed | yesNo}}</td>
								<td display-mode="edit">
									<label class="radio-inline"><input type="radio" ng-model="member.is_idcard_printed" value="1"> Yes</label>
									<label class="radio-inline"><input type="radio" ng-model="member.is_idcard_printed" value="0"> No</label>
								</td>
							</tr>
							<tr>
								<td>Returned to Bn</td>
								<td display-mode="view">@{{member.idcard_at_bn | yesNo}}</td>
								<td display-mode="edit">
									<label class="radio-inline"><input type="radio" ng-model="member.idcard_at_bn" value="1"> Yes</label>
									<label class="radio-inline"><input type="radio" ng-model="member.idcard_at_bn" value="0"> No</label>
								</td>
							</tr>
							<tr>
								<td>Expiry Date</td>
								<td display-mode="view">@{{member.idcard_expiry | date}}</td>
								<td display-mode="edit"><input type="date" ng-model="member.idcard_expiry"></td>
							</tr>
							<tr>
								<td>Serial Number</td>
								<td display-mode="view">@{{member.idcard_serial_num | markBlanks}}</td>
								<td display-mode="edit"><input type="text" ng-model="member.idcard_serial_num"></td>
							</tr>
							<tr>
								<td>Remarks</td>
								<td display-mode="view">@{{member.idcard_remarks | markBlanks}}</td>
								<td display-mode="edit"><textarea ng-model="member.idcard_remarks" rows="4"></textarea></td>
							</tr>
						</table>
					</section>
				</div>
				<div role="tabpanel" class="tab-pane" id="postings">
					<section>
						<h3>Promotions and Postings</h3>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Status</th>
									<th>Rank</th>
									<th>Effective Date</th>
									<th>Platoon</th>
									<th>Posting</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="postingPromo in member.postings" ng-class="{'warning': postingPromo.is_discharge}">
									<td>
										<span class="glyphicon glyphicon-time" title="Recorded by @{{postingPromo.recorded_by}}, at @{{postingPromo.created_at}}"></span>
										<span class="glyphicon glyphicon-ban-circle" title="Discharged" ng-show="!!+postingPromo.is_discharge"></span>
									</td>
									<td><span class="glyphicon glyphicon-hourglass" ng-show="!!+postingPromo.is_acting" title="Acting Rank"></span> @{{postingPromo.new_rank | markBlanks}}</td>
									<td>@{{postingPromo.effective_date | date}}</td>
									<td>@{{postingPromo.new_platoon | markBlanks}}</td>
									<td>@{{postingPromo.new_posting | markBlanks}}</td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</section>
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
	@yield('dischargeDisplay')
	@yield('memberDisplay')
@endsection


@section('ng-script')

<script src="/app/components/member/flaresMemberViewEdit.js"></script>
<script src="/assets/js/flow/ng-flow-standalone.min.js"></script>


@endsection