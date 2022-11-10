@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            {{--            @if($packages)--}}
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
                        {{--                            @if(Auth::user('merchant')->can('create_subscriptions'))--}}
                        <a href="{{url('/merchant/admin/subscription/add')}}">
                            <button type="button" title="@lang('admin.subspack_add')"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-plus"></i>
                            </button>
                        </a>
                        {{--                            @endif--}}
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.subscription_package_management")</h3>
                </header>
                <div class="panel-body">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th> {{--S.no--}}
                            <th>@lang("$string_file.package_name")</th>
                            <th>@lang("$string_file.package_type")</th>
                            <th>@lang("$string_file.price")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.duration")</th>
                            <th>@lang("$string_file.maximum_rides") </th>
                            <th>@lang("$string_file.expire_date")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        @php $sr = $packages->firstItem();
                                                        $arr_package = get_package_type();
                        @endphp
                        <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>@if(empty($package->LangSubscriptionPackageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $package->LangSubscriptionPackageAny->LanguageName->name }}
                                                                            : {{ $package->LangSubscriptionPackageAny->name }}
                                                                            )</span>
                                    @else
                                        {{ $package->LangSubscriptionPackageSingle->name }}
                                    @endif
                                </td>
                                <td>
                                    {{ $arr_package[$package->package_type] }}
                                </td>
                                <td>
                                    {{ $package->price }}
                                </td>
                                <td><img src="{{ get_image($package->image,'package') }}"
                                         align="center" width="80px" height="80px"
                                         class="img-radius"
                                         alt="Image">
                                </td>

                                <td>@if(!empty($package->PackageDuration->NameAccMerchant))
                                        @if(empty($package->PackageDuration->LangPackageDurationAccMerchantSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $package->PackageDuration->LangPackageDurationAccMerchantAny->LanguageName->name }}
                                                                        : {{ $package->PackageDuration->LangPackageDurationAccMerchantAny['name'] }}
                                                                        )</span>
                                        @else
                                            {{ $package->PackageDuration->LangPackageDurationAccMerchantSingle['name'] }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ $package->max_trip }}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($package->expire_date, null, null, $package->Merchant, 2) !!}
                                </td>

                                <td>
                                    @if( !empty($package->expire_date) && $package->expire_date < date('Y-m-d'))
                                        <label class="label_danger"
                                               style="width:60px;display: inline-block;"> @lang('admin.expired')</label>
                                    @else
                                        @if($package->status == 1)
                                            <span class="badge badge-success">@lang("$string_file.active")</span>
                                        @else
                                            <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                        @endif
                                    @endif
                                </td>

                                <td>

                                    <div class="button-margin">
                                        <a href="{{ url('merchant/admin/subscription/add/'.$package->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i>
                                        </a>
                                    </div>
                                    @csrf
                                    <div>
                                        @if($package->status == 1)
                                            <a href="{{ route('subscription.changepackstatus',['id'=>$package->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('subscription.changepackstatus',['id'=>$package->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i>
                                            </a>
                                        @endif
                                        <button onclick="DeleteEvent({{ $package->id }})" type="submit"
                                                data-original-title="@lang("$string_file.delete")"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn"><i
                                                    class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @empty
                            {{--                                @if(($packages->total() > 0) ||  (isset($_REQUEST['keyword'])))--}}
                            {{--                                    <p class="alert alert-warning">No Subscription Found.</p>--}}
                            {{--                                @else--}}
                            {{--                                    <p class="alert alert-warning">No Subscription added yet. <a href="{{ route('subscription.create') }}"><br>Create one now</a></p>--}}
                            {{--                                @endif--}}
                        @endforelse
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $packages, 'data' => []])
                    {{--                        <div class="pagination1" style="float:right;">{{$packages->links()}}</div>--}}
                </div>
            </div>
            {{--            @endif--}}
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
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
                        type: "DELETE",
                        url: 'subscription/' + id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('subscription.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection

