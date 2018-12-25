{{-- Display a single member --}}
@extends('layouts.base')

@section('ng-app', 'flaresMemberViewEdit')
@section('ng-controller', 'memberViewEditController')
@section('title', 'Member View')

@push('scripts')
<script src="/ng-app/components/member/flaresMemberViewEdit.js"></script>
@endpush
@push('vendor-scripts')
<script src="/assets/js/flow/ng-flow-standalone.min.js"></script>
@endpush

@section('heading')
@verbatim
<!-- page main header -->
<div ng-cloak ng-if="state.isMemberLoaded">
	<aside class="titlebar-actions pull-right" ng-show="!(member.data.deleted_at || state.isDischarge())">
        <!-- EDIT BUTTON -->
		<button class="btn btn-link" ng-class="{'btn-success': state.isEdit()}" ng-click="edit()">
            <span class="glyphicon" ng-class="{'glyphicon-pencil': state.isView(), 'glyphicon-floppy-disk': state.isEdit()}"></span> 
            {{state.isEdit() ? 'Save' : 'Edit'}}
            </button>
		<button class="btn btn-link" ng-show="state.isEdit()" ng-click="cancelEdit()">Cancel</button>
        <!-- DotDotDot menu toggle -->
        <span uib-dropdown>
            <a class="btn btn-link" uib-dropdown-toggle>
                Menu <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li role="menuitem"><a ng-href="{{nav.assignAward}}">Assign award</a></li>
                <li role="menuitem"><a href="#">Record Leave</a></li>
                <li role="menuitem"><a href="#">Promote</a></li>
                <li role="menuitem"><a href="#">Change posting</a></li>
                <li role="menuiten"><a ng-click="confirmDischarge()">Discharge</a></li>
            </ul>
            
        </span>
	</aside>
	<h1>
        <a href="./members">Members</a>
        &rsaquo;
        Member Service Record
    </h1>
</div>
@endverbatim
@endsection

@section('alerts')
<div ng-cloak ng-if="state.isMemberLoaded">
    <!-- Loading failure warnings -->
    <div class="alert alert-info" ng-show="!state.isRequested">
        <strong>No Member ID specified:</strong> Please go back and request the member record again
    </div>
    <div class="alert alert-warning" ng-show="member.data.errorNotFound">
        <strong>Member Lookup failed:</strong> The user with Regt# &diams;@{{state.path.id}} couldn't be found.
    </div>
    <div class="alert alert-danger" ng-show="member.data.errorServerSide">
        <strong>Member Lookup failed:</strong> There was a server-side error and this record could not be retrieved
    </div>

    <!-- Inactive and discharged warnings -->
    <div class="alert alert-warning" ng-show="!member.data.is_active">
        <h4>Incomplete Member Record</h4>
        <p>This record wasn't completely filled during the enrolment process. Perhaps it was cancelled or no longer required. </p>
        <p>
            <button type="button" class="btn btn-danger" ng-click="permanentDelete()" ng-disabled="state.isAsync">Delete this record, it's not needed</button>
            <button type="button" class="btn btn-default" ng-click="activate()" ng-disabled="state.isAsync">Activate member</button>
        </p>
    </div>
    <div class="alert alert-danger" ng-show="member.data.deleted_at">
        <h4>Discharged Member</h4>
        <p>This member has been discharged so this record cannot be edited.</p>
    </div>
</div>
@endsection

