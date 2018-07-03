@extends('layouts.app')

@section('title', 'ARMS System-'.__('project.page_title'))

@section('styles')
    <link rel="stylesheet" href="{{asset('css/dashboard/manage_project.css')}}">
    <link rel="stylesheet" href="{{asset('css/client/style.css')}}">
@endsection

@section('header')@endsection

@section('content')
    <div id="app">
        <project-manager-list :entries="{{$entries}}" :project_resource="{{$project_resource}}"></project-manager-list>
    </div>
@endsection

@section('sidebar')@endsection

@section('footer')@endsection


@section('scripts')@endsection