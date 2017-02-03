{{-- Individual decoration view public page --}}
@extends('layouts.template')

@section('title', 'View Decoration Info')

@section('heading')
<h4>Decoration info</h4>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-6">
        <h1>{{ $dec->name }}</h1>
        <h3>Award Tier <span class="label label-info">{{ $dec->tier }}</span></h3>

        <p>{{ $dec->desc  }}</p>
    </div>
    <div class="col-sm-6">
        <div class="well">
            <img src="{{ $decBadgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->desc }}">        
        </div>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>Precedence within Tier <span class="label label-info">{{ $dec->tier }}</label></td>
                    <td>{{ $dec->precedence or 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Authorised by</td>
                    <td>{{ $dec->authorized_by or 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Shortcode</td>
                    <td>{{ $dec->shortcode or 'N/A' }}</td>
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
@endsection
