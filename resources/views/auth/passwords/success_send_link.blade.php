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
                <div class="panel ">
                    <div class="panel-body">
                        <div class="col-md-offset-4">
                            <p>Sent succeeded!<br>
                               Please follow the contents of the mail.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
