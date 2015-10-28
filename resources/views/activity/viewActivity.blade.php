{{-- Activities View existing --}}
@extends('master')

@section('ng-app', 'flaresActivityView')
@section('ng-controller', 'activityViewEditController')
@section('title', 'View Activity')


@section('heading')
<!-- page main header -->
<div class="page-header container-fluid" ng-cloak ng-show="activity.acty_id">

	<!-- EDIT BUTTON -->
	<aside class="title-actions pull-right" ng-show="workflow.isEdit()">
		<button class="btn btn-success" ng-click="edit()"><span class="glyphicon glyphicon-floppy-disk"></span> Save changes</button>
		<button class="btn btn-default" ng-click="cancelEdit()">Cancel</button>
	</aside>
	<aside class="title-actions pull-right" ng-show="workflow.isView()">
		<button class="btn btn-default" ng-click="edit()"><span class="glyphicon glyphicon-pencil"></span> Edit mode</button>
	</aside>
	
	<h1>@{{activity.type}} @{{activity.name}}</h1>
</div>
@endsection

@section('activity-details')
<div role="tabpanel" id="details" class="tab-pane active">
    <section>
        <div class="row">
            <div class="col-sm-6">
                <h3>Activity Details</h3>
                <table class="table record-view">
                    <tr>
                        <td>Type</td>
                        <td display-mode="view">@{{activity.type | markBlanks}}</td>
                        <td display-mode="edit">
                            <select class="form-control" ng-model="activity.type">
                                <option ng-repeat="type in formData.activityTypes" value="@{{type}}">@{{type}}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td display-mode="view">@{{activity.name | markBlanks}}</td>
                        <td display-mode="edit"><input type="text" ng-model="activity.name"></td>
                    </tr>
                    <tr>
                        <td>Start date</td>
                        <td display-mode="view">@{{activity.start_date | date:'fullDate'}}</td>
                        <td display-mode="edit"><input type="date" ng-model="activity.start_date"></td>
                    </tr>
                    <tr ng-show="!activity.is_half_day">
                        <td>End date</td>
                        <td display-mode="view">@{{activity.end_date | date:'fullDate'}}</td>
                        <td display-mode="edit"><input type="date" ng-model="activity.end_date"></td>
                    </tr>
                </table>
                
            </div>
            <div class="col-sm-6">
                <h3>Status</h3>
                <table class="table record-view">
                    <tr>
                        <td>Rescheduled?</td>
                        <td display-mode="view">@{{activity.is_rescheduled | yesNo}}</td>
                        <td display-mode="edit"><input type="checkbox" ng-model="activity.is_rescheduled" ng-true-value="1" ng-false-value="0"></td>
                    </tr>
                    <tr>
                        <td>Half day activity?</td>
                        <td display-mode="view">@{{activity.is_half_day | yesNo}}</td>
                        <td display-mode="edit"><input type="checkbox" ng-model="activity.is_half_day" ng-true-value="1" ng-false-value="0"></td>
                    </tr>
                    <tr>
                        <td>Is a parade night?</td>
                        <td display-mode="view">@{{activity.is_parade_night | yesNo}}</td>
                        <td display-mode="edit"><input type="checkbox" ng-model="activity.is_parade_night" ng-true-value="1" ng-false-value="0"></td>
                    </tr>
                </table>
                
                {{--
                <h3>Nominal roll</h3>
                <div class="well">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3" ng-repeat="(statKey, statValue) in activityRollStats">
                            <figure class="dashboard-stat">
                                <figcaption class="stat-caption">@{{statKey}}</figcaption>
                                <div class="stat-figure ng-binding">@{{statValue}}</div>
                            </figure>
                        </div>
                    </div>
                </div>
                --}}
            </div>
        </div>
    </section>
</div>
@endsection

@section('activity-rollbuilder')
<div role="tabpanel" id="rollbuilder" class="tab-pane">
    <section ng-controller="rollBuilderController">
        <div class="row">
            <div class="col-sm-12">
                <div display-mode="view">
                    <h3>Nominal roll</h3>
                
                </div>
                <div display-mode="edit">
                    <h3>Edit nominal roll</h3>
                    <p>Select members to insert into the nominal roll.</p>
                    <div class="activity-rollselector">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Last name</th>
                                    <th>Given names</th>
                                    <th>Rank</th>
                                    <th>Platoon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="member in memberList" ng-click="toggleRoll(member)" ng-class="{'info': member.isRoll}">
                                    <td><input type="checkbox" ng-model="member.isRoll"/></td>
                                    <td><strong class="text-uppercase">@{{member.data.last_name}}</strong></td>
                                    <td>@{{member.data.first_name}}</td>
                                    <td>@{{member.data.current_rank.rank}}</td>
                                    <td>@{{member.data.current_platoon.platoon}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('content')
<form class="form-horizontal" name="contextForm" ng-submit="submitOnly()">
    <div class="col-sm-3 col-sm-push-9">
        <section>
            <h4>Actions</h4>
            <!-- For fully active members -->
            <div class="list-group">
                <a class="list-group-item list-group-item-success"><span class="badge">26</span> Mark roll</a>
                <a class="list-group-item" ng-click="deleteActivity()">Delete activity</a>
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
            <li role="presentation"><a bs-show-tab href="#paradestate" aria-controls="paradestate" role="tab">Parade State</a></li>
        </ul>
        
        <div class="tab-content">
            @yield('activity-details')
            @yield('activity-rollbuilder')
        </div>
    </div>
</form>
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityViewEdit.js"></script>
@endsection