{{-- Approval list for all decorations, for insertion into dashboard --}}
<section id="dashboardApprovals" ng-if="state.approvalsLoaded">
    <div class="alert alert-success" ng-hide="state.approvalsRemaining">
        <strong>No pending approvals.</strong> All requests have been processed.
    </div>

    <div ng-show="state.approvalsRemaining">
        <p class="pull-right text-muted">
            <span class="glyphicon glyphicon-th-list"></span> @{{approvals.length}} approval(s) pending
        </p>
        <h4>Pending decoration requests</h4>
    </div>
    
    <table class="table table-hover" ng-show="state.approvalsRemaining">
        <colgroup>
            <col>
            <col>
            <col style="width: 100px;">
            <col style="width: 40px;">
        </colgroup>
        <thead>
            <tr>
                <th>Decoration</th>
                <th>Requester</th>
                <th>Date lodged</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="appr in approvals" class="dashboard-approval-row" ng-click="selectApproval(appr)">
                <td>@{{appr.requested_decoration.name}}</td>
                <td title="@{{appr.requester.forums_username}}">@{{appr.requester.last_name}}, @{{appr.requester.first_name}}</td>
                <td>@{{appr.created_at | date:'shortDate'}}</td>
                <td>
                    <a class="btn btn-default btn-block btn-xs" target="_blank" ng-click="$event.stopPropagation()"
                        ng-href="{{ route('approval::approve-decoration') }}#!/@{{appr.dec_appr_id}}/edit/details">
                        <span class="glyphicon glyphicon-share text-muted"></span>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</section>
<div class="alert alert-info" ng-hide="state.approvalsLoaded">Loading pending approvals&hellip;</div>