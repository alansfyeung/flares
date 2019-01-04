{{-- Display a decoration --}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresDecoration')
@section('ng-controller', 'decorationViewEditController')
@section('title', 'View Decoration')

@push('scripts')
<script src="{{asset('ng-app/components/decoration/flaresDecorationViewEdit.js')}}"></script>
<script src="{{asset('assets/js/flow/ng-flow-standalone.min.js')}}"></script>
@endpush

@section('heading')
<!-- page main header -->
<div ng-if="state.isDecorationLoaded">
    <aside class="titlebar-actions pull-right">
        <!-- EDIT BUTTON -->
		<button class="btn btn-link" ng-class="{'btn-success': state.isEdit()}" ng-click="edit()">
            <span class="glyphicon" ng-class="{'glyphicon-pencil': state.isView(), 'glyphicon-floppy-disk': state.isEdit()}"></span> 
            @{{state.isEdit() ? 'Save details' : 'Edit details'}}
        </button>
		<button class="btn btn-link" ng-show="state.isEdit()" ng-click="cancelEdit()">Cancel</button>
        <!-- DotDotDot menu toggle -->
        <span uib-dropdown>
            <a id="decoration-menu" class="btn btn-link" uib-dropdown-toggle>
                Menu <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <ul class="dropdown-menu uib-dropdown-menu dropdown-menu-right" uib-dropdown-menu role="menu" aria-labelledby="decoration-menu">
                <li role="menuitem"><a ng-click="beginEdit()">Edit</a></li>
                <li class="divider"></li>
                <li role="menuitem"><a href="{{url('/decorations/new')}}">Add new decoration</a></li>
            </ul>
        </span>
    </aside>
	<h1>
        <a href="./decorations">All Decorations</a>
        &rsaquo;
        <span display-mode="edit">Edit</span>
        <span display-mode="view">View</span>
        Decoration
    </h1>
</div>
@endsection

@section('content')
@verbatim

<div ng-if="state.isDecorationLoaded">

    <div class="alert alert-warning" ng-if="state.errorMessage">{{state.errorMessage}}</div>
    <div class="alert alert-success" ng-if="state.successMessage">{{state.successMessage}}</div>

    <div class="row">
    
        <div class="col-sm-8">
            <form name="forms.decorationDetails">
                <div class="form-group">
                    <input display-mode="edit" type="text" class="form-control input-lg" name="name" ng-model="dec.data.name">            
                    <h2 display-mode="view">
                        {{dec.data.name}}
                        <button class="btn btn-link" ng-click="beginEdit()">
                            <span class="glyphicon glyphicon-pencil"></span> Edit
                        </button>
                    </h2> 
                </div>
                
                <div class="fl-content col-sm-12">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a bs-show-tab href="#details" aria-controls="details" role="tab">Details</a></li>
                        <li role="presentation"><a bs-show-tab href="#related" aria-controls="related" role="tab">Related</a></li>
                        <li role="presentation"><a bs-show-tab href="#prec" aria-controls="prec" role="tab">Precedence</a></li>
                    </ul>
                    
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="details">
                            <h3>Decoration details</h3>
                            <table class="table record-view">
                                <tr>
                                    <td>Purpose</td>
                                    <td display-mode="view">{{dec.data.desc | markBlanks}}</td>
                                    <td display-mode="edit"><textarea name="desc" ng-model="dec.data.desc" rows="4"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Visual description</td>
                                    <td display-mode="view">{{dec.data.visual | markBlanks}}</td>
                                    <td display-mode="edit"><textarea name="visual" ng-model="dec.data.visual" rows="4"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Tier</td>
                                    <td display-mode="view">{{dec.data.tier | markBlanks}}</td>
                                    <td display-mode="edit">
                                        <select name="tier" ng-options="tier.tier as tier.tierName for tier in formData.decorationTiers" ng-model="dec.data.tier"></select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shortcode </td>
                                    <td display-mode="view"><code>{{dec.data.shortcode | markBlanks}}</code></td>
                                    <td display-mode="edit"><input type="text" name="shortcode" ng-model="dec.data.shortcode" placeholder="Shortcode (10 letters)"></td>
                                </tr>
                                <tr>
                                    <td>Date Commenced</td>
                                    <td display-mode="view">{{dec.data.date_commence | date | markBlanks}}</td>
                                    <td display-mode="edit"><input type="date" name="date_commence" ng-model="dec.data.date_commence" placeholder="yyyy-MM-dd"></td>
                                </tr>
                                <tr>
                                    <td>Date Concluded</td>
                                    <td display-mode="view">{{dec.data.date_conclude | date | markBlanks}}</td>
                                    <td display-mode="edit"><input type="date" name="date_conclude" ng-model="dec.data.date_conclude" placeholder="yyyy-MM-dd"></td>
                                </tr>
                                <tr>
                                    <td>Service period </td>
                                    <td display-mode="view">{{dec.data.service_period_months | markBlanks}}</td>
                                    <td display-mode="edit"><input type="number" name="service_period_months" ng-model="dec.data.service_period_months" placeholder="In months e.g. 6"></td>
                                </tr>
                                <tr>
                                    <td>Authorised by </td>
                                    <td display-mode="view">{{dec.data.authorized_by | markBlanks}}</td>
                                    <td display-mode="edit"><input type="text" name="authorized_by" ng-model="dec.data.authorized_by" placeholder="Award authority e.g. OC"></td>
                                </tr>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="related">
                            <h3>Decoration parent</h3>
                            <p>If this decoration is part of a family (e.g. varying seniority) then select the parent decoration below.</p>
                            <table class="table record-view">
                                <tr>
                                    <th>Decoration parent</th>
                                    <td display-mode="view">{{dec.parentDecoration.name | markBlanks}}</td>
                                    <td display-mode="edit">
                                        <select name="parent_id" ng-options="exDec.dec_id as exDec.name for exDec in formData.existingDecorations | filter:{tier: dec.data.tier}" ng-model="dec.data.parent_id">
                                            <option value="">-- None --</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <h3>Other decorations in this family</h3>
                            <p class="text-muted" ng-hide="state.areRelatedDecorationsLoaded">There are no related decorations</p>
                            <table class="table table-condensed" ng-show="state.areRelatedDecorationsLoaded">
                                <colgroup>
                                    <col style="width: 110px;">
                                    <col>
                                    <col style="width: 60px;">
                                    <col style="width: 40px;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Shortcode</th>
                                        <th>Name</th>
                                        <th>Order</th>
                                        <th><!-- Go --></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="familyDec in dec.familyDecorations" ng-class="{ 'info': familyDec.dec_id == dec.id }">
                                        <td><code>{{familyDec.shortcode | markBlanks}}</code></td>
                                        <td>{{familyDec.name}}</td>
                                        <td class="text-center text-muted">{{familyDec.parent_order}}</td>
                                        <td>
                                            <button class="btn btn-default btn-xs" ng-click="navToDecoration(familyDec.dec_id)" ng-disabled="!familyDec.dec_id">Go</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="prec">
                            <h3>Precedence</h3>
                            <p>Set the order of precedence in which these decorations will be shown.</p>
                            <table class="table record-view">
                                <tr>
                                    <td>Precedence <span class="text-muted" ng-show="dec.parentDecoration">(from parent)</span></td>
                                    <td display-mode="view">{{dec.data.precedence | markBlanks}}</td>
                                    <td display-mode="edit">
                                        <input type="text" name="precedence" ng-model="dec.data.precedence" placeholder="In months e.g. 6" ng-if="!dec.parentDecoration">
                                        <span class="text-muted" ng-show="dec.parentDecoration">This decoration has a parent so precedence cannot be changed.</span>
                                    </td>
                                </tr>
                                <tr ng-show="dec.parentDecoration">
                                    <td>Ordering under parent</td>
                                    <td display-mode="view">{{dec.data.parent_order}}</td>
                                    <td display-mode="edit">
                                        <input type="text" name="parent_order" ng-model="dec.data.parent_order" placeholder="Ordinal number">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
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
                                    <img ng-if="image.isLoaded" ng-src="{{image.url}}" alt="{{dec.data.name}}">
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
    
    <div display-mode="view">
        <button class="btn btn-default" ng-click="navToDecoration(dec.higherId)" ng-disabled="!dec.higherId">
            <span class="glyphicon glyphicon-menu-left"></span> Higher
        </button>
        <button class="btn btn-default" ng-click="navToDecoration(dec.lowerId)" ng-disabled="!dec.lowerId">
            Lower <span class="glyphicon glyphicon-menu-right"></span>
        </button>
    </div>
    <div class="text-right" display-mode="edit">
        <button class="btn btn-danger pull-left" ng-click="delete()">Delete</button>
        <button class="btn btn-primary" ng-click="finishEdit()">
            <span class="glyphicon glyphicon-floppy-disk"></span> Save
        </button>
        <button class="btn btn-default" ng-click="cancelEdit()">Cancel</button>
    </div>

</div>
@endverbatim
@endsection
