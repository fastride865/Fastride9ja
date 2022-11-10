<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category">@lang("$string_file.general")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('corporate.dashboard') }}">
                            <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.dashboard")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('employeeDesignation.index') }}">
                            <i class="site-menu-icon fab fa-get-pocket" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.designation_management")</span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.users")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('user.index') }}">
                            <i class="site-menu-icon fa fa-users" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.user_management")</span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.rides")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('corporate.manualDispatch') }}">
                            <i class="site-menu-icon fa fa-car" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.manual_dispatch")</span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.rides")</li>
                    <li class="site-menu-item has-sub">
                        <a href="#">
                            <i class="site-menu-icon fa fa-car" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.ride_management")</span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub open">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('corporate.activeride') }}">
                                    <span class="site-menu-title">@lang("$string_file.on_going_rides")</span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('corporate.completeride') }}">
                                    <span class="site-menu-title">@lang("$string_file.completed_rides") </span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('corporate.cancelride') }}">
                                    <span class="site-menu-title">@lang("$string_file.cancelled_rides")</span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('corporate.failride') }}">
                                    <span class="site-menu-title">@lang("$string_file.failed_rides")</span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('corporate.autocancel') }}">
                                    <span class="site-menu-title">@lang("$string_file.auto_cancelled")  @lang("$string_file.rides")</span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('corporate.all.ride') }}">
                                    <span class="site-menu-title">@lang("$string_file.all_rides")</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.others")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('corporate.transactions') }}">
                            <i class="site-menu-icon wb-users" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.transaction")</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="site-menubar-footer">
        <a href="{{ route('corporate.profile') }}" style="width:50%" data-placement="top" data-toggle="tooltip" data-original-title="Update Profile">
            <span class="icon wb-user" aria-hidden="true"></span>
        </a>
        <a href="{{ route('corporate.logout') }}" style="width:50%" data-placement="top" data-toggle="tooltip" data-original-title="Logout">
            <span class="icon wb-power" aria-hidden="true"></span>
        </a>
    </div>
</div>
<div class="site-gridmenu">
    <div>
        <div>
            <ul><!--[if lt IE 8]> -->
                <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            </ul>
        </div>
    </div>
</div>