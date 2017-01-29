{{-- Add a single member using the simple form --}}
@extends('primary')

@section('ng-app', 'flaresDecoration')
@section('ng-controller', 'newDecorationController')
@section('title', 'New Decoration')

@push('scripts')
<script src="/app/components/decoration/flaresDecorationNew.js"></script>
@endpush

@section('heading')
<h1>Add a new decoration</h1>
@endsection

@section('content')
{{-- Enter details --}}
<section>

    <div class="alert alert-warning" ng-if="state.errorMessage">
        @{{state.errorMessage}}
    </div>

    <form class="form-horizontal" ng-submit="wf.submitData()" name="decorationData">
        <h2>Decoration details <small ng-if="state.totalStages > 1">Stage 1 of 2</small></h2>
        <hr>
        
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-3 control-label-required">Tier</label>
                <div class="col-sm-3">
                    <select class="form-control" ng-model="dec.data.tier" ng-disabled="member.isSaved" required>
                        <option value="A">A - Official AAC awards</option>
                        <option value="B">B - Discretionary unit awards</option>
                        <option value="C">C - Unit achievement awards</option>
                        <option value="D">D - Special activity participation</option>
                        <option value="E" selected>E - Forums participation badges</option>
                    </select>                    
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 control-label-required">Name of decoration</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" ng-model="dec.data.name" placeholder="e.g. Best NCO Award" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Description</label>
                <div class="col-sm-9">
                    <textarea class="form-control" type="text" rows="3" ng-model="dec.data.desc" placeholder="e.g. Awarded to the most outstanding NCO effort during the year"></textarea>
                </div>
            </div>
            <div class="form-group" ng-if="dec.data.tier === 'E'">
                <label class="control-label col-sm-3">Forums special rank ID</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" ng-model="dec.data.forums_special_rank_id" placeholder="ID">
                    <p class="help-block">Enter the numeric ID of the forums special rank, for sync purposes</p>
                </div>
            </div>
        </fieldset>
        <hr>
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-3 control-label-required">Date of commencement</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" ng-model="dec.data.date_commence">
                    <p class="help-block">The decoration can only be assigned to members after this date</p>
                </div>
                <div class="col-sm-3">
                    <button type="button" class="btn btn-link" ng-click="setCommencementToday()">Set to today's date</button>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Date of conclusion</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" ng-model="dec.data.date_conclude" ng-disabled="dec.hasNoConclusionDate">
                    <p class="help-block">The decoration can only be assigned to members before this date; leave blank for no expiry</p>
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
                <label class="control-label col-sm-3">Authorised by</label>
                <div class="col-sm-3">
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
