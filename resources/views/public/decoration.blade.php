{{-- Individual decoration view public page --}}
@extends('layouts.public')

@section('title', 'View Decoration Info')
@section('ng-app', '')

@section('heading')
<h4>Decoration info</h4>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-6 col-md-8">
        <h1>{{ $dec->name }}</h1>
        <h3>Tier <span class="label label-tier">{{ $dec->tier }}</span> Award</h3>
        <hr>
        @if ($dec->desc)
        <h4>Purpose</h4>
        <p>{{ $dec->desc  }}</p>
        @endif
        @if ($dec->visual)
        <h4>Symbolism</h4>
        <p>{{ $dec->visual }}</p>
        @endif
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="well">
            <div class="thumbnail fl-record-thumbnail">
                <img src="{{ $decBadgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->name }}">
            </div>
        </div>
        <div class="table-wrapper">
            <table class="table table-striped">
                <thead>
                    <th colspan="2">Precedence</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Within Tier <span class="label label-tier">{{ $dec->tier }}</label></td>
                        <td>#{{ $dec->precedence or '--' }}</td>
                    </tr>
                    <tr>
                        <td>Next (higher)</td>
                        <td>@if ($nextDec) <a href="{{ route('public::decorationDetails', ['shortcode'=>$nextDec->shortcode]) }}">{{ $nextDec->name }}</a> @else -- @endif</td>
                    </tr>
                    <tr>
                        <td>Next (lower)</td>
                        <td>@if ($prevDec) <a href="{{ route('public::decorationDetails', ['shortcode'=>$prevDec->shortcode]) }}">{{ $prevDec->name }}</a> @else -- @endif</td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-striped">
                <thead>
                    <th colspan="2">General Info</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Authorised by</td>
                        <td>{{ $dec->authorized_by or 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Shortcode</td>
                        <td><code>{{ $dec->shortcode or 'N/A' }}</code></td>
                    </tr>
                    <tr>
                        <td>Min service period</td>
                        <td>{{ $dec->service_period_months or 'N/A' }} months</td>
                    </tr>
                    <tr>
                        <td>Date commenced</td>
                        <td>{{ $dec->date_commence or 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Date concluded</td>
                        <td>{{ $dec->date_conclude or 'N/A' }}</td>
                    </tr>                
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
