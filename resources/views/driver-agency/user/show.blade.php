@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ URL::previous() }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-info-circle" aria-hidden="true"></i>
                        @lang("$string_file.user_details")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <div id="user-profile">
                        <div class="row">
                            <!-- Column -->
                            <div class="col-md-4 col-xs-12">
                                <div class="card shadow">
                                    <div class="card-block text-center">
                                        <img  src="{{ get_image($user->UserProfileImage,'user',$user->merchant_id) }}"
                                              class="rounded-circle" width="120" height="120">
                                        @if(Auth::user()->demo == 1)
                                            <h5 class="user-name mb-3">{{ "********".substr($user->last_name,-2) }}</h5>
                                            <p class="user-job mb-3">{{ "********".substr($user->UserPhone,-2) }}</p>
                                            <p class="user-info mb-3">{{ "********".substr($user->email,-2) }}</p>
                                        @else
                                            <h5 class="user-name mb-3">{{ $user->first_name." ".$user->last_name }}</h5>
                                            <p class="user-job mb-3">{{ $user->UserPhone }}</p>
                                            <p class="user-info mb-3">{{ $user->email }}</p>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-xs-12 mt-20">
                                <div class="row mb-5">
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2 ">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-car fa-2x text-gray-300"></i>
                                                    @lang("$string_file.user_type")
                                                </div>
                                                <div class="mb-0">
                                                    @if($user->user_type == 1)
                                                        @lang("$string_file.corporate_user")
                                                    @else
                                                        @lang("$string_file.retail")
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="white-box"> -->
                                        <!-- <ul class="book_details"> -->
                                        <!-- <li> -->
                                    <!-- <h4>@lang("$string_file.user_type")</h4> -->
                                        <!-- <p> -->
                                    <!-- @if($user->user_type == 1) -->
                                    <!-- @lang("$string_file.corporate_user") -->
                                    <!-- @else -->
                                    <!-- @lang("$string_file.retail") -->
                                    <!-- @endif -->
                                        <!-- </p> -->
                                        <!-- </li> -->
                                        <!-- </ul> -->
                                        <!-- </div> -->
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-tag fa-2x text-gray-300"></i>
                                                    @lang("$string_file.referral_code")
                                                </div>
                                                <div class="mb-0 text-gray-800">{{ $user->ReferralCode }}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="white-box"> -->
                                        <!-- <ul class="book_details"> -->
                                        <!-- <li> -->
                                    <!-- <h4>@lang("$string_file.referral_code")</h4> -->
                                    <!-- <p>{{ $user->ReferralCode }}</p> -->
                                        <!-- </li> -->
                                        <!-- </ul> -->
                                        <!-- </div> -->
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-signing fa-2x text-gray-300"></i>
                                                    @lang("$string_file.signup_type")
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    @switch($user->UserSignupType)
                                                        @case(1)
                                                        @lang("$string_file.normal")
                                                        @break
                                                        @case(2)
                                                        @lang("$string_file.google")
                                                        @break
                                                        @case(3)
                                                        @lang("$string_file.facebook")
                                                        @break
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-mobile fa-2x text-gray-300"></i>
                                                    @lang("$string_file.signup_plateform")
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    @switch($user->UserSignupFrom)
                                                        @case(1)
                                                        @lang("$string_file.application")
                                                        @break
                                                        @case(2)
                                                        @lang("$string_file.admin")
                                                        @break
                                                        @case(3)
                                                        @lang("$string_file.web")
                                                        @break
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i>
                                                    @lang("$string_file.registered_date")
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    {!! convertTimeToUSERzone($user->created_at, null,null,$user->Merchant,2) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="white-box"> -->
                                        <!-- <ul class="book_details"> -->
                                        <!-- <li> -->
                                    <!-- <h4>@lang("$string_file.registered_date")</h4> -->
                                    <!-- <p>{{ $user->created_at }}</p> -->
                                        <!-- </li> -->
                                        <!-- </ul> -->
                                        <!-- </div> -->
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i>
                                                    @lang("$string_file.update")
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    {!! convertTimeToUSERzone($user->updated_at, null,null,$user->Merchant,2) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="white-box"> -->
                                    <!-- <h4>@lang("$string_file.update")</h4> -->
                                    <!-- <p>{{ $user->updated_at }}</p> -->
                                        <!-- </div> -->
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    @if($appConfig->gender == 1)
                                        <div class="col-md-6 col-sm-6 col-xs-12" >
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="h5 text-uppercase mb-1">
                                                        <i class="icon fa-sign-in fa-2x text-gray-300"></i>
                                                        @lang("$string_file.gender")
                                                    </div>
                                                    <div class="mb-0 text-gray-800">
                                                        @if($user->user_gender == 1) @lang("$string_file.male") @else @lang("$string_file.female")  @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($appConfig->smoker == 1)
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="h5 text-uppercase mb-1">
                                                        <i class="icon wi-smoke text-gray-300" area-hidden="true"></i>
                                                         @lang("$string_file.smoke")
                                                    </div>
                                                    <div class="mb-0 text-gray-800">
                                                    </div>
                                                    @if($user->smoker_type == 1)  @lang("$string_file.smoker") @else  @lang("$string_file.non_smoker") @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mt-30">
                                <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>@lang("$string_file.sn")</th>
                                        <th>@lang("$string_file.ride_type")</th>
                                        <th>@lang("$string_file.service_area") </th>
                                        <th>@lang("$string_file.service_type")</th>
                                        <th> @lang("$string_file.vehicle")  @lang("$string_file.type")</th>
                                        <th>@lang("$string_file.pickup_location")</th>
                                        <th>@lang('admin.message445')</th>
                                        <th>@lang("$string_file.created_at")</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $sr = $bookings->firstItem() @endphp
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $sr }}</td>
                                            <td>
                                                @if($booking->booking_type == 1)
                                                    @lang("$string_file.ride_now")
                                                @else
                                                    @lang("$string_file.ride_later")
                                                @endif
                                            </td>
                                            <td> {{ $booking->CountryArea->AreaName }}</td>
                                            <td> {{ $booking->ServiceType->serviceName }}</td>
                                            <td> {{ $booking->VehicleType->vehicleTypeName }}</td>
                                            <td> {{ $booking->pickup_location }}</td>
                                            <td> {{ $booking->drop_location }}</td>
                                            <td>
                                                {{ $booking->created_at }}
                                            </td>
                                        </tr>
                                        @php $sr++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="pagination1 float-right">{{ $bookings->links() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
