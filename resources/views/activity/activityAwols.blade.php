{{-- 
Activity AWOLs 
Displays a historical aggregation of AWOLs across all activities
--}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresActivityAwols')
@section('ng-controller', 'activitySearchController')
@section('title', 'Search activities')


@section('heading')
<aside class="title-actions pull-right">
    <a href="/activities/new" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Add new</a>
</aside>
<h1>AWOLs</h1>
@endsection

@section('searchbar')
<div class="row">
    <div class="col-sm-12">
        <div class="input-group input-group-lg">
            <input type="text" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button">Go!</button>
            </span>
        </div><!-- /input-group -->
    </div>
</div>
@endsection

@section('activity-display')
<div>
    
</div>
@endsection

@section('content')
	@yield('searchbar')
@endsection

@section('ng-script')
<script src="/ng-app/components/activity/flaresActivitySearch.js"></script>
@endsection