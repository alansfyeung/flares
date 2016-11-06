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
<div ng-show="dec.dec_id">
    <aside class="titlebar-actions pull-right">
        <!-- DotDotDot menu toggle -->
        <span uib-dropdown>
            <a id="decoration-menu" class="btn btn-link" uib-dropdown-toggle>
                Menu <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <!-- For fully active members -->
            <ul class="dropdown-menu uib-dropdown-menu dropdown-menu-right" uib-dropdown-menu role="menu" aria-labelledby="decoration-menu">
                <li role="menuitem"><a href="#">Assign to a user</a></li>
                <li class="divider"></li>
                <li role="menuitem"><a href="/decorations/new">Add new decoration</a></li>
            </ul>
        </span>
    </aside>
	<h1>
        <span display-mode="edit">Edit</span>
        <span display-mode="view">View</span>
        Decoration
    </h1>
</div>
@endsection

@section('content')
@verbatim

<div ng-show="dec.dec_id">

    <div class="alert alert-warning" ng-if="state.errorMessage">{{state.errorMessage}}</div>
    <div class="alert alert-success" ng-if="state.successMessage">{{state.successMessage}}</div>

    <div class="row">
    
        <div class="col-sm-8">
            <form name="decorationDetails">
                <div class="form-group">
                    <input display-mode="edit" type="text" class="form-control input-lg" name="name" ng-model="dec.name">            
                    <h2 display-mode="view">
                        {{dec.name}}
                        <button class="btn btn-link" ng-click="beginEdit()">
                            <span class="glyphicon glyphicon-pencil"></span>
                            Edit
                        </button>
                    </h2> 
                </div>
            
                <table class="table record-view">
                    <tr>
                        <td>Description</td>
                        <td display-mode="view">{{dec.desc | markBlanks}}</td>
                        <td display-mode="edit"><input type="text" name="desc" ng-model="dec.desc"></td>
                    </tr>
                    <tr>
                        <td>Tier</td>
                        <td display-mode="view">{{dec.tier | markBlanks}}</td>
                        <td display-mode="edit">
                            <select name="tier" ng-model="dec.tier">
                                <option ng-repeat="(tierCode, tierDesc) in formData.tiers" value="{{tierCode}}">{{tierCode}} - {{tierDesc}}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Date Commenced</td>
                        <td display-mode="view">{{dec.date_commence | date | markBlanks}}</td>
                        <td display-mode="edit"><input type="date" name="date_commence" ng-model="dec.date_commence" placeholder="yyyy-MM-dd"></td>
                    </tr>
                    <tr>
                        <td>Date Concluded</td>
                        <td display-mode="view">{{dec.date_conclude | date | markBlanks}}</td>
                        <td display-mode="edit"><input type="date" name="date_conclude" ng-model="dec.date_conclude" placeholder="yyyy-MM-dd"></td>
                    </tr>
                    <tr>
                        <td>Database ID</td>
                        <td>{{dec.dec_id | markBlanks}}</td>
                    </tr>
                </table>
                
            </form>
            
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Badge</h4>
                </div>
                <div ng-controller="pictureController" flow-init>
                    <div class="panel-body">
                        <div flow-files-submitted="$flow.upload()" flow-file-success="$file.msg = $message">
                            <div class="fl-dec-badge-wrapper" flow-drag-enter="uploader.dropzone = true" flow-drag-leave="uploader.dropzone = false" flow-drop flow-drop-enabled="uploader.ready()" ng-class="{'uploader-drop-zone': uploader.dropzone, 'uploader-not-ready': !uploader.ready()}">
                                <div class="fl-dec-badge" ng-hide="uploader.uploading">
                                    <img ng-if="image.isLoaded" ng-src="{{image.url}}" alt="{{dec.name}}">
                                </div>
                                <div class="text-center" ng-repeat="file in $flow.files" ng-show="uploader.uploading">
                                    <h3 ng-show="file.isUploading()">Uploading</h3>
                                    <h3 class="text-success" ng-show="file.isComplete()"><span class="glyphicon glyphicon-ok-sign"></span> Successful</h3>
                                    <div class="thumbnail">
                                        <img flow-img="file">
                                        <div class="caption">{{file.name}} ({{Math.floor(file.size/1024)}} KB)</div>
                                    </div>
                                    <div class="progress progress-striped" ng-class="{active: file.isUploading()}">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" ng-style="{width: (file.progress() * 100) + '%'}" ng-class="{'progress-bar-success': file.isComplete()}">
                                        <span class="sr-only">1% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div ng-show="uploader.ready() && !uploader.uploading" flow-upload-started="uploadStart()" flow-complete="uploadFinish()">
                            <div class="btn-group">
                                <span class="btn btn-default" flow-btn>Upload File</span>
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a ng-click="deleteAll()"><span class="text-danger"><span class="glyphicon glyphicon-ban-circle"></span> Delete all</span></a></li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <small>Tip: Drag and drop new picture onto the existing picture </small>
            
        </div>
        
    </div><!-- endrow -->
    
    <hr>
    
    <div class="text-right" display-mode="edit">
        <button class="btn btn-primary" ng-click="finishEdit()">
            <span class="glyphicon glyphicon-floppy-disk"></span> Save
        </button>
        <button class="btn btn-default" ng-click="cancelEdit()">Cancel</button>
    </div>

</div>
@endverbatim
@endsection
