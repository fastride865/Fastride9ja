@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right" style="margin:20px">
                            <a href="{{ route('driver-commission-choices.index') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.edit_driver_commission_choice")({{ $edit->slug }}) (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('driver-commission-choices.update',$edit->id) }}">
                        {{method_field('PUT')}}
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="commission_choice_name">
                                            @lang("$string_file.name") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id=commission_choice_name"
                                               name="name" value="{{ isset($edit->LangCommissionChoiceAccMerchantSingle) ? $edit->LangCommissionChoiceAccMerchantSingle['name'] : "" }}"
                                               placeholder="@lang("$string_file.name")" required>
                                        @if ($errors->has('name'))
                                            <label class="text-danger">{{ $errors->first('name') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> {{trans("$string_file.update")}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection
