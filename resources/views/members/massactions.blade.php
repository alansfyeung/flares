{{-- Display a single member --}}
@extends('master')

@section('ng-app', 'flaresApp')
@section('ng-controller', 'memberController')
@section('title', 'Member View')


@section('heading')
<div class="page-header container-fluid">
	<h1>Mass Actions</h1>
</div>
@endsection


@section('content')
	@yield('memberDisplay')
@endsection

@section('ng-script')
<script src="/app/components/member/flaresMemberMassAction.js"></script>
@endsection