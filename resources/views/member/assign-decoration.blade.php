{{-- Display a single member --}}
@extends('layouts.primary')

@section('ng-app', 'flaresMemberAssignDecoration')
@section('ng-controller', 'memberAssignDecorationController')
@section('title', 'Decoration Management')

@push('scripts')
<script src="/app/components/member/flaresMemberAssignDecoration.js"></script>
@endpush

@section('heading')
<!-- page main header -->
<div ng-show="member.regt_num">
    <h1>Award a decoration</h1>
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
            <a class="btn btn-default" ng-href="{{ url('/') }}@{{cancelHref()}}" target="_blank">View member profile <span class="glyphicon glyphicon-share"></span></a>
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
[uib-typeahead-popup].dropdown-menu {
  display: block;
}
</style>

<div ng-show="member.regt_num">

    <h2>
        @{{member.last_name}}, @{{member.first_name}} &nbsp;
        <br>
        <small style="display: inline-block">&diams; @{{member.regt_num}}</small>
    </h2>
    <hr>
    
    <div class="row" ng-show="award.saved">
        <div class="col-sm-12 col-md-6">
            <div class="alert alert-success">
                <h3><strong>Saved</strong>: The decoration was applied</h3>
                <hr>
                <div>
                    <button type="button" class="btn btn-success" ng-click="assignAnother()">Assign another</button>
                    <a class="btn btn-default" ng-href="{{ url('/') }}@{{cancelHref()}}">View member profile</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row" ng-hide="award.saved">
        <div class="col-sm-8">
            <form class="form-horizontal" ng-submit="submit()">
                <fieldset>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Decoration Tier</label>
                        <div class="col-sm-9">
                            <select class="form-control" ng-options="tier.tierName for tier in formData.decorationTiers" ng-model="selectedTier">
                                <option value="">All</option>
                            </select>
                            <pre class="hidden">@{{selectedTier}}</pre>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Decoration to assign</label>
                        <div class="col-sm-9">
                            <input ng-hide="showDecorationDropdownList" id="selectedDecorationField" type="text" class="form-control" ng-model="award.selectedDecoration" placeholder="Search for a decoration" uib-typeahead="dec as dec.name for dec in decorations | filter: (selectedTier ? { tier: selectedTier.tier } : undefined) | filter:{ name:$viewValue }" typeahead-template-url="decorationTypeaheadTemplate.html" typeahead-popup-template-url="/app/components/decoration/decorationTypeaheadPopupTemplate.html" typeahead-show-hint="true" typeahead-min-length="0">
                            <select ng-show="showDecorationDropdownList" class="form-control" ng-options="dec as dec.name for dec in decorations | filter:(selectedTier ? { tier: selectedTier.tier } : undefined) track by dec.dec_id" ng-model="award.selectedDecoration">
                                <option value="">Choose a decoration</option>
                            </select>
                            <p class="help-block"> 
                                <a ng-show="showDecorationDropdownList" ng-click="showDecorationDropdownList = false">Show lookup field</a>
                                <a ng-hide="showDecorationDropdownList" ng-click="showDecorationDropdownList = true">Show dropdown list</a>
                            </p>
                        </div>
                    </div>
                </fieldset>

                <fieldset ng-show="award.selectedDecoration.dec_id">
                    <hr>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Citation</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" ng-model="award.data.citation" placeholder="Write a short memo about why the recipient received this award (optional)." rows="4"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Date awarded</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6">
                                    <select class="form-control" ng-model="formData.awardDate.month" ng-options="month.value as month.name for month in formData.months"></select> 
                                </div>
                                <div class="col-sm-6">
                                    <input class="form-control" ng-model="formData.awardDate.year" type="number">
                                </div>
                            </div>
                            <input type="date" class="form-control hidden" ng-model="award.data.date" readonly>
                        </div>
                    </div>
                </fieldset>
                
                <hr>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="text-right">
                            <a class="btn btn-default pull-left" ng-href="{{ url('/') }}@{{cancelHref()}}">Back to Member</a>
                            <button ng-show="award.selectedDecoration.dec_id" class="btn btn-primary" type="submit">Award this decoration</button>
                        </div>
                    </div>
                </div>
            
            </form>
            
            
        </div>
        <div class="col-sm-4">
    
        <div class="panel panel-default" ng-show="award.selectedDecoration.dec_id">
            <div class="panel-heading">
                <h4 class="panel-title">@{{award.selectedDecoration.name}}</h4>
            </div>
            <div class="panel-body">
                <div class="thumbnail fl-record-thumbnail">
                    <img ng-src="@{{award.selectedDecorationBadgeUrl}}" alt="@{{award.name}}" class="image-rounded memberview-thumb">
                </div>
                <br>
                <div class="caption">
                    <p>@{{award.selectedDecoration.desc}}</p>
                </div>
            </div>
            <div class="panel-footer">
                Tier <span class="label label-info" style="vertical-align: 2px;">@{{award.selectedDecoration.tier}}</span>
            </div>
        </div>
    
        </div>
    </div>
    
</div>
@endsection
