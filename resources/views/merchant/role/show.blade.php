@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="{{ route('role.index') }}">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-list" aria-hidden="true"></i>
                        @lang('admin.message673')
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="firstName3">
                                    @lang("$string_file.role")
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name"
                                       name="name"
                                       value="{{ $role->display_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="firstName3">
                                    @lang("$string_file.description")
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="description"
                                          name="description" rows="2" readonly>{{ $role->description }}
                                </textarea>
                            </div>
                        </div>
                    </div>

                    <table class="table table-default">
                        {{--<tr>--}}
                        {{--<th>@lang('admin.message520')</th>--}}
                        {{--<th>@lang('admin.message521')</th>--}}
                        {{--<th>@lang('admin.message522')</th>--}}
                        {{--<th>@lang("$string_file.edit")</th>--}}
                        {{--<th>@lang('admin.message524')</th>--}}
                        {{--</tr>--}}
                        @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission['display_name'] }} :</td>
                                @if(!empty($permission['children']))
                                    @foreach($permission['children'] as $child)
                                        <td><input type="checkbox" name="permission" @if(in_array($child['id'], $permission_array)) checked @endif>{{ $child['display_name'] }}</td>
                                    @endforeach
                                @else
                                    <td><input type="checkbox" name="permission" @if(in_array($permission['id'], $permission_array)) checked @endif>view</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

