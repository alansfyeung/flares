{{-- Display a single member --}}
@extends('layouts.primary')

@section('ng-app', 'flaresApp')
@section('ng-controller', 'memberController')
@section('title', 'Member View')


@section('heading')
<h1>Mass Actions</h1>
@endsection


@section('content')
	@yield('memberDisplay')
@endsection

@section('ng-script')
<script src="/app/components/member/flaresMemberMassAction.js"></script>
@endsection