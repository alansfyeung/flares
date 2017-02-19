{{-- Search all members --}}
@extends('layouts.primary')

@section('ng-app', 'flaresDecoration')
@section('ng-controller', 'indexController')
@section('title', 'All Decorations')

@section('heading')
<h1>All decorations</h1>
@endsection

@push('scripts')
<script src="/app/components/decoration/flaresDecorationIndex.js"></script>
@endpush
@push('vendor-scripts')
<script src="/assets/js/flow/ng-flow-standalone.min.js"></script>
@endpush

@section('content')
@verbatim
<section class="index-loading" ng-show="state.loading">
    <div class="alert alert-info">
        Loading
    </div>
</section>
<section class="index" ng-if="!state.loading">
    <div class="alert alert-warning" ng-if="decorations.length === 0">
        <strong>No decorations found:</strong> Consider <a ng-href="{{gotoCreateNew}}">creating a new decoration</a>
    </div>
    
    <uib-accordion close-others="false" template-url="/app/components/decoration/decorationAccordionTemplate.html">
        <div uib-accordion-group class="panel panel-default" ng-repeat="decTier in decorations" heading="Tier {{decTier.tier}} – {{decTier.tierName}}" template-url="/app/components/decoration/decorationAccordionGroupTemplate.html">
            <div class="table-wrapper" ng-show="decTier.decorations.length > 0">
                <table class="table table-hover">
                    <colgroup>
                        <col style="width: 120px;">
                        <col style="width: 40px;">
                        <col style="width: 120px;">
                        <col>
                        <col style="width: 80px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Badge</th>
                            <th>Tier</th>
                            <th>Shortcode</th>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="dec in decTier.decorations" ng-click="selectDecoration(dec)">
                            <td><span class="fl-dec-badge smaller"><img ng-src="{{badgeSrc(dec)}}"></span></td>
                            <td>{{dec.tier}}</td>
                            <td>{{dec.shortcode | markBlanks}}</td>
                            <td>{{dec.name}}</td>
                            <td><a class="btn btn-primary btn-xs fl-context-modal-button" ng-click="selectDecorationContext(dec); $event.stopPropagation();">More</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-muted" ng-hide="decTier.decorations.length > 0">No decorations found</div>
        </div>
    </uib-accordion>

</section>
@endverbatim
@endsection
