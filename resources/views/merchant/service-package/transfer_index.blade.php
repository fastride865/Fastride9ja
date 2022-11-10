@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid">
            <div class=" content-wrapper">
                <div class="card shadow mb-4">
                    <div class="col-md-6 col-12">
                        @if(session('package'))
                            <div class="col-md-6 alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                                <span class="alert-icon"><i class="fa fa-info"></i></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>@lang('admin.message103')</strong>
                            </div>
                        @endif
                    </div>
                        <div class="card-header py-md-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="form-section">@lang('admin.message434')</h4>
                                </div>
                                @if(Auth::user('merchant')->can('create_package'))
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-icon btn-success mr-1 float-right"
                                            title="@lang('admin.message102')" data-toggle="modal"
                                            data-target="#inlineForm">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                        </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTable"
                                                   class="table table-white-space table-bordered row-grouping display no-wrap icheck table-middle">
                                <thead>
                                <tr>
                                    <th>@lang("$string_file.sn")</th>
                                    <th>@lang('admin.message99')</th>
                                    <th>@lang('admin.message100')</th>
                                    <th>@lang('admin.message101')</th>
                                    <th>@lang("$string_file.status")</th>
                                    <th>@lang("$string_file.action")</th>
                                </tr>
                                </thead>
                                <tfoot></tfoot>
                                <tbody>
                                @php $sr = $packages->firstItem() @endphp
                                @foreach($packages as $package)
                                    <tr>
                                        <td>{{ $sr  }}</td>
                                        <td>@if(empty($package->LanguagePackageSingle))
                                                <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                                <span class="text-primary">( In {{ $package->LanguagePackageAny->LanguageName->name }}
                                                                : {{ $package->LanguagePackageAny->name }}
                                                                )</span>
                                            @else
                                                {{ $package->LanguagePackageSingle->name }}
                                            @endif
                                        </td>
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
                                                <label class="label_success">@lang("$string_file.active")</label>
                                            @else
                                                <label class="label_danger">@lang("$string_file.inactive")</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if(Auth::user('merchant')->can('edit_package'))
                                                <a href="{{ route('packages.edit',$package->id) }}"
                                                   data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-warning"> <i
                                                            class="fa fa-edit"></i> </a>
                                            @endif

                                            @if($package->packageStatus == 1)
                                                <a href="{{ route('merchant.rental.packages.active-deactive',['id'=>$package->id,'status'=>2]) }}"
                                                   data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-danger"> <i
                                                            class="fa fa-eye-slash"></i> </a>
                                            @else
                                                <a href="{{ route('merchant.rental.packages.active-deactive',['id'=>$package->id,'status'=>1]) }}"
                                                   data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-success"> <i
                                                            class="fa fa-eye"></i> </a>
                                            @endif

                                        </td>
                                    </tr>
                                    @php $sr++  @endphp
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="col-sm-12">
                        <div class="pagination1">{{ $packages->links() }}</div>
                    </div>
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
                            (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('transferpackage.store')  }}">
                    @csrf
                    <div class="modal-body">

                        <label>@lang('admin.message99') : <span class="danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="@lang('admin.message658')" required>
                        </div>


                        <label> @lang("$string_file.description") :
                            <span class="danger">*</span>: </label>
                        <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="@lang('admin.message648')"></textarea>
                        </div>


                        <label>  @lang('admin.message101') :
                            <span class="danger">*</span>: </label>
                        <div class="form-group">
                            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3"
                                      placeholder="@lang('admin.message659')"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection