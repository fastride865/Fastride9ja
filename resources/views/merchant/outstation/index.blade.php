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
                        <a href="{{route('outstationpackage.create')}}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin: 10px;">
                                <i class="wb-plus" title="@lang('admin.message102')"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-building-o" aria-hidden="true"></i>
                        @lang("$string_file.outstation_service")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.special_city")</th>
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $packages->firstItem() @endphp
                        @foreach($packages as $package)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>@if(empty($package->LanguageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $package->LanguageAny->LanguageName->name }}
                                                            : {{ $package->LanguageAny->city }}
                                                            )</span>
                                    @else
                                        {{ $package->LanguageSingle->city }}
                                    @endif
                                </td>
                                <td>
                                    {{$package->ServiceType->serviceName}}
                                </td>
                                <td>@if(empty($package->LanguageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary map_address">( In {{ $package->LanguageAny->LanguageName->name }}
                                                            : {{ substr($package->LanguageAny->description,0,50) }}
                                                            )</span>
                                    @else
                                        <span class="map_address">{{ substr($package->LanguageSingle->description,0,50) }}..</span>
                                    @endif
                                </td>
                                <td>
                                    @if($package->status  == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('outstationpackage.edit',$package->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                    @if($package->status == 1)
                                        <a href="{{ route('merchant.outstationpackage.active-deactive',['id'=>$package->id,'status'=>2]) }}"
                                           data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.outstationpackage.active-deactive',['id'=>$package->id,'status'=>1]) }}"
                                           data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i class="fa fa-eye"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $packages, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b>@lang("$string_file.special_city")
                            (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('outstationpackage.store')  }}">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.name")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="city" name="city" placeholder="" required>
                        </div>
                        <label> @lang("$string_file.description")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary" data-dismiss="modal"
                               value="@lang("$string_file.reset")">
                        <input type="submit" class="btn btn-outline-primary" value="@lang("$string_file.save")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
    {{--    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDkKetQwosod2SZ7ZGCpxuJdxY3kxo5Po&v=3.exp&libraries=places&language=en&region=ES"></script>--}}
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=get_merchant_google_key(NULL, 'admin_backend');?>&v=3.exp&libraries=places&language=en&region=ES"></script>
    <script>
        function initialize() {
            var input = document.getElementById('city');
            var options = {
                types: ['(cities)'],
            };
            var autocomplete = new google.maps.places.Autocomplete(input, options);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
@endsection