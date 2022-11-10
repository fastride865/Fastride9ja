@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="{{route('excel.ratings')}}" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                        </div>
                    <h3 class="panel-title"><i class="fa-star-o" aria-hidden="true"></i>
                    @lang("$string_file.reviews_and_symbol_ratings")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('merchant.ratings.search') }}" method="get">
                        <div class="table_search row">
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="booking_id"
                                           placeholder="@lang("$string_file.ride_id")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="rider"
                                           placeholder="@lang("$string_file.user_details")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="driver"
                                           placeholder="@lang("$string_file.driver_details")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>

                            <div class="col-sm-2  col-xs-12 form-group ">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="col-sm-4 float-right form-group">

                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.ride_id")</th>
                                <th>@lang("$string_file.ride_type")</th>
                                <th>@lang("$string_file.user_details")</th>
                                <th>@lang("$string_file.rating_by_user") </th>
                                <th>@lang("$string_file.user")  @lang("$string_file.review")</th>
                                <th>@lang("$string_file.driver_details")</th>
                                <th>@lang("$string_file.rating")  @lang("$string_file.by_driver")</th>
                                <th>@lang("$string_file.driver")  @lang("$string_file.review")</th>
                                <th>@lang("$string_file.date_and_symbol_time")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = $ratings->firstItem() @endphp
                            @foreach($ratings as $rating)
                                <tr>
                                    <td>{{ $sr }}</td>
                                    <td><a target="_blank" class="address_link"
                                           href="{{ route('merchant.booking.details',$rating->booking_id) }}">{{ $rating->Booking->merchant_booking_id }}</a>
                                    </td>
                                    <td>
                                        @if($rating->Booking->booking_type == 1)
                                            @lang("$string_file.ride_now")
                                        @else
                                            @lang("$string_file.ride")  @lang("$string_file.later")
                                        @endif
                                    </td>

                                    @if(Auth::user()->demo == 1)
                                        <td>
                                            <span class="long_text">
                                                {{ "********".substr($rating->Booking->User->UserName,-2) }}
                                               <br>
                                               {{ "********".substr($rating->Booking->User->UserPhone,-2) }}
                                               <br>
                                              {{ "********".substr($rating->Booking->User->email,-2) }}
                                            </span>
                                        </td>
                                    @else
                                        <td>
                                            <span class="long_text">
                                                {{ $rating->Booking->User->UserName }}
                                               <br>
                                               {{ $rating->Booking->User->UserPhone }}
                                               <br>
                                              {{ $rating->Booking->User->email }}
                                            </span>
                                        </td>
                                    @endif
                                    <td>
                                        @if ($rating->user_rating_points)
                                            @while($rating->user_rating_points>0)
                                                @if($rating->user_rating_points >0.5)
                                                    <img src="{{ view_config_image("static-images/star.png") }}"
                                                         alt='Whole Star'>
                                                @else
                                                    <img src="{{ view_config_image('static-images/halfstar.png') }}"
                                                         alt='Half Star'>
                                                @endif
                                                @php $rating->user_rating_points--; @endphp
                                            @endwhile
                                        @else
                                            @lang("$string_file.not_rated_yet")
                                        @endif
                                    </td>
                                    <td>
                                        @if($rating->user_comment)
                                            {{ $rating->user_comment }}
                                        @else
                                            ------
                                        @endif
                                    </td>
                                    @if(Auth::user()->demo == 1)
                                        <td>
                                            <span class="long_text">
                                                 {{ "********".substr($rating->Booking->Driver->last_name,-2) }}
                                            <br>
                                            {{ "********".substr($rating->Booking->Driver->phoneNumber,-2) }}
                                            <br>
                                            {{ "********".substr($rating->Booking->Driver->email,-2) }}
                                                </span>
                                        </td>
                                    @else
                                        <td>
                                            <span class="long_text">
                                                 {{ $rating->Booking->Driver->first_name." ".$rating->Booking->Driver->last_name }}
                                            <br>
                                            {{ $rating->Booking->Driver->phoneNumber }}
                                            <br>
                                            {{ $rating->Booking->Driver->email }}
                                                </span>
                                        </td>
                                    @endif
                                    <td>
                                        @if ($rating->driver_rating_points)
                                            @while($rating->driver_rating_points>0)
                                                @if($rating->driver_rating_points >0.5)
                                                    <img src="{{ view_config_image("static-images/star.png") }}"
                                                         alt='Whole Star'>
                                                @else
                                                    <img src="{{ view_config_image('static-images/halfstar.png') }}"
                                                         alt='Half Star'>
                                                @endif
                                                @php $rating->driver_rating_points--; @endphp
                                            @endwhile
                                        @else
                                            @lang("$string_file.not_rated_yet")
                                        @endif
                                    </td>
                                    <td>
                                        @if($rating->driver_comment)
                                            {{ $rating->driver_comment }}
                                        @else
                                            ------
                                        @endif
                                    </td>
                                    <td>{{ $rating->created_at->toDateString()}}
                                    <br>
                                    {{ $rating->created_at->toTimeString() }}</td>
                                </tr>
                                @php $sr++;  @endphp
                            @endforeach
                            </tbody>
                        </table>
                        <div class="pagination1 float-right">{{ $ratings->appends($data)->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection