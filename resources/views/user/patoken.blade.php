{{-- Search all members --}}
@extends('layouts.template')

@section('title', 'Personal Access Token')

@section('heading')
<h1>Personal Access Token</h1>
@endsection

@section('content')
@if (!empty($token))
<h2>Token successfully generated</h2>
<table class="table">
    <colgroup>
        <col style="width: 200px">
        <col>
    </colgroup>
    <tr>
        <th>User</th>
        <td>{{$user->id}} {{$user->username}}</td>
    </tr>
    <tr>
        <th>Applied scopes</th>
        <td>{{$appliedScopes or '--'}}</td>
    </tr>
    <tr>
        <th>Bearer Token</th>
        <td><pre style="white-space: pre-wrap;">{{$token}}</pre></td>
    </tr>
</table>
<hr>
<div class="text-right">
    <a href="{{url('/users/patokens')}}" class="btn btn-default">Generate another</a>
</div>
@else
<h2>Generate a token</h2>
<form class="form form-horizontal" action="{{url('/users/patokens')}}" method="post">
    <div class="form-group">
        <label for="select-username" class="col-sm-3 control-label">Target user</label>
        <div class="col-sm-6">
            <select class="form-control" name="user_id" id="select-username">
                @foreach ($users as $user)
                <option value="{{$user->user_id}}">
                    {{$user->username}}
                    @if ($user->forums_username) 
                    (Forums: {{$user->forums_username or '--'}})
                    @elseif ($user->email)
                    ({{$user->email or '--'}})
                    @endif
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Token scopes</label>
        <div class="col-sm-9">
            @forelse ($scopes as $scope)
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="scopes[]" value="{{$scope->id}}" checked> 
                    {{$scope->id}} <span class="text-muted">{{$scope->description}}</span>
                </label>
            </div>
            @empty
            <div class="text-muted">None available</div>
            @endforelse
        </div>
    </div>

    {{ csrf_field() }}
    <hr>
    <div class="form-group">
        <div class="col-sm-9 col-sm-offset-3 text-right">
            <button class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
@endif
@endsection
