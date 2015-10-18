{{-- Activity Search --}}
@extends('master')

@section('ng-app', 'flaresActivitySearch')
@section('ng-controller', 'activitySearchController')
@section('title', 'Search activities')


@section('heading')
<div class="page-header container-fluid">
    <aside class="title-actions pull-right">
        <a href="/activities/new" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Add new</a>
    </aside>
	<h1>Search</h1>
</div>
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
<script src="/app/components/activity/flaresActivitySearch.js"></script>
@endsection