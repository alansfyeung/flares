{{-- Activities overview --}}
@extends('master')

@section('ng-app', 'flaresActivityOverview')
@section('ng-controller', 'activityOverviewController')
@section('title', 'Activities Overview')


@section('heading')
<div class="page-header container-fluid">
    <aside class="title-actions pull-right">
        <a href="/activities/new" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Add new</a>
    </aside>
	<h1>Activity Overview</h1>
</div>
@endsection

@section('archived')
<div class="activities-archived">
    <div class="text-right"> 
        <a href="btn btn-link"><span class="glyphicon glyphicon-chevron-left"></span>View Upcoming</a>
    </div>
    <section>
        
    </section>
</div>
@endsection

@section('upcoming')
<div class="activities-upcoming">
    <div class="text-right">
        <a class="btn btn-link" ng-click="">View Archived<span class="glyphicon glyphicon-chevron-right"></span></a>
    </div>
    <section ng-repeat="category in upcoming track by $index" ng-if="category.activities.length > 0">
        <h4>@{{category.name}}</h4>
        <div class="list-group">
            <a class="list-group-item activity-colorcode" ng-repeat="activity in category.activities track by $index" ng-click="selectActivity(activity)">
                <span class="sr-only"></span>
                <div class="row">
                    <div class="col-sm-6">
                        <span class="activity-fullname">@{{activity.type}} @{{activity.name}}</span>
                    </div>
                    <div class="col-sm-3">
                        <div>@{{ activity.dateTopLine() }}</div>
                        <div ng-class="{'text-info': activity.is_half_day}">@{{ activity.dateBottomLine() }}</div>
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