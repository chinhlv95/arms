@extends('layouts.app')

@section('title', 'Home page')

@section('styles')
    <link rel="stylesheet" href="{{asset('css/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/dashboard/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/dashboard/manage_project.css')}}">
    <link rel="stylesheet" href="{{asset('css/worktime/worktime.css')}}">
    <link rel="stylesheet" href="{{asset('css/auth/profile.css')}}">
@endsection

@section('header')@endsection

@section('content')
    <!-- content here -->
    <div id="app">
        <div class="row tar_bar">
            <section class="col-lg-12">
                <div class="tabs-top">
                    <div class="item tab-active">
                        <router-link :class="{'tab-active': $route.name == 'index'}" :to="{ name: 'index' }">{{__('dashboard.label_dashboard')}}</router-link>
                    </div>
                    <div class="item">
                        <router-link :class="{'tab-active': $route.name == 'list-worktime'}" :to="{ name: 'list-worktime' }">{{__('dashboard.label_mywork')}}</router-link>
                    </div>
                    <div class="item">
                        <router-link :class="{'tab-active': $route.name == 'userprofile'}" :to="{ name: 'userprofile' }" >{{__('dashboard.label_profile')}}</router-link>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </section>
        </div>
        <router-view  :user="{{ json_encode(Auth::user()) }}" :callingcode="{{ json_encode($calling_code) }}" :projects_data = "{{ json_encode($projects_data) }}" :local="{{ json_encode(App::getLocale()) }}" :mtoolresource="{{ json_encode($mtool_resource) }}"></router-view>
    </div>
    <!-- content here -->
@endsection

@section('sidebar')@endsection

@section('footer')@endsection


@section('scripts')@endsection