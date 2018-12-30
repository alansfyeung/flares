{{-- Search all members --}}
@extends('layouts.template')

@section('title', 'Admin Users')

@section('heading')
<h1>Admin Users</h1>
@endsection

@section('content')

<h2>Currently registered admin users</h2>
<div class="table-wrapper">
    <table class="table table-striped">
        <colgroup>
            <col style="width: 40px">
            <col>
            <col style="width: 120px;">
            <col style="width: 120px;">
            <col style="width: 60px;">
            <col style="width: 200px;">
        </colgroup>
        <tbody>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Access Level</th>
                    <th>Added on</th>
                    <th>SSO?</th>
                    <th>Linked Forums account</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{$user->user_id}}</td>
                    <td>{{$user->username}} <span class="text-muted">({{$user->email}})</span></td>
                    <td>{{$user->access_level}}</td>
                    <td>{{date('d-M-Y', strtotime($user->created_at))}}</td>
                    <td>{{$user->allow_sso or 'no'}}</td>
                    <td>{{$user->forums_username or '--'}}</td>
                </tr>
                @endforeach
            </tbody> 
        </tbody>
    </table>
</div>

<h3>Registering new users</h3>
<div class="well">
    <p>New users can be added or modified via CLI using <code>php artisan create:user</code>, <code>php artisan users:reset</code>, etc</p>
</div>
@endsection
