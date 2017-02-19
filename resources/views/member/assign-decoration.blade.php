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
    <h1>Award a decoration</h1>
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
        <div class="col-sm-12 col-md-6">
            <div class="alert alert-success">
                <h2>Saved</h2>
                <div>
                    <button type="button" class="btn btn-success" ng-click="assignAnother()">Assign another</button>
                    <button type="button" class="btn btn-default" ng-click="cancel()">Close</button>
                </div>
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
                            <textarea class="form-control" ng-model="award.data.citation" placeholder="Write a short memo about why the recipient received this award (optional)." rows="4"></textarea>
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
                            <button class="btn btn-default pull-left" type="button" ng-click="cancel()">Cancel</button>
                            <button ng-show="award.selectedDecoration" class="btn btn-primary" type="submit">Award this decoration</button>
                        </div>
                    </div>
                </div>
            
            </form>
            
            
        </div>
        <div class="col-sm-4">
    
        <div class="panel panel-default" ng-show="award.selectedDecoration">
            <div class="panel-heading">
                <h4 class="panel-title">{{award.selectedDecoration.name}}</h4>
            </div>
            <div class="panel-body">
                <div class="thumbnail fl-record-thumbnail">
                    <img ng-src="{{award.selectedDecorationBadgeUrl}}" alt="{{award.name}}" class="image-rounded memberview-thumb">
                </div>
                <div class="caption">
                    <p>{{award.selectedDecoration.desc}}</p>
                    <p>Tier {{award.selectedDecoration.tier}} award</p>
                </div>
            </div>
        </div>
    
        </div>
    </div>
    
</div>
@endverbatim
@endsection