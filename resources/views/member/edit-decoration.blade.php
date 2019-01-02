{{-- Edit a member's existing decoration --}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresMemberDecoration')
@section('ng-controller', 'memberEditDecorationController')
@section('title', 'Edit - Decoration')

@push('scripts')
<script src="{{asset('ng-app/components/member/flaresMemberEditDecoration.js')}}"></script>
@endpush

@section('heading')
<!-- page main header -->
<div>
    <h1>
        <a ng-href="@{{cancelHref()}}">Member</a>
        &rsaquo; Edit an awarded decoration
    </h1>
</div>
@endsection

@section('alerts')
<div ng-show="award.saveError">
    <div class="alert alert-danger">
        <h3>Saving failed</h3>
        <hr>
        <p>We couldn't save this decoration.</p>
    </div>
</div>
@endsection

@section('content')
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
            <div class="alert alert-success" ng-show="award.saved">
                <a id="viewMemberProfileButton" class="btn btn-success btn-xs pull-right" ng-href="@{{cancelHref()}}" tabindex="104">View member profile</a>
                <p><strong>Saved</strong>: The decoration updates were saved</p>
            </div>
            
            <div class="text-muted">&hellip; was awarded</div>
            <h4>@{{award.existingDecoration.name}}</h4>
            
            <hr>
            <form class="form-horizontal" ng-submit="submit()" autocomplete="off" display-mode="edit">
                <fieldset>
                    <div class="form-group">
                        <div class="col-sm-3">Award date</div>
                        <div class="col-sm-9">@{{award.data.date | date:'MMM yyyy'}} <span class="text-muted">(selected when awarding)</span></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">Record created on</div>
                        <div class="col-sm-9">@{{award.data.created_at | date:'short'}}</div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">Record last updated on</div>
                        <div class="col-sm-9">@{{award.data.updated_at | date:'short'}}</div>
                    </div>
                </fieldset>
                <hr>
                <fieldset ng-show="award.existingDecoration.dec_id">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Date awarded</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-4">
                                    <select class="form-control" ng-model="formData.awardDate.month" ng-options="month.value as month.name for month in formData.months" tabindex="10"></select> 
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control" ng-model="formData.awardDate.year" type="number" tabindex="11">
                                </div>
                            </div>
                            <input type="date" class="form-control hidden" ng-model="award.data.date" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Citation</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" ng-model="award.data.citation" placeholder="Provide a short citation for why the recipient received this award (optional)." rows="4" tabindex="12"></textarea>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="text-right">
                            <img ng-show="state.isSaving" src="{{ asset('assets/img/spinner.gif') }}" alt="Working...">
                            <a class="btn btn-default hidden" ng-href="@{{cancelHref()}}" tabindex="101" ng-disabled="state.isSaving">Back to Member</a>
                            <button ng-show="award" class="btn btn-primary" type="submit" tabindex="100" ng-disabled="state.isSaving">Update award details</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="form-group" display-mode="view">
                <div class="text-right">
                    <button class="btn btn-default" tabindex="100" ng-click="state.path.mode = 'edit'">Edit again</button>                
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">@{{award.existingDecoration.name}}</div>
                <div class="panel-body">
                    <div class="thumbnail fl-record-thumbnail">
                        <img ng-src="@{{award.existingDecorationBadgeUrl}}" alt="@{{award.name}}" class="image-rounded memberview-thumb">
                    </div>
                    <div class="fl-record-caption">
                        <p>@{{award.existingDecoration.desc}}</p>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <tr>
                            <td>Shortcode</td>
                            <td>
                                <code>@{{award.existingDecoration.shortcode}}</code> &nbsp;
                                <a target="_blank" ng-href="{{ route('public::decorationDetails', [ 'shortcode' => '' ]) }}/@{{award.existingDecoration.shortcode}}">
                                    <span class="glyphicon glyphicon-new-window"></span>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Tier/precedence</td>
                            <td><span class="label label-tier">@{{award.existingDecoration.tier}}</span> &middot; #@{{award.existingDecoration.precedence}}</td>
                        </tr>
                        <tr>
                            <td>Min service period</td>
                            <td>@{{award.existingDecoration.service_period_months | markBlanks}} months</td>
                        </tr>
                        <tr>
                            <td>Awarded from/until</td>
                            <td>
                                @{{award.existingDecoration.date_commence | markBlanks | date:'MMM yyyy'}} until 
                                @{{award.existingDecoration.date_conclude | markBlanks | date:'MMM yyyy'}}
                            </td>
                        </tr>
                        <tr>
                            <td>Awarding authority</td>
                            <td>@{{award.existingDecoration.authorized_by | markBlanks}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
