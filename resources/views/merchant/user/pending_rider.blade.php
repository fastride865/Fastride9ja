@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('moneyAdded'))
                <div class="col-md-8 alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-info"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong>@lang('admin.message430')</strong>
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                        @if(Auth::user('merchant')->can('edit_drivers'))
                            <a href="{{ route('driver.add-subscription-pack',$driver->id) }}" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add_subscription_package") "></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        @lang('admin.pendingRider')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('pending_search_approval') }}" method="post">
                        @csrf
                        <div class="table_search row ">
                            <div class="col-md-3 col-xs-3 form-group ">
                                @lang("$string_file.search_by") :
                            </div>
                            <div class="col-md-2 col-xs-3 form-group ">
                                <div class="input-group">
                                    <select class="form-control" name="parameter" id="parameter" required>
                                        <option value="1">@lang("$string_file.name")</option>
                                        <option value="2">@lang("$string_file.email")</option>
                                        <option value="3">@lang("$string_file.phone")</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-3 form-group ">
                                <div class="input-group">
                                    <input type="text" name="keyword" placeholder="@lang("$string_file.enter_text")"
                                           class="form-control col-md-12 col-xs-12" required>
                                </div>
                            </div>
                            <div class="col-sm-2  col-xs-3 form-group ">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.user_details")</th>
                            @if($config->gender == 1)
                                <th>Gender</th>
                            @endif
                            <th>@lang("$string_file.service_statistics")</th>
                            <th>@lang("$string_file.referral_code")</th>
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
                                <td>
                                    <span class="long_text">   {!! nl2br($user->first_name." ".$user->last_name."\n".$user->UserPhone."\n".$user->email) !!}</span>
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
                                <td>{{ $user->ReferralCode }}</td>
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
                                        <label class="label_success">@lang("$string_file.active")</label>
                                    @else
                                        <label class="label_danger">@lang("$string_file.inactive")</label>
                                    @endif
                                </td>
                                <td>
                                    <div class="button-margin">
                                        @if(Auth::user('merchant')->can('edit_rider'))
                                            <a href="{{ route('users.edit',$user->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i> </a>
                                        @endif
                                        <button onclick="DeleteEvent({{ $user->id }})"
                                                type="submit"
                                                data-original-title="@lang("$string_file.delete")"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                            <i class="fa fa-trash"></i></button>

                                        @if ($config->user_document ==1)
                                            <a href="{{ route('merchant.user.documents',['id'=>$user->id]) }}"
                                               data-original-title="documents" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-info menu-icon action_btn">
                                                <i class="fa fa-file"></i> </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $users, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $users->links() }}</div>--}}
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
                                   placeholder="@lang('admin.message627')" required>
                        </div>

                        <label>@lang("$string_file.message") </label>
                        <div class="form-group">
                           <textarea class="form-control" id="message" name="message"
                                     rows="3"
                                     placeholder="@lang('admin.message628')"></textarea>
                        </div>

                        <label>@lang("$string_file.image") </label>
                        <div class="form-group">
                            <input type="file" class="form-control" id="image"
                                   name="image"
                                   placeholder="@lang("$string_file.image")">
                            <input type="hidden" name="persion_id" id="persion_id">
                        </div>

                        <label>@lang("$string_file.url") </label>
                        <div class="form-group">
                            <input type="url" class="form-control" id="url"
                                   name="url"
                                   placeholder="@lang("$string_file.url")(@lang("$string_file.optional"))">
                            <label class="danger">@lang("$string_file.example") :  https://www.google.com/</label>
                        </div>

                        <label>@lang("$string_file.show_in_promotion") </label>
                        <div class="form-group">
                            <input type="checkbox" value="1" name="expery_check"
                                   id="expery_check_two" onclick="show()">
                        </div>

                        <label>@lang("$string_file.expire_date") </label>
                        <div class="form-group">
                            <input type="text" class="form-control datepicker"
                                   id="datepicker-backend" name="date"
                                   placeholder="@lang('admin.message689')" disabled>
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
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang("$string_file.add_money_in_wallet")</label>
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

                        <label>@lang("$string_file.amount") </label>
                        <div class="form-group">
                            <input type="text" name="amount" placeholder="@lang('admin.message164')"
                                   class="form-control" required>
                            <input type="hidden" name="add_money_user_id" id="add_money_driver_id">
                        </div>

                        <label>@lang("$string_file.receipt_number") </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" placeholder="@lang('admin.message630')"
                                   class="form-control" required>
                        </div>
                        <label>@lang("$string_file.description") </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder="@lang('admin.message632')"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function show() {
            if (document.getElementById("expery_check_two").checked = true) {
                document.getElementById('datepicker-backend').disabled = false;
            }
        }

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
                        type: "DELETE",
                        url: 'users/' + id,
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



