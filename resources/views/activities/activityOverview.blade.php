{{-- Activities overview --}}
@extends('master')

@section('ng-app', 'flaresActivityOverview')
@section('ng-controller', 'activityOverviewController')
@section('title', 'Activities Overview')


@section('heading')
<h1>Activity Overview</h1>
@endsection

@section('archived')
<div class="activities-archived" ng-show="state.mode == 'archived'">
    <div class="text-right"> 
        <a ng-click="state.mode = 'upcoming'">View Upcoming</a>
    </div>
    <div ng-show="upcoming.summary.total === 0">
        <p class="fl-helptext">
            There are no archived activities
            <a href="/activities/new" class="btn btn-default" ng-click="activate()" ng-disabled="state.isAsync">Add a new activity</a>
        </p>
    </div>
    <section>
        
    </section>
</div>
@endsection

@section('upcoming')
<div class="activities-upcoming" ng-show="state.mode == 'upcoming'">
    <div class="text-right">
        <a ng-click="goToNewActivity()">Add new activity</a>
        &bull;
        <a ng-click="state.mode = 'archived'">View Archived</a>
    </div>
    <div ng-show="upcoming.summary.total === 0">
        <p class="fl-helptext">
            There are no upcoming activities. 
            <a href="/activities/new" class="btn btn-default" ng-click="activate()" ng-disabled="state.isAsync">Add a new activity</a>
        </p>
    </div>
    <section ng-repeat="category in upcoming track by $index" ng-if="category.activities.length > 0">
        <h4>@{{category.name}}</h4>
        <div class="list-group">
            <a class="list-group-item activity-colorcode" ng-repeat="activity in category.activities track by $index" ng-click="selectActivity(activity)">
                <span class="sr-only"></span>
                <div class="row">
                    <div class="col-sm-6">
                        <h2 class="activity-fullname">@{{activity.name}}<br><small>@{{activity.type}}</small></h2>
                    </div>
                    <div class="col-sm-3">
                        <p>@{{ activity.dateTopLine() }} @{{ activity.dateBottomLine() }}</p>
                    </div>
                    <div class="col-sm-3">
                        XX on roll
                    </div>
                </div>
            </a>    
        </div>
    </section>
</div>
@endsection

@section('content')
	@yield('archived')
	@yield('upcoming')
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityOverview.js"></script>
@endsection