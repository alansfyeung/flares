{{-- Search all members --}}
@extends('layouts.base')

@section('ng-app', 'flaresUser')
@section('ng-controller', 'indexController')
@section('title', 'Flares Users')

@section('heading')
<h1>Users</h1>
@endsection

@push('scripts')
<script src="/ng-app/components/decoration/flaresDecorationIndex.js"></script>
@endpush
@push('vendor-scripts')
<script src="/assets/js/flow/ng-flow-standalone.min.js"></script>
@endpush

@section('content')

<div class="table-wrapper">
    <table class="table table-striped">
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{$user}}</td>
            </tr>
            @endforeach        
        </tbody>
    </table>
</div>


@endsection
