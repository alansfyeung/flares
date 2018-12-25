@extends('layouts.template')
    
{{--

$userInfo = User::find(Auth::id())->with('personalInfo')->first();
return View::make('page')->with('userInfo',$userInfo);

//in your view then you have access to 
{ {$userInfo->name } }
{ {$userInfo->address} }
//the values from the table and related model.

--}}

@section('navbar-sections')
    @include('partials.navbar')
@endsection
