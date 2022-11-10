@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            @if($merchant_file_exist == false)
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i> {{ trans("$string_file.string_file_not_found") }}
                </div>
            @endif
            <div class="alert dark alert-icon alert-warning" role="alert">
                <i class="icon fa-warning" aria-hidden="true"></i> Note- Please don't change/remove <b>: & a word attached to it</b>. For example <b>:area,  :NUM, :OBJECT,:AMOUNT, :FROM, :count, :ID, :successfully, :delivery, :., :TAX</b> etc.
            </div>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                            @lang("$string_file.translation_status")
                            <i class="fa fa-info red-900"></i>: @lang("$string_file.pending") | <i class="fa fa-check green-500"></i>: @lang("$string_file.done")
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        @lang("$string_file.string_translation")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{ route('merchant.module-string.submit') }}">
                            @csrf
                            <div class="row">
                                @php $i = 1; $translation_done = "";@endphp
                                @foreach($project_strings as $key=> $string)
                                    @php $string_val = "";
                                    $title = $string;
                                    $translation_done = "red-900";
                                      $text_icon = '<i class="fa fa-info" title="'.trans("$string_file.translation_pending").'"></i>';
                                    @endphp
                                   @if(isset($merchant_lang_file[$key]))
                                     @php  $string =  $merchant_lang_file[$key];
                                      $translation_done = "green-500";
                                      $text_icon = '<i class="fa fa-check" title="'.trans("$string_file.translation_done").'"></i>';
                                     @endphp
                                   @endif
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                              {{$i}})  {{ $title }}<span class="{{$translation_done}}"> {!! $text_icon !!} </span>
                                            </label>
                                            {{Form::text("name[$key]",$string,["class"=>"form-control","place_holder"=>$string,'required'=>true])}}
                                            @if ($errors->has('name'))
                                                <label class="danger">{{ $errors->first('name') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    @php $i++; @endphp
                                @endforeach
                            </div>
                            <div class="form-actions right" style="margin-bottom: 3%">
{{--                                Note:- Please don't change/remove '%s' as it is being used in apps and can stop working--}}
{{--                                after alteration.--}}
                                @if(Auth::user('merchant')->can('edit_language_strings'))
                                    <button type="submit" class="btn btn-primary float-right">
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
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
