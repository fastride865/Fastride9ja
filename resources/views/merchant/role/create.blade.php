@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="{{ route('role.index') }}">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
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
                        @lang("$string_file.add_role")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('role.store') }}">
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
                                              placeholder=""></textarea>
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
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.check_all")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="checkbox" onclick="checkall(this);" name="permission[]" value="1">
                                </div>
                            </div>

                        </div>
                        <table class="table table-default">
                            @php
                                use App\Custom\Helper;
                                $object = new Helper();
                            @endphp
                            @foreach($permissions as $permission)
                                @php
                                    $merchant_id = get_merchant_id();
                                    if(($permission['special_permission'] == 0) || $object->show_permissions($merchant_id, $permission['name'])):
                                @endphp
                                <tr>
                                    <td><b>{{ $permission['display_name'] }}</b></td>
                                    @if(!empty($permission['children']))
                                        @foreach($permission['children'] as $child)
                                            <td><input type="checkbox" class="checked" name="permission[]" value="{{ $child['id']  }}"> {{ $child['display_name'] }}</td>
                                        @endforeach
                                    @else
                                        <td><input type="checkbox" class="checked" name="permission[]" value="{{ $permission['id'] }}"> view</td>
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
                        <div class="form-actions float-right ">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
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

