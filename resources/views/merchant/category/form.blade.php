@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('merchant.category') }}">
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
                        <i class=" fa fa-building" aria-hidden="true"></i>
                        {!! $data['title'] !!}
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'category','id'=>'category-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ) !!}
                    {!! $data['segment_html'] !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="banner_name">
                                    @lang("$string_file.name")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('category_name',old('category_name',isset( $data['category']['id']) ? $data['category']->Name($data['category']['merchant_id']) : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('category_name'))
                                    <label class="text-danger">{{ $errors->first('banner_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_parent_id">
                                    @lang("$string_file.parent_category")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('category_parent_id',$data['arr_category'],old('category_parent_id',isset($data['category']['category_parent_id']) ? $data['category']['category_parent_id'] : NULL),['id'=>'category_parent_id','class'=>'form-control','required'=>false]) !!}
                                @if ($errors->has('category_parent_id'))
                                    <label class="text-danger">{{ $errors->first('category_parent_id') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.status")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('status',$data['arr_status'],old('status',isset($data['category']['status']) ? $data['category']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('status'))
                                    <label class="text-danger">{{ $errors->first('status') }}</label>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence">
                                    @lang("$string_file.sequence")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::number('sequence',old('sequence',isset($data['category']['sequence']) ? $data['category']['sequence'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('sequence'))
                                    <label class="text-danger">{{ $errors->first('sequence') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group  ">
                                <label for="message26">
                                    @lang("$string_file.image") (W:{{ Config('custom.image_size.category.width')  }} *
                                    H:{{ Config('custom.image_size.category.height')  }})
                                </label>
                                @if(!empty($data['category']['category_image']))
                                    <a href="{{ get_image($data['category']['category_image'],'category',$data['category']['merchant_id']) }}"
                                       target="_blank">@lang("$string_file.view")</a>
                                @endif
                                {!! Form::file('category_image',['id'=>'category_image','class'=>'form-control']) !!}
                                @if ($errors->has('category_image'))
                                    <label class="text-danger">{{ $errors->first('category_image') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if(!$is_demo)
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i>@lang("$string_file.save")
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

