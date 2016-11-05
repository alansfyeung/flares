{{-- Display a single member --}}
@extends('primary')

@section('ng-app', 'flaresDecoration')
@section('ng-controller', 'decorationViewEditController')
@section('title', 'View Decoration')

@push('scripts')
<script src="/app/components/decoration/flaresDecorationViewEdit.js"></script>
<script src="/assets/js/flow/ng-flow-standalone.min.js"></script>
@endpush

@section('heading')
<!-- page main header -->
<div ng-cloak ng-show="member.regt_num">
	<aside class="titlebar-actions pull-right" ng-show="!(member.deleted_at || state.isDischarge())">
        <!-- EDIT BUTTON -->
		<button class="btn btn-link" ng-class="{'btn-success': state.isEdit()}" ng-click="edit()"><span class="glyphicon" ng-class="{'glyphicon-pencil': state.isView(), 'glyphicon-floppy-disk': state.isEdit()}"></span> @{{state.isEdit() ? 'Save' : 'Edit'}}</button>
		<button class="btn btn-link" ng-show="state.isEdit()" ng-click="cancelEdit()">Cancel</button>
        <!-- DotDotDot menu toggle -->
        <span uib-dropdown>
            <a class="btn btn-link" uib-dropdown-toggle>
                <span class="glyphicon glyphicon-option-vertical"></span>
            </a>
            <div class="uib-dropdown-menu dropdown-menu-right">
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
                <div class="titlebar-audit-info">
                    <h6>Record audit info</h4>
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
            </div>
            
        </span>
	</aside>
	<h1>Member Service Record</h1>
</div>
@endsection

@section('alerts')
<!-- Loading failure warnings -->
<div class="alert alert-info" ng-cloak ng-show="!state.isRequested">
    <strong>No Member ID specified:</strong> Please go back and request the member record again
</div>
<div class="alert alert-warning" ng-cloak ng-show="member.errorNotFound">
    <strong>Member Lookup failed:</strong> The user with Regt# &diams;@{{state.path.id}} couldn't be found.
</div>
<div class="alert alert-danger" ng-cloak ng-show="member.errorServerSide">
    <strong>Member Lookup failed:</strong> There was a server-side error and this record could not be retrieved
</div>

<!-- Inactive and discharged warnings -->
<div class="alert alert-danger" ng-cloak ng-if="state.isLoaded && !member.is_active">
    <h4>Incomplete Member Record</h4>
    <p>This record wasn't completely filled during the enrolment process. Perhaps it was cancelled or no longer required. </p>
    <p>
        <button type="button" class="btn btn-danger" ng-click="permanentDelete()" ng-disabled="state.isAsync">Delete this record, it's not needed</button>
        <button type="button" class="btn btn-default" ng-click="activate()" ng-disabled="state.isAsync">Activate member</button>
    </p>
</div>
<div class="alert alert-warning" ng-cloak ng-if="state.isLoaded && member.deleted_at">
    <h4>Discharged Member</h4>
    <p>This member has been discharged so this record cannot be edited.</p>
</div>
@endsection

@section('content')

<div ng-show="member.regt_num && !state.isDischarge()">

    <div class="row">
        <div class="col-xs-9 col-sm-9">
            <h2>@{{dec.name}}</h2>
        </div>
        <div class="col-xs-3 col-sm-3">
            <!-- Header thumbnail Display Picture -->
            <section ng-controller="pictureController" ng-click="displayPictureModal()">
                <div class="thumbnail fl-record-thumbnail">
                    <img ng-src="@{{memberImage.url}}" alt="@{{member.last_name}}" class="image-rounded memberview-thumb">
                </div>
            </section>

        </div>
    </div>
    
    <hr>
    
    
    <section>
        <table class="table record-view">
            <tr>
                <td>Surname</td>
                <td display-mode="view">@{{member.last_name | markBlanks}}</td>
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
            
    </section>

    {{-- Upload image --}}
    @verbatim
    <section ng-show="wf.state.stage === 2">
        <h2>Decoration image <small>Stage 2 of 2</small></h2>
        <hr>
        
        <div ng-controller="pictureController" flow-init>
            <div flow-files-submitted="$flow.upload()" flow-file-success="$file.msg = $message">
                <div class="thumbnail member-dp-lg" flow-drag-enter="uploader.dropzone = true" flow-drag-leave="uploader.dropzone = false" flow-drop flow-drop-enabled="uploader.ready()" ng-class="{'uploader-drop-zone': uploader.dropzone, 'uploader-not-ready': !uploader.ready()}">
                    <img ng-src="{{memberImage.url}}" alt="{{member.last_name}}" class="image-rounded" ng-show="!uploader.uploading">
                    <div class="text-center" ng-repeat="file in $flow.files" ng-show="uploader.uploading">
                        <h3 ng-show="file.isUploading()">Uploading</h3>
                        <h3 class="text-success" ng-show="file.isComplete()"><span class="glyphicon glyphicon-ok-sign"></span> Successful</h3>
                        <div class="thumbnail">
                            <img flow-img="file">
                            <div class="caption">{{file.name}} ({{Math.floor(file.size/1024)}} KB)</div>
                        </div>
                        <div class="progress progress-striped" ng-class="{active: file.isUploading()}">
                            <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" ng-style="{width: (file.progress() * 100) + \'%\'}" ng-class="{\'progress-bar-success\': file.isComplete()}">
                            <span class="sr-only">1% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div ng-show="uploader.ready() && !uploader.uploading" flow-upload-started="uploadStart()" flow-complete="uploadFinish()">
                <small>Tip: Drag and drop new picture onto the existing picture </small>&nbsp;&nbsp;
                <div class="btn-group">
                    <span class="btn btn-default" flow-btn>Upload File</span>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{memberImage.url}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Download</a></li>
                        <li><a ng-click="deleteLast()"><span class="glyphicon glyphicon-step-backward"></span> Rewind ({{memberImage.count}})</a></li>
                        <li><a ng-click="deleteAll()"><span class="text-danger"><span class="glyphicon glyphicon-ban-circle"></span> Delete all</span></a></li>
                    </ul>
                </div>
                <button class="btn btn-default" ng-click="closeModal()">Done</button>
            </div>
        </div>

    </section>
    @endverbatim

</div>
@endsection
