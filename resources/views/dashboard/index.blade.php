{{-- Home page for Flares users. Shows links for quick access --}}

@extends('layouts.base')

@section('ng-app', 'flaresDashboard')
@section('ng-controller', 'dashboardController')
@section('title', 'Dashboard')

@section('heading')
	<h1>Dashboard</h1>
@endsection

@push('scripts')
<script src="/ng-app/components/dashboard/flaresDashboard.js"></script>
@endpush

@section('content')
<div class="row">

    <div class="col-sm-6 col-md-8">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a bs-show-tab href="#approval" aria-controls="approval" role="tab">Approval</a></li>
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
        @verbatim
        <div class="container-fluid">
            <h4>Quick status</h4>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Member management</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-6">
                                    <figure class="dashboard-stat" ng-cloak>
                                        <figcaption class="stat-caption">Posted Strength</figcaption>
                                        <div class="stat-figure">{{stats.member.numActive}}</div>						
                                    </figure>
                                </div>
                                <div class="col-xs-6">
                                    <figure class="dashboard-stat" ng-cloak>
                                        <figcaption class="stat-caption">In system</figcaption>
                                        <div class="stat-figure">{{stats.member.numTotal}}</div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                        <div class="list-group">
                            <a href="/members" class="list-group-item">View/search members</a>
                            <a href="/members/new" class="list-group-item">Add single member</a>
                            <!-- <a href="/members/newmulti" class="list-group-item">Multi member onboarding</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Decorations</h3>
                        </div>
                        <div class="panel-body">
                            <figure class="dashboard-stat" ng-cloak>
                                <figcaption class="stat-caption">Decorations</figcaption>
                                <div class="stat-figure">{{stats.decoration.num}}</div>
                            </figure>
                        </div>
                        <div class="list-group">
                            <a href="/decorations" class="list-group-item">Manage decorations</a>
                            <a href="/decorations/new" class="list-group-item">Create new decoration</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endverbatim
    </div>
    
</div>
@endsection