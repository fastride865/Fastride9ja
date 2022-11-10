@extends('merchant.layouts.main')
@section('content')
    @php $id = NULL; @endphp
    @if(isset($data['handyman_charge_type']['id']))
        @php $id = $data['handyman_charge_type']['id']; @endphp
    @endif
    <div class="page">
        <div class="page-content">
            {{--            file to display error and success message --}}
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('segment.handyman-charge-type') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.charge_types")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        {!! Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("segment.handyman-charge-type.save",$id)]) !!}
                        {!! Form::hidden('id',$id) !!}
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.segment") <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            {!! Form::select('segment_id',add_blank_option($data['arr_segment'],trans("$string_file.select")),old('segment_id',isset($data['handyman_charge_type']['segment_id']) ? $data['handyman_charge_type']['segment_id'] :NULL),["class"=>"form-control","id"=>"area_segment","required"=>true,'onChange'=>"getService()"]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.charges_type") <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            {!! Form::text('charge_type',old('charge_type',isset($data['handyman_charge_type']['charge_type']) ? $data['handyman_charge_type']['charge_type'] :NULL),["class"=>"form-control","id"=>"charge_type","required"=>true]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.maximum_amount") <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            {!! Form::number('maximum_amount',old('maximum_amount',isset($data['handyman_charge_type']['charge_type']) ? $data['handyman_charge_type']['maximum_amount'] :NULL),["class"=>"form-control","id"=>"maximum_amount"]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.status") <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            {!! Form::select('status',$data['arr_status'],old('status',isset($data['handyman_charge_type']['status']) ? $data['handyman_charge_type']['status'] :NULL),['class'=>'form-control','required'=>true,'id'=>'status']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i>{!! $data['submit_button'] !!}
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
