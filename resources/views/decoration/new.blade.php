{{-- Add a single member using the simple form --}}
@extends('layouts.primary')

@section('ng-app', 'flaresDecoration')
@section('ng-controller', 'newDecorationController')
@section('title', 'New Decoration')

@push('scripts')
<script src="/app/components/decoration/flaresDecorationNew.js"></script>
@endpush

@section('heading')
<h1>
    <a href="{{ route('decoration::index') }}">All Decorations</a>
    &rsaquo;
    Add a new decoration
</h1>
@endsection

@section('content')
{{-- Enter details --}}
<section>

    <div class="alert alert-warning" ng-if="state.errorMessage">
        <pre>@{{state.errorMessage}}</pre>
    </div>

    <form class="form-horizontal" ng-submit="wf.submitData()" name="decorationData">
        <h2>Decoration details <small ng-if="state.totalStages > 1">Stage 1 of 2</small></h2>
        <hr>
        
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-3 control-label-required">Tier</label>
                <div class="col-sm-3">
                    <select class="form-control" ng-options="decTier.tier as decTier.tierName for decTier in formData.decorationTiers" ng-model="dec.data.tier" ng-disabled="member.isSaved" required></select>                    
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 control-label-required">Name of decoration</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" ng-model="dec.data.name" placeholder="e.g. Best NCO Award" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Purpose</label>
                <div class="col-sm-9">
                    <textarea class="form-control" type="text" rows="2" ng-model="dec.data.desc" placeholder="e.g. Awarded to the most outstanding NCO effort during the year"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Visual description</label>
                <div class="col-sm-9">
                    <textarea class="form-control" type="text" rows="2" ng-model="dec.data.visual" placeholder="e.g. Blue and gold background represent the colours of the AAC."></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 control-label-required">Shortcode</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" ng-model="dec.data.shortcode" maxlength="10" placeholder="10 letter max" required>
                </div>
            </div>
            <div class="form-group" ng-if="dec.data.tier === 'F'">
                <label class="control-label col-sm-3">Forums special rank ID</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" ng-model="dec.data.forums_special_rank_id" placeholder="ID">
                    <p class="help-block">Enter the numeric ID of the forums special rank, for sync purposes</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">Precedence</div>
                <div class="col-sm-9"><div class="alert alert-info">Note: Precedence within tier is automatically calculated to be the next available value, and can be edited after creation.</div></div>
            </div>
        </fieldset>
        <hr>
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-3 control-label-required">Date of commencement</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" ng-model="dec.data.date_commence" required>
                    <p class="help-block">The decoration can only be assigned to members after this date. <a ng-click="setCommencementToday()">Set to today's date</a></p>
                </div>
                <div class="col-sm-3">
                    <input type="date" class="form-control" ng-model="dec.data.date_conclude" ng-disabled="dec.hasNoConclusionDate">
                    <p class="help-block">The decoration can only be assigned to members before this date</p>
                </div>
                <div class="col-sm-3">
                    <div class="checkbox">
                        <label for="checkbox-no-expiry">
                            <input id="checkbox-no-expiry" type="checkbox" ng-model="dec.hasNoConclusionDate"> 
                            No date of conclusion
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Service requirement (months)</label>
                <div class="col-sm-3">
                    <input class="form-control" type="number" ng-model="dec.data.service_period_months" placeholder="e.g. 12">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Authorised by</label>
                <div class="col-sm-6">
                    <input class="form-control" type="text" ng-model="dec.data.authorized_by" placeholder="e.g. OC">
                </div>
            </div>
        </fieldset>
        
        <hr>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="text-right">
                    <button class="btn btn-default pull-left" type="button" ng-click="cancel()">Cancel</button>
                    <button class="btn btn-default" type="submit" ng-click="state.submitPreference = 2">Submit and create another</button>
                    <button class="btn btn-primary" type="submit" ng-click="state.submitPreference = 1">Submit then view</button>
                </div>            
            </div>
        </div>
    </form>
</section>
@endsection
