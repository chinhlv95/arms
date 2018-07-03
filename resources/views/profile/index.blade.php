@extends('layouts.app')

@section('title', 'ARMS System - '.__(' profile.page_title'))

@section('styles')
    <!-- Custom stylesheet for profile -->
    <link rel="stylesheet" href="{{asset('css/auth/profile.css')}}">
@endsection

@section('header')@endsection

@section('content')
@section('page_title', __(' profile.page_title'))
<div id="app">

    <form role="form" method="post" action="{{ route('updateProfile') }}" enctype="multipart/form-data">
        {{csrf_field()}}
        <!-- content here -->
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <div class="avatar_wrapper">
                            <i class="fa fa-pencil change_icon" aria-hidden="true"></i>
                            <input type="file" name="avatar" class="avatar change_avatar" value="{{ old('avatar') ? old('avatar') : ''}}">

                            <img onerror="this.src='{{asset('images/avatar_default.png')}}'" class="profile-user-img img-responsive img-circle" src="@if($user->avatar) {{Image::url(asset(Config::get('contains.TARGET_UPLOAD_DIR').$user->avatar) ),160,160,array('crop')}} @else{{asset('images/avatar_default.png')}} @endif" alt="User profile image" />
                            @if ($errors->has('avatar'))
                                <span class="help-block text-center {{ $errors->has('avatar') ? ' has-error' : '' }}">
                                    <strong>{{ $errors->first('avatar') }}</strong>
                                </span>
                            @endif
                        </div>
                        <h3 class="profile-username text-center">{{$user->fullname}}</h3>

                        <p class="text-muted text-center">Software Engineer</p>

                        <a href="{{route('home')}}" class="btn btn-primary btn-block"><b>{{__('auth.back_home_label')}}</b></a>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{__('auth.profile_about_me')}}</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>
                                    <i class="fa fa-user-circle-o margin-r-5" aria-hidden="true"></i>
                                    {{__('auth.profile_fullname')}}
                                </b> <a class="pull-right">{{$user->fullname}}</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fa fa-id-card margin-r-5"></i>{{__('auth.profile_id')}}</b> <a class="pull-right">{{$user->username}}</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fa fa-envelope margin-r-5"></i>{{__('auth.profile_email')}}</b> <a class="pull-right">{{$user->email}}</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fa fa-phone margin-r-5"></i>{{__('auth.profile_phone')}}</b> <a class="pull-right">{{$user->calling_code}} {{$user->phone}}</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fa fa-skype margin-r-5"></i>{{__('auth.profile_skype')}}</b> <a class="pull-right">{{$user->skype}}</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{__('auth.profile_edit')}}</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group {{ $errors->has('fullname') ? ' has-error' : '' }}">
                                <label for="fullname">{{__('auth.profile_fullname')}}</label>
                                <input type="text" name="fullname" class="form-control" id="fullname" value="{{ old('fullname') ? old('fullname') : $user->fullname }}" placeholder="Enter email">
                                <input type="hidden" value="{{$user->id}}" name="id">
                                @if ($errors->has('fullname'))
                                    <span class="help-block {{ $errors->has('fullname') ? ' has-error' : '' }}">
                                        <strong>{{ $errors->first('fullname') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email">{{__('auth.profile_update_email_label')}}</label>
                                <input type="text" class="form-control" name="email" id="email" value="{{ old('email') ? old('email') : $user->email }}" placeholder="Enter email">
                                @if ($errors->has('email'))
                                    <span class="help-block {{ $errors->has('email') ? ' has-error' : '' }}">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone">{{__('auth.profile_update_phone_label')}}</label>
                                <div class="row">
                                    <div class="col-md-2">
                                        <select class="form-control select2" style="width: 100%;" id="calling_code" name="calling_code">
                                            <optgroup label="Calling code">
                                                @foreach($calling_code as $key => $value)
                                                    <option {{ old('calling_code') == $key || $user->calling_code == $key  ? 'selected' : ''}} value="{{$key}}">{{$value}}</option>
                                                    @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input id="phone" type="text" value="{{old('phone') ? old('phone') : $user->phone}}" name="phone" class="form-control" aria-invalid="false">
                                        </div>
                                    </div>
                                    @if ($errors->has('phone'))
                                        <div class="col-md-12">
                                            <span class="help-block {{ $errors->has('phone') ? ' has-error' : '' }}">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('skype') ? ' has-error' : '' }}">
                                <label for="skype">Skype</label>
                                <input type="text" class="form-control" id="skype" name="skype" placeholder="Skype" value="{{old('skype') ? old('skype') : $user->skype}}">
                                @if ($errors->has('skype'))
                                    <span class="help-block {{ $errors->has('skype') ? ' has-error' : '' }}">
                                        <strong>{{ $errors->first('skype') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{__('auth.profile_update_btn')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content here -->
    </form>
</div>
@endsection

@section('sidebar')@endsection

@section('footer')@endsection


@section('scripts')@endsection