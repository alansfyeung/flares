{{-- Display a single member --}}
@extends('layouts.primary')

@section('ng-app', 'flaresMemberReports')
@section('ng-controller', 'memberController')
@section('title', 'Member View')


@section('heading')
<h1>Reports</h1>
@endsection


@section('content')
	@yield('memberDisplay')
@endsection

@section('ng-script')
<script src="/app/components/member/flaresMemberReports.js"></script>
@endsection