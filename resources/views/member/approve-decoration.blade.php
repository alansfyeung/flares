{{-- Approve a decoration for a member --}}
@extends('layouts.base')

@section('ng-app', 'flaresMemberApproveDecoration')
@section('ng-controller', 'memberApproveDecorationController')
@section('title', 'Approve - Decoration')

@push('scripts')
<script src="/ng-app/components/member/flaresMemberApproveDecoration.js"></script>
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
        <h3>Saving failed</h3>
        <hr>
        <p>We couldn't save this decoration approval.</p>
    </div>
</div>
<div ng-show="appr.saveDuplicateError">
    <div class="alert alert-warning">
        <h3>Saving failed</h3>
        <p>The selected decoration was already assigned. To assign this decoration, delete the existing entries from the member's profile.</p>
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
                <h3><strong>Saved.</strong> The decision was recorded.</h3>
                <hr>
                <div class="text-right">
                    <button id="approveAnotherDecorationRequest" type="button" class="btn btn-success" ng-click="approveAnother()" tabindex="103">Back to approvals</button>
                    <a class="btn btn-default" ng-href="{{ url('/') }}@{{cancelHref()}}" tabindex="104">View member profile</a>
                </div>
            </div>
            
            <div class="text-muted">&hellip; has requested approval for</div>
            <h4>@{{appr.requestedDecoration.name}}</h4>
            <hr>

            <div ng-show="appr.isDecided">
                <div class="form-group">
                    <div class="col-sm-3">Decision</div>
                    <div class="col-sm-9">
                        <p ng-show="appr.isApproved"><span class="glyphicon glyphicon-ok-sign text-success"></span> Approved</p>
                        <p ng-hide="appr.isApproved"><span class="glyphicon glyphicon-minus-sign text-danger"></span> Declined</p>
                        <p>Decision submitted on @{{appr.decisionDate}} by @{{appr.decisionedBy.username}}</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3">Date submitted</div>
                    <div class="col-sm-9">@{{appr.submittedDate}}</div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3">Justification</div>
                    <div class="col-sm-9">
                        <p class="text-muted">@{{appr.justification}}</p>
                    </div>
                </div>

                <hr>

                <div class="alert alert-warning" display-mode="edit">
                    Sorry, you cannot change an approval decision after it has been submitted. 
                    You can always edit or award a new decoration to a member.    
                </div>

            </div>
            <div ng-hide="appr.isDecided">
                <form class="form-horizontal" ng-submit="submit()" autocomplete="off" display-mode="edit">
                    <fieldset>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Date for award</label>
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
                                <input type="date" class="form-control hidden" ng-model="award.data.date" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Citation for award</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" ng-model="award.data.citation" placeholder="Why did the recipient receive this award? (optional)" rows="4" tabindex="12"></textarea>
                                <span class="help-text">This citation will appear on the awarded decoration. </span>
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
                                <textarea class="form-control" ng-model="award.data.justification" placeholder="Reason for decision (optional for approved)" rows="4" tabindex="12"></textarea>
                                <span class="help-block">It is mandatory to provide a justification if rejecting a decoration request.</span>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="form-group">
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
                        <img ng-src="@{{award.requestedDecorationBadgeUrl}}" alt="@{{award.name}}" class="image-rounded memberview-thumb">
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
                                <a target="_blank" ng-href="{{ route('public::decoration-details', [ 'shortcode' => '' ]) }}/@{{appr.requestedDecoration.shortcode}}">
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
