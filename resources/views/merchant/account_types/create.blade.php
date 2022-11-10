@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
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
                        <a href="{{ route('account-types.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        @lang("$string_file.account_type")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('account-types.store') }}">
                            @csrf
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">
                                                @lang("$string_file.account_type")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="name"
                                                   required
                                                   name="name"
                                                   placeholder="">
                                            @if ($errors->has('name'))
                                                <label class="text-danger">{{ $errors->first('name') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('status', trans("$string_file.status").'<span class="text-danger">*</span> :', ['class' => 'control-label'], false) !!}
                                            &nbsp;
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    {{ Form::radio('status','1',true,['class' => 'custom-control-input','id'=>'active',])  }}
                                                    {!! Form::label('active', trans("$string_file.active"), ['class' => 'custom-control-label'], false) !!}
                                                </div>
                                            </fieldset>
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    {{ Form::radio('status','0',false,['class' => 'custom-control-input','id'=>'deactive',])  }}
                                                    {!! Form::label('deactive', trans("$string_file.inactive"), ['class' => 'custom-control-label'], false) !!}
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>

                            </fieldset>
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                @if(Auth::user('merchant')->can('create-account-types'))
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                    </button>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
