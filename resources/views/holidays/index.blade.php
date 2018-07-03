@extends('layouts.app')

@section('title', 'ARMS System-'.__('holiday.page_title'))

@section('styles')
    <!-- nestable plugin -->
    <link rel="stylesheet" href="{{asset('css/origanization/nesteable.css')}}">
@endsection

@section('header')@endsection

@section('content')
    <div id="app">
        <holiday-list local="{{\App::getLocale()}}" :initdata = "{{json_encode($data)}}"></holiday-list>
    </div>
@endsection

@section('sidebar')@endsection

@section('footer')@endsection

@section('scripts')@endsection