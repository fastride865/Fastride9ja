@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
	        <div class="container-fluid">
        <div class="content-wrapper">
   <div class="card shadow mb-4">
      <div class="card-header py-3">
             <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2 ">
                    <h3 class="content-header-title mb-0 d-inline-block">
					 <i class=" fa fa-car" aria-hidden="true"></i>
					@lang("$string_file.completed_rides") </h3>
                </div>
                <div class="content-header-left col-md-4 col-12 mb-2 "></div>
                <div class="content-header-right col-md-4 col-12">
                    <div class="btn-group float-md-right">
                        <div class="heading-elements">
                            <div class="btn-group float-md-right">
                                <div class="heading-elements">
                                    <a href="{{route('excel.complete')}}" data-original-title="@lang("$string_file.export")" data-toggle="tooltip">
                                        <button type="button" class="btn btn-icon btn-primary mr-1"><i
                                                    class="fa fa-download" title="@lang("$string_file.export_excel")"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            </div>


			 <div class="content-body">
			 <section id="horizontal">

                    <div class="row">
                        <div class="col-12">
                            @if(session('noridecompleteexport'))
                                <div class="row container mx-auto">
                                    <div class="col-md-12 alert alert-icon-right alert-info alert-dismissible mb-2"
                                         role="alert">
                                        <span class="alert-icon"><i class="fa fa-info"></i></span>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                        <strong>@lang('admin.message448')</strong>
                                    </div>
                                </div>
                            @endif
                            <div class="card">

                                <div class="card-body  card-header p-3">
                                    <form method="post" action="{{ route('merchant.completeride.search') }}">
                                        @csrf
                                        <div class="table_search row">
                                             <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                                 Search By:
                                                 </div>
                                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                                <div class="input-group">
                                                    <input type="text" id="" name="booking_id"
                                                           placeholder="@lang("$string_file.ride_id")"
                                                           class="form-control col-md-12 col-xs-12">
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                                <div class="input-group">
                                                    <input type="text" id="" name="rider"
                                                           placeholder="@lang("$string_file.user_details")"
                                                           class="form-control col-md-12 col-xs-12">
                                                </div>
                                                 <p>@lang('admin.searchhint')</p>
                                            </div>
                                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                                <div class="input-group">
                                                    <input type="text" id="" name="driver"
                                                           placeholder="@lang("$string_file.driver_details")"
                                                           class="form-control col-md-12 col-xs-12">
                                                </div>
                                                 <p>@lang('admin.searchhint')</p>
                                            </div>

                                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                                <div class="input-group">
                                                    <input type="text" id="" name="date"
                                                           placeholder="@lang("$string_file.ride")  @lang("$string_file.date")"
                                                           class="form-control col-md-12 col-xs-12 datepickersearch"
                                                           id="datepickersearch" autocomplete="off">
                                                </div>
                                            </div>


                                            <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                                                <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                            class="fa fa-search" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                                <div class="card-content collapse show" style="margin:1%">
										<table  class="table table-responsive display nowrap table-striped table-bordered " id="dataTable" width="100%" cellspacing="0">

                                            <thead>
                                            <tr>
                                                <th>@lang("$string_file.ride_id")</th>
                                                <th>@lang("$string_file.ride_type")</th>
                                                <th>@lang("$string_file.user_details")</th>
                                                <th>@lang("$string_file.driver_details")</th>
                                                <th>@lang("$string_file.request_from")</th>
                                                <th>@lang("$string_file.ride_details")</th>
                                                <th>@lang("$string_file.pickup_location")</th>
                                                <th>@lang("$string_file.drop_off_location")</th>
                                                <th>@lang("$string_file.payment_method")</th>
                                                <th>@lang("$string_file.bill_amount")</th>
                                                <th>@lang('admin.bookingdate')</th>
                                                <th>@lang("$string_file.action")</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($bookings as $booking)
                                                <tr>
                                                    <td>{{ $booking->merchant_booking_id }}</td>
                                                    <td>
                                                        @if($booking->booking_type == 1)
                                                            @lang("$string_file.ride_now")
                                                        @else
                                                            @lang("$string_file.ride")  @lang("$string_file.later")
                                                        @endif
                                                    </td>

                                                    @if(Auth::user()->demo == 1)
                                                    <td>
                                                         <span class="long_text">
                                                        {{ "********".substr($booking->User->UserName,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->User->UserPhone,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->User->email,-2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                         <span class="long_text">
                                                         {{ "********".substr($booking->Driver->last_name,-2) }}
                                                        <br>
                                                       {{ "********".substr($booking->Driver->phoneNumber,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->Driver->email,-2) }}
                                                        </span>
                                                    </td>
                                                    @else
                                                    <td>
                                                         <span class="long_text">
                                                        {{ $booking->User->UserName }}
                                                        <br>
                                                        {{ $booking->User->UserPhone }}
                                                        <br>
                                                        {{ $booking->User->email }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                         <span class="long_text">
                                                         {{ $booking->Driver->first_name.' '.$booking->Driver->last_name }}
                                                        <br>
                                                        {{ $booking->Driver->phoneNumber }}
                                                        <br>
                                                        {{ $booking->Driver->email }}
                                                        </span>
                                                    </td>
                                                    @endif

                                                    <td>
                                                        @switch($booking->platform)
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
                                                    </td>
                                                    @php
                                                      $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                                    @endphp
                                                    <td>{!! nl2br($booking->CountryArea->CountryAreaName ."\n". $service_text."\n".$booking->VehicleType->VehicleTypeName) !!}</td>

                                                    <td>
                                                        @if(!empty($booking->BookingDetail->start_location))
                                                        <a title="{{ $booking->BookingDetail->start_location }}"
                                                           class="long_text hyperLink" target="_blank"
                                                           href="https://www.google.com/maps/place/{{ $booking->BookingDetail->start_location }}">{{ $booking->BookingDetail->start_location }}</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!empty($booking->BookingDetail->end_location))
                                                        <a title="{{ $booking->BookingDetail->end_location }}"
                                                           class="long_text hyperLink" target="_blank"
                                                           href="https://www.google.com/maps/place/{{ $booking->BookingDetail->end_location }}">{{ $booking->BookingDetail->end_location }}</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $booking->PaymentMethod->payment_method }}
                                                    </td>
                                                    <td>
                                                        {{ $booking->CountryArea->Country->isoCode . $booking->final_amount_paid }}
                                                    </td>
                                                    <td>
                                                        {{ $booking->created_at->toDayDateTimeString() }}
                                                    </td>
                                                    <td>
                                                        <a target="_blank" title="@lang("$string_file.requested_drivers")"
                                                           href="{{ route('merchant.ride-requests',$booking->id) }}"
                                                           class="btn btn-sm btn-success menu-icon btn_detail action_btn"><span
                                                                    class="fa fa-list-alt"></span></a>

                                                        <a target="_blank" title="@lang("$string_file.ride_details")"
                                                           href="{{ route('merchant.booking.details',$booking->id) }}"
                                                           class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                                    class="fa fa-info-circle"
                                                                    title="Booking Details"></span></a>

                                                        <a target="_blank" title="@lang('"$string_file.invoice"')"
                                                           href="{{ route('merchant.booking.invoice',$booking->id) }}"
                                                           class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                                    class="fa fa-print"></span></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                </div>
                                <div class="pagination1 float-right">{{ $bookings->links() }}</div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

        </div>
        </div>
    </div>
    </div>
@endsection
