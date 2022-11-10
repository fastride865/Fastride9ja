@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('users.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-map" aria-hidden="true"></i>
                        {{ $user->first_name." ".$user->last_name }}'s @lang("$string_file.saved_address")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.address")</th>
                            <th>@lang("$string_file.latitude_longitude")</th>
                            <th>@lang("$string_file.title")</th>
                            <th>@lang("$string_file.category")</th>
                            <th>@lang("$string_file.created_at")</th>
{{--                            <th>@lang("$string_file.updated_at")</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @php $a = 1; @endphp
                        @foreach($user->UserAddress as $location)
                            <tr>
                                <td>{{ $a }}</td>
                                <td>{{ $location->address }}</td>
                                <td>{{ $location->latitude.','.$location->longitude }}</td>
                                <td>
                                    {{ $location->address_title }}
                                </td>
                                @switch($location->category)
                                    @case(1)
                                    <td>@lang("$string_file.home")</td>
                                    @break
                                    @case(2)
                                    <td>@lang("$string_file.work")</td>
                                    @break
                                    @case(3)
                                    <td>
                                        @if($location->other_name)
                                            {{ $location->other_name }}
                                        @else
                                            @lang("$string_file.other")
                                        @endif
                                    </td>
                                    @break
                                    @default
                                    <td>----</td>
                                @endswitch

                                <td>
                                    @if(isset($user->CountryArea->timezone))
                                        {!! convertTimeToUSERzone($location->created_at, $user->CountryArea->timezone, null, $user->Merchant) !!}
                                    @else
                                        {!! convertTimeToUSERzone($location->created_at, null, null, $user->Merchant) !!}
                                    @endif
                                </td>
                            </tr>
                            @php $a++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection