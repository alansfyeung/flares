{{-- Activities NEW --}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresActivityNew')
@section('ng-controller', 'activityAddController')
@section('title', 'New Activity')


@section('heading')
<h1>New Activity</h1>
@endsection

@section('activity-form')
<form class="form-horizontal" name="contextForm" ng-submit="submitOnly()">
    <div class="col-xs-12">
        <h3>Details</h3>
        <aside ng-show="workflow.isSubmitAttempted && workflow.validation.hasErrors">
            <div class="alert alert-danger">@{{workflow.validation.message}}</div>
        </aside>
         <div class="form-group">
            <label class="control-label col-sm-3">Type</label>
            <div class="col-sm-9">
                <select class="form-control" ng-model="newActivity.data.type">
                    <option ng-repeat="type in formData.activityTypes" value="@{{type}}">@{{type}}</option>
                </select>
            </div>
        </div>       
        <div class="form-group">
            <label class="control-label col-sm-3">Name of Activity</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" class="form-control" ng-model="newActivity.data.name" aria-describedby="descActivityName"/>
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Presets <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                          <li ng-repeat="preset in formData.namePresets"><a ng-click="workflow.setName(preset)">@{{preset}}</a></li>
                        </ul>
                      </div><!-- /btn-group -->
                </div>
                <span id="descActivityName" class="help-block">Enter the exercise name, location name or common name for this activity. <strong>Don't include</strong> the activity type again; for example, Hampton 03-2015 <strike>Bivouac</strike>, Tuesday Night <strike>Parade</strike></span>
            </div>
        </div>
         <div class="form-group">
            <label class="control-label col-sm-3">Start Date</label>
            <div class="col-sm-6">
                <input type="date" class="form-control" ng-model="newActivity.data.start_date"/>
            </div>
            <div class="col-sm-3">
                <p class="form-control-static">@{{ newActivity.data.start_date | date:'EEEE' }}</p>
            </div>
        </div>    
        <div class="form-group" ng-show="!newActivity.data.is_half_day">
            <label class="control-label col-sm-3">End Date</label>
            <div class="col-sm-6">
                <input type="date" class="form-control" ng-model="newActivity.data.end_date"/>
            </div>
            <div class="col-sm-3">
                <p class="form-control-static">@{{ newActivity.data.end_date | date:'EEEE' }}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Half Day</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" ng-model="newActivity.data.is_half_day" ng-true-value="1" ng-false-value="0"/>
                        (activity is less than 6 hrs long)
                    </label>
                </div>
            </div>
        </div>
        <div class="text-right">
            <button type="button" class="btn btn-default" ng-click="submitOnly()">Submit only</button>
            <button type="button" class="btn btn-primary" ng-click="submitThenAddRoll()">Submit and add roll</button>
        </div>
    </div>
</form>
@endsection

@section('content')
	@yield('activity-form')
@endsection

@section('ng-script')
<script src="{{asset('ng-app/components/activity/flaresActivityNew.js')}}"></script>
@endsection