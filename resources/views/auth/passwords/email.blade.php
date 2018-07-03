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
            <div class="panel" id="reset">
                <div class="panel-heading">{{ __('auth.resetpass.form_reset_title') }}</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal"  method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">{{ __('auth.resetpass.form_reset_email_label')  }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
								<input type="hidden" name="status" value="0">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label col-md-offset-4">
                                <a class="under-link" href="{{Route('login')}}"> {{ __('auth.resetpass.form_reset_back_label')  }} </a>
                            </label>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('auth.resetpass.form_reset_button_label')  }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
