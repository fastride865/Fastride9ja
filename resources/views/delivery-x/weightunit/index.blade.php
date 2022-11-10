@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('weightunitadded'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('weightunitadded') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <button type="button" title="@lang('admin.add_new')"data-toggle="modal" data-target="#myModal"
                                class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                        </button>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                        @lang('admin.weightunits_list')</h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>#Sr.No</th>
                            <th>@lang('admin.name')</th>
                            <th>@lang('admin.message206')</th>
                            <th>@lang('admin.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($weightunits as $weightunit)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $weightunit->WeightUnitName }}
                                </td>
                                <td>
                                    {{ $weightunit->WeightUnitDescription }}
                                </td>
                                <td>


                                    <form method="POST" action="{{ route('weightunit.destroy',$weightunit['id']) }}"
                                          onsubmit="return confirm('@lang('admin.are_you_sure')')">
                                        @csrf
                                        {{method_field('DELETE')}}
                                        <a href="{{ route('weightunit.edit',$weightunit['id']) }}"
                                           data-original-title="Edit" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>

                                    </form>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><b>@lang('admin.add')</b></h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('weightunit.store')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="name">@lang('admin.message106')</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="@lang('admin.message189')">
                        </div>
                        <div class="form-group">
                            <label for="description">@lang('admin.description')</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="@lang('admin.description')">
                        </div>
                        <div class="modal-footer">
                            <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal" value="@lang('admin.message366')">
                            <input type="submit" class="btn btn-outline-primary btn" value="@lang('admin.message365')">
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

