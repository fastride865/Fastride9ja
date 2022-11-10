@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('advertisement.index') }}">
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
                        <i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_banner")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('advertisement.store') }}@if(!empty($banner)){{ '/'.$banner->id }} @endif">
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.name")
                                            <span class="text-danger">*</span>
                                        </label>
                                        {{ Form::text('name', old('name', isset($banner->name) ? $banner->name : ''), ['class' => 'form-control', 'id' => 'name', 'placeholder' => '', 'required'])}}
                                        @if ($errors->has('name'))
                                            <label class="text-danger">{{ $errors->first('name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.image")
                                            <span class="text-danger">*</span>
                                            (W:{{ Config('custom.image_size.banner.width')  }} *
                                            H:{{ Config('custom.image_size.banner.height')  }})
                                            @if(isset($banner->image) && $banner->image != '')
                                                <a href="{{ get_image($banner->image,'banners',$banner->merchant_id) }}"
                                                   target="_blank">view</a>
                                            @endif
                                        </label>
                                        {{ Form::File('image', ['class' => 'form-control', 'id' => 'image', isset($banner) ? '' : 'required'])}}
                                        @if ($errors->has('image'))
                                            <label class="text-danger">{{ $errors->first('image') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.url")
                                        </label>
                                        {{ Form::url('redirect_url',  old('redirect_url', isset($banner->redirect_url) ? $banner->redirect_url : ''),['class' => 'form-control', 'id' => 'redirect_url'])}}
                                        @if ($errors->has('redirect_url'))
                                            <label class="text-danger">{{ $errors->first('redirect_url') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.sequence")
                                            <span class="text-danger">*</span>
                                        </label>
                                        {{ Form::number('sequence', old('sequence', isset($banner->sequence) ? $banner->sequence : ''), ['class' => 'form-control', 'id' => 'sequence', 'placeholder' =>'', 'required'])}}
                                        @if ($errors->has('sequence'))
                                            <label class="text-danger">{{ $errors->first('sequence') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.banner_for")
                                            <span class="text-danger">*</span>
                                        </label>
                                        {{ Form::select('banner_for', array('1' => 'User', '2' => 'Driver', '4' => 'All'),old('banner_for', isset($banner->banner_for ) ? $banner->banner_for  : ''), ['class' => 'form-control', 'id' => 'banner_for', 'required'])}}
                                        @if ($errors->has('banner_for'))
                                            <label class="text-danger">{{ $errors->first('banner_for') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.is_display_on_home_screen")
                                            <span class="text-danger">*</span>
                                        </label>
                                        {{ Form::select('home_screen', get_status(true,$string_file),old('home_screen', isset($banner->home_screen ) ? $banner->home_screen  : ''), ['class' => 'form-control', 'id' => 'home_screen', 'required'])}}
                                        @if ($errors->has('home_screen'))
                                            <label class="text-danger">{{ $errors->first('home_screen') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 @if(empty($banner->id) || $banner->home_screen == 1)custom-hidden @else @endif"
                                     id="segment">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.segment")
                                            <span class="text-danger">*</span>
                                        </label>
                                        {{ Form::select('segment_id', $arr_segment,old('segment_id', isset($banner->segment_id ) ? $banner->segment_id  : ''), ['class' => 'form-control', 'id' => 'segment_id'])}}
                                        @if ($errors->has('segment_id'))
                                            <label class="text-danger">{{ $errors->first('segment_id') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 @if(empty($banner->id) || $banner->home_screen == 1)custom-hidden @else @endif"
                                     id="business_segment">
                                    <div class="form-group">
                                        <label for="">
                                            @lang("$string_file.business_segment")
                                        </label>
                                        {{ Form::select('business_segment_id', [],old('business_segment_id', isset($banner->business_segment_id ) ? $banner->business_segment_id  : ''), ['class' => 'form-control', 'id' => 'business_segment_id'])}}
                                        @if ($errors->has('business_segment_id'))
                                            <label class="text-danger">{{ $errors->first('business_segment_id') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="@lang('admin.banner_status')">
                                            @lang("$string_file.status")
                                            <span class="text-danger">*</span>
                                        </label>
                                        {{ Form::select('status', $arr_active_status,old('status', isset($banner->status ) ? $banner->status  : ''), ['class' => 'form-control', 'id' => 'status', 'required'])}}
                                        @if ($errors->has('status'))
                                            <label class="text-danger">{{ $errors->first('status') }}</label>
                                        @endif
                                    </div>
                                </div>
                                {{--                            </div>--}}
                                {{--                            <div class="row">--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.validity"):</label>
                                        <br>
                                        <div class="form-control">
                                            <label class="radio-inline">
                                                <input type="radio" value="1"
                                                       @if(isset($banner) && $banner->validity == 1) checked @endif
                                                       id="validity"
                                                       name="validity"
                                                       required>@lang("$string_file.unlimited")
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="2"
                                                       @if(isset($banner) && $banner->validity == 2) checked @endif
                                                       name="validity"
                                                       id="validity"
                                                       required>@lang("$string_file.limited")
                                            </label>
                                            @if ($errors->has('validity'))
                                                <label class="text-danger">{{ $errors->first('validity') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.activate_date")
                                            :</label>
                                        <input type="text"
                                               class="form-control docs_datepicker"
                                               id="activate_date" name="activate_date"
                                               placeholder=""
                                               autocomplete="off"
                                               value="{{ old('activate_date', isset($banner->activate_date ) ? $banner->activate_date  : '') }}">
                                        @if ($errors->has('activate_date'))
                                            <label class="text-danger">{{ $errors->first('activate_date') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 @if(empty($banner) || (!empty($banner) && $banner->validity == 1)) custom-hidden @endif"
                                     id="expire-date">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.expire_date")
                                            :</label>
                                        <input type="text"
                                               class="form-control docs_datepicker"
                                               id="expire_date" name="expire_date"
                                               placeholder=""
                                               autocomplete="off"
                                               value="{{ old('expire_date', isset($banner->expire_date ) ? $banner->expire_date  : '') }}">
                                        @if ($errors->has('expire_date'))
                                            <label class="text-danger">{{ $errors->first('expire_date') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.save")
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
        $(document).on('click', '#validity', function () {
            if ($(this).val() == 1) {
                $('#expire-date').hide();
            } else if ($(this).val() == 2) {
                $('#expire-date').show();
            }
        });
        $(document).on('change', '#home_screen', function () {
            if ($(this).val() == 1) {
                $('#segment').hide();
                $('#business_segment_id').hide();
                $("#segment_id").prop("required",false)
            } else if ($(this).val() == 2) {
                $('#segment').show();
                $("#segment_id").prop("required",true)
                $('#business_segment').show();
            }
        });
        $(document).on('click', '.docs_datepicker', function () {
            var dateFrom = new Date($('#banner_activate_date').val());
            var dateTo = new Date($('#banner_expire_date').val());
            var dateCurrent = new Date();

            console.log(dateTo);
            if ((dateFrom != 'Invalid Date' && dateTo != 'Invalid Date') && dateCurrent.getTime() < dateFrom.getTime() || dateCurrent.getTime() < dateTo.getTime())
                console.log("Please select future date");
            else if ((dateFrom != 'Invalid Date' && dateTo != 'Invalid Date') && dateFrom.getTime() >= dateTo.getTime())
                console.log("Expire date will be greater");
            else
                console.log("FIne");
        });
        $(document).on('change', '#segment_id', function () {
            $.ajax({
                type: "GET",
                data: {
                    id: $('#segment_id').val(),
                },
                url: "{{ route('advertisement.get.business-segment') }}",
            }).done(function (data) {
                $('#business_segment_id').empty().append('<option selected="selected" value="">@lang("$string_file.select")</option>');
                $.each(data,function(i,data)
                {
                    var div_data="<option value="+i+">"+data+"</option>";
                    $(div_data).appendTo('#business_segment_id');
                });
            });
        });
    </script>
@endsection