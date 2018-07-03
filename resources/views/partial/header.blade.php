 <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>A</b>LT</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>ARMS</b> System</span>
    </a>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              @if(App::getLocale() == 'vi')
                <img src="{{asset('images/ico_vietnam.png')}}" />
              @endif

              @if(App::getLocale() == 'ja')
                <img src="{{asset('images/ico_japan.png')}}" />
              @endif

            </a>
            <ul class="language dropdown-menu">
              @foreach (Config::get('languages') as $lang => $language)
                <li>
                    <a href="{{ route('lang.switch', $lang) }}">{{$language}}</a>
                </li>
              @endforeach
            </ul>
          </li>
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img class="user-image" src="
                @if(Auth::user()->avatar != null)
                  {{Image::url(asset(Config::get('contains.TARGET_UPLOAD_DIR').Auth::user()->avatar) ),160,160,array('crop')}}
                @else
                  {{asset('images/avatar_default.png')}}
                @endif
              " onerror="this.src='{{asset('images/avatar_default.png')}}'" alt="User profile image" />
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">{{Auth::user()->fullname != null ? Auth::user()->fullname : 'No name'}}</span>
            </a>

            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="
                  @if(Auth::user()->avatar != null)
                    {{Image::url(asset(Config::get('contains.TARGET_UPLOAD_DIR').Auth::user()->avatar) ),160,160,array('crop')}}
                  @else
                    {{asset('images/avatar_default.png')}}
                  @endif
                " onerror="this.src='{{asset('images/avatar_default.png')}}'" class="img-circle" alt="User Image">

                <p>
                  {{Auth::user()->fullname != null ? Auth::user()->fullname : 'No name'}}
                  <small>Phone: {{Auth::user()->phone != null ? Auth::user()->phone : ''}}</small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <a href="{{ Route('logout') }}" class="btn btn-default btn-flat">{{__('auth.signout_label')}}</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>