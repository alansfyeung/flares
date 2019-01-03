{{-- Search all members --}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresDecoration')
@section('ng-controller', 'indexController')
@section('title', 'All Decorations')

@section('heading')
<h1>All decorations</h1>
@endsection

@push('scripts')
<script src="{{asset('ng-app/components/decoration/flaresDecorationIndex.js')}}"></script>
@endpush
@push('vendor-scripts')
<script src="{{asset('assets/js/flow/ng-flow-standalone.min.js')}}"></script>
@endpush

@section('content')
<section class="index-loading" ng-show="state.loading">
    <div class="alert alert-info">
        Loading
    </div>
</section>
<section class="index" ng-if="!state.loading">
    <div class="alert alert-warning" ng-if="decorations.length === 0">
        <strong>No decorations found:</strong> Consider <a ng-href="@{{gotoCreateNew}}">creating a new decoration</a>
    </div>
    
    <uib-accordion close-others="false" template-url="ng-app/components/decoration/decorationAccordionTemplate.html">
        <div uib-accordion-group class="panel panel-default" ng-repeat="decTier in decorations" heading="Tier @{{decTier.tier}} – @{{decTier.tierName}}" template-url="ng-app/components/decoration/decorationAccordionGroupTemplate.html">
            <div class="table-wrapper" ng-show="decTier.decorations.length > 0">
                <table class="table table-hover">
                    <colgroup>
                        <col style="width: 120px;">
                        <col style="width: 40px;">
                        <col style="width: 120px;">
                        <col>
                        <col style="width: 80px;">
                        <col style="width: 40px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Badge</th>
                            <th>Tier</th>
                            <th>Shortcode</th>
                            <th>Name</th>
                            <th>Actions</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="dec in decTier.decorations" ng-click="selectDecoration(dec)">
                            <td>
                                <span class="text-muted" ng-show="dec.parent_id">&#8618;</span>
                                <span class="fl-dec-badge smaller"><img ng-src="@{{badgeSrc(dec)}}"></span>
                            </td>
                            <td><span class="label label-tier">@{{dec.tier}}</span></td>
                            <td>@{{dec.shortcode | markBlanks}}</td>
                            <td>@{{dec.name}}</td>
                            <td><button class="btn btn-primary btn-block btn-xs fl-context-modal-button" ng-click="selectDecorationContext(dec); $event.stopPropagation();">Actions</button></td>
                            <td>
                                <a class="btn btn-default btn-block btn-xs" target="_blank" ng-click="$event.stopPropagation()"
                                    ng-href="{{ route('decoration::view') }}#!/@{{dec.dec_id}}/edit">
                                    <span class="glyphicon glyphicon-share text-muted"></span>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-muted" ng-hide="decTier.decorations.length > 0">No decorations found</div>
        </div>
    </uib-accordion>

</section>
@endsection
