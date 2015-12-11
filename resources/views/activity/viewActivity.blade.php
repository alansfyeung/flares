{{-- Activities View existing --}}
@extends('master')

@section('ng-app', 'flaresActivityView')
@section('ng-controller', 'activityViewEditController')
@section('title', 'View Activity')


@section('heading')
<!-- page main header -->
<div ng-cloak ng-show="activity.acty_id">
	<aside class="title-actions pull-right">
        <!-- EDIT BUTTON groups -->
        <span ng-show="state.isEdit()">
            <button class="btn btn-success" ng-click="saveEdit()"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
            <button class="btn btn-default" ng-click="cancelEdit()">Cancel</button>        
        </span>
        <span ng-show="state.isView()">
            <button class="btn btn-default" ng-click="edit()"><span class="glyphicon glyphicon-pencil"></span> Edit</button>
        </span>
        <!-- Sidebar toggle -->
        <span>
            <a sidebar-toggle class="btn btn-link"><span class="glyphicon glyphicon-option-vertical"></span></a>
        </span>
	</aside>
	
	<h1>Activity preparation</h1>
</div>
@endsection

@section('activity-titleBlock')
<div class="row">
    <div class="col-xs-12">
	   <h2>@{{activity.type}} @{{activity.name}}<br><small style="display: inline-block">@{{activity.start_date | date:'fullDate'}}</small></h2>          
    </div>
</div>
<hr> 
@endsection

@section('activity-details')
<div role="tabpanel" id="details" class="tab-pane active">
    <form class="form-horizontal" name="contextForm" ng-submit="submitOnly()">
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
    </form>
</div>
@endsection

