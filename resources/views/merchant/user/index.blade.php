@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->can('create_rider'))
                            <a href="{{route('excel.user',$data['export_search'])}}" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                            <a href="{{route('users.create')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add_user")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-users" aria-hidden="true"></i>
                       @lang("$string_file.user_management")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('merchant.user.search') }}" method="get">
                        <div class="table_search row p-3">
                            <div class="col-md-2 col-xs-6 active-margin-top">@lang("$string_file.search_by") :</div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <select class="form-control" name="parameter"
                                            id="parameter"
                                            required>
                                        <option value="1">@lang("$string_file.name")</option>
                                        <option value="2">@lang("$string_file.email")</option>
                                        <option value="3">@lang("$string_file.phone")</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" name="keyword"
                                           placeholder="@lang("$string_file.enter_text")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <select class="form-control" name="country_id"
                                            id="country_id">
                                        <option value="">--@lang("$string_file.country")--</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}"> {{
                                                                    $country->CountryName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"><i
                                            class="fa fa-search"
                                            aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.user_id")</th>
                            <th>@lang("$string_file.user_details")</th>
                            @if($config->gender == 1)
                                <th>@lang("$string_file.gender")</th>
                            @endif
                            <th>@lang("$string_file.service_statistics")</th>
                            <th>@lang("$string_file.wallet_money")</th>
{{--                            <th>@lang("$string_file.referral_code")</th>--}}
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
                                <td>{{$user->user_merchant_id}}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        <span class="long_text">   {!! nl2br("********".substr($user->last_name, -2)."\n"."********".substr($user->UserPhone, -2)."\n"."********".substr($user->email, -2)) !!}</span>
                                    </td>
                                @else
                                    <td>
                                        <span class="long_text">   {!! nl2br($user->first_name." ".$user->last_name."\n".$user->UserPhone."\n".$user->email) !!}</span>
                                    </td>
                                @endif
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
                                    @lang("$string_file.rating") :
                                    @if (!empty($user->rating) && $user->rating > 0)
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
                                    @else
                                     @lang("$string_file.not_rated_yet")
                                    @endif
                                </td>
                                <td><a class="hyperLink"
                                       href="{{ route('merchant.user.wallet',$user->id) }}" j>
                                        @if($user->wallet_balance)
                                            {{ $user->wallet_balance }}
                                        @else
                                            0.00
                                        @endif
                                    </a>
                                </td>
                                {{--                                <td>{{ $user->ReferralCode }}</td>--}}
                                <td>
                                    @lang("$string_file.user_type") :
                                    @if($user->user_type == 1)
                                     @lang("$string_file.corporate_user")
                                    @else
                                     @lang("$string_file.retail")
                                    @endif
                                    <br>
                                        @lang("$string_file.signup_type") :
                                    @switch($user->UserSignupType)
                                        @case(1)
                                        @lang("$string_file.app")/@lang("$string_file.admin")
                                        @break
                                        @case(2)
                                        @lang("$string_file.google")
                                        @break
                                        @case(3)
                                        @lang("$string_file.facebook")
                                        @break
                                    @endswitch
                                    <br>
                                    @lang("$string_file.signup_from") :
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
                                        @case(4)
                                        @lang("$string_file.whatsapp")
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    @if(isset($user->CountryArea->timezone))
                                        {!! convertTimeToUSERzone($user->created_at, $user->CountryArea->timezone, null, $user->Merchant) !!}
                                    @else
                                        {!! convertTimeToUSERzone($user->created_at, null, null, $user->Merchant) !!}
                                    @endif
                                </td>
                                <td>
                                    @if($user->UserStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="button-margin">
                                        @if(Auth::user('merchant')->can('edit_rider'))
                                            <a href="{{ route('users.edit',$user->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('users.show',$user->id) }}"
                                           class="btn btn-sm btn-info menu-icon btn_delete action_btn"
                                           data-original-title="@lang("$string_file.details")"
                                           data-toggle="tooltip"
                                           data-placement="top"><span class="fa fa-user"></span>
                                        </a>
                                        @if(Auth::user('merchant')->can('edit_rider'))
                                            @if($user->UserStatus == 1)
                                                <a href="{{ route('merchant.user.active-deactive',['id'=>$user->id,'status'=>2]) }}"
                                                   title="@lang("$string_file.inactive")" data-toggle="tooltip" data-placement="top"
                                                   class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                    <i class="fa fa-eye-slash"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('merchant.user.active-deactive',['id'=>$user->id,'status'=>1]) }}"
                                                   title="@lang("$string_file.active")" data-toggle="tooltip" data-placement="top"
                                                   class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if(Auth::user()->demo != 1)
                                            @if(Auth::user('merchant')->can('delete_rider'))
                                                <button onclick="DeleteEvent({{ $user->id }})"
                                                        type="submit" title="@lang("$string_file.delete")" data-toggle="tooltip"
                                                        data-placement="top" class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        @endif

                                            @if(Auth::user('merchant')->can('create_promotion'))

                                                <span data-target="#sendNotificationModelUser"
                                                      data-toggle="modal"
                                                      id="{{ $user->id }}"><a
                                                            title="@lang("$string_file.send_notification")"
                                                            data-toggle="tooltip"
                                                            id="{{ $user->id }}"
                                                            data-placement="top"
                                                            class="btn  text-white btn-sm btn-warning menu-icon btn_eye action_btn">
                                                    <i class="wb-bell"></i> </a></span>

                                            @endif
                                            @if($config->user_wallet_status == 1)
                                                <span data-target="#addMoneyModel" data-toggle="modal" id="{{ $user->id }}">
                                                    <a title="@lang("$string_file.add_money")"
                                                       id="{{ $user->id }}" data-placement="top"
                                                       class="btn text-white btn-sm btn-success menu-icon btn_eye action_btn" role="menuitem">
                                                        <i class="icon fa-money"></i>
                                                    </a>
                                                </span>
                                            @endif
                                            @if($config->user_wallet_status == 1)
                                                <a href="{{ route('merchant.user.wallet',$user->id) }}"
                                                   title="@lang("$string_file.wallet_transaction")" data-placement="top"
                                                   class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem">
                                                    <i class="icon fa-window-maximize"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('merchant.user.favourite-location',$user->id) }}"
                                               class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem"
                                               title="@lang("$string_file.saved_address")"
                                               data-placement="top"><i class="icon fa fa-location-arrow"></i>
                                            </a>
                                            @if(isset($merchant) && $merchant->ApplicationConfiguration->favourite_driver_module == 1)
                                                <a href="{{ route('merchant.user.favourite-driver',$user->id) }}"
                                                   class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem"
                                                   title="@lang("$string_file.favourite_drivers")" data-placement="top">
                                                    <i class="icon fa fa-id-card"></i>
                                                </a>
                                            @endif
                                            @if ($config->user_document == 1)
                                                <a href="{{ route('merchant.user.documents',['id'=>$user->id]) }}"
                                                   title="@lang("$string_file.documents")" data-placement="top"
                                                   class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem">
                                                    <i class="icon fa fa-file"></i></a>
                                            @endif
                                    </div>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $users, 'data' => $data])
                    {{--                    <div class="pagination1 float-right">{{ $users->appends($data)->links() }}</div>--}}
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
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.send_notification") </b></label>
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
                                     placeholder=""></textarea>
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
                        <div class="input-group">
                            <input type="text" class="form-control customDatePicker1 bg-this-color"
                                   id="datepicker" name="date" readonly
                                   placeholder="">
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

    {{--add money in user wallet--}}
    <div class="modal fade text-left" id="addMoneyModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.add_money_in_wallet")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('merchant.user.add.wallet') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.payment_method") </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1">@lang("$string_file.cash")</option>
                                <option value="2">@lang("$string_file.non_cash")</option>
                            </select>
                        </div>

                        <label for="transaction_type">
                            @lang("$string_file.transaction_type")<span
                                    class="text-danger">*</span>
                        </label>
                        <div class="form-group">
                            <select id="transaction_type" name="transaction_type" class="form-control" required>
                                <option value="1">@lang("$string_file.credit")</option>
                                <option value="2">@lang("$string_file.debit")</option>
                            </select>
                        </div>

                        <label>@lang("$string_file.amount") </label>
                        <div class="form-group">
                            <input type="text" name="amount" placeholder=""
                                   class="form-control" required>
                            <input type="hidden" name="add_money_user_id" id="add_money_driver_id">
                        </div>

                        <label>@lang("$string_file.receipt_number") </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" placeholder=""
                                   class="form-control" required>
                        </div>
                        <label>@lang("$string_file.description") </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" id="sub" class="btn btn-primary" value="@lang("$string_file.save")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script>
        $('#sub').on('click', function () {
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
        });
    </script>
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
                        type: "GET",
                        url: "{{ route('merchant.user.delete') }}/" + id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('users.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection

