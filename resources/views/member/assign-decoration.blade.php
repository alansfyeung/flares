{{-- Display a single member --}}
@extends('primary')

@section('ng-app', 'flaresMemberAssignDecoration')
@section('ng-controller', 'memberAssignDecorationController')
@section('title', 'Assign Decoration to member')

@push('scripts')
<script src="/app/components/member/flaresMemberAssignDecoration.js"></script>
@endpush

@section('heading')
<!-- page main header -->
<div ng-cloak ng-show="member.regt_num">
	<h1>Member Service Record</h1>
</div>
@endsection

@section('content')

    @verbatim
    <div ng-show="member.regt_num && !state.isDischarge()">

        <div class="media">
            <div class="media-left">
                <img ng-src="{{memberImage.url}}" alt="{{member.last_name}}" class="image-rounded memberview-thumb">
            </div>
            <div class="media-body">
                <h2>
                    {{member.last_name}}, {{member.first_name}} &nbsp;
                    <br>
                    <small style="display: inline-block">&diams; {{member.regt_num}}</small>
                </h2>
            </div>
        </div>

        <hr>
        
        <div>
            
            
        </div>
    </div>
    @endverbatim
    
@endsection