{{-- Display a single member --}}
@extends('layouts.base')

@section('ng-app', 'flaresMemberDecorationViewEdit')
@section('ng-controller', 'memberEditDecorationController')
@section('title', 'Edit Awarded Decoration')

@push('scripts')
<script src="/ng-app/components/member/flaresMemberEditDecoration.js"></script>
@endpush

@section('heading')
<!-- page main header -->
<div>
    <h1>
        <a ng-href="{{ url('/') }}@{{cancelHref()}}">Member</a>
        &rsaquo;
        Edit an awarded decoration
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
        <div class="col-sm-8">
            <div>
                <h2>@{{member.last_name}}, @{{member.first_name}} <small style="display: inline-block">&diams; @{{member.regt_num}}</small></h2>
                <div>
                    <div class="thumbnail fl-record-thumbnail" style="vertical-align: middle;">
                        <img ng-src="@{{award.existingDecorationBadgeUrl}}" alt="@{{award.name}}" class="image-rounded memberview-thumb">
                    </div> &nbsp;
                    <h4 style="display: inline-block;">@{{award.existingDecoration.name}} <span class="label label-info">@{{award.existingDecoration.tier}}</span></h4>            
                </div>
            </div>
            
            <hr>
            
            <div class="alert alert-success" ng-show="award.saved">
                <a id="viewMemberProfileButton" class="btn btn-success btn-sm pull-right" ng-href="{{ url('/') }}@{{cancelHref()}}" tabindex="104">View member profile</a>
                <p class="lead"><strong>Saved</strong>: The decoration details were saved</p>
            </div>
    
            <form class="form-horizontal" ng-submit="submit()" autocomplete="off" display-mode="edit">
                <fieldset ng-show="award.existingDecoration.dec_id">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Date awarded</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6">
                                    <select class="form-control" ng-model="formData.awardDate.month" ng-options="month.value as month.name for month in formData.months" tabindex="10"></select> 
                                </div>
                                <div class="col-sm-6">
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
                            <a class="btn btn-default hidden" ng-href="{{ url('/') }}@{{cancelHref()}}" tabindex="101" ng-disabled="state.isSaving">Back to Member</a>
                            <button ng-show="award" class="btn btn-primary" type="submit" tabindex="100" ng-disabled="state.isSaving">Save award details</button>
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
                <div class="panel-heading">Award info</div>
                <div class="table-wrapper">
                    <table class="table">
                        <tr>
                            <td>Member awarded date</td>
                            <td>@{{award.data.date | date:'MMM yyyy'}}</td>
                        </tr>
                        <tr>
                            <td>Record created on</td>
                            <td>@{{award.data.created_at | date:'short'}}</td>
                        </tr>
                        <tr>
                            <td>Record last updated on</td>
                            <td>@{{award.data.updated_at | date:'short'}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Decoration info</div>
                <div class="table-wrapper">
                    <table class="table">
                        <tr>
                            <td>Decoration min service period</td>
                            <td>@{{award.existingDecoration.service_period_months | markBlanks}} months</td>
                        </tr>
                        <tr>
                            <td>Decoration commencement</td>
                            <td>@{{award.existingDecoration.date_commence | markBlanks | date:'MMM yyyy'}}</td>
                        </tr>
                        <tr>
                            <td>Decoration conclusion</td>
                            <td>@{{award.existingDecoration.date_conclude | markBlanks | date:'MMM yyyy'}}</td>
                        </tr>
                        <tr>
                            <td>Decoration authority</td>
                            <td>@{{award.existingDecoration.authorized_by | markBlanks}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
