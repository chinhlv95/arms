<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img class="img-circle" src="
                    @if(Auth::user()->avatar != null)
                        {{Image::url(asset(Config::get('contains.TARGET_UPLOAD_DIR').Auth::user()->avatar) ),160,160,array('crop')}}
                    @else
                        {{asset('images/avatar_default.png')}}
                    @endif
                " onerror="this.src='{{asset('images/avatar_default.png')}}'" alt="User profile image" />
            </div>
            <div class="pull-left info">
                <p>{{Auth::user()->fullname}}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            @if(App\Helper::check_permission(1) == 1 || App\Helper::check_permission(2) == 1)
            <!-- manage user -->
            <li class="treeview @if(App\Helper::check_current_page('user')) active @endif">
                <a href="#"> <i class="fa fa-users"></i> <span>{{ __('user.label.label_manager') }}</span>
                    <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{route('getListUser')}}"><i class="fa fa-circle-o"></i>{{ __('user.label.label_user_list') }}</a></li>
                    <li><a href="{{route('getCreateUser')}}"><i class="fa fa-circle-o"></i>{{ __('user.label.label_user_create') }}</a></li>
                </ul>
            </li>
            <!--  end manager users -->

            <!-- manage project -->
            <li class="treeview @if(App\Helper::check_current_page('projectManager')) active @endif">
                <a href="#"> <i class="fa fa-fw fa-file-text-o"></i> <span>{{__('project.page_title')}}</span>
                    <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('project') }}"><i class="fa fa-circle-o"></i>{{__('project.menu.list')}}</a></li>
                </ul>
            </li>
            <!--  end manager users -->

            <!-- manage Origanization -->
            <li class="treeview @if(App\Helper::check_current_page('division') || App\Helper::check_current_page('tags') || App\Helper::check_current_page('holiday') ) active @endif">
                <a href="#"><i class="fa fa-cogs" aria-hidden="true" ></i> <span>{{__('configmanager.label.label_title')}}</span>
                    <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('division') }}"><i class="fa fa-circle-o"></i>{{__('configmanager.label.label_list_division')}}</a></li>
                    <li><a href="{{ route('config') }}"><i class="fa fa-circle-o"></i>{{__('configmanager.label.label_list_tag')}}</a></li>
                    <li><a href="{{ route('holiday') }}"><i class="fa fa-circle-o"></i>{{__('holiday.label.list_holidays')}}</a></li>
                </ul>
            </li>
            @endif
            <!--  end manager Origanization -->
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
