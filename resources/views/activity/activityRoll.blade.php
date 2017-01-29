{{-- Roll for an activity --}} 
@extends('layouts.primary')

@section('ng-app', 'flaresActivityMarkRoll')
@section('ng-controller', 'activityRollController')
@section('title', 'Activity roll')

@section('heading')
<!-- page main header -->
<div>
    <aside class="title-actions pull-right">
        <span uib-dropdown>
            <a class="btn btn-link" uib-dropdown-toggle>
                <span class="glyphicon glyphicon-option-vertical"></span>
            </a>
            <div class="list-group uib-dropdown-menu dropdown-menu-right">
                <a class="list-group-item" ng-href="@{{ actions.editActivity() }}">Edit activity</a>
                <a class="list-group-item" ng-click="actions.leave()">Record leave</a>
                <a class="list-group-item" ng-click="actions.reviewAwols()">Review all AWOLs</a>
            </div>
        </span>
	</aside>
	<h1>Activity run sheet &rsaquo; @{{ breadcrumbTabTitle() }}</h1>
</div>
@endsection

@section('activity-titleBlock')
<div class="row">
    <div class="col-xs-12">
	   <h2>@{{activity.type}} &rsaquo; @{{activity.name}}<br><small style="display: inline-block">@{{activity.start_date | date:'fullDate'}}</small></h2>
    </div>
</div>
@endsection

@section('activity-roll')
<div role="tabpanel" id="markroll" class="tab-pane active">
    <section class="roll-view-container">
        <div ng-show="roll.length === 0">
            <p class="fl-helptext">
                There are no members on the roll.
                <a ng-href="/activity/#!/@{{activity.acty_id}}/edit/rollbuilder" class="btn btn-default">Edit the nominal roll</a>
            </p>
        </div>
        <div ng-show="roll.length > 0">
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
                            <span class="roll-view-rank">@{{ rollEntry.data.member.current_rank.new_rank | markBlanks }}</span> 
                            @{{ rollEntry.data.member.last_name.toUpperCase() }}, @{{ rollEntry.data.member.first_name.substr(0, 1) }}
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-3 text-right">
                        <span ng-show="rollEntry.locked">
                            <a class="roll-view-cell roll-mark-action" ng-click="rollEntry.unlockRollEntry()"> 
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <span class="roll-view-cell roll-mark-value" ng-class="{'saving': rollEntry.saving}"> 
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
            <div class="text-center">
            
                <div ng-controller="activityParadeStateController">
                    <p class="fl-helptext">@{{ totalNumbers.posted || 0 }} on the activity roll, @{{ totalNumbers.present || 0 }} present, @{{ totalNumbers.leaveOrSick || 0 }} leave or sick, @{{ totalNumbers.awol || 0 }} AWOL </p>
                </div>
                
                <span ng-show="!state.isRollUnsaved">Your roll is autosaved.</span><span ng-show="state.isRollUnsaved">Save pending &hellip;</span> &nbsp;
                <button type="submit" class="btn btn-success" ng-click="showParadeState()">
                    View Parade State <span class="glyphicon glyphicon-ok-circle"></span> 
                </button>
                
            </div>
        </div>
    </section>
</div>
@endsection

@section('activity-paradeState')
<div role="tabpanel" id="paradestate" class="tab-pane">
    <section ng-controller="activityParadeStateController" class="paradestate-container">
        <form>
            <h3>Attendance</h3>
            <table class="table table-bordered">
                <colgroup>
                    <col style="width: 30%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Sub-unit</th>
                        <th class="text-center">Posted</th>
                        <th class="text-center">Leave</th>
                        <th class="text-center">Sick</th>
                        <th class="text-center">AWOL</th>
                        <th class="text-center">Present</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="plt in refData.relevantPlatoons()">
                        <td>@{{ plt.name }} (@{{plt.abbr}}) </td>
                        <td class="text-center">@{{ numbers[plt.abbr].posted || '--' }}</td>
                        <td class="text-center">@{{ numbers[plt.abbr].leave || '--' }}</td>
                        <td class="text-center">@{{ numbers[plt.abbr].sick || '--' }}</td>
                        <td class="text-center">@{{ numbers[plt.abbr].awol || '--' }}</td>
                        <td class="text-center">@{{ numbers[plt.abbr].present || '--' }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Company</th>
                        <th class="text-center">@{{ totalNumbers.posted || '--' }}</th>
                        <th class="text-center">@{{ totalNumbers.leave || '--' }}</th>
                        <th class="text-center">@{{ totalNumbers.sick || '--' }}</th>
                        <th class="text-center">@{{ totalNumbers.awol || '--' }}</th>
                        <th class="text-center">@{{ totalNumbers.present || '--' }}</th>
                    </tr>
                </tfoot>
            </table>
            
            <h3>Non-present</h3>
            <table class="table table-bordered">
                <colgroup>
                    <col style="width: 30%;">
                </colgroup>
                <tbody>
                    <tr>
                        <td>Leave</td>
                        <td>@{{ nonPresentList['L'].join(', ') }}</td>
                    </tr>
                    <tr>
                        <td>Sick</td>
                        <td>@{{ nonPresentList['S'].join(', ') }}</td>
                    </tr>
                    <tr>
                        <td>AWOL</td>
                        <td>@{{ nonPresentList['A'].join(', ') }}</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>Future leave</h3>

            <h3>Past leave</h3>
            
        </form>
        <div class="text-right">
            <button type="button" class="btn btn-default">Save a copy</button>
            <button type="button" class="btn btn-default">Print</button>
        </div>
    </section>
</div>
@endsection

@section('activity-awols')
<div role="tabpanel" id="awols" class="tab-pane">
    <section ng-controller="activityParadeStateController">
        <h3>AWOL for this activity</h3>
        <table class="table table-bordered">
            <colgroup>
                <col style="width: 40%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="">
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </section>
</div>
@endsection

@section('content')
@yield('activity-titleBlock')
<div class="row">
    <div class="fl-content col-sm-12">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a bs-show-tab href="#markroll" aria-controls="markroll" role="tab">Mark roll</a></li>
            <li role="presentation"><a bs-show-tab href="#paradestate" aria-controls="paradestate" role="tab">Parade State</a></li>
            <li role="presentation"><a bs-show-tab href="#awols" aria-controls="awols" role="tab">AWOLs</a></li>
        </ul>
        <div class="tab-content">
            @yield('activity-roll')
            @yield('activity-paradeState')
            @yield('activity-awols')
        </div>
    </div> 
</div>




@endsection

@section('ng-script')
<script src="/app/components/activity/flaresActivityMarkRoll.js"></script>
@endsection