@section('content')

    {{-- Drop-in screen which displays if member is discharged --}}
	@include('member.partials.discharged')
    
    <div ng-hide="state.isDischarge()">
    
        @verbatim
        <div class="row" ng-if="state.isMemberLoaded">
            <div class="col-sm-6 col-sm-push-6">
                <div class="row">
                    <div class="col-md-8 hidden-sm hidden-xs">
                        <div class="table-wrapper text-muted">
                            <table class="table table-condensed">
                                <tr>
                                    <td>Regimental Number</td>
                                    <td>{{member.data.regt_num | markBlanks}}</td>
                                </tr>
                                <tr>
                                    <td>Years of Service</td>
                                    <td>{{member.data.role_class | markBlanks}}</td>
                                </tr>
                                <tr>
                                    <td>Current Rank / Posting</td>
                                    <td>-- TBA -- TBA</td>
                                </tr>
                                <tr>
                                    <td>Forums ID / Username</td>
                                    <td>{{member.data.forums_username | markBlanks}}</td>
                                </tr>
                                <tr>
                                    <td>COMS ID / Username</td>
                                    <td>{{member.data.coms_username | markBlanks}} {{member.coms_id}}</td>
                                </tr>
                                <tr>
                                    <td>Training awards</td>
                                    <td>
                                        <span class="label" ng-class="{'label-success': member.data.is_qual_mb, 'label-danger': !member.data.is_qual_mb}">MB: {{member.data.is_qual_mb | yesNo}}</span>
                                        <span class="label" ng-class="{'label-success': member.data.is_qual_s303, 'label-danger': !member.data.is_qual_s303}">S303: {{member.data.is_qual_s303 | yesNo}}</span>
                                        <span class="label" ng-class="{'label-success': member.data.is_qual_gf, 'label-danger': !member.data.is_qual_gf}">GF: {{member.data.is_qual_gf | yesNo}}</span>    
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Header thumbnail Display Picture -->
                        <section ng-controller="pictureController" ng-click="displayPictureModal()">
                            <div class="thumbnail fl-record-thumbnail">
                                <img ng-src="{{memberImage.url}}" alt="{{member.data.last_name}}" class="image-rounded memberview-thumb">
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-sm-pull-6">
                <h2>
                    <span class="text-upper">{{member.data.last_name}}</span>, {{member.data.first_name}} &nbsp;<br>
                    <small style="display: inline-block">&diams; {{member.data.regt_num}}</small>
                </h2>
                <div class="member-status-labels">
                    <span member-status></span>
                    <span hmp-status></span>
                    <span allergy-status></span>
                </div>
                <hr>
                <div>
                    <span class="label label-danger">TBA</span> <em>Member details can be edited through the Actions menu</em><br>
                    <span class="label label-danger">TBA</span> <em>Use Mass Actions to update Forums and COMS usernames</em>
                </div>
            </div>
        </div>
        @endverbatim

        <hr>
        
        <!-- Member info tabs & panel -->
        <div class="row">
            <div class="fl-content col-sm-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a bs-show-tab href="#details" aria-controls="details" role="tab">Details</a></li>
                    <li role="presentation"><a bs-show-tab href="#healthmed" aria-controls="healthmed" role="tab">Health &amp; Med</a></li>
                    <li role="presentation"><a bs-show-tab href="#iddocs" aria-controls="iddocs" role="tab">Docs &amp; ID</a></li>
                    <li role="presentation"><a bs-show-tab href="#decorations" aria-controls="decorations" role="tab">Decorations</a></li>
                    <li role="presentation"><a bs-show-tab href="#postings" aria-controls="postings" role="tab">Postings</a></li>
                    <li role="presentation"><a bs-show-tab href="#attendance" aria-controls="attendance" role="tab">Attendance</a></li>
                    <li role="presentation"><a bs-show-tab href="#payments" aria-controls="payments" role="tab">Payments</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="details">
                        <section ng-if="state.isMemberLoaded">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h3>Member Details</h3>
                                    <table class="table record-view">
                                        <tr>
                                            <td>Surname</td>
                                            <td display-mode="view">@{{member.data.last_name | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.last_name"></td>
                                        </tr>
                                        <tr>
                                            <td>Given Name</td>
                                            <td display-mode="view">@{{member.data.first_name | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.first_name"></td>
                                        </tr>
                                        <tr>
                                            <td>Sex</td>
                                            <td display-mode="view">@{{member.data.sex | markBlanks}}</td>
                                            <td display-mode="edit">
                                                <select ng-model="member.data.sex">
                                                    <option ng-repeat="sex in formData.sexes" value="@{{sex}}">@{{sex}}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>DOB</td>
                                            <td display-mode="view">@{{member.data.dob | date}}</td>
                                            <td display-mode="edit"><input type="date" ng-model="member.data.dob" placeholder="yyyy-MM-dd"></td>
                                        </tr>
                                        <tr>
                                            <td>School</td>
                                            <td display-mode="view">@{{member.data.school | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.school"></td>
                                        </tr>
                                        <tr>
                                            <td>Street Address</td>
                                            <td display-mode="view">
                                                @{{member.data.street_addr | markBlanks}}<br>
                                                @{{member.data.suburb}}<br>
                                                @{{member.data.state}} @{{member.postcode}} 
                                            </td>
                                            <td display-mode="edit">
                                                <input type="text" ng-model="member.data.street_addr" placeholder="e.g. 270 Miller St">
                                                <input type="text" ng-model="member.data.suburb" placeholder="e.g. North Sydney">										
                                                <select class="form-control" ng-model="member.data.state">
                                                    <option ng-repeat="state in ['NSW', 'ACT', 'QLD', 'VIC', 'SA', 'TAS', 'WA', 'NT']" value="@{{state}}">@{{state}}</option>
                                                </select> 
                                                <input type="text" ng-model="member.data.postcode" placeholder="e.g. 2065">
                                            </td>
                                        </tr>
                                    </table>						
                                </div>
                                <div class="col-sm-6">
                                    <h3>Contact Details</h3>
                                    <table class="table record-view">
                                        <tr>
                                            <td>Mobile (member)</td>
                                            <td display-mode="view">@{{member.data.member_mobile | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.member_mobile"></td>
                                        </tr>
                                        <tr>
                                            <td>Email (member)</td>
                                            <td display-mode="view">@{{member.data.member_email | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.member_email"></td>
                                        </tr>
                                    </table>
                                    
                                    <h3>Parental Details</h3>
                                    <table class="table record-view">
                                        <tr>
                                            <td>Preferred contact method</td>
                                            <td display-mode="view">@{{member.data.parent_preferred_comm | markBlanks}}</td>
                                            <td display-mode="edit">
                                                <select class="form-control" ng-model="member.data.parent_preferred_comm">
                                                    <option ng-repeat="comm in ['Email', 'MobilePhone', 'HomePhone']" value="@{{comm}}">@{{comm}}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Parent mobile</td>
                                            <td display-mode="view">@{{member.data.parent_mobile | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.parent_mobile"></td>
                                        </tr>
                                        <tr>
                                            <td>Parent email</td>
                                            <td display-mode="view">@{{member.data.parent_email | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.parent_email"></td>
                                        </tr>
                                        <tr>
                                            <td>Home phone</td>
                                            <td display-mode="view">@{{member.data.home_phone | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.home_phone"></td>
                                        </tr>
                                        <tr>
                                            <td>Special custody arrangement</td>
                                            <td display-mode="view">@{{member.data.parent_custodial | markBlanks}}</td>
                                            <td display-mode="edit"><input type="text" ng-model="member.data.parent_custodial"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="healthmed">
                        <section ng-if="state.isMemberLoaded">
                            <h3>Health and Medical</h3>
                            <p>
                                <span class="label label-default" ng-class="{'label-warning': !!+member.data.is_med_hmp }">Requires HMP: @{{member.is_med_hmp | yesNo}}</span>
                                <span class="label label-default" ng-class="{'label-danger': !!+member.data.is_med_lifethreat}">Life threatening: @{{member.is_med_lifethreat | yesNo}}</span>
                            </p>
                            <table class="table record-view">
                                <tr display-mode="edit">
                                    <td>Requires HMP</td>
                                    <td>
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.is_med_hmp" value="1"> Yes</label>
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.is_med_hmp" value="0"> No</label>
                                    </td>
                                </tr>
                                <tr display-mode="edit">
                                    <td>Allergies life threatening?</td>
                                    <td>
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.is_med_lifethreat" value="1"> Yes</label>
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.is_med_lifethreat" value="0"> No</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Allergies</td>
                                    <td display-mode="view">@{{member.data.med_allergies | markBlanks}}</td>
                                    <td display-mode="edit"><input type="text" ng-model="member.data.med_allergies"></td>
                                </tr>
                                <tr>
                                    <td>Medical Conditions</td>
                                    <td display-mode="view">@{{member.data.med_cond | markBlanks}}</td>
                                    <td display-mode="edit"><input type="text" ng-model="member.data.med_cond"></td>
                                </tr>
                                <tr>
                                    <td>Special Dietary Requirements (SDR)</td>
                                    <td display-mode="view">@{{member.data.sdr | markBlanks}}</td>
                                    <td display-mode="edit"><input type="text" ng-model="member.data.sdr"></td>
                                </tr>
                            </table>
                        </section>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="iddocs">
                        <section ng-if="state.isMemberLoaded">
                            <div display-mode="edit" class="pull-right">
                                <label class="checkbox-inline"><input type="checkbox" ng-model="member.data.is_fully_enrolled" ng-true-value="1" ng-false-value="0"> All enrolment documents uploaded?</label>
                            </div>
                            <h3>Documents</h3>
                            <p><span class="label label-warning">Work in progress</span></p>
                        </section>
                        
                        <section ng-if="state.isMemberLoaded">
                            <h3>ID Card</h3>
                            <table class="table record-view">
                                <tr>
                                    <td>Has been printed?</td>
                                    <td display-mode="view">@{{member.data.is_idcard_printed | yesNo}}</td>
                                    <td display-mode="edit">
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.is_idcard_printed" value="1"> Yes</label>
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.is_idcard_printed" value="0"> No</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Returned to Bn</td>
                                    <td display-mode="view">@{{member.data.idcard_at_bn | yesNo}}</td>
                                    <td display-mode="edit">
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.idcard_at_bn" value="1"> Yes</label>
                                        <label class="radio-inline"><input type="radio" ng-model="member.data.idcard_at_bn" value="0"> No</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Expiry Date</td>
                                    <td display-mode="view">@{{member.data.idcard_expiry | date}}</td>
                                    <td display-mode="edit"><input type="date" ng-model="member.data.idcard_expiry"></td>
                                </tr>
                                <tr>
                                    <td>Serial Number</td>
                                    <td display-mode="view">@{{member.data.idcard_serial_num | markBlanks}}</td>
                                    <td display-mode="edit"><input type="text" ng-model="member.data.idcard_serial_num"></td>
                                </tr>
                                <tr>
                                    <td>Remarks</td>
                                    <td display-mode="view">@{{member.data.idcard_remarks | markBlanks}}</td>
                                    <td display-mode="edit"><textarea ng-model="member.data.idcard_remarks" rows="4"></textarea></td>
                                </tr>
                            </table>
                        </section>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="decorations">
                        <section ng-if="state.isMemberLoaded">
                            <h3>Decorations</h3>
                            <p>Hold <kbd>SHIFT</kbd> to remove awards</p>
                            <div class="table-wrapper">
                                <table class="table table-striped">
                                    <colgroup>
                                        <col style="width: 120px;">
                                        <col style="width: 200px;">
                                        <col>
                                        <col style="width: 140px;">
                                        <col style="width: 80px;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Decoration</th>
                                            <th>Citation</th>
                                            <th>Date awarded</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="award in member.awards" ng-class="{ warning: award.isDeleting }">
                                            <td><img ng-src="@{{award.url}}" alt="@{{award.data.decoration.shortcode}}"></td>
                                            <td><span class="label label-info">@{{award.data.decoration.tier}}</span> @{{award.data.decoration.shortcode || award.data.decoration.name}} </td>
                                            <td>@{{award.data.citation}} @{{award.data.awd_score}} @{{award.data.awd_grade}}</td>
                                            <td>@{{award.data.date | date:'MMM yyyy'}}</td>
                                            <td>
                                                <a ng-hide="shiftKeyPressed" class="btn btn-primary btn-xs fl-context-modal-button" ng-href="{{ route('member::edit-decoration') }}#!/@{{member.regtNum}}/edit/@{{award.data.awd_id}}">Edit</a>
                                                <a ng-show="shiftKeyPressed" class="btn btn-danger btn-xs fl-context-modal-button" ng-click="removeAward(award)">Remove</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="postings">
                        <section ng-if="state.isMemberLoaded">
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
                                    <tr ng-repeat="postingPromo in member.data.postings" ng-class="{'warning': postingPromo.is_discharge}">
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
