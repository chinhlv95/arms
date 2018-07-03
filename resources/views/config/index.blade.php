@extends('layouts.app')

@section('title', 'ARMS System-'.__('configmanager.page_title'))

@section('styles')
    <link rel="stylesheet" href="{{asset('css/dashboard/manage_project.css')}}">
@endsection

@section('header')@endsection

@section('content')
    <!-- content here -->
<div id="app">
    <tags-list :initdata="{{json_encode($tags)}}"></tags-list>
</div>
<!-- content here -->
@endsection

@section('sidebar')@endsection

@section('footer')@endsection


@section('scripts')@endsection