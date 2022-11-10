@extends('corporate.layouts.main')
@section('content')
    @csrf
    <div class="page">
        <div class="page-content">
{{--            @if(session('success'))--}}
{{--                <div class="alert dark alert-icon alert-success" role="alert">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                        <span aria-hidden="true">×</span>--}}
{{--                    </button>--}}
{{--                    <i class="icon wb-info" aria-hidden="true"></i> {{ session('success') }}--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if(session('error'))--}}
{{--                <div class="alert dark alert-icon alert-danger" role="alert">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                        <span aria-hidden="true">×</span>--}}
{{--                    </button>--}}
{{--                    <i class="icon wb-close" aria-hidden="true"></i> {{ session('error') }}--}}
{{--                </div>--}}
{{--            @endif--}}
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('corporate.user.create')}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus" title="@lang("$string_file.add_user")"></i>
                            </button>
                        </a>
                        {{--<a href="{{route('excel.user')}}" data-toggle="tooltip">--}}
                            {{--<button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">--}}
                                {{--<i class="wb-download" title="@lang("$string_file.export_excel")"></i>--}}
                            {{--</button>--}}
                        {{--</a>--}}
                        <a href="{{ route('corporate.user.import.fail') }}">
                            <button type="button" class="btn btn-icon btn-danger float-right" style="margin:10px">
                                @lang("$string_file.fail_import")
                                <span class="badge badge-pill">{{$failImport}}</span>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-users" aria-hidden="true"></i>
                        @lang("$string_file.user_management")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{route('corporate.user.import')}}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="table_search row p-3">
                            <div class="col-md-4 col-xs-6 active-margin-top">
                                <input type="file" class="form-control" name="import_data"/>
                            </div>
                            <div class="col-md-2 col-xs-6 form-group active-margin-top">
                                <button class="btn btn-success" type="submit" value="Import"><i
                                            class="icon wb-upload"
                                            aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('corporate.user.search') }}" method="post">
                        @csrf
                        <div class="table_search row p-3 ">
                            <div class="col-md-2 col-xs-6 active-margin-top">@lang("$string_file.search_by") :</div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <select class="form-control" name="parameter" id="parameter" required>
                                    <option value="1">@lang("$string_file.name")</option>
                                    <option value="2">@lang("$string_file.email")</option>
                                    <option value="3">@lang("$string_file.phone")</option>
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-12 form-group active-margin-top">
                                <select class="form-control" name="country_id" id="country_id" required>
                                    <option value="">--@lang("$string_file.select")--</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}"> {{ $country->CountryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <input type="text" id="keyword" name="keyword" placeholder="@lang("$string_file.enter_text")" class="form-control" type="text">
                            </div>
                            <div class="col-sm-2 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.profile_image")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.designation")</th>
                            @if($config->gender == 1)
                                <th>@lang("$string_file.gender")</th>
                            @endif
                            <th>@lang("$string_file.service_statistics")</th>
                            <th>@lang("$string_file.signup_details")</th>
                            <th>@lang("$string_file.registered_date")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $users->firstItem() @endphp
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $sr }}  </td>
                                <td><img class="rounded-circle" height="60px" width="60px"
                                         src="{{get_image($user->UserProfileImage,'corporate_user',$merchant->id)}}">
                                </td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        <span class="long_text">   {!! nl2br("********".substr($user->last_name, -2)."\n"."********".substr($user->UserPhone, -2)."\n"."********".substr($user->email, -2)) !!}</span>
                                    </td>
                                @else
                                    <td>
                                        <span class="long_text">   {!! nl2br($user->first_name." ".$user->last_name."\n".$user->UserPhone."\n".$user->email) !!}</span>
                                    </td>
                                @endif
                                <td>
                                   {{$user->emp_designation}}
                                </td>
                                @if($config->gender == 1)
                                    @switch($user->user_gender)
                                        @case(1)
                                        <td>@lang("$string_file.male")</td>
                                        @break
                                        @case(2)
                                        <td>@lang("$string_file.female")</td>
                                        @break
                                        @default
                                        <td>------</td>
                                    @endswitch
                                @endif
                                <td>
                                    @if($user->total_trips)
                                        {{ $user->total_trips }}  @lang("$string_file.rides")
                                    @else
                                        @lang("$string_file.no_ride")
                                    @endif
                                    <br>
                                    @if ($user->rating == "0.0")
                                        @lang("$string_file.not_rated_yet")
                                    @else
                                        @while($user->rating>0)
                                            @if($user->rating >0.5)
                                                <img src="{{ view_config_image("static-images/star.png") }}"
                                                     alt='Whole Star'>
                                            @else
                                                <img src="{{ view_config_image('static-images/halfstar.png') }}"
                                                     alt='Half Star'>
                                            @endif
                                            @php $user->rating--; @endphp
                                        @endwhile
                                    @endif
                                </td>

                                <td>
                                    @if($user->user_type == 1)
                                         @lang("$string_file.corporate_user")
                                    @else
                                        @lang("$string_file.retail")
                                    @endif
                                    <br>
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
                                    <br>
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
                                </td>
                                <td>{{ $user->created_at->toformatteddatestring() }}</td>
                                <td>
                                    @if($user->UserStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        @if(Auth::user('merchant')->can('create_promotion'))
                                            <span data-target="#sendNotificationModelUser"
                                                  data-toggle="modal" id="{{ $user->id }}"><a
                                                        data-original-title="@lang("$string_file.send_notification")"
                                                        data-toggle="tooltip"
                                                        id="{{ $user->id }}"
                                                        data-placement="top"
                                                        class="btn text-white btn-sm btn-warning menu-icon btn_detail action_btn"> <i
                                                            class="fa fa-bell"></i> </a></span>
                                        @endif
                                        <a href="{{ route('corporate.user.edit',$user->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>

                                        <a href="{{ route('corporate.user.show',$user->id) }}"
                                           class="btn btn-sm btn-info menu-icon btn_delete action_btn"
                                           data-original-title="@lang("$string_file.details")"
                                           data-toggle="tooltip"
                                           data-placement="top"><span
                                                    class="fa fa-user"></span></a>
                                        <a href="{{ route('corporate.user.favourite',$user->id) }}"
                                           class="btn btn-sm btn-info menu-icon btn_location action_btn"
                                           data-original-title="Favourite Locations"
                                           data-toggle="tooltip"
                                           data-placement="top"><span
                                                    class="fa fa-location-arrow"></span></a>
                                        @if(isset($merchant) && $merchant->ApplicationConfiguration->favourite_driver_module == 1)
                                            <a href="{{ route('corporate.user.favourite.driver',$user->id) }}"
                                               class="btn btn-sm btn-warning menu-icon btn_detail action_btn"
                                               data-original-title="Favourite Drivers"
                                               data-toggle="tooltip"
                                               data-placement="top"><span
                                                        class="fa fa-id-card"></span></a>
                                        @endif

                                        @if($user->UserStatus == 1)
                                            <a href="{{ route('corporate.user.change.status',['id'=>$user->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                <i class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('corporate.user.change.status',['id'=>$user->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                                <i class="fa fa-eye"></i> </a>
                                        @endif

                                        @if(Auth::user()->demo != 1)
                                            <button onclick="DeleteEvent({{ $user->id }})"
                                                    type="submit"
                                                    data-original-title="@lang("$string_file.delete")"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                                <i class="fa fa-trash"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="sendNotificationModelUser" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang("$string_file.send_notification") </label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('merchant.sendsingle-user') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.title") </label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="title"
                                   name="title"
                                   placeholder="" required>
                        </div>

                        <label>@lang("$string_file.message") </label>
                        <div class="form-group">
                           <textarea class="form-control" id="message" name="message"
                                     rows="3"
                                     placeholder="" required></textarea>
                        </div>

                        <label>@lang("$string_file.image") </label>
                        <div class="form-group">
                            <input type="file" class="form-control" id="image"
                                   name="image"
                                   placeholder="@lang("$string_file.image")">
                            <input type="hidden" name="persion_id" id="persion_id" required>
                        </div>
                        <label>@lang("$string_file.show_in_promotion") </label>
                        <div class="form-group">
                            <input type="checkbox" value="1" name="expery_check"
                                   id="expery_check_two">
                        </div>

                        <label>@lang("$string_file.expire_date") </label>
                        <div class="form-group">
                            <input type="text" class="form-control datepicker"
                                   id="datepicker-backend" name="date"
                                   placeholder="" disabled readonly>
                        </div>
                        <label>@lang("$string_file.url") </label>
                        <div class="form-group">
                            <input type="url" class="form-control" id="url"
                                   name="url"
                                   placeholder="@lang("$string_file.url")(@lang("$string_file.optional"))">
                            <label class="danger">@lang("$string_file.example") :  https://www.google.com/</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn" value="@lang("$string_file.send")">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_warning")",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        method: 'POST',
                        type: "DELETE",
                        data: {id: id},
                        url: "{{ route('corporate.user.destroy') }}",
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('user.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection

