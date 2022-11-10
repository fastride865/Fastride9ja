@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
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
                        <button type="button" class="btn btn-icon btn-success float-right" style="margin: 10px;"
                                data-toggle="modal" data-target="#inlineForm">
                            <i class="wb-plus" title="@lang("$string_file.add_package")"></i>
                        </button>
                    </div>
                    <h3 class="panel-title"><i class="fa-gift" aria-hidden="true"></i>
                        @lang("$string_file.package_based_services")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.package_name")</th>
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.terms_conditions")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $packages->firstItem() @endphp
                        @foreach($packages as $package)
                            <tr>
                                <td>{{$sr}}</td>
                                <td>@if(empty($package->LanguagePackageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $package->LanguagePackageAny->LanguageName->name }}
                                                            : {{ $package->LanguagePackageAny->name }}
                                                            )</span>
                                    @else
                                        {{ $package->LanguagePackageSingle->name }}
                                    @endif
                                </td>

                                <td>{{$package->ServiceType->serviceName}}</td>

                                <td>@if(empty($package->LanguagePackageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary map_address">( In {{ $package->LanguagePackageAny->LanguageName->name }}
                                                            : {{ $package->LanguagePackageAny->description }}
                                                            )</span>
                                    @else
                                        <span class="map_address">{{ $package->LanguagePackageSingle->description }}</span>
                                    @endif
                                </td>
                                <td>@if(empty($package->LanguagePackageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary map_address">( In {{ $package->LanguagePackageAny->LanguageName->name }}
                                                            :{{  $package->LanguagePackageAny->terms_conditions }}
                                                            )</span>
                                    @else
                                        <span class="map_address">{{  $package->LanguagePackageSingle->terms_conditions }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($package->packageStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('packages.edit',$package->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                        <i class="fa fa-edit"></i> </a>

                                    @if($package->packageStatus == 1)
                                        <a href="{{ route('merchant.rental.packages.active-deactive',['id'=>$package->id,'status'=>2]) }}"
                                           data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.rental.packages.active-deactive',['id'=>$package->id,'status'=>1]) }}"
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
                    {{--                   <div class="pagination1 float-right">{{ $packages->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
    {{--add package model--}}
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b>@lang("$string_file.add_package")
                            ( @lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('packages.store')  }}">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.package_name")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="" required>
                        </div>
                        <label>@lang("$string_file.service_type")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            {!! Form::select('service_type_id',add_blank_option($arr_services,trans("$string_file.select")),old('service_type_id'),['id'=>'package_service_type','class'=>'form-control','required'=>true]) !!}

                        </div>

                        <label> @lang("$string_file.description")<span class="text-danger">*</span> </label>
                        <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="" required></textarea>
                        </div>

                        <label>  @lang("$string_file.terms_conditions")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3"
                                      placeholder="" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary" data-dismiss="modal"
                               value="@lang("$string_file.cancel")">
                        <input type="submit" class="btn btn-outline-primary" value="@lang("$string_file.save")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection