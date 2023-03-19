{{-- Activity log, for insertion into dashboard --}}
<section id="dashboardActivityLog" ng-if="state.activityLogLoaded">
    <table class="table table-hover" ng-show="activityLog">
        <colgroup>
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 100px;">
            <col>
        </colgroup>
        <thead>
            <tr>
                <th>Type</th>
                <th>Date</th>
                <th>Outcome</th>
                <th>Text</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="entry in activityLog" class="dashboard-approval-row" ng-click="navToLog(entry.log_type, entry.log_id)">
                <td>@{{entry.log_type}}</td>
                <td>@{{entry.log_date | date:'shortDate'}}</td>
                <td>@{{entry.log_outcome}}</td>
                <td><small>@{{entry.log_text}}</small></td>
            </tr>
        </tbody>
    </table>

    <hr>
    <div class="row">
        <div class="col-sm-6"><button class="btn btn-default" ng-click="loadMoreLog()">Load more</button></div>
        <div class="col-sm-6 text-right text-muted">@{{state.activityLogCounter}} log entries loaded</div>
    </div>

</section>
<!-- <div class="alert alert-warning" ng-hide="state.activityLoaded">Activity Log TBC</div> -->