@extends('layouts.app') 

@section('title', 'Manage User')

@section('header')@endsection
 
@section('styles')
<link rel="stylesheet" href="{{asset('css/dataTables.bootstrap.min.css')}}">
@endsection

@section('content')
<!-- content here -->
    @section('page_title', __('user.label.label_update'))
	<div id="app">
        <update-user :users="{{ $result }}" :path="'{{ $path }}'" :callingcode="{{$callingcode}}" :imagedefault="'{{ $url_image }}'"></update-user>
    </div>
<!-- content here -->
@endsection
 
@section('sidebar')@endsection
 
@section('footer')@endsection

@section('scripts')
@endsection
