@extends('layouts.app')

@section('title', 'ARMS System-'.__('project.edit_page_title'))

@section('styles')

@endsection

@section('header')@endsection

@section('content')
    <div id="app">
        <project-manager-update :project = "{{$project}}" local="{{\App::getLocale()}}" :clients="{{ json_encode($clients) }}"></project-manager-update>
    </div>
@endsection

@section('sidebar')@endsection

@section('footer')@endsection


@section('scripts')@endsection