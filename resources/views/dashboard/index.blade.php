{{-- Home page for Flares users. Shows links for quick access --}}

@extends('layouts.template-ng')

@section('ng-app', 'flaresDashboard')
@section('ng-controller', 'dashboardController')
@section('title', 'Dashboard')

@section('heading')
	<h1>Dashboard</h1>
@endsection

@push('scripts')
<script src="{{asset('ng-app/components/dashboard/flaresDashboard.js')}}"></script>
@endpush

@section('content')
<div class="row">

    <div class="col-sm-6 col-md-8">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a bs-show-tab href="#approval" aria-controls="approval" role="tab">Approval Queue</a></li>
            <li role="presentation"><a bs-show-tab href="#activity" aria-controls="activity" role="tab">Activity Log</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="approval">
                @include('dashboard.partials.approval')
            </div>
            <div role="tabpanel" class="tab-pane" id="activity">
                @include('dashboard.partials.activity')
            </div>
        </div>

    </div>
    
	<div class="col-sm-6 col-md-4">
        <div class="container-fluid">
            <h4>Quick status</h4>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Member management</h3>
                        </div>
                        <div class="panel-body">
                            <figure class="row dashboard-inline-stat" ng-cloak>
                                <figcaption class="stat-caption col-xs-8">Active members</figcaption>
                                <div class="stat-figure col-xs-4">@{{stats.member.numActive}}</div>						
                            </figure>
                            <figure class="row dashboard-inline-stat" ng-cloak>
                                <figcaption class="stat-caption col-xs-8">Total members</figcaption>
                                <div class="stat-figure col-xs-4">@{{stats.member.numTotal}}</div>
                            </figure>
                            <figure class="row dashboard-inline-stat" ng-cloak>
                                <figcaption class="stat-caption col-xs-8">Added this month</figcaption>
                                <div class="stat-figure col-xs-4">@{{stats.member.numNewThisMonth}}</div>
                            </figure>
                            <figure class="row dashboard-inline-stat" ng-cloak>
                                <figcaption class="stat-caption col-xs-8">Added this year</figcaption>
                                <div class="stat-figure col-xs-4">@{{stats.member.numNewThisYear}}</div>
                            </figure>
                            <figure class="row dashboard-inline-stat" ng-cloak>
                                <figcaption class="stat-caption col-xs-8">Latest Regt Number</figcaption>
                                <div class="stat-figure col-xs-4">@{{stats.member.latestRegtNumber}}</div>
                            </figure>
                        </div>
                        <div class="list-group">
                            <a href="{{url('members')}}" class="list-group-item">View/search members</a>
                            <a href="{{url('members/new')}}" class="list-group-item">Add single member</a>
                            <!-- <a href="{{url('/members/newmulti')}}" class="list-group-item">Multi member onboarding</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Decorations</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <figure class="dashboard-big-stat col-xs-6" ng-cloak>
                                    <figcaption class="stat-caption">Available</figcaption>
                                    <div class="stat-figure">@{{stats.decoration.num}}</div>
                                </figure>
                                <figure class="dashboard-big-stat col-xs-6" ng-cloak>
                                    <figcaption class="stat-caption">Awarded</figcaption>
                                    <div class="stat-figure">@{{stats.decoration.numAwarded}}</div>
                                </figure>
                            </div>
                        </div>
                        <div class="list-group">
                            <a href="{{url('/decorations')}}" class="list-group-item">Manage decorations</a>
                            <a href="{{url('/decorations/new')}}" class="list-group-item">Create new decoration</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection