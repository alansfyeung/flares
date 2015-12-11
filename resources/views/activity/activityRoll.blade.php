{{-- Roll for an activity --}} 
@extends('master')

@section('ng-app', 'flaresActivityMarkRoll')
@section('ng-controller', 'activityRollController')
@section('title', 'Activity roll')

@section('heading')
<!-- page main header -->
<div>
    <aside class="title-actions pull-right">
        <!-- FILL BUTTON groups -->
        <span ng-show="state.isFill()">
            <button class="btn btn-default" ng-click="fill()"><span class="glyphicon glyphicon-floppy-disk"></span> Stop filling</button>        
        </span>
        <span ng-show="state.isView()">
            <button class="btn btn-success" ng-click="fill()"><span class="glyphicon glyphicon-pencil"></span> Fill</button>
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
<form class="form-horizontal" name="contextForm" ng-submit="submitOnly()">
    <h2>Mark roll</h2>
    <div>
        <div class="row" ng-repeat="rollEntry in roll">
            <div class="col-xs-4 col-sm-3">
                <p>@{{rollEntry.rank}}</p>
            </div>
            <div class="col-xs-4 col-sm-6">
                <h4>@{{rollEntry.last_name}}, @{{rollEntry.first_name.substr(0, 1)}}</h4>            
            </div>
            <div class="col-xs-4 col-sm-3">
                
            </div>
        </div>
    </div>
</form>
@endsection

@section('activity-paradeState')

@endsection

@section('content')
@yield('activity-titleBlock')
@yield('activity-roll')
@yield('activity-paradeState')
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityMarkRoll.js"></script>
@endsection