@extends('layouts.app')

@section('title', 'ARMS System-'.__('origanization.page_title'))

@section('styles')
    <!-- nestable plugin -->
    <link rel="stylesheet" href="{{asset('css/origanization/nesteable.css')}}">
@endsection

@section('header')@endsection

@section('content')
    <div id="app">
        <?php

        ?>
        <div id="display-message">

        </div>
        <division-list :initdata = "{{json_encode($initData)}}"></division-list>
    </div>
@endsection

@section('sidebar')@endsection

@section('footer')@endsection

@section('scripts')@endsection