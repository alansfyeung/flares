{{-- Roll for an activity --}} 
@extends('master')

@section('ng-app', 'flaresActivityRollView')
@section('ng-controller', 'activityRollController')
@section('title', 'Activity roll')

@section('heading')
<!-- page main header -->
<div>
	<h1>Activity run sheet</h1>
</div>
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
<div class="row">
    
</div>
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