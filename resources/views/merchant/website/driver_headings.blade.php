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
                        @lang("$string_file.website_driver_headings")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('website-driver-home-headings.store') }}">
                            @csrf
                            <h5 class="form-section col-md-12" ><i class="wb-add-file"></i> @lang('admin.website_driver_main')
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="banner_image">
                                            @lang("$string_file.banner_image") (1500x1000px):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="banner_image"
                                               name="banner_image"
                                               placeholder="">
                                        @if ($errors->has('banner_image'))
                                            <label class="text-danger">{{ $errors->first('banner_image') }}</label>
                                        @endif
                                    </div>
                                {{--<img src="{{asset($details['user_banner_image'])}}" alt="" style="height:130px;">--}}
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_heading">
                                            @lang("$string_file.driver_heading"):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_heading"
                                               name="driver_heading"
                                               placeholder="" value="@if(!empty($details)) {{$details->DriverHeading}} @endif" required>
                                        @if ($errors->has('driver_heading'))
                                            <label class="text-danger">{{ $errors->first('driver_heading') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_sub_heading">
                                            @lang("$string_file.driver_sub_heading"):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_sub_heading"
                                               name="driver_sub_heading"
                                               placeholder="" value="@if(!empty($details)) {{$details->subHeading}} @endif" required>
                                        @if ($errors->has('driver_sub_heading'))
                                            <label class="text-danger">{{ $errors->first('driver_sub_heading') }}</label>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_buttonText">
                                            @lang("$string_file.driver_button_text") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" maxlength="200" class="form-control" id="driver_buttonText"
                                               name="driver_buttonText"
                                               placeholder="" value="@if(!empty($details)) {{$details->driverButtonText}} @endif" required>
                                        @if ($errors->has('driver_buttonText'))
                                            <label class="text-danger">{{ $errors->first('driver_buttonText') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.login")</label>
                                        <input type="file" class="form-control" name="driver_login_bg_image"/>
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
                                               placeholder="Book Button Title" value="{{$features[0]->FeatureTitle}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[{{$features[0]['id']}}]">
                                            @lang("$string_file.section_three_description")  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[{{$features[0]['id']}}]" name="features[{{$features[0]['id']}}][description]"
                                                  rows="3"
                                                  placeholder="" required>{{$features[0]->FeatureDiscription }}</textarea>
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
                                               placeholder="" value="{{$features[1]->FeatureTitle}}" required>
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
                                                  placeholder="" required>{{$features[1]->FeatureDiscription }}</textarea>
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
                                               placeholder="Book Button Title" value="{{$features[2]->FeatureTitle}}" required>
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
                                                  placeholder="" required>{{$features[2]->FeatureDiscription }}</textarea>
                                        @if ($errors->has('estimate_description'))
                                            <label class="text-danger">{{ $errors->first('estimate_description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> @lang("$string_file.how_app_works")
                            </h5>
                            <hr>
                            @php $i =1; @endphp
                            <?php for($i=0;$i<=4;$i++){?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" value={{$i}} name="position[]" />
                                        <label for="app_image">
                                            @lang("$string_file.app_image") (90 x 190) :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="app_image"
                                               name="data[{{$i}}][image]"
                                               placeholder="">
                                        @if($errors->has('app_image'))
                                            <label class="text-danger">{{ $errors->first('app_image') }}</label>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_title">
                                            @lang("$string_file.title") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="app_title"
                                               name="data[{{$i}}][title]"
                                               placeholder="" value="{{isset($app_detil[$i]->FeatureTitle) ? $app_detil[$i]->FeatureTitle : NULL}}" required>
                                        @if ($errors->has('app_title'))
                                            <label class="text-danger">{{ $errors->first('app_title') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_description">
                                            @lang("$string_file.description") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" max="200" class="form-control" id="app_description"
                                               name="data[{{$i}}][description]"
                                               placeholder="" value="{{isset($app_detil[$i]->FeatureDiscription) ? $app_detil[$i]->FeatureDiscription : NULL}}" required>
                                        @if ($errors->has('app_description'))
                                            <label class="text-danger">{{ $errors->first('app_description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> @lang("$string_file.driver_footer")
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_footer_image">
                                           @lang("$string_file.footer_image") (200 x 200) :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="driver_footer_image"
                                               name="driver_footer_image"
                                               placeholder="">
                                        @if ($errors->has('driver_footer_image'))
                                            <label class="text-danger">{{ $errors->first('driver_footer_image') }}</label>
                                        @endif
                                    </div>
                                {{--<img src="{{asset($details['driver_footer_image'])}}" alt="" style="height:130px;">--}}
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_title">
                                            @lang("$string_file.driver_footer_heading"):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="footer_heading"
                                               name="footer_heading"
                                               placeholder="" value="@if(!empty($details)) {{$details->FooterHeading}} @endif" required>
                                        @if ($errors->has('footer_heading'))
                                            <label class="text-danger">{{ $errors->first('footer_heading') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="footer_sub_heading">
                                            @lang("$string_file.footer_sub_heading") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="footer_sub_heading"
                                               name="footer_sub_heading"
                                               placeholder="" value="@if(!empty($details)) {{$details->FooterSubHeading}} @endif" required>
                                        @if ($errors->has('footer_sub_heading'))
                                            <label class="text-danger">{{ $errors->first('footer_sub_heading') }}</label>
                                        @endif
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