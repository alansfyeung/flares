{{-- Approve a decoration for a member --}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresDecorationApproval')
@section('ng-controller', 'memberApproveDecorationController')
@section('title', 'Approve - Decoration')

@push('scripts')
<script src="{{asset('ng-app/components/approval/flaresApproveDecoration.js')}}"></script>
@endpush

@section('heading')
<!-- page main header -->
<div ng-show="member.regt_num">
    <h1>
        <a ng-href="{{ url('/') }}@{{cancelHref()}}">Member</a>
        &rsaquo; Approve a decoration 
    </h1>
</div>
@endsection

@section('alerts')
<div ng-show="appr.saveError">
    <div class="alert alert-danger">
        <p><strong>Saving failed.</strong> We couldn't save this decoration approval.</p>
    </div>
</div>
<div ng-show="appr.saveDuplicateError">
    <div class="alert alert-warning">
        <p><strong>Saving failed.</strong> The selected decoration was already assigned. To assign this decoration, delete the existing entries from the member's profile.</p>
        <hr>
        <div>
            <a class="btn btn-default" ng-href="{{ url('/') }}@{{cancelHref()}}" target="_blank" tabindex="105">View member profile <span class="glyphicon glyphicon-share"></span></a>
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
[uib-typeahead-popup].dropdown-menu { display: block; }
</style>

<div ng-show="member.regt_num">

    <div class="row">
        <div class="col-sm-12">
            <header class="member-header">
                <h2>@{{member.last_name}}, @{{member.first_name}}</h2>
                <div class="subheading text-muted">Flares ID: @{{member.regt_num}} &nbsp; Forums username: @{{member.forums_username}}</div>
            </header>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">

            <div class="alert alert-success" ng-show="appr.saved">
                <div class="pull-right">
                    <a class="btn btn-default btn-xs" ng-href="{{ url('/') }}@{{cancelHref()}}" tabindex="103">Back to approvals</a>
                    <button id="viewApprovalDecision" type="button" class="btn btn-success btn-xs" ng-click="appr.saved = null" tabindex="104">View decision</button>
                </div>
                <p><strong>Saved.</strong> The decision was recorded</p>
            </div>
            
            <div class="text-muted">&hellip; has requested approval for</div>
            <h4>@{{appr.requestedDecoration.name}}</h4>
            <hr>

            <div ng-if="appr.isDecided">
                <div class="row form-group">
                    <div class="col-sm-3">Decision</div>
                    <div class="col-sm-9">
                        <p ng-show="appr.isApproved"><span class="glyphicon glyphicon-ok-sign text-success"></span> Approved</p>
                        <p ng-hide="appr.isApproved"><span class="glyphicon glyphicon-minus-sign text-danger"></span> Declined</p>
                        <p class="text-muted">Decision submitted on @{{appr.decisionDate | date:'shortDate'}} by @{{appr.decisionedBy.username}}</p>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-3">Date of award</div>
                    <div class="col-sm-9">@{{appr.submittedDate | date:'shortDate'}}</div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-3">Justification</div>
                    <div class="col-sm-9">
                        <p class="text-muted">@{{appr.justification | markBlanks}}</p>
                    </div>
                </div>

                <hr>

                <div class="row" display-mode="view">
                    <div class="col-sm-12">
                        <a class="btn btn-default " ng-href="{{ url('/') }}@{{cancelHref()}}" tabindex="101">Back to approvals</a>
                    </div>
                </div>

                <div class="alert alert-warning" display-mode="edit">
                    Sorry, you cannot change an approval decision after it has been submitted. 
                    You can always edit or award a new decoration to a member.    
                </div>

            </div>
            <div ng-if="!appr.isDecided">
                <form class="form-horizontal" ng-submit="submit()" autocomplete="off" display-mode="edit">
                    <fieldset>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Date of award</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <select class="form-control" ng-model="formData.awardDate.month" ng-options="month.value as month.name for month in formData.months" tabindex="10"></select> 
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control" ng-model="formData.awardDate.year" type="number" tabindex="11">
                                    </div>
                                </div>
                                <span class="help-block">You can override the date that was provided by the requester.</span>
                                <input type="date" class="form-control hidden" ng-model="appr.data.date" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Citation for award</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" ng-model="appr.data.citation" placeholder="Why did the recipient receive this award? (optional)" rows="4" tabindex="12"></textarea>
                                <span class="help-block">This citation will appear on the awarded decoration. </span>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Decision</label>
                            <div class="col-sm-9">
                                <label class="radio-inline"><input type="radio" name="approvalDecision" ng-model="formData.approvalDecision" value="yes"> Yes</label>
                                <label class="radio-inline"><input type="radio" name="approvalDecision" ng-model="formData.approvalDecision" value="no"> No</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Justification</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" ng-model="appr.data.justification" placeholder="Reason for decision (optional for approved)" rows="4" tabindex="12"></textarea>
                                <span class="help-block">It is mandatory to provide a justification if rejecting a decoration request.</span>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="form-group">
                        <div class="col-sm-12" ng-show="appr.validationError">
                            <p class="text-right text-danger">@{{appr.validationError}}</p>
                        </div>
                        <div class="col-sm-12">
                            <div class="text-left">
                                <a class="btn btn-default " ng-href="{{ url('/') }}@{{cancelHref()}}" tabindex="101">Cancel</a>
                                <button class="btn btn-primary pull-right" type="submit" tabindex="100">Submit decision</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <div class="col-sm-4">
    
            <div class="panel panel-default" ng-show="appr.requestedDecoration.dec_id">
                <div class="panel-heading">@{{appr.requestedDecoration.name}}</div>
                <div class="panel-body">
                    <div class="thumbnail fl-record-thumbnail">
                        <img ng-src="@{{appr.requestedDecorationBadgeUrl}}" alt="@{{appr.requestedDecoration.name}}" class="image-rounded memberview-thumb">
                    </div>
                    <div class="fl-record-caption">
                        <p>@{{appr.requestedDecoration.desc}}</p>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <tr>
                            <td>Shortcode</td>
                            <td>
                                <code>@{{appr.requestedDecoration.shortcode}}</code> &nbsp;
                                <a target="_blank" ng-href="{{ route('public::decorationDetails', [ 'shortcode' => '' ]) }}/@{{appr.requestedDecoration.shortcode}}">
                                    <span class="glyphicon glyphicon-new-window"></span>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Tier/precedence</td>
                            <td><span class="label label-tier">@{{appr.requestedDecoration.tier}}</span> &middot; #@{{appr.requestedDecoration.precedence}}</td>
                        </tr>
                        <tr>
                            <td>Min service period</td>
                            <td class="text-muted">@{{appr.requestedDecoration.service_period_months | markBlanks}} months</td>
                        </tr>
                        <tr>
                            <td>Awarded from/until</td>
                            <td class="text-muted">
                                @{{appr.requestedDecoration.date_commence | markBlanks | date:'MMM yyyy'}} until 
                                @{{appr.requestedDecoration.date_conclude | markBlanks | date:'MMM yyyy'}}
                            </td>
                        </tr>
                        <tr>
                            <td>Awarding authority</td>
                            <td class="text-muted">@{{appr.requestedDecoration.authorized_by | markBlanks}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        
        </div>
    </div>
    
</div>
@endsection
