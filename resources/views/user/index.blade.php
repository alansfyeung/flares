{{-- Search all members --}}
@extends('layouts.template')

@section('title', 'System Users')

@section('heading')
<h1>Admin Users</h1>
@endsection

@section('content')

<h2>Currently registered system users</h2>
<div class="table-wrapper">
    <table class="table table-striped">
        <colgroup>
            <col style="width: 40px">
            <col>
            <col style="width: 60px;">
            <col style="width: 200px;">
            <col style="width: 120px;">
        </colgroup>
        <tbody>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>SSO?</th>
                    <th>Access Level</th>
                    <th>Added on</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{$user->user_id}}</td>
                    <td>
                        <span></span>{{$user->username}} 
                        @if ($user->forums_username) 
                        <span class="text-muted">(Forums: {{$user->forums_username or '--'}})</span>
                        @elseif ($user->email)
                        <span class="text-muted">({{$user->email or '--'}})</span>
                        @endif
                    </td>
                    <td>{{$user->allow_sso ? 'yes' : 'no'}}</td>
                    <td>{{$get_access_level_label($user->access_level)}}</td>
                    <td>{{date('d-M-Y', strtotime($user->created_at))}}</td>
                </tr>
                @endforeach
            </tbody> 
        </tbody>
    </table>
</div>

<hr>

<h3>Flares User registration</h3>
<p>Creating <strong>Forums SSO (single-sign on) users</strong> is highly encouraged - this links an existing forums account. To do this, go to Forums > ACP > .MODS > Flares SSO Users.</p>
<p>Otherwise, new non-SSO users can be added or modified via CLI using <code>php artisan create:user</code>, <code>php artisan users:reset</code>, etc</p>
@endsection
