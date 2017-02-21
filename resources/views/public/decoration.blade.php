{{-- Individual decoration view public page --}}
@extends('layouts.public')

@section('title', 'View Decoration Info')
@section('ng-app', '')

@section('heading')
<h4>Decoration info</h4>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-6">
        <h1>{{ $dec->name }}</h1>
        <h3>Tier <span class="label label-info">{{ $dec->tier }}</span> Award</h3>
        <p>{{ $dec->desc  }}</p>
    </div>
    <div class="col-sm-6">
        <div class="well">
            <img src="{{ $decBadgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->desc }}">
        </div>
        <div class="table-wrapper">
            <table class="table table-striped">
                <thead>
                    <th colspan="2">Precedence</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Within Tier <span class="label label-info">{{ $dec->tier }}</label></td>
                        <td>{{ $dec->precedence or 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Previous decoration</td>
                        <td>{{ $dec->prevDec->name or 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Next decoration</td>
                        <td>{{ $dec->nextDec->name or 'N/A' }}</td>
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
                        <td>Minimum service period</td>
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
