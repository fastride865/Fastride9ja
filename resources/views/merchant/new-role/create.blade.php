@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('new-role.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->add_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        @lang("$string_file.role")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('new-role.store',isset($role->id) ? $role->id : null) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.role")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name"
                                           name="name"
                                           value="{{ isset($role->display_name) ? $role->display_name : "" }}"
                                           placeholder="@lang("$string_file.role")" required>
                                    @if ($errors->has('name'))
                                        <label class="danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.description")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description"
                                              name="description" rows="3"
                                              placeholder="">{{ isset($role->description) ? $role->description : "" }}</textarea>
                                    @if ($errors->has('description'))
                                        <label class="danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" id="check_all" class="check_all" onclick="checkall(this);"
                                           name="permission[]"
                                           value="1">
                                    <label for="check_all">@lang("$string_file.check_all")</label>
                                </div>
                            </div>
                        </div>
                        @php use App\Custom\Helper;  $object = new Helper(); @endphp
                        @foreach($permissions as $permission)
                            @php $merchant_id = get_merchant_id(); @endphp
                            @if(($permission['special_permission'] == 0) || $object->show_permissions($merchant_id, $permission['name']))
                                <h4>{{ $permission['display_name'] }}</h4>
                                <div class="row ml-2">
                                    @if(!empty($permission['children']))
                                        @foreach($permission['children'] as $child)
                                            <div class="col-md-2">
                                                <div class="checkbox-custom checkbox-primary">
                                                    <input type="checkbox" id="{{ $child['id']  }}" class="checked"
                                                           name="permission[]"
                                                           @if(!empty($permission_array) && in_array($child['id'], $permission_array)) checked
                                                           @endif
                                                           value="{{ $child['id']  }}">
                                                    <label for="{{ $child['id']  }}">{{ $child['display_name'] }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-md-2">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" id="{{ $permission['id'] }}" class="checked"
                                                       name="permission[]"
                                                       @if(!empty($permission_array) && in_array($permission['id'], $permission_array)) checked
                                                       @endif
                                                       value="{{ $permission['id'] }}">
                                                <label for="{{ $permission['id'] }}">{{ $permission['display_name'] }}</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        @foreach($type_two_permissions as $type_two_permission)
                            @php $merchant_id = get_merchant_id(); @endphp
                            @if(($type_two_permission['special_permission'] == 0) || $object->show_permissions($merchant_id, $type_two_permission['name']))
                                @php
                                    $segment_checked_status = false;
                                    if(!empty($permission_array) && !empty($type_two_permission['children'])){
                                        $child_ids = array_pluck($type_two_permission['children'],"id");
                                        $segment_checked_status = !array_diff($child_ids, $permission_array);
                                    }
                                @endphp
                                <h4>
                                    <input type="checkbox" id="{{ $type_two_permission['id'] }}"
                                           class="checked" name="permission[]"
                                           @if($segment_checked_status) checked
                                           @endif
                                           onclick="checkSegment(this, '{{$type_two_permission["name"]}}');"
                                           value="{{ $type_two_permission['id'] }}">
                                    <label for="{{ $type_two_permission['id'] }}">{{ $type_two_permission['display_name'] }}</label>
                                </h4>
                                <div class="row ml-2">
                                    @if(!empty($type_two_permission['children']))
                                        @foreach($type_two_permission['children'] as $child)
                                            <div class="col-md-2">
                                                <li>{{ $child['display_name'] }}</li>
                                                <div class="checkbox-custom checkbox-primary" style="display: none">
                                                    <input type="checkbox" id="{{ $child['id']  }}"
                                                           class="checked {{$type_two_permission['name']}}" readonly
                                                           name="permission[]"
                                                           @if(!empty($permission_array) && in_array($child['id'], $permission_array)) checked
                                                           @endif
                                                           value="{{ $child['id']  }}">
                                                    <label for="{{ $child['id']  }}">{{ $child['display_name'] }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        <div class="form-actions float-right ">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> @lang("$string_file.save")
                            </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
@section('js')
    <script>
        function checkall(data) {
            //alert(data);
            var requiredCheckboxes = $('.checked');
            if ($(data).is(':checked')) {
                requiredCheckboxes.prop('checked', true);
            } else {
                requiredCheckboxes.prop('checked', false);
            }
        }

        function checkSegment(data, attr) {
            console.log(attr);
            var requiredCheckboxes = $('.' + attr);
            if ($(data).is(':checked')) {
                requiredCheckboxes.prop('checked', true);
            } else {
                requiredCheckboxes.prop('checked', false);
            }
        }

        $(document).on("change", ".checked", function () {
            if ($('.checked:checked').length == $('.checked').length) {
                $('.check_all').prop('checked', true);
            } else {
                $('.check_all').prop('checked', false);
            }
        });
    </script>
@endsection

