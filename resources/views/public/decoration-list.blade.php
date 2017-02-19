{{-- List of decorations public page --}}
@extends('layouts.public')

@section('title', 'Decorations')
@section('ng-app', '')

@section('heading')
<h4>Available Decorations</h4>
@endsection

@section('content')
@foreach ($decorationTiers as $decorationTier)
<div class="row">
    <div class="col-sm-3">
        <h3>Tier {{ $decorationTier->tier }} &ndash; {{ $decorationTier->name }}</h3>    
    </div>
    <div class="col-sm-9">
        <div class="table-wrapper">
            <table class="table table-hover">
                <colgroup>
                    <col style="width: 120px;">
                    <col style="width: 120px;">
                    <col>
                    <col style="width: 120px;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Badge</th>
                        <th>Shortcode</th>
                        <th>Name</th>
                        <th>Eligible from</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($decorationTier->decorations as $dec)
                    <tr onclick="openDecorationDetails('{{ route("public::decoration-details", [ "shortcode" => $dec->shortcode ]) }}')">
                        <td>
                            <span class="fl-dec-badge smaller"><img src="{{ $dec->badgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->desc }}"></span>
                        </td>
                        <td><code>{{ $dec->shortcode }}</code></td>
                        <td>{{ $dec->name }}</td>
                        <td>{{ date('M Y', strtotime($dec->date_commence)) }}</td>
                    </tr>
                    @foreach ($dec->related as $related)
                    <tr onclick="openDecorationDetails('{{ route("public::decoration-details", [ "shortcode" => $dec->shortcode ]) }}')">
                        <td>
                            <span class="text-muted">&#8618;</span>
                            <span class="fl-dec-badge smaller"><img src="{{ $dec->badgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->desc }}"></span>
                        </td>
                        <td><code>{{ $dec->shortcode }}</code></td>
                        <td>{{ $dec->name }}</td>
                        <td>{{ date('M Y', strtotime($dec->date_commence)) }}</td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<hr>
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
