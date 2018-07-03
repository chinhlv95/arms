@extends('layouts.app')

@section('title', 'Home page')

@section('styles')
@endsection

@section('header')@endsection

@section('content')
    <!-- content here -->
    <div id="app">
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> {{ trans('error.label.label_dialog_title') }}</h4>
            {{ trans('error.permission_denied') }}
        </div>
    </div>
    <!-- content here -->
@endsection

@section('sidebar')@endsection

@section('footer')@endsection

@section('scripts')@endsection