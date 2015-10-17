{{-- Activities View existing --}}
@extends('master')

@section('ng-app', 'flaresActivityView')
@section('ng-controller', 'activityViewEditController')
@section('title', 'View Activity')


@section('heading')
<!-- page main header -->
<div class="page-header container-fluid" ng-cloak ng-show="activity.acty_id">

	<!-- EDIT BUTTON -->
	<aside class="title-actions pull-right">
		<button class="btn btn-default" ng-class="{'btn-success': workflow.isEdit()}" ng-click="edit()"><span class="glyphicon" ng-class="{'glyphicon-pencil': workflow.isView(), 'glyphicon-floppy-disk': workflow.isEdit()}"></span> @{{workflow.isEdit() ? 'Save Details' : 'Edit Details'}}</button>
		<button class="btn btn-default" ng-show="workflow.isEdit()" ng-click="cancelEdit()">Cancel</button>
	</aside>
	
	<h1>@{{activity.type}} @{{activity.name}}</h1>
</div>
@endsection

@section('activity-form')
<form class="form-horizontal" name="contextForm" ng-submit="submitOnly()">
    <div class="col-sm-3 col-sm-push-9">
        <section>
            <h4>Actions</h4>
            <!-- For fully active members -->
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-success">Mark roll</a>
                <a href="#" class="list-group-item">XXXYYYZZZ</a>
                <button type="button" class="list-group-item" ng-click="confirmDischarge()">Delete activity</button>
            </div>
        </section>
        
        <h4>Record audit info</h4>
        <dl>
            <dt>Date created</dt>
            <dd>@{{activity.created_at | date:'medium'}}</dd>
            <dt>Last updated</dt>
            <dd>@{{activity.updated_at | date:'medium'}}</dd>
        <dl>
    </div>
    
    <div class="col-sm-9 col-sm-pull-3">	
    <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a bs-show-tab href="#details" aria-controls="details" role="tab">Details</a></li>
            <li role="presentation"><a bs-show-tab href="#rollbuilder" aria-controls="rollbuilder" role="tab">Roll Builder</a></li>
        </ul>
        
        <div class="tab-content">
            <div role="tabpanel" id="details" class="tab-pane active">
                <section>
                    <div class="row">
                        <div class="col-sm-6">
                            <h3>Personal Details</h3>
                            <table class="table record-view">
                                <tr>
                                    <td>Activity name</td>
                                    <td display-mode="view">@{{activity.name | markBlanks}}</td>
                                    <td display-mode="edit"><input type="text" ng-model="activity.name"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div role="tabpanel" id="rollbuilder" class="tab-pane active">
                <section>
                
                </section>
            </div>
        </div>
        
    </div>
</form>
@endsection

@section('content')
	@yield('activity-form')
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityViewEdit.js"></script>
@endsection