@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->can('create_promotion'))
                            <a href="{{route('promotions.create')}}">
                                <button type="button"
                                        title="@lang("$string_file.notification")"
                                        class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                            class="wb-plus"></i>
                                </button>
                            </a>
                        @endif
                        <a href="{{route('excel.promotionnotifications',$search_param)}}">
                            <button type="button" class="btn btn-icon btn-primary float-right"
                                    style="margin:10px"
                                    data-original-title="@lang("$string_file.export_excel")"
                                    data-toggle="tooltip">
                                <i class="icon fa-download"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-bell" aria-hidden="true"></i>
                        @lang("$string_file.promotional_notification")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="get" action="{{ route('promotions.search') }}">
                        <div class="table_search">
                            <div class="row">

                                <div class="col-md-4  form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="title" value="{{isset($search_param['title']) ? $search_param['title'] : ""}}"
                                               placeholder="@lang("$string_file.title")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-12 form-group ">
                                    <div class="input-group">
                                        {!! Form::select('application',[''=>trans("$string_file.application"),1=>trans("$string_file.driver"),2=>trans("$string_file.user")],isset($search_param['application']) ? $search_param['application'] : NULL,['class'=>'form-control','id'=>'application']) !!}
{{--                                        <select class="form-control" name="application"--}}
{{--                                                id="application">--}}
{{--                                            <option value="">--@lang("$string_file.application")--</option>--}}
{{--                                            <option value="1">@lang("$string_file.driver")</option>--}}
{{--                                            <option value="2">@lang("$string_file.user")</option>--}}
{{--                                        </select>--}}
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="date" value="{{isset($search_param['date']) ? $search_param['date'] : ""}}"
                                               placeholder="@lang("$string_file.date")"
                                               class="form-control col-md-12 col-xs-12 datepickersearch"
                                               id="datepickersearch">
                                    </div>
                                </div>
                                <div class="col-sm-2  col-xs-12 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.title")</th>
                            <th>@lang("$string_file.message")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.url")</th>
                            <th>@lang("$string_file.application")</th>
                            <th>@lang("$string_file.receiver")</th>
                            <th>@lang("$string_file.show_in_promotion")</th>
                            <th>@lang("$string_file.expire_date")</th>
                            <th>@lang("$string_file.created_at")</th>
                            @if(Auth::user('merchant')->can('edit_promotion') || Auth::user('merchant')->can('delete_promotion'))
                                <th>@lang("$string_file.action")</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $promotions->firstItem() @endphp
                        @foreach($promotions as $promotion)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    @if($promotion->country_area_id)
                                        {{ $promotion->CountryArea->CountryAreaName }}
                                    @else
                                        ------
                                    @endif
                                </td>
                                <td>
                                    {{ $promotion->title }}
                                </td>
                                <td>
                                    <span class="map_address">{{ $promotion->message }}</span>
                                </td>
                                <td>
                                    <img src="{{ get_image($promotion->image, 'promotions') }}"
                                         align="center" width="100px" height="80px"
                                         class="img-radius"
                                         alt="Promotion Notification Image">
                                </td>
                                <td>
                                    <a title="{{ $promotion->url }}"
                                       href="{{ $promotion->url }}" class="btn btn-icon btn-success ml-20"><i class="icon wb-link"></i></a>
                                </td>
                                @switch($promotion->application)
                                    @case(1)
                                    <td>@lang("$string_file.driver")</td>
                                    @break
                                    @case(2)
                                    <td>@lang("$string_file.user")</td>
                                    @break
                                @endswitch
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        @switch($promotion->application)
                                            @case(1)
                                            @if($promotion->driver_id == 0)
                                                @lang("$string_file.all_drivers")
                                            @else
                                                {{ "********".substr($promotion->Driver->last_name, -2) }}
                                                <br>
                                                {{ "********".substr($promotion->Driver->phoneNumber, -2) }}
                                                <br>
                                                {{ "********".substr($promotion->Driver->email, -2) }}
                                            @endif
                                            @break
                                            @case(2)
                                            @if($promotion->user_id == 0)
                                                @lang("$string_file.all_users")
                                            @else
                                                {{  "********".substr($promotion->User->UserName, -2) }}
                                                <br>
                                                {{ "********".substr($promotion->User->UserPhone, -2) }}
                                                <br>
                                                {{  "********".substr($promotion->User->email, -2) }}
                                            @endif
                                            @break
                                        @endswitch
                                    </td>
                                @else
                                    <td>
                                        @switch($promotion->application)
                                            @case(1)
                                            @if($promotion->driver_id == 0)
                                                @lang("$string_file.all_drivers")
                                            @else
                                                @if($promotion->Driver)
                                                    {{ $promotion->Driver->first_name." ".$promotion->Driver->last_name }}
                                                    <br>
                                                    {{ $promotion->Driver->phoneNumber }}
                                                    <br>
                                                    {{ $promotion->Driver->email }}
                                                @else
                                                    ---
                                                @endif
                                            @endif
                                            @break
                                            @case(2)
                                            @if($promotion->user_id == 0)
                                                @lang("$string_file.all_users")
                                            @else
                                                @if($promotion->User)
                                                    {{ $promotion->User->UserName }}
                                                    <br>
                                                    {{ $promotion->User->UserPhone }}
                                                    <br>
                                                    {{ $promotion->User->email }}
                                                @else
                                                    -----
                                                @endif
                                            @endif
                                            @break
                                        @endswitch
                                    </td>
                                @endif
                                <td>
                                    @if($promotion->show_promotion == 1)
                                        @lang("$string_file.yes")
                                    @else
                                        @lang("$string_file.no")
                                    @endif
                                </td>
                                <td>
                                    @if($promotion->show_promotion == 1)
                                        @if(isset($promotion->CountryArea->timezone))
                                            {!! convertTimeToUSERzone($promotion->expiry_date, $promotion->CountryArea->timezone, null, $promotion->Merchant, 2) !!}
                                        @else
                                            {!! convertTimeToUSERzone($promotion->expiry_date, null, null, $promotion->Merchant, 2) !!}
                                        @endif
                                    @else
                                        -----
                                    @endif
                                </td>
                                <td>
                                    @if(isset($promotion->CountryArea->timezone))
                                        {!! convertTimeToUSERzone($promotion->created_at, $promotion->CountryArea->timezone, null, $promotion->Merchant) !!}
                                    @else
                                        {!! convertTimeToUSERzone($promotion->created_at, null, null, $promotion->Merchant) !!}
                                    @endif
                                </td>
                                @if(Auth::user('merchant')->can('edit_promotion') || Auth::user('merchant')->can('delete_promotion'))
                                    <td>
                                        @if(Auth::user('merchant')->can('edit_promotion'))
                                            <a href="{{ route('promotions.edit',$promotion->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i> </a>
                                        @endif
                                        @if(Auth::user('merchant')->can('delete_promotion'))
                                            <a href="{{ route('promotions.delete',$promotion->id) }}"
                                               data-original-title="@lang("$string_file.delete")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                                <i class="fa fa-trash"></i> </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $promotions, 'data' => $data])
                    {{--                    <div class="pagination1 float-right">{{ $promotions->appends($data)->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

