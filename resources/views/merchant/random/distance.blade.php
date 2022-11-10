@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title mb-0 d-inline-block">@lang('admin.message397')</h3>
                </div>
                @if(Auth::user('merchant')->can('edit_distnace'))
                    <div class="content-header-right col-md-8 col-12">
                        <div class="btn-group float-md-right">
                            <div class="heading-elements">
                                <a href="{{route('merchant.distnace.create')}}">
                                    <button type="button" class="btn btn-icon btn-success mr-1"><i
                                                class="fa fa-plus"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            <div class="content-body">
                <section id="horizontal">
                    <div class="row">
                        <div class="card">
                            <div class="card-content collapse show">
                                <div class="">
                                    <table class="table display nowrap table-striped table-bordered scroll-horizontal">
                                        <thead>
                                        <tr>
                                        <tr>
                                            <th>@lang('admin.message398')</th>
                                            <th>@lang('admin.message399')</th>
                                            <th>@lang('admin.lastlat')</th>
                                            <th>@lang('admin.maxlat')</th>
                                            <th>@lang('admin.minilat')</th>
                                            <th>@lang('admin.unnamed')</th>
                                            <th>@lang("$string_file.min") @lang('admin.speed')</th>
                                            <th>@lang('admin.max') @lang('admin.speed')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($distance)
                                            @php $settings = json_decode($distance->distance_methods,true) @endphp
                                            @foreach($settings as $key => $setting)
                                                <tr>
                                                    @switch($key)
                                                        @case(0)
                                                        <td>First</td>
                                                        @break
                                                        @case(1)
                                                        <td>Second</td>
                                                        @break
                                                        @case(2)
                                                        <td>Third</td>
                                                        @case(3)
                                                        <td>Fourth</td>
                                                        @break
                                                    @endswitch
                                                    <td>{{ $setting['method_name'] }}</td>
                                                    <td>
                                                        @if($setting['last_timestamp_difference'] != "")
                                                            {{ $setting['last_timestamp_difference'] }}
                                                        @else
                                                            -----
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($setting['maximum_timestamp_difference'] != "")
                                                            {{ $setting['maximum_timestamp_difference'] }}
                                                        @else
                                                            -----
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($setting['minimum_lat_long'] != "")
                                                            {{ $setting['minimum_lat_long'] }}
                                                        @else
                                                            -----
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($setting['unknown_road'] != "")
                                                            YES
                                                        @else
                                                            NO
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($setting['min_speed'] != "")
                                                            {{ $setting['min_speed'] }}
                                                        @else
                                                            -----
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($setting['max_speed'] != "")
                                                            {{ $setting['max_speed'] }}
                                                        @else
                                                            -----
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

            </div>
        </div>
    </div>
@endsection