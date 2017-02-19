{{-- List of decorations public page --}}
@extends('layouts.public')

@section('title', 'Decorations')
@section('ng-app', '')

@section('heading')
<h4>Available Decorations</h4>
@endsection

@section('content')
@foreach ($decorationTiers as $decorationTier)
<h3>Tier {{ $decorationTier->tier }} &ndash; {{ $decorationTier->name }}</h3>
<div class="table-wrapper">
    <table class="table table-hover">
        <colgroup>
            <col style="width: 120px;">
            <col style="width: 120px;">
            <col>
            <col style="width: 120px;">
            <col style="width: 120px;">
        </colgroup>
        <thead>
            <tr>
                <th>Badge</th>
                <th>Shortcode</th>
                <th>Name</th>
                <th>Date from</th>
                <th>Min service</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($decorationTier->decorations as $dec)
            <tr onclick="openDecorationDetails('{{ route("public::decoration-details", [ "shortcode" => $dec->shortcode ]) }}')">
                <td>
                    @if ($dec->parent_id) <span class="text-muted">&#8618;</span> @endif
                    <span class="fl-dec-badge smaller"><img src="{{ $dec->badgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->desc }}"></span>
                </td>
                <td>{{$dec->shortcode}}</td>
                <td>{{$dec->name}}</td>
                <td>{{ date('d/m/Y', strtotime($dec->date_commence)) }}</td>
                <td>{{$dec->service_period_months}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endsection

@push('scripts')
<script>
function openDecorationDetails(decorationDetailsUrl){
    window.open(decorationDetailsUrl, 'FlaresDecorationDescription', 'width=800, height=600'); 
    return false;
}
</script>
@endpush
