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
<div ng-cloak ng-show="member.regt_num">
	<h1>Award a decoration to &diam; @{{member.regt_num}} @{{member.last_name}}</h1>
</div>
@endsection

@section('alerts')
<div ng-show="award.saveError">
    <div class="alert alert-danger">
        <h2>Saving failed</h2>
        <p>We couldn't save this decoration.</p>
    </div>
</div>
@endsection

@section('content')
@verbatim
<div ng-show="member.regt_num">

    <h2>
        {{member.last_name}}, {{member.first_name}} &nbsp;
        <br>
        <small style="display: inline-block">&diams; {{member.regt_num}}</small>
    </h2>
    <hr>
    
    <div class="row" ng-show="award.saved">
        <div class="col-sm-8">
            <h2>Saved</h2>
            
            <hr>
            <div class="text-right">
                <button type="button" class="btn btn-default" ng-click="cancel()">Close</button>
                <button type="button" ng-click="assignAnother()" class="btn btn-primary">Assign another</button>
            </div>
        </div>
    </div>

    <div class="row" ng-hide="award.saved">
        <div class="col-sm-8">
            <form class="form-horizontal" ng-submit="submit()">
                <fieldset>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Decoration to assign</label>
                        <div class="col-sm-9">
                            <select class="form-control" ng-options="dec.name for dec in decorations track by dec.dec_id" ng-model="award.selectedDecoration">
                                <option value="">Choose a decoration</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset ng-show="award.selectedDecoration">
                    <hr>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Citation</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" ng-model="award.data.citation" placeholder="Write a short memo about why the recipient received this award." rows="4"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Date awarded</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" ng-model="award.data.date">
                        </div>
                    </div>
                </fieldset>
                
                <hr>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="text-right">
                            <button class="btn btn-default" type="button" ng-click="cancel()">Cancel</button>
                            <button ng-show="award.selectedDecoration" class="btn btn-primary" type="submit">Award this decoration</button>
                        </div>
                    </div>
                </div>
            
            </form>
            
            
        </div>
        <div class="col-sm-4">
    
            <div class="alert alert-info clearfix" ng-show="award.selectedDecoration">
                <div class="pull-right">
                    <div class="thumbnail fl-record-thumbnail">
                        <img ng-src="{{award.selectedDecorationBadgeUrl}}" alt="{{award.name}}" class="image-rounded memberview-thumb">
                    </div>
                </div>
                <h4>{{award.selectedDecoration.name}}</h4>
                <p>{{award.selectedDecoration.desc}}</p>
                <p>Tier {{award.selectedDecoration.tier}} award</p>
            </div>
    
    
        </div>
    </div>
    
</div>
@endverbatim
@endsection