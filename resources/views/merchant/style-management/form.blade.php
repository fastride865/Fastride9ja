@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right">
                            <a href="{{ route('merchant.style-management') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-hammer" aria-hidden="true"></i>
                        {!!$data['title'] !!}
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'style-management','id'=>'style-management-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">
                                    @lang("$string_file.name")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('style_name',old('style_name',isset( $data['style_management']['id']) ? $data['style_management']->Name($data['style_management']['merchant_id']) : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('style_name'))
                                    <label class="text-danger">{{ $errors->first('style_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.status")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('status',$data['arr_status'],old('status',isset($data['style_management']['status']) ? $data['style_management']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('status'))
                                    <label class="text-danger">{{ $errors->first('status') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if(!$is_demo)
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                        </button>
                        @else
                            <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                        @endif
                    </div>
                    {!!  Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
