@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a class="heading-elements-toggle"><i
                                    class="ft-ellipsis-h font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa-globe" aria-hidden="true"></i>
                        @lang("$string_file.website_headings")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('website-user-home-headings.store') }}">
                            @csrf
                            <h5 class="form-section col-md-12" ><i class="wb-add-file"></i> @lang("$string_file.general_configuration")
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_logo">
                                            @lang("$string_file.app_logo") (512x512):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="app_logo"
                                               name="app_logo"
                                               placeholder="">

                                        @if ($errors->has('app_logo'))
                                            <label class="text-danger">{{ $errors->first('app_logo') }}</label>
                                        @endif

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_logo">
                                            @lang("$string_file.login_background_image") 
                                            (1500x1000):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="user_login_bg_image"
                                               name="user_login_bg_image"
                                               placeholder="">
                                        @if ($errors->has('user_login_bg_image'))
                                            <label class="text-danger">{{ $errors->first('user_login_bg_image') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="banner_image">
                                            @lang("$string_file.banner_image") (1500x1000):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="banner_image"
                                               name="banner_image"
                                               placeholder="">
                                        @if ($errors->has('banner_image'))
                                            <label class="text-danger">{{ $errors->first('banner_image') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_address_hint">
                                            @lang("$string_file.pickup_location") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="start_address_hint"
                                               name="start_address_hint"
                                               placeholder="" value="@if(!empty($details)) {{$details->StartAddress}} @endif" required>
                                        @if ($errors->has('start_address_hint'))
                                            <label class="text-danger">{{ $errors->first('start_address_hint') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_address_hint">
                                            @lang("$string_file.drop_off_location"):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="end_address_hint"
                                               name="end_address_hint"
                                               placeholder="" value="@if(!empty($details)) {{$details->EndAddress}} @endif" required>
                                        @if ($errors->has('end_address_hint'))
                                            <label class="text-danger">{{ $errors->first('end_address_hint') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="book_btn_title">
                                           @lang("$string_file.book_button_title") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="book_btn_title"
                                               name="book_btn_title"
                                               placeholder="" value="@if(!empty($details)) {{$details->BookingButton}} @endif" required>
                                        @if ($errors->has('book_btn_title'))
                                            <label class="text-danger">{{ $errors->first('book_btn_title') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="book_btn_title">
                                            @lang("$string_file.estimate_button_title") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="estimate_btn_title"
                                               name="estimate_btn_title"
                                               placeholder="Book Button Title" value="@if(!empty($details)) {{$details->EstimateButton}} @endif" required>
                                        @if ($errors->has('estimate_btn_title'))
                                            <label class="text-danger">{{ $errors->first('estimate_btn_title') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.left_image")</label>
                                        <span class="text-danger">*</span>
                                        <input type="file" class="form-control" name="estimate_image" />
                                        @if($errors->has('estimate_image'))
                                            <label class="text-danger"> {{  $errors->first('estimate_image') }} </label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estimate_description">
                                            <label>@lang("$string_file.estimate_description")</label>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="estimate_description" name="estimate_description"
                                                  rows="3"
                                                  placeholder="" required>@if(!empty($details)) {{$details->EstimateDescription }} @endif</textarea>
                                        @if ($errors->has('estimate_description'))
                                            <label class="text-danger">{{ $errors->first('estimate_description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> @lang("$string_file.features")
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_title[{{$features[0]['id']}}]">
                                            @lang("$string_file.section_one_title")  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="features_title[{{$features[0]['id']}}]"
                                               name="features[{{$features[0]['id']}}][title]"
                                               placeholder="" value="@if(!empty($features[0])) {{$features[0]->FeatureTitle}} @endif" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[{{$features[0]['id']}}]">
                                            @lang("$string_file.section_one_description")  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[{{$features[0]['id']}}]" name="features[{{$features[0]['id']}}][description]"
                                                  rows="3"
                                                  placeholder="" required>@if(!empty($features[0])) {{$features[0]->FeatureDiscription }} @endif</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_title[{{$features[1]['id']}}]">
                                            @lang("$string_file.section_two_title")  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="features_title[{{$features[1]['id']}}]"
                                               name="features[{{$features[1]['id']}}][title]"
                                               placeholder="Book Button Title" value="@if(!empty($features[1])) {{$features[1]->FeatureTitle}} @endif" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[{{$features[1]['id']}}]">
                                            @lang("$string_file.section_two_description")  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[{{$features[0]['id']}}]"
                                                  name="features[{{$features[1]['id']}}][description]"
                                                  rows="3"
                                                  placeholder="" required>@if(!empty($features[1])) {{$features[1]->FeatureDiscription }} @endif</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_title[{{$features[2]['id']}}]">
                                            @lang("$string_file.section_three_title")  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="features_title[{{$features[2]['id']}}]"
                                               name="features[{{$features[2]['id']}}][title]"
                                               placeholder="Book Button Title" value="@if(!empty($features[2])) {{$features[2]->FeatureTitle}} @endif" required>
                                        @if ($errors->has('estimate_btn_title'))
                                            <label class="text-danger">{{ $errors->first('estimate_btn_title') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[{{$features[2]['id']}}]">
                                            @lang("$string_file.section_three_description")  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[{{$features[1]['id']}}]"
                                                  name="features[{{$features[2]['id']}}][description]"
                                                  rows="3"
                                                  placeholder="" required>@if(!empty($features[2])) {{$features[2]->FeatureDiscription }} @endif</textarea>
                                        @if ($errors->has('estimate_description'))
                                            <label class="text-danger">{{ $errors->first('estimate_description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> @lang("$string_file.feature_components")
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.main_image")</label>
                                        <input type="file" class="form-control" name="featured_component_main_image" />
                                    </div>
                                </div>
                            </div>
                            <?php for($i=0;$i<=4;$i++){?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" value="{{$i}}" name="position[]" />
                                        <label for="featre_compnt_image">
                                            @lang("$string_file.image") (200 x 200) :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="featre_compnt_image"
                                               name="data[{{$i}}][featre_compnt_image]"
                                               placeholder="">
                                        @if ($errors->has('featre_compnt_image'))
                                            <label class="text-danger">{{ $errors->first('featre_compnt_image') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_title">
                                            @lang("$string_file.title") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="featre_compnt_title"
                                               name="data[{{$i}}][featre_compnt_title]"
                                               placeholder="" value="{!! isset($features_component) ? $features_component[$i]->FeatureTitle : "" !!}" required>
                                        @if ($errors->has('featre_compnt_title'))
                                            <label class="text-danger">{{ $errors->first('featre_compnt_title') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_description">
                                            @lang("$string_file.description") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" maxlength="200" class="form-control" id="featre_compnt_description"
                                               name="data[{{$i}}][featre_compnt_description]"
                                               placeholder="" value="{!! isset($features_component) ? $features_component[$i]->FeatureDiscription :"" !!}" required>
                                        @if ($errors->has('featre_compnt_description'))
                                            <label class="text-danger">{{ $errors->first('featre_compnt_description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="android_link[{{$features[0]['id']}}]">
                                            @lang("$string_file.android_app_link") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="android_link"
                                               name="android_link"
                                               placeholder="" value="@if(!empty($details)) {{$details['android_link']}} @endif" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ios_link[{{$features[0]['id']}}]">
                                            @lang("$string_file.ios_app_link") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="ios_link"
                                               name="ios_link"
                                               placeholder="" value="@if(!empty($details)) {{$details['ios_link']}} @endif" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> @lang("$string_file.driver_footer")
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group px-2">
                                        <label>@lang("$string_file.background_color"):</label>
                                        <input type="color" class="form-control" name="footer_bg_color" value="{{(!empty($details) && $details['footer_bgcolor']) ? $details['footer_bgcolor'] : '#ffffff'}}"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group px-2">
                                        <label>@lang("$string_file.text_color"):</label>
                                        <input type="color" class="form-control" name="footer_text_color" value="{{(!empty($details) && $details['footer_text_color']) ? $details['footer_text_color'] : '#000000'}}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions float-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-square-o"></i> @lang("$string_file.save")
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')

@endsection