{{-- Approval list for all decorations, for insertion into dashboard --}}
<section id="dashboardApprovals" ng-if="state.approvalsLoaded">
    <div class="alert alert-success" ng-hide="state.approvalsRemaining">
        <strong>No pending approvals.</strong> All requests have been processed.
    </div>

    <header class="dashboard-tabcontent-header" ng-show="state.approvalsRemaining">
        <div class="pull-right">
            <!-- <span class="glyphicon glyphicon-th-list"></span> -->
            <span class="badge" ng-show="approvals.length > 0"> @{{approvals.length}}</span>
            <span ng-hide="approvals.length > 0">No</span>
            <span>approval<span ng-hide="approvals.length == 1">s</span> pending</span>
        </div>
        <h4>Pending requests</h4>
    </header>
    
    <table class="table table-hover" ng-show="state.approvalsRemaining">
        <colgroup>
            <col style="width: 200px;">
            <col>
            <col style="width: 140px;">
            <col style="width: 40px;">
        </colgroup>
        <thead>
            <tr>
                <th>Requester</th>
                <th>Decoration</th>
                <th>Date lodged</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="appr in approvals" class="dashboard-approval-row" ng-click="goToSingleApproval(appr)">
                <td title="Forums username: @{{appr.requester.forums_username}}">@{{appr.requester.last_name}}, @{{appr.requester.first_name}}</td>
                <td>@{{appr.requested_decoration.name}}</td>
                <td>@{{appr.created_at | date:'shortDate'}} (@{{appr.created_at | timeAgo}})</td>
                <td>
                    <span class="btn btn-default btn-block btn-xs" ng-click="openSingleApprovalInWindow(appr, $event)">
                        <span class="glyphicon glyphicon-new-window text-muted"></span>
                    </span>
                    <!-- <a class="btn btn-default btn-block btn-xs" target="_blank" ng-click="$event.stopPropagation()"
                        ng-href="{{ route('approval::approveDecoration') }}#!/@{{appr.dec_appr_id}}/edit">
                        <span class="glyphicon glyphicon-new-window text-muted"></span>
                    </a> -->
                </td>
            </tr>
        </tbody>
    </table>
</section>
<div class="alert alert-info" ng-hide="state.approvalsLoaded">Loading pending approvals&hellip;</div>