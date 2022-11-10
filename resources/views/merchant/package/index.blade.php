@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @if(session('package'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message103')
                </div>
            @endif
            @if(session('success'))
                <div class="alert dark alert-icon alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i> {{ session('success') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            @if(Auth::user('merchant')->can('create_package'))
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin: 10px;" data-toggle="modal" data-target="#inlineForm">
                                    <i class="wb-plus" title="@lang('admin.message102')"></i>
                                </button>
                            @endif
                        </div>
                    <h3 class="panel-title"><i class="fa-gift" aria-hidden="true"></i>
                        @lang('admin.message433')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang('admin.message188')</th>
                            <th>@lang('admin.message99')</th>
                            <th>@lang('admin.message100')</th>
                            <th>@lang('admin.message101')</th>
                            <th>@lang('admin.Status')</th>
                            <th>@lang('admin.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $packages->firstItem() @endphp
                        @foreach($packages as $package)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>@if(empty($package->LanguagePackageSingle))
                                        <span style="color:red">{{ trans('admin.name-not-given') }}</span>
                                        <span class="text-primary">( In {{ $package->LanguagePackageAny->LanguageName->name }}
                                                            : {{ $package->LanguagePackageAny->name }}
                                                            )</span>
                                    @else
                                        {{ $package->LanguagePackageSingle->name }}
                                    @endif
                                </td>
                                <td>@if(empty($package->LanguagePackageSingle))
                                        <span style="color:red">{{ trans('admin.name-not-given') }}</span>
                                        <span class="text-primary map_address">( In {{ $package->LanguagePackageAny->LanguageName->name }}
                                                            : {{ $package->LanguagePackageAny->description }}
                                                            )</span>
                                    @else
                                        <span class="map_address">{{ $package->LanguagePackageSingle->description }}</span>
                                    @endif
                                </td>
                                <td>@if(empty($package->LanguagePackageSingle))
                                        <span style="color:red">{{ trans('admin.name-not-given') }}</span>
                                        <span class="text-primary map_address">( In {{ $package->LanguagePackageAny->LanguageName->name }}
                                                            :{{  $package->LanguagePackageAny->terms_conditions }}
                                                            )</span>
                                    @else
                                        <span class="map_address">{{  $package->LanguagePackageSingle->terms_conditions }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($package->packageStatus == 1)
                                        <span class="badge badge-success">@lang('admin.active')</span>
                                    @else
                                        <span class="badge badge-danger">@lang('admin.deactive')</span>
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit_package'))
                                        <a href="{{ route('packages.edit',$package->id) }}"
                                           data-original-title="Edit" data-toggle="tooltip" data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>
                                    @endif

                                    @if($package->packageStatus == 1)
                                        <a href="{{ route('merchant.rental.packages.active-deactive',['id'=>$package->id,'status'=>2]) }}"
                                           data-original-title="Inactive" data-toggle="tooltip" data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.rental.packages.active-deactive',['id'=>$package->id,'status'=>1]) }}"
                                           data-original-title="Active" data-toggle="tooltip" data-placement="top"
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
    {{--add package model--}}
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b>@lang('admin.message102')
                            (@lang('admin.message459') {{ strtoupper(Config::get('app.locale')) }})</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('packages.store')  }}">
                    @csrf
                    <div class="modal-body">

                        <label>@lang('admin.message99')<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="@lang('admin.message658')" required>
                        </div>


                        <label> @lang('admin.description')<span class="text-danger">*</span> </label>
                        <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="@lang('admin.message648')"></textarea>
                        </div>


                        <label>  @lang('admin.message101')<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3"
                                      placeholder="@lang('admin.message659')"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary" data-dismiss="modal"
                               value="@lang('admin.message366')">
                        <input type="submit" class="btn btn-outline-primary" value="@lang('admin.message365')">
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection