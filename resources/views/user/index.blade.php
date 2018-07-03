@extends('layouts.app') 

@section('title', 'Manager User')
    
@section('styles')
<link rel="stylesheet" href="{{asset('css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('css/client/style.css')}}">
@endsection

@section('header')@endsection 

@section('content')
<!-- content here -->
    @section('page_title', __('user.label.label_manager'))
	<div id="app">
        <list-user :numberentry="{{ $entries }}" :resource="{{ $resource }}" :auth="{{ Auth::id() }}"></list-user>
    </div>
<!-- content here -->
@endsection 

@section('sidebar')@endsection 

@section('footer')@endsection

@section('scripts')
@endsection