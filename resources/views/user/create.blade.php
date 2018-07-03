@extends('layouts.app')

@section('title', 'ARMS System - ' . __('user.title_create'))

@section('styles')
<link rel="stylesheet" href="{{asset('css/dataTables.bootstrap.min.css')}}">
@endsection

@section('header')@endsection

@section('content')
    @section('page_title', __('user.label.label_manager'))
    <!-- content here -->
    <div id="app">
        <create-user  :callingcode="{{ $callingcode }}" :resource="{{ $resource }}"></create-user>
    </div>
    <!-- content here -->
@endsection

@section('sidebar')@endsection

@section('footer')@endsection

@section('scripts')
@endsection