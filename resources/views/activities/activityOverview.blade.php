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
<div class="row">
    
</div>
@endsection

@section('upcoming')
<section ng-repeat="category in upcoming track by $index" ng-if="category.activities.length > 0">
    <h3>@{{category.name}}</h3>
    <div class="list-group">
        <a class="list-group-item activity-colorcode" ng-repeat="activity in category.activities track by $index" ng-click="selectActivity(activity)">
            <span class="sr-only"></span>
            <div class="row">
                <div class="col-sm-6">
                    @{{activity.type}} @{{activity.name}}
                </div>
                <div class="col-sm-3">
                    @{{activity.start_date | date:'EEE dd MMM yyyy'}}
                </div>
                <div class="col-sm-3">
                    XX on roll
                </div>
            </div>
        </a>    
    </div>
</section>
@endsection

@section('content')
	@yield('archived')
	@yield('upcoming')
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityOverview.js"></script>
@endsection