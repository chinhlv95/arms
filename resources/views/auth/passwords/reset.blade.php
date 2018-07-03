@extends('layouts.auth')
@section('title', 'ARMS Asia Resource Management System - Reset password')
@section('styles')
        <!-- Custom stylesheet for login -->
<link rel="stylesheet" href="{{asset('css/auth/resetpass.css')}}">
@endsection
@section('content')
<div class="container box-reset-password">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__('auth.resetpass.confirm_title')}}</div>

                <div class="panel-body">
                    @if($errors->has('token_error'))
                            <div class="text-center">
                                <p>{{ $errors->first('token_error') }}</p>
                            </div>
                            <div class="col-md-12 text-center">
                                <a class="under-link" href="{{Route('login')}}"> {{ __('auth.resetpass.form_reset_back_label')  }} </a>
                            </div>
                        @else
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form class="form-horizontal" method="POST" id="frmResetPass" action="{{ route('password.request') }}">
                                {{ csrf_field() }}

                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{$email}}">
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">{{__('auth.resetpass.confirm_password_label')}}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password" required>
                                        <span class="help-block">
                                            <strong>{{ __('validation.between.string',['attribute' => 'Password', 'min' => 8, 'max' => 16])}}</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label for="password-confirm" class="col-md-4 control-label">{{__('auth.resetpass.confirm_password_one_label')}}</label>
                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                        <span class="help-block">
                                            <strong>{{ __('validation.same',['attribute' => 'Password', 'other' => 'Password confirm'])}}</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="button" id="btnSubmit" class="btn btn-primary">
                                            {{__('auth.resetpass.confirm_password_button_label')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="{{asset('js/forgot/script.js')}}"></script>
@endsection
