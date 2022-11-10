@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        {{--request()->get('area_id')--}}
        <div class="app-content content">
            <div class="content-wrapper">
                @if(session('reward'))
                    <div class="box no-border">
                        <div class="box-tools">
                            <p class="alert alert-warning alert-dismissible">
                                {{ session('reward') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                            </p>
                        </div>
                    </div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h3 class="content-header-title mb-0 d-inline-block"><i
                                    class="far fa-money-bill-alt"></i> @lang('admin.reward.points')</h3>
                        <div class="btn-group float-md-right">
                            <div class="heading-elements">
                                <div class="btn-group float-md-left">
                                    <div class="heading-elements">
                                        {{-- <a href="{{route('excel.pricecard')}}">
                                            <button type="button" class="btn btn-icon btn-primary mr-1" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                                        class="fa fa-download"></i>
                                            </button>
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user('merchant')->can('create_reward'))
                            <div class="btn-group float-md-right">
                                <div class="heading-elements">
                                    <a href="{{route('reward-points.create')}}">
                                        <button type="button" title="@lang("$string_file.add") @lang('admin.reward.add')"
                                                class="btn btn-icon btn-success mr-1"><i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display nowrap table-striped table-bordered" id="dataTable" width="100%"
                                   cellspacing="0">
                                <thead>
                                <tr>
                                    <th class="text-capitalize">@lang("$string_file.sn")</th>
                                    <th class="text-capitalize">@lang('admin.area')</th>
                                    <th class="text-capitalize">@lang('admin.registration.enable')</th>
                                    <th class="text-capitalize">@lang('admin.user.registration.reward')</th>
                                    <th class="text-capitalize">@lang('admin.driver.registration.reward')</th>
                                    <th class="text-capitalize">@lang('admin.referral.enable')</th>
                                    <th class="text-capitalize">@lang('admin.user.referral.reward')</th>
                                    <th class="text-capitalize">@lang('admin.driver.referral.reward')</th>
                                    <th class="text-capitalize">@lang('admin.reward.equals')</th>
                                    <th class="text-capitalize">@lang('admin.max.redeem')</th>
                                    <th class="text-capitalize">@lang('admin.trips.count')</th>
                                    <th class="text-capitalize">@lang("$string_file.active")</th>
                                    @if(Auth::user('merchant')->can('edit_reward') || Auth::user('merchant')->can('delete_reward'))
                                        <th class="text-capitalize">@lang('admin.actions')</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $sn = 0;
                                @endphp
                                @foreach ($rewards as $reward)


                                    <tr>
                                        <td>{{ ++$sn }}</td>
                                        <td>{{$reward->countryArea->CountryAreaName}}</td>
                                        <td>
                                            @if ($reward->registration_enable == 1)
                                                <span class="text-success"> @lang('admin.enabled') </span>
                                            @else
                                                <span class="text-danger"> @lang('admin.disabled') </span>
                                            @endif
                                        </td>

                                        <td>{{ $reward->user_registration_reward}}</td>
                                        <td>{{ $reward->driver_registration_reward }}</td>
                                        <td>
                                            @if ($reward->referral_enable == 1)
                                                <span class="text-success"> @lang('admin.enabled') </span>
                                            @else
                                                <span class="text-danger"> @lang('admin.disabled') </span>
                                            @endif
                                        </td>

                                        <td>{{ $reward->user_referral_reward}}</td>
                                        <td>{{ $reward->driver_referral_reward }}</td>

                                        <td>
                                            {{$reward->value_equals}}
                                        </td>

                                        <td>{{$reward->max_redeem}}</td>
                                        <td>{{$reward->trips_count}}</td>
                                        <td>
                                            @if ($reward->active == 1)
                                                <span class="text-success"> @lang("$string_file.active") </span>
                                            @else
                                                <span class="text-danger"> @lang("$string_file.inactive") </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if(Auth::user('merchant')->can('edit_reward'))
                                                <a class="mr-1 btn btn-sm btn-warning"
                                                   href="{{route('reward-points.edit' , ['id' => $reward->id])}}">
                                                    <span class="fas fa-edit"></span>
                                                </a>
                                            @endif
                                            @if(Auth::user('merchant')->can('delete_reward'))
                                                <button class="btn btn-sm btn-danger" onclick="
                                                        if(confirm('Do you want to delete ?')) {
                                                        $('#delete-reward-{{$reward->id}}').submit();
                                                        }
                                                        ">
                                                    <span class="fas fa-trash"></span>
                                                </button>
                                                <form id="delete-reward-{{$reward->id}}" method="post"
                                                      action="{{route('reward-points.destroy' , ['id' => $reward->id])}}">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
