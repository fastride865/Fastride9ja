@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
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
                    <h3 class="panel-title"><i class="fa-cubes" aria-hidden="true"></i>
                        {{ $driver->first_name." ".$driver->last_name }} @lang("$string_file.subscription_packages") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.package_details") </th>
                            <th>@lang("$string_file.maximum_rides")</th>
                            <th>@lang("$string_file.used_rides") </th>
                            <th>@lang("$string_file.package_valid_from")</th>
                            <th>@lang("$string_file.package_valid_to")</th>
{{--                            <th>@lang("$string_file.package_valid_period")</th>--}}
                            <th>@lang("$string_file.payment_method")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.buy_date")</th>
                        </tr>
                        </thead>
                        @php $sr = $driver_all_packs->firstItem();
                            $arr_status = \Config::get('custom.driver_sub_package_status');
                            $arr_package_type = \Config::get('custom.package_type');
                        @endphp
                        <tbody>
                        @forelse($driver_all_packs as $driver_all_pack)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>
                                    @lang("$string_file.name") :&nbsp;{{ $driver_all_pack->SubscriptionPackage->name }}<br>
                                    @lang("$string_file.price") :&nbsp;{{ $driver_all_pack->price }}<br>
                                    @lang("$string_file.package_type") :&nbsp; {{ $arr_package_type[$driver_all_pack->package_type] }}
                                </td>
                                <td>
                                    {{ $driver_all_pack->package_total_trips }}
                                </td>
                                <td>
                                    {{ $driver_all_pack->used_trips }}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($driver_all_pack->start_date_time, $driver_all_pack->Driver->CountryArea->timezone,null,$driver_all_pack->Driver->Merchant) !!}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($driver_all_pack->end_date_time, $driver_all_pack->Driver->CountryArea->timezone,null,$driver_all_pack->Driver->Merchant) !!}
{{--                                    {{ $driver_all_pack->start_date_time }} - {{ $driver_all_pack->end_date_time }}--}}
                                </td>
                                <td>
                                    {{ isset($driver_all_pack->PaymentMethod->payment_method) ?  $driver_all_pack->PaymentMethod->payment_method : '' }}
                                </td>
                                <td>
                                    @switch($driver_all_pack->status)
                                        @case(1)
                                        <span class="badge badge-success">@lang('admin.assigned')</span>
                                        @break;
                                        @case(2)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                        @break;
                                        @case(3)
                                        <span class="badge badge-danger">@lang('admin.expired')</span>
                                        @break;
                                        @case(4)
                                        <span class="badge badge-success">@lang('admin.carry_forwarded_to_next_package')</span>
                                        @break;
                                    @endswitch
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($driver_all_pack->created_at, $driver_all_pack->Driver->CountryArea->timezone,null,$driver_all_pack->Driver->Merchant) !!}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @empty
{{--                            <p class="alert alert-warning">No Subscription Package Bought.</p>--}}
                        @endforelse
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $driver_all_packs, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $driver_all_packs->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection