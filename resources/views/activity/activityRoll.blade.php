{{-- Roll for an activity --}} 
@extends('master')

@section('ng-app', 'flaresActivityMarkRoll')
@section('ng-controller', 'activityRollController')
@section('title', 'Activity roll')

@section('heading')
<!-- page main header -->
<div>
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
<section ng-show="state.path.tab == 'markroll'">
    <div class="roll-view-container" ng-show="roll.length === 0">
        <p class="fl-helptext">
            There are no members on the roll.
            <a ng-href="/activity/#!/@{{activity.acty_id}}/edit/rollbuilder" class="btn btn-default">Edit the nominal roll</a>
            </p>
    </div>
    <div class="roll-view-container" ng-show="roll.length > 0">
        <div class="row">
            <div class="col-xs-8 col-sm-9">
                <span class="roll-view-rank">Rank</span> 
                Surname, Initial
            </div>
        </div>
        <article class="roll-view">
            <div class="row" ng-repeat="rollEntry in roll">
                <div class="col-xs-8 col-sm-9">
                    <div class="roll-view-cell">
                        <span class="roll-view-rank">@{{rollEntry.data.member.current_rank.new_rank | markBlanks}}</span> 
                        @{{rollEntry.data.member.last_name}}, @{{rollEntry.data.member.first_name.substr(0, 1)}}
                    </div>
                </div>
                <div class="col-xs-4 col-sm-3 text-right">
                    <span ng-show="rollEntry.locked">
                        <a class="roll-view-cell roll-mark-action" ng-click="rollEntry.unlockRollEntry()"> 
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <span class="roll-view-cell roll-mark-value"> 
                            @{{rollEntry.data.recorded_value | rollDisplayValue }}
                        </span>
                    </span>
                    <span ng-show="!rollEntry.locked">
                        <a class="roll-view-cell roll-mark-selector noselect" ng-class="rollValueDisplayClass(rollEntry)" ng-click="rollEntry.scrollAttendance()">@{{rollEntry.scroller.selectedSymbol}}</a>
                        <a class="roll-view-cell roll-mark-action accept" ng-click="rollEntry.markAttendance()">
                            <span class="glyphicon glyphicon-ok"></span>
                        </a> 
                        <a class="roll-view-cell roll-mark-action reject" ng-click="rollEntry.cancelAttendanceChange()">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a> 
                    </span>
                </div>
            </div>
        </article>
        <div class="text-right">
            Your roll is autosaved. &nbsp;
            <button type="submit" class="btn btn-success" ng-click="showParadeState()">
                View Parade State <span class="glyphicon glyphicon-ok-circle"></span> 
            </button>
        </div>
    </div>
</section>
@endsection

@section('activity-paradeState')
<section ng-show="state.path.tab == 'paradestate'">
    <div class="roll-view-container" ng-show="roll.length === 0">
        <p class="fl-helptext">
            There are no members on the roll.
            <a ng-href="/activity/#!/@{{activity.acty_id}}/edit/rollbuilder" class="btn btn-default">Edit the nominal roll</a>
            </p>
    </div>
</section>
@endsection

@section('content')
@yield('activity-titleBlock')
@yield('activity-roll')
@yield('activity-paradeState')
@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityMarkRoll.js"></script>
@endsection