@section('activity-rollbuilder')
<div role="tabpanel" id="rollbuilder" class="tab-pane">
    <section ng-controller="rollBuilderController">
        <div display-mode="view" class="row">
            <div class="col-sm-12">
                <h3>Nominal roll</h3> 
                <p>@{{(memberList | filter: { onRoll: true }).length}} members currently on the nominal roll</p>
                <table class="table table-condensed fl-table-header">
                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 10%;">
                        <col style="width: 40%;">
                        <col style="width: 5%;">
                        <col style="width: 20%;">
                        <col style="width: 20%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Rank</th>
                            <th>Last name, Inital</th>
                            <th>PL</th>
                            <th>Status</th>
                            <th>Last modified</th>
                        </tr>
                    </thead>
                </table>
                <div class="fl-table-scrollable">
                    <table class="table table-condensed">
                        <colgroup>
                            <col style="width: 5%;">
                            <col style="width: 10%;">
                            <col style="width: 40%;">
                            <col style="width: 5%;">
                            <col style="width: 20%;">
                            <col style="width: 20%;">
                        </colgroup>
                        <tbody>
                            <tr ng-repeat="member in memberList | filter: { onRoll: true } track by $index">
                                <td>@{{$index + 1}}</td>
                                <td>@{{member.data.current_rank.rank | markBlanks}}</td>
                                <td><span class="text-uppercase">@{{member.data.last_name}}</span>, @{{member.data.first_name.substr(0,1) }}</td>
                                <td>@{{member.data.current_platoon.platoon | markBlanks}}</td>
                                <td>@{{member.displayStatus()}}</td>
                                <td>@{{member.roll.created_at | markBlanks}}</td>
                            </tr>                        
                        </tbody>
                    </table>                
                </div>
            </div>
        </div>
        <div display-mode="edit" class="row">
            <div class="col-sm-12">
                <h3>Edit nominal roll</h3>
                <p>
                    @{{ (memberList | filter:{ onRoll: true }).length }}/@{{ memberList.length }} selected
                    <span ng-show="filtering.filterFired">, @{{filtering.showing}} displayed under this filter</span>
                </p>
                <div class="alert alert-danger" ng-show="lastError.code">
                    <strong>@{{lastError.code}}</strong> @{{lastError.reason}}
                </div>
            </div>
        </div>
        <div display-mode="edit" class="row">
            <div class="col-sm-8">
                <table class="table fl-table-header">
                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 30%;">
                        <col style="width: 30%;">
                        <col style="width: 20%;">
                        <col style="width: 15%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Last name</th>
                            <th>Given names</th>
                            <th>Rank</th>
                            <th>Platoon</th>
                        </tr>
                    </thead>
                </table>
                <div class="fl-table-scrollable">
                    <table class="table">
                        <colgroup>
                            <col style="width: 5%;">
                            <col style="width: 30%;">
                            <col style="width: 30%;">
                            <col style="width: 20%;">
                            <col style="width: 15%;">
                        </colgroup>
                        <tbody>
                            <tr ng-repeat="member in memberList | filter: { visible: true }" ng-click="toggleRollSelection(member); bumpRollRefreshTimer();" ng-class="{'success': member.onRoll, 'info': member.isMarked()}">
                                <td><input type="checkbox" ng-model="member.onRoll" ng-show="!member.isMarked()"/></td>
                                <td><strong class="text-uppercase">@{{member.data.last_name}}</strong></td>
                                <td>@{{member.data.first_name}}</td>
                                <td>@{{member.data.current_rank.rank}}</td>
                                <td>@{{member.data.current_platoon.platoon}}</td>
                            </tr>
                        </tbody>
                    </table>                
                </div>
            </div>
            <div class="col-sm-4">
                <div class="">
                    <h4>Legend</h4>
                    <table class="table table-condensed">
                        <tr class="success">
                            <td><strong>Included on roll, unmarked</strong></td>
                        </tr>
                        <tr class="info">
                            <td><strong>Already marked</strong> (Cannot be removed)</td>
                        </tr>
                    </table>
                </div>
                <div class="">
                    <h4>Filter (display only)</h4>
                    <div class="form-group">
                        <div class="input-group">
                            <select class="form-control" ng-model="filtering.activeFilterIndex">
                                <option ng-repeat="filter in filtering.filters track by $index" value="@{{$index}}">@{{filter.desc}}</option>
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" ng-click="filtering.runFilter()">Filter</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <h4>Quick selections</h4>
                    <div class="form-group">
                        <div class="input-group">
                            <select class="form-control">
                                <option ng-repeat="platoon in formData.platoons" value="@{{platoon.abbr}}">@{{platoon.name}}</option>
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary">Select</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('activity-permission')
<div role="tabpanel" id="permission" class="tab-pane">
    <section ng-controller="permissionController">
        <h1>Todo: Permission note uploading</h1>
    </section>
</div>
@endsection

@section('content')
@yield('activity-titleBlock')
<div class="row">
    <div class="fl-sidebar col-sm-3 col-sm-push-9 hidden">
        <section>
            <h4>Actions</h4>
            <!-- For fully active members -->
            <div class="list-group">
                <a class="list-group-item" ng-click="actions.markRoll()"><span class="badge">@{{ memberList.length }}</span> Mark roll</a>
                <a class="list-group-item" ng-click="actions.paradeState()">Parade State</a>
                <a class="list-group-item" ng-click="actions.leave()">Configure leave</a>
                <a class="list-group-item" ng-click="actions.reviewAwol()">Review AWOLs</a>
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
    <div class="fl-content col-sm-12">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a bs-show-tab href="#details" aria-controls="details" role="tab">Details</a></li>
            <li role="presentation"><a bs-show-tab href="#rollbuilder" aria-controls="rollbuilder" role="tab">Roll Builder</a></li>
            <li role="presentation"><a bs-show-tab href="#permission" aria-controls="permission" role="tab">Permission</a></li>
        </ul>
        <div class="tab-content">
            @yield('activity-details')
            @yield('activity-rollbuilder')
            @yield('activity-permission')
        </div>
    </div> 
</div>
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityViewEdit.js"></script>
@endsection