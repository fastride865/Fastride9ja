@extends('merchant.layouts.main')
@section('content')
    @php
        $segment = App\Http\Controllers\Helper\Merchant::MerchantSegments();
//p(Auth::user('merchant')->getAllPermissions()->toArray());
//p(Auth::user('merchant')->hasPermissionTo('active_ride'));
//p(Auth::user('merchant')->can('view_drivers'));
    @endphp
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="mr--10 ml--10">
                <div class="row" style="margin-right: 0rem;margin-left: 0rem">
                @if(Auth::user('merchant')->can('dashboard'))
                    <!-- Site Statistics -->
                        @if(Auth::user('merchant')->can('view_rider') || Auth::user('merchant')->can('view_drivers') || Auth::user('merchant')->can('view_countries') || Auth::user('merchant')->can('view_area') || Auth::user('merchant')->can('expired_driver_documents'))
                            <div class="col-12 col-md-12 col-sm-12">
                                <!-- Example Panel With Heading -->
                                <div class="panel panel-bordered">
                                    <div class="panel-heading">
                                        <div class="panel-actions">
                                        </div>
                                        <h3 class="panel-title">@lang("$string_file.site_statistics")</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            @if(Auth::user('merchant')->can('view_rider'))
                                                <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                    <a href="@if(Auth::user('merchant')->can('view_rider')) {{ route('users.index') }} @else # @endif">
                                                        <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                            <div class="card-block bg-white p-20">
                                                                <button type="button"
                                                                        class="btn btn-floating btn-sm btn-success"
                                                                        style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                    <i class="icon fa-cab"></i>
                                                                </button>
                                                                <span class="ml-10 font-weight-400">@lang("$string_file.active_users")</span>
                                                                <div class="content-text text-center mb-0">
                                                                    <span class="font-size-18 font-weight-100">{{ $site_states->users }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                            @if(Auth::user('merchant')->can('view_drivers'))
                                                <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                    <a href="@if(Auth::user('merchant')->can('view_drivers')) {{ route('driver.index') }} @else # @endif">
                                                        <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                            <div class="card-block bg-white p-20">
                                                                <button type="button"
                                                                        class="btn btn-floating btn-sm btn-primary"
                                                                        style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                    <i class="icon wb-users"></i>
                                                                </button>
                                                                <span class="ml-10 font-weight-400">@lang("$string_file.active_drivers")</span>
                                                                <div class="content-text text-center mb-0">
                                                                    <span class="font-size-18 font-weight-100">{{ $site_states->drivers }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                            @if(Auth::user('merchant')->can('view_countries'))
                                                <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                    <a href="@if(Auth::user('merchant')->can('view_countries')){{ route('country.index') }} @else # @endif">
                                                        <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                            <div class="card-block bg-white p-20">
                                                                <button type="button"
                                                                        class="btn btn-floating btn-sm btn-warning"
                                                                        style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                    <i class="icon fa-flag"></i>
                                                                </button>
                                                                <span class="ml-10 font-weight-400">@lang("$string_file.service_countries")</span>
                                                                <div class="content-text text-center mb-0">
                                                                    <span class="font-size-18 font-weight-100">{{ $site_states->totalCountry }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                            @if(Auth::user('merchant')->can('view_area'))
                                                <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                    <a href="{{ route('countryareas.index') }}">
                                                        <div class="card card-shadow">
                                                            <div class="card-block bg-white ml-20  p-20">
                                                                <button type="button"
                                                                        class="btn btn-floating btn-sm btn-warning"
                                                                        style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                    <i class="icon fa-area-chart"></i>
                                                                </button>
                                                                <span class="ml-10 font-weight-400">@lang("$string_file.service_area")</span>
                                                                <div class="content-text text-center mb-0">
                                                                    <span class="font-size-18 font-weight-100">{{ $site_states->totalCountryArea }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                            @if(Auth::user('merchant')->can('expired_driver_documents'))
                                                <div class="col-xl-4 col-md-3 col-sm-6 info-panel">
                                                    <a href="{{ route('merchant.driver.goingtoexpiredocuments') }}">
                                                        <div class="card card-shadow"
                                                             style="margin-bottom:0.243rem">
                                                            <div class="card-block bg-white p-20">
                                                                <button type="button"
                                                                        class="btn btn-floating btn-sm btn-danger"
                                                                        style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                    <i class="icon wb-file"></i>
                                                                </button>
                                                                <span class="ml-10 font-weight-400">@lang("$string_file.docs_going_expire")</span>
                                                                <div class="content-text text-center mb-0">
                                                                    <span class="font-size-18 font-weight-100">{{ $expire_driver_doc }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            @if(Auth::user('merchant')->can('view_reports_charts'))
                                                <div class="col-xl-4 col-md-3 col-sm-6 info-panel">
                                                    <a href="#">
                                                        <div class="card card-shadow"
                                                             style="margin-bottom:0.243rem">
                                                            <div class="card-block bg-white p-20">
                                                                <button type="button"
                                                                        class="btn btn-floating btn-sm btn-info"
                                                                        style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                    <i class="icon fa fa-money"></i>
                                                                </button>
                                                                <span class="ml-10 font-weight-400">@lang("$string_file.total_earning")</span>
                                                                <div class="content-text text-center mb-0">
                                                                    <span class="font-size-18 font-weight-100">{{ $site_states->total_earning }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                            @if(Auth::user('merchant')->can('view_reports_charts'))
                                                <div class="col-xl-4 col-md-3 col-sm-6 info-panel">
                                                    <a href="#">
                                                        <div class="card card-shadow"
                                                             style="margin-bottom:0.243rem">
                                                            <div class="card-block bg-white p-20">
                                                                <button type="button"
                                                                        class="btn btn-floating btn-sm btn-danger"
                                                                        style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                    <i class="icon fa fa-minus-square-o"></i>
                                                                </button>
                                                                <span class="ml-10 font-weight-400">@lang("$string_file.total_discount")</span>
                                                                <div class="content-text text-center mb-0">
                                                                    <span class="font-size-18 font-weight-100">{{ $site_states->total_discount }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    <!-- Taxi Statistics -->
                        @if(!empty($taxi_states))
                            @foreach($taxi_states as $segment)
                                @if(Auth::user('merchant')->can($segment->slag) && (Auth::user('merchant')->can("ride_management_$segment->slag") || Auth::user('merchant')->can("view_reports_charts")))
                                    <div class="col-12 col-md-12 col-sm-12">
                                        <!-- Example Panel With Heading -->
                                        <div class="panel panel-bordered">
                                            <div class="panel-heading">
                                                <div class="panel-actions"></div>
                                                <h3 class="panel-title">{{$segment->name}} @lang("$string_file.statistics")</h3>
                                            </div>
                                            @php $ride_statistics = !empty($segment->ride) && isset($segment->ride) ? $segment->ride : NULL;
                                            @endphp
                                            <div class="panel-body">
                                                <div class="row">
                                                    @if($segment->is_coming_soon == 1)
                                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                                            <h5>@lang("$string_file.segment_coming_soon_text")</h5>
                                                        </div>
                                                    @else
                                                        @if(Auth::user('merchant')->can("ride_management_$segment->slag"))
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.all.ride', ['slug' => $segment->slag]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-info"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-calculator"></i>
                                                                            </button>

                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_rides")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($ride_statistics) ? $ride_statistics->all_rides : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.activeride',['slug' => $segment->slag]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-warning"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-road"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.on_going_rides")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($ride_statistics) ? $ride_statistics->ongoing : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.cancelride', ['slug' => $segment->slag]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-danger"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-times"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.cancelled_rides")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($ride_statistics) ? $ride_statistics->cancelled : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.completeride', ['slug' => $segment->slag]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-success"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon wb-check"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.completed_rides")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($ride_statistics) ? $ride_statistics->completed : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.autocancel', ['slug' => $segment->slag]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-danger"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-times"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.auto_cancelled")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($ride_statistics) ? $ride_statistics->auto_cancelled : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if(Auth::user('merchant')->can('corporate') && isset($merchant->Configuration->corporate_admin) && $merchant->Configuration->corporate_admin == 1)
                                                            <div class="col-xl-4 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('corporate.index')}}">
                                                                    <div class="card card-shadow">
                                                                        <div class="card-block bg-white ml-20 p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-primary"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-industry"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.corporate_panels")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ $corporates }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if(Auth::user('merchant')->can('view_reports_charts'))
                                                            <div class="col-xl-4 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{route("merchant.taxi-services-report",['segment_id'=>$segment->id])}}">
                                                                    <div class="card card-shadow">
                                                                        <div class="card-block bg-white ml-20 p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-info"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon  fa fa-money"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_earning")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($segment) ?  $segment->total_earning : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-4 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{route("merchant.taxi-services-report",['segment_id'=>$segment->id])}}">
                                                                    <div class="card card-shadow">
                                                                        <div class="card-block bg-white ml-20 p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-danger"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon  fa fa-minus-square-o"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_discount")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($segment) ?  $segment->total_discount : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    <!-- Food Statistics -->
                        @if(!empty($home_delivery))
                            @foreach($home_delivery as $segment)
                                @if(Auth::user('merchant')->can($segment->slag) || Auth::user('merchant')->can("view_reports_charts"))
                                    <div class="col-12 col-md-12 col-sm-12">
                                        <!-- Example Panel With Heading -->
                                        <div class="panel panel-bordered">
                                            <div class="panel-heading">
                                                <div class="panel-actions"></div>
                                                <h3 class="panel-title">{{$segment->Name($merchant_id)}} @lang("$string_file.statistics")</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    @if($segment->Merchant[0]->pivot->is_coming_soon == 1)
                                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                                            <h5>@lang("$string_file.segment_coming_soon_text")</h5>
                                                        </div>
                                                    @else
                                                        @if(Auth::user('merchant')->can("view_business_segment_$segment->slag"))
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.business-segment',$segment->slag) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-success"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-columns"></i>
                                                                            </button>
                                                                            @php $bs_statistics = !empty($segment->BusinessSegment) ? $segment->BusinessSegment->count() : 0;  $store = $segment->slag == "FOOD" ? trans("$string_file.restaurants") : trans("$string_file.stores") ; @endphp
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total") {{$store}}</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                @php $bs_statistics = !empty($segment->BusinessSegment) ? $segment->BusinessSegment->count() : 0; @endphp
                                                                                <span class="font-size-18 font-weight-100">{{ $bs_statistics }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if(Auth::user('merchant')->can("category_$segment->slag"))
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.category') }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-info"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-cubes"></i>
                                                                            </button>
                                                                            @php $category_statistics = !empty($segment->Category) ? $segment->Category->count() : 0;  @endphp
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_categories")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ $category_statistics }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                            <a href="#">
                                                                <div class="card card-shadow"
                                                                     style="margin-bottom:0.243rem">
                                                                    <div class="card-block bg-white p-20">
                                                                        <button type="button"
                                                                                class="btn btn-floating btn-sm btn-info"
                                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                            <i class="icon fa-glass"></i>
                                                                        </button>
                                                                        @php $product_statistics = !empty($segment->Product) ? $segment->Product->count() : 0;  @endphp
                                                                        <span class="ml-10 font-weight-400">@lang("$string_file.total_products")</span>
                                                                        <div class="content-text text-center mb-0">
                                                                            <span class="font-size-18 font-weight-100">{{ $product_statistics }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        @if(Auth::user('merchant')->can("order_statistics_$segment->slag"))
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('merchant.business-segment.statistics',[$segment->slag]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-warning"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-shopping-bag"></i>
                                                                            </button>
                                                                            @php $order_statistics = !empty($segment->Order) ? $segment->Order->count() : 0;  @endphp
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_orders")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ $order_statistics }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif

                                                        @if(Auth::user('merchant')->can('view_reports_charts'))
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{route("merchant.delivery-services-report",['segment_id'=>$segment->id])}}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-info"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa fa-money"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_earning")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ $segment->total_earning }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{route("merchant.delivery-services-report",['segment_id'=>$segment->id])}}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-danger"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa fa-minus-square-o"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_discount")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ $segment->total_discount }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    <!-- Handyman Services Statistics -->
                        @if(!empty($handyman_booking_statistics) && Auth::user('merchant')->can("HANDYMAN"))
                            @foreach($handyman_booking_statistics as $segment)
                                @if(Auth::user('merchant')->can("booking_management_HANDYMAN") || Auth::user('merchant')->can("view_reports_charts"))
                                    <div class="col-12 col-md-12 col-sm-12">
                                        <!-- Example Panel With Heading -->
                                        <div class="panel panel-bordered">
                                            <div class="panel-heading">
                                                <div class="panel-actions"></div>
                                                <h3 class="panel-title">{{$segment->name}} @lang("$string_file.booking_statistics")</h3>
                                            </div>
                                            @php $booking_statistics = !empty($segment->booking) && isset($segment->booking) ? $segment->booking : NULL; @endphp
                                            <div class="panel-body">
                                                <div class="row">
                                                    @if($segment->is_coming_soon == 1)
                                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                                            <h5>@lang("$string_file.segment_coming_soon_text")</h5>
                                                        </div>
                                                    @else
                                                        @if(Auth::user('merchant')->can("booking_management_HANDYMAN"))
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('handyman.orders',['segment_id'=>$segment->id,'order_status'=>NULL]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-info"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-calculator"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($booking_statistics ) ? $booking_statistics->all_bookings : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('handyman.orders',['segment_id'=>$segment->id,'order_status'=>6]) }}">
                                                                    {{--                                                <a href="@if(Auth::user('merchant')->can('active_ride')) {{ route('merchant.activeride') }} @else # @endif">--}}
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-warning"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-road"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.on_going")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($booking_statistics ) ? $booking_statistics->ongoing :0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('handyman.orders',['segment_id'=>$segment->id,'order_status'=>2]) }}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-danger"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa-times"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.cancelled")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($booking_statistics ) ? $booking_statistics->cancelled : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{ route('handyman.orders',['segment_id'=>$segment->id,'order_status'=>7]) }}">
                                                                    {{--                                                <a href="@if(Auth::user('merchant')->can('completed_ride')) {{ route('merchant.completeride') }} @else # @endif">--}}
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-success"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon wb-check"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.completed")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($booking_statistics ) ? $booking_statistics->completed : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if(Auth::user('merchant')->can('view_reports_charts'))
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{route("merchant.handyman-services-report",['segment_id'=>$segment->id])}}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-info"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa fa-money"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_earning")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($segment ) ? $segment->total_earning : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                                                <a href="{{route("merchant.handyman-services-report",['segment_id'=>$segment->id])}}">
                                                                    <div class="card card-shadow"
                                                                         style="margin-bottom:0.243rem">
                                                                        <div class="card-block bg-white p-20">
                                                                            <button type="button"
                                                                                    class="btn btn-floating btn-sm btn-danger"
                                                                                    style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                                                <i class="icon fa fa-minus-square-o"></i>
                                                                            </button>
                                                                            <span class="ml-10 font-weight-400">@lang("$string_file.total_discount")</span>
                                                                            <div class="content-text text-center mb-0">
                                                                                <span class="font-size-18 font-weight-100">{{ !empty($segment ) && !empty($segment->total_discount) ? $segment->total_discount : 0 }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection