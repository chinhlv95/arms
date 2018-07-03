<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('css/ionicons.min.css')}}">
    <!-- Select 2 -->
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}">
    <!--datepicker -->
    <link rel="stylesheet" href="{{asset('css/bootstrap-datepicker.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('css/AdminLTE.min.css')}}">

    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="{{asset('css/skin-blue.min.css')}}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Other stylesheet -->
    @yield('styles')
</head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            @include('partial.header')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <section class="content-header">
                    <h1>@yield('page_title')</h1>
                </section>
                <!-- Main content -->
                <section class="content container-fluid">
                    @if(session()->has('message'))
                    <div class="row alert-message">
                        <div class="col-md-12">
                            <div class="box-body">
                                    <div class="alert alert-{{ session('message.level') }} alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4>{!! session('message.title') !!}</h4>
                                    <p>{!! session('message.content') !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @yield('content')
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            @include('partial.sidebar')
            @include('partial.footer')
        </div>
        
        <!-- pass language data -->
        <script>
            window.trans = <?php
            // copy all translations from /resources/lang/CURRENT_LOCALE/* to global JS variable
            $lang_files = File::files(resource_path() . '/lang/' . App::getLocale());
            // echo json_encode(File::files(resource_path() . '/lang/' . App::getLocale()));
            $trans = [];
            foreach ($lang_files as $f) {
                $filename = pathinfo($f)['filename'];
                $trans[$filename] = trans($filename);
            }
            echo json_encode(App\Helper::utf8_converter($trans));
            ?>;
            
        </script>
        <script src="{{asset('js/app.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset('js/adminlte.min.js')}}"></script>
        <script src="{{asset('js/common/Untility.js')}}"></script>
        @yield('scripts')
    </body>
</html>