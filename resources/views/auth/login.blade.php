@extends('layouts.auth')
@section('title', 'ARMS Asia Resource Management System - Login')
@section('styles')
    <!-- Custom stylesheet for login -->
    <link rel="stylesheet" href="{{asset('css/auth/login.css')}}">
@endsection
@section('content')

<div class="login-box">
    <div class="login-logo">
        <a href="{{route('home')}}">
            <h1>{{ __('auth.form_login_title.big') }}</h1>
            <small>{{ __('auth.form_login_title.small') }}</small>
        </a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">{{ __('auth.login_title') }}</p>

        <form action="{{route('login')}}" method="post">
            {{ csrf_field() }}
            <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                <label for="username">{{__('auth.label_id')}}</label>
                <input type="text" class="form-control" placeholder="{{__('auth.label_yourID')}}" value="{{ old('username') }}" name="username" id="username" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('username'))
                        <span class="help-block  {{ $errors->has('username') ? ' has-error' : '' }}">
                            <strong>{{ $errors->first('username') }}</strong>
                         </span>
                    @endif
            </div>
            <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password">{{__('auth.label_password')}}</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="{{__('auth.label_password')}}">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block {{ $errors->has('password') ? ' has-error' : '' }}">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">{{__('auth.label_sign_in')}}</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <a href="{{ route('password.request') }}">{{__('auth.label_forgot')}}</a><br>

    </div>
    <!-- /.login-box-body -->
</div>

@endsection
