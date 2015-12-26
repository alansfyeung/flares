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
@endsection

@section('activity-roll')
<section>
    <form class="form-horizontal" name="contextForm" ng-submit="submitOnly()">
        <div class="roll-view" ng-show="roll.length === 0">
            <p class="fl-helptext">
                There are no members on the roll.
                <a ng-href="/activity/#!/@{{activity.acty_id}}/edit/rollbuilder" class="btn btn-default">Edit the nominal roll</a>
                </p>
        </div>
        <div class="roll-view" ng-show="roll.length > 0">
            <div class="row">
                <div class="col-xs-9 col-sm-10">
                    <span class="roll-view-rank">Rank</span> 
                    Surname, Initial
                </div>
            </div>
            <div class="row" ng-repeat="rollEntry in roll">
                <div class="col-xs-9 col-sm-10">
                    <div class="roll-view-cell">
                        <span class="roll-view-rank">@{{rollEntry.data.member.rank | markBlanks}}</span> 
                        @{{rollEntry.data.member.last_name}}, @{{rollEntry.data.member.first_name.substr(0, 1)}}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-2 text-right">
                    <a class="roll-view-cell roll-mark-value" ng-class="rollValueDisplayClass(rollEntry)" ng-click="scrollAttendance(rollEntry)">@{{rollEntry.data.recorded_value | rollDisplayValue }}</a>    
                    <a class="roll-view-cell roll-mark-action" ng-show="rollEntry.locked" ng-click="unlockRollEntry()"><span class="glyphicon glyphicon-edit"></span></a> 
                    <a class="roll-view-cell roll-mark-action" ng-show="!rollEntry.locked" ng-click="lockRollEntry()"><span class="glyphicon glyphicon-ok"></span></a> 
                </div>
            </div>
        </div>
        <div class="text-right">
            Your roll is autosaved. &nbsp;
            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> View Parade State</button>
        </div>
    </form>
</section>
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