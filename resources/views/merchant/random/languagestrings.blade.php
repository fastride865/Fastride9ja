@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        @lang("$string_file.language_string")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{ route('merchant.languagestring.submit') }}">
                            @csrf
                            <div class="row">
                                @php $i = 0; @endphp
                                @foreach($language_strings as $language_string)
                                    @php $string_val = ""; @endphp
                                    @if($language_string->LanguageSingleMessage)
                                        @php $string_val = $language_string->LanguageSingleMessage->name; @endphp
                                    @elseif(isset($string[$i]['language_string']))
                                        @php $string_val = $string[$i]['language_string']; @endphp
                                    @endif
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                {{    $language_string->language_string }}<span
                                                        class="text-danger">*</span>
                                            </label>

                                            <input type="text" class="form-control"
                                                   id="name" name="name[{{ $language_string['id'] }}]"
                                                   placeholder="{{ $language_string->language_string }}"
                                                   value="{{$string_val}}">
                                            @if ($errors->has('name'))
                                                <label class="danger">{{ $errors->first('name') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    @php $i++; @endphp
                                @endforeach
                            </div>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                Note:- Please don't change/remove '%s' as it is being used in apps and can stop working
                                after alteration.
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
