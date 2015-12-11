{{-- Roll for an activity --}} 
@extends('master')

@section('ng-app', 'flaresActivityRollView')
@section('ng-controller', 'activityRollController')
@section('title', 'Activity roll')

@section('heading')
<!-- page main header -->
<div>
    <aside class="title-actions pull-right">
        <!-- FILL BUTTON groups -->
        <span ng-show="state.isFill()">
            <button class="btn btn-success" ng-click="fill()"><span class="glyphicon glyphicon-floppy-disk"></span> Stop filling</button>        
        </span>
        <span ng-show="state.isView()">
            <button class="btn btn-default" ng-click="fill()"><span class="glyphicon glyphicon-pencil"></span> Fill</button>
        </span>
    </aside>
	<h1>Activity run sheet</h1>
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

@section('activity-roll')
<div role="tabpanel" id="markroll" class="tab-pane active">
    <form class="form-horizontal" name="contextForm" ng-submit="submitOnly()">
        <div class="row">
            <h2>Mark roll</h2>
        </div>
    </div>
</div>
@endsection

@section('activity-paradeState')

@endsection

@section('content')
@yield('activity-titleBlock')
<div class="row">
    <div class="col-sm-12">	
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a bs-show-tab href="markroll" aria-controls="markroll" role="tab">Mark Roll</a></li>
            <li role="presentation"><a bs-show-tab href="paradestate" aria-controls="paradestate" role="tab">Parade State</a></li>
        </ul>
        <div class="tab-content">
            @yield('activity-roll')
            @yield('activity-paradeState')
        </div>
    </div>
</div>
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityMarkRoll.js"></script>
@endsection