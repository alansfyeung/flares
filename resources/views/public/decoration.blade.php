{{-- Individual decoration view public page --}}
@extends('layouts.template')

@section('heading')
<h4>Decoration info</h4>
@endsection

@section('content')
<div class="row">
    <div class="col-md-9 col-sm-6">
        <h1>{{ $dec->name }}</h1>
        <h3>Tier {{ $dec->tier }} award</h3>
        <p>{{ $dec->desc  }}</p>
    </div>
    <div class="col-md-3 col-sm-6">
        <img src="{{ $decBadgeUrl }}" alt="{{ $dec->shortcode }}" title="{{ $dec->desc }}">
        
    </div>

</div>
<table class="table table-striped">
    <tbody>
        <tr>
            <td>Precedence within Tier {{ $dec->tier }}</td>
            <td>{{ $dec->precedence or 'Nope' }}</td>
        </tr>
        <tr>
            <td>Authorised by</td>
            <td>{{ $dec->authorized_by }}</td>
        </tr>
    </tbody>
</table>
@endsection
