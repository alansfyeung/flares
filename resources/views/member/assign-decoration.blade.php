{{-- Assign a decoration to a member --}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresMemberDecoration')
@section('ng-controller', 'memberAssignDecorationController')
@section('title', 'Assign - Decoration')

@push('scripts')
<script src="{{asset('ng-app/components/member/flaresMemberAssignDecoration.js')}}"></script>
@endpush

@section('heading')
<!-- page main header -->
<div ng-show="member.regt_num">
    <h1>
        <a ng-href="@{{cancelHref()}}">Member</a>
        &rsaquo; Award a decoration
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
<div ng-show="award.saveDuplicateError">
    <div class="alert alert-warning">
        <h3>Saving failed</h3>
        <p>The selected decoration was already assigned. To assign this decoration, delete the existing entries from the member's profile.</p>
        <hr>
        <div>
            <a class="btn btn-default" ng-href="@{{cancelHref()}}" target="_blank" tabindex="105">View member profile <span class="glyphicon glyphicon-share"></span></a>
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
[uib-typeahead-popup].dropdown-menu { display: block; }
</style>

<div ng-show="member.regt_num">

    <header class="member-header">
        <h2>@{{member.last_name}}, @{{member.first_name}}</h2>
        <div class="subheading text-muted">Flares ID: @{{member.regt_num}} &nbsp; Forums username: @{{member.forums_username}}</div>
    </header>
    <hr>
    
    <div class="row" ng-show="award.saved">
        <div class="col-sm-12 col-md-6">
            <div class="alert alert-success">
                <h3><strong>Saved</strong>: The decoration was applied</h3>
                <hr>
                <div>
                    <button id="assignAnotherDecorationButton" type="button" class="btn btn-success" ng-click="assignAnother()" tabindex="103">Assign another</button>
                    <a class="btn btn-default" ng-href="@{{cancelHref()}}" tabindex="104">View member profile</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row" ng-hide="award.saved">
        <div class="col-sm-8">
            <form class="form-horizontal" ng-submit="submit()" autocomplete="off">
                <fieldset>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Decoration Tier</label>
                        <div class="col-sm-9">
                            <select class="form-control" ng-options="tier.tierName for tier in formData.decorationTiers" ng-model="selectedTier" tabindex="5">
                                <option value="">Show all</option>
                            </select>
                            <pre class="hidden">@{{selectedTier}}</pre>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Decoration to assign</label>
                        <div class="col-sm-9">
                            <input ng-hide="state.showDecorationDropdownList" 
                                id="selectedDecorationField" 
                                type="text" 
                                class="form-control" 
                                ng-model="award.selectedDecoration" 
                                placeholder="Search for a decoration" 
                                uib-typeahead="dec as dec.name for dec in decorations | filter: (selectedTier ? { tier: selectedTier.tier } : undefined) | filter:{ name:$viewValue }" 
                                typeahead-template-url="decorationTypeaheadTemplate.html" 
                                typeahead-popup-template-url="ng-app/components/decoration/decorationTypeaheadPopupTemplate.html" 
                                typeahead-show-hint="true" 
                                typeahead-min-length="0" 
                                tabindex="6">
                            <select ng-show="state.showDecorationDropdownList" 
                                class="form-control" 
                                ng-options="dec as dec.name for dec in decorations | filter:(selectedTier ? { tier: selectedTier.tier } : undefined) track by dec.dec_id" 
                                ng-model="award.selectedDecoration"
                                tabindex="7">
                                <option value="">Choose a decoration</option>
                            </select>
                            <p class="help-block"> 
                                <a ng-show="state.showDecorationDropdownList" ng-click="state.showDecorationDropdownList = false" tabindex="8">Show lookup field</a>
                                <a ng-hide="state.showDecorationDropdownList" ng-click="state.showDecorationDropdownList = true" tabindex="9">Show dropdown list</a>
                            </p>
                        </div>
                    </div>
                </fieldset>

                <fieldset ng-show="award.selectedDecoration.dec_id">
                    <hr>
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
                            <textarea class="form-control" ng-model="award.data.citation" placeholder="Why did the recipient receive this award? (optional)" rows="4" tabindex="12"></textarea>
                        </div>
                    </div>
                    <div class="form-group">

                    </div>
                </fieldset>
                
                <hr>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="text-right">
                            <a class="btn btn-default pull-left" ng-href="@{{cancelHref()}}" tabindex="101">Back to Member</a>
                            <button ng-show="award.selectedDecoration.dec_id" class="btn btn-primary" type="submit" tabindex="100">Award this decoration</button>
                        </div>
                    </div>
                </div>
            
            </form>
            
            
        </div>
        <div class="col-sm-4">
    
            <div class="panel panel-default" ng-show="award.selectedDecoration.dec_id">
                <div class="panel-heading">@{{award.selectedDecoration.name}}</div>
                <div class="panel-body">
                    <div class="thumbnail fl-record-thumbnail">
                        <img ng-src="@{{award.selectedDecorationBadgeUrl}}" alt="@{{award.name}}" class="image-rounded memberview-thumb">
                    </div>
                    <div class="fl-record-caption">
                        <p>@{{award.selectedDecoration.desc}}</p>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <tr>
                            <td>Shortcode</td>
                            <td>
                                <code>@{{award.selectedDecoration.shortcode}}</code> &nbsp;
                                <a target="_blank" ng-href="{{ route('public::decorationDetails', [ 'shortcode' => '' ]) }}/@{{award.selectedDecoration.shortcode}}">
                                    <span class="glyphicon glyphicon-new-window"></span>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Tier/precedence</td>
                            <td><span class="label label-tier">@{{award.selectedDecoration.tier}}</span> &middot; #@{{award.selectedDecoration.precedence}}</td>
                        </tr>
                        <tr>
                            <td>Min service period</td>
                            <td class="text-muted">@{{award.selectedDecoration.service_period_months | markBlanks}} months</td>
                        </tr>
                        <tr>
                            <td>Awarded from/until</td>
                            <td class="text-muted">
                                @{{award.selectedDecoration.date_commence | markBlanks | date:'MMM yyyy'}} until 
                                @{{award.selectedDecoration.date_conclude | markBlanks | date:'MMM yyyy'}}
                            </td>
                        </tr>
                        <tr>
                            <td>Awarding authority</td>
                            <td class="text-muted">@{{award.selectedDecoration.authorized_by | markBlanks}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        
        </div>
    </div>
    
</div>
@endsection
