{{-- Display a single member --}}
@extends('master')

@section('ng-app', 'flaresMemberReports')
@section('ng-controller', 'memberController')
@section('title', 'Member View')


@section('heading')
<div class="page-header container-fluid">
	<h1>Reports</h1>
</div>
@endsection


@section('content')
	@yield('memberDisplay')
@endsection

@section('ng-script')
<script src="/app/components/member/flaresMemberReports.js"></script>
@endsection