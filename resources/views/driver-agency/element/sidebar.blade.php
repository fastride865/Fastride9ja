@php
    $driver_agency = Auth::user('driver-agency');
@endphp
<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-item">
                        <a href="{{ route('driver-agency.dashboard') }}">
                            <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.dashboard")</span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.driver")</li>
                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
                            <i class="site-menu-icon fa-drivers-license" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.driver_management") </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('driver-agency.driver.index') }}">
                                    <span class="site-menu-title">@lang("$string_file.all_drivers")</span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('driver-agency.driver.basic') }}">
                                    <span class="site-menu-title">@lang("$string_file.basic_signup_drivers")</span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('driver-agency.driver.basic') }}">
                                    <span class="site-menu-title">@lang("$string_file.rejected_drivers")</span>
                                </a>
                            </li>

{{--                            <li class="site-menu-item">--}}
{{--                                <a class="animsition-link" href="{{ route('account.index') }}">--}}
{{--                                    <span class="site-menu-title">@lang("$string_file.driver_account")</span>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                        </ul>
                    </li>


                    <li class="site-menu-category">@lang("$string_file.others")</li>
{{--                    <li class="site-menu-item">--}}
{{--                        <a href="{{ route('driver-agency.transaction') }}">--}}
{{--                            <i class="site-menu-icon fa fa-exchange" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.transaction_management")</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
                    <li class="site-menu-item">
                        <a href="{{ route('driver-agency.wallet') }}">
                            <i class="site-menu-icon fa fa-google-wallet" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.wallet_transaction")</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
