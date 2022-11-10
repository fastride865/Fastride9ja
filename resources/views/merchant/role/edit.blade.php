@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('role.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                    <i class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->edit_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-edit" aria-hidden="true"></i>
                        @lang("$string_file.edit_role")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('role.update',$role->id) }}">
                        @csrf
                        {{method_field('PUT')}}
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.role")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name"
                                               name="name"
                                               placeholder="@lang("$string_file.role")" value="{{ $role->display_name }}" required>
                                        @if ($errors->has('name'))
                                            <label class="text-danger">{{ $errors->first('name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.description")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description"
                                                  name="description" rows="2"
                                                  placeholder="@lang('admin.destination')">{{ $role->description }}</textarea>
                                        @if ($errors->has('description'))
                                            <label class="text-danger">{{ $errors->first('description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.check_all")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="checkbox" onclick="checkall(this);" name="permission[]" value="1">
                                    </div>
                                </div>
                            <!--<div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="firstName3">
                                                                @lang('admin.uncheck_all')
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="checkbox" onclick="checkall(this.value);" name="permission[]" value="0">
                                                        </div>
                                                    </div>-->
                            </div>
                            <table id="dataTable" class="table table-default table-striped">
                                {{--<tr>--}}
                                {{--<th>@lang('admin.message520')</th>--}}
                                {{--<th>@lang('admin.message521')</th>--}}
                                {{--<th>@lang('admin.message522')</th>--}}
                                {{--<th>@lang("$string_file.edit")</th>--}}
                                {{--<th>@lang('admin.message524')</th>--}}
                                {{--</tr>--}}
                                @php
                                    use App\Custom\Helper;
                                    $object = new Helper();
                                @endphp
                                @foreach($permissions as $permission)
{{--                                    @if($permission['name'] == "handyman_booking")--}}
{{--                                        {{p($permission)}}--}}
{{--                                    @endif--}}
                                    @php
                                        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
                                        if(($permission['special_permission'] == 0) || $object->show_permissions($merchant_id, $permission['name'])):
                                    @endphp

                                    <tr>
                                        <td><b>{{ $permission['display_name'] }}</b></td>
                                        @if(!empty($permission['children']))
                                            @foreach($permission['children'] as $child)
                                                <td><input type="checkbox" name="permission[]" class="checked" value="{{ $child['id']  }}" @if(in_array($child['id'], $permission_array)) checked @endif> {{ $child['display_name'] }}</td>
                                            @endforeach
                                        @else
                                            <td><input type="checkbox" name="permission[]" class="checked" value="{{ $permission['id'] }}" @if(in_array($permission['id'], $permission_array)) checked @endif> view</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                    </tr>
                                    @php
                                        endif;
                                    @endphp
                                @endforeach
                            </table>
                        </fieldset>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
    <script>
        function checkall(data){
            //alert(data);
            var requiredCheckboxes = $('.checked');
            if($(data).is(':checked')) {
                requiredCheckboxes.attr('checked',true);
            } else {
                requiredCheckboxes.attr('checked',false);
            }
        }
    </script>
@endsection


