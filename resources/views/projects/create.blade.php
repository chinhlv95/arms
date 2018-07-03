@extends('layouts.app')

@section('title', 'ARMS System-'.__('project.list_project.create'))

@section('styles')

@endsection

@section('header')@endsection

@section('content')
    <div id="app">
        <project-manager-create local="{{\App::getLocale()}}" :clients="{{ json_encode($clients) }}"></project-manager-create>
    </div>
@endsection

@section('sidebar')@endsection

@section('footer')@endsection


@section('scripts')@endsection