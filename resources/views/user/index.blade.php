{{-- Search all members --}}
@extends('layouts.template-ng')

@section('title', 'Admin Users')

@section('heading')
<h1>Admin Users</h1>
@endsection

@section('content')

<div class="alert alert-info">
    <p>New users can be added:</p>
    <ul>
        <li>Via CLI using <code>php artisan create:user</code></li>
        <li>By creating a Forums-linked admin user then SSO-ing to Flares</li>
    </ul>
</div>

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
