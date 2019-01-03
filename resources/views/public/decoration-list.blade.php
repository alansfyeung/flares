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
                    <col style="width: 60px;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Badge</th>
                        <th>Shortcode</th>
                        <th>Name</th>
                        <th>Eligible from</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($decorationTier->decorations as $dec)
                    <tr>
                        <td>
                            <span class="fl-dec-badge smaller"><img src="{{ $dec->badgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->name }}"></span>
                        </td>
                        <td><code>{{ $dec->shortcode }}</code></td>
                        <td>
                            {{ $dec->name }} 
                            @if (count($dec->related)) &rsaquo; <a href="#" class="subdec-toggle" data-subdec-visible="false" onclick="toggleVisible(event, {{ $dec->dec_id }}, this)">show more</a> @endif
                        </td>
                        <td>{{ date('M Y', strtotime($dec->date_commence)) }}</td>
                        <td><a class="btn btn-default btn-xs" href="{{ route('public::decorationDetails', [ 'shortcode' => $dec->shortcode ]) }}" onclick="openDecorationDetails(event, '{{ route("public::decorationDetails", [ "shortcode" => $dec->shortcode ]) }}'); return false;">
                            <span class="glyphicon glyphicon-new-window"></span>
                        </a></td>
                    </tr>
                    @foreach ($dec->related as $related)
                    <tr class="info subdec-row" data-dec-parent="{{ $dec->dec_id }}" style="display: none;">
                        <td>
                            <span class="text-muted">&#8618;</span>
                            <span class="fl-dec-badge smaller"><img src="{{ $related->badgeUrl }}" alt="{{ $related->shortcode }}" title="{{ $related->desc }}"></span>
                        </td>
                        <td><code>{{ $related->shortcode }}</code></td>
                        <td>{{ $related->name }}</td>
                        <td>{{ date('M Y', strtotime($related->date_commence)) }}</td>
                        <td><a class="btn btn-default btn-xs" href="{{ route('public::decorationDetails', [ 'shortcode' => $related->shortcode ]) }}" onclick="openDecorationDetails(event, '{{ route("public::decorationDetails", [ "shortcode" => $related->shortcode ]) }}'); return false;">
                            <span class="glyphicon glyphicon-new-window"></span>
                        </a></td>
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
function toggleVisible(event, decorationId, toggleElem, cssDisplayValue){
    event.preventDefault();
    cssDisplayValue = cssDisplayValue || 'table-row';
    var currentlyVisible = toggleElem.getAttribute('data-subdec-visible') === 'true';
    var rows = document.querySelectorAll('.subdec-row[data-dec-parent=\''+decorationId+'\']');
    for (var i = 0; i < rows.length; i++){
        rows[i].style.display = currentlyVisible ? 'none' : cssDisplayValue;
    }
    toggleElem.setAttribute('data-subdec-visible', currentlyVisible ? 'false' : 'true');
    return false;
}
</script>
@endpush
