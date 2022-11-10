@php
    $hotelObj = get_hotel();
@endphp
<div class="site-menubar">
    <div class="site-menubar-body">
    <div>
    <div>
        <ul class="site-menu" data-plugin="menu">
            <li class="site-menu-category">@lang("$string_file.general")</li>
            <li class="site-menu-item">
                <a href="{{ route('hotel.dashboard') }}">
                    <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                    <span class="site-menu-title">@lang("$string_file.dashboard")</span>
                </a>
            </li>
            <li class="site-menu-item">
                <a href="{{ route('hotel.test.manualdispach') }}">
                    <i class="site-menu-icon fa fa-list-alt" aria-hidden="true"></i>
                    <span class="site-menu-title">@lang("$string_file.manual_dispatch")</span>
                </a>
            </li>
            <li class="site-menu-item has-sub">
                <a href="javascript:void(0)">
                    <i class="site-menu-icon fa fa-car" aria-hidden="true"></i>
                    <span class="site-menu-title">@lang("$string_file.ride_management")</span>
                    <span class="site-menu-arrow"></span>
                </a>
                <ul class="site-menu-sub">
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('hotel.activeride') }}">
                            <span class="site-menu-title">@lang("$string_file.on_going_rides")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('hotel.completeride') }}">
                            <span class="site-menu-title">@lang("$string_file.completed_rides") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('hotel.cancelride') }}">
                            <span class="site-menu-title">@lang("$string_file.cancelled_rides")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('hotel.allrides') }}">
                            <span class="site-menu-title">@lang("$string_file.all_rides")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('hotel.ratings') }}">
                            <span class="site-menu-title">@lang("$string_file.reviews_and_symbol_ratings")</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="site-menu-item">
                <a href="{{ route('hotel.wallet') }}">
                    <i class="site-menu-icon icon fa-exchange" aria-hidden="true"></i>
                    <span class="site-menu-title">@lang("$string_file.wallet_transaction")</span>
                </a>
            </li>
        </ul>
    </div>
    </div>
    </div>
    <div class="site-menubar-footer">
        <a href="{{ route('hotel.profile') }}" style="width:50%" data-placement="top" data-toggle="tooltip" data-original-title="Update Profile">
            <span class="icon wb-user" aria-hidden="true"></span>
        </a>
        <a href="{{ route('hotel.logout') }}" style="width:50%" data-placement="top" data-toggle="tooltip" data-original-title="Logout">
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