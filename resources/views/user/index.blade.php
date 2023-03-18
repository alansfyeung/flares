{{-- Search all members --}}
@extends('layouts.template')

@section('title', 'Manage System Users')

@section('heading')
<h1>System Users</h1>
@endsection

@section('content')

<h2>Current system users</h2>
<p>System users are either "direct user accounts", or may be SSO users linked from the Forums.</p>
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
                        @if ($user->forums_username) 
                        <span class="glyphicon glyphicon-import" title="Manage this user from Forums ACP"></span>
                        @endif
                        <span>{{$user->username}}</span>
                        @if ($user->forums_username) 
                        <span class="text-muted">(Forums: {{$user->forums_username or '--'}})</span>
                        @elseif ($user->email)
                        <span class="text-muted">({{$user->email or '--'}})</span>
                        @endif
                    </td>
                    <td>{{$user->allow_sso ? 'yes' : 'no'}}</td>
                    <td>
                        @if ($user->access_level > 0)
                        <span class="glyphicon glyphicon-check text-success"></span>
                        @else 
                        <span class="glyphicon glyphicon-ban-circle text-danger"></span>
                        @endif
                        {{$get_access_level_label($user->access_level)}}
                    </td>
                    <td>{{date('d-M-Y', strtotime($user->created_at))}}</td>
                </tr>
                @endforeach
            </tbody> 
        </tbody>
    </table>
</div>

<hr>

<h3>How to add system users</h3>
<p>Creating <strong>Forums SSO (single-sign on) users</strong> is recommended - this links to an existing forums account. To do this, go to Forums > ACP > .MODS > Flares SSO Users.</p>
<p>Otherwise, new non-SSO users can be added or modified via CLI using <code>php artisan create:user</code>, <code>php artisan users:reset</code>, etc.</p>
@endsection
