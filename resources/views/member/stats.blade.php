{{-- 
Statistics for members
--}}

@extends('layouts.template-ng')

@section('ng-app', 'flaresApp')
@section('ng-controller', 'memberController')
@section('title', 'Member View')

@section('heading')
<h1>Statistics</h1>
@endsection

@push('scripts')
<script src="/ng-app/components/member/flaresMemberStats.js"></script>
@endpush

@section('content')
	@yield('memberDisplay')
@endsection
