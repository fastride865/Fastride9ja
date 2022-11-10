@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
               <div class="panel-heading">
                   <div class="panel-actions">
                       <div class="btn-group float-right" style="margin:10px">
                           <a href="{{ route('corporate.index') }}">
                               <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                               </button>
                           </a>
                       </div>
                   </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_corporate")
                    </h3>
               </div>
                @php
                    $id = isset($corporate->id) ? $corporate->id : NULL;
                    $required = !empty($id) ? "" : "required"
                 @endphp
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('corporate.store',$id) }}">
                            @csrf
                            {!! Form::hidden('id',$id) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.name") :<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="corporate_name"
                                               name="corporate_name"
                                               value="{{old('corporate_name',isset($corporate->corporate_name) ? $corporate->corporate_name : NULL)}}"
                                               placeholder="" required>
                                        @if ($errors->has('corporate_name'))
                                            <label class="text-danger">{{ $errors->first('corporate_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.country")</label>
                                        <select class="form-control" name="country" id="country"
                                                required>
                                            <option value="">@lang("$string_file.select")</option>
                                            @foreach($countries  as $country)
                                                <option data-min="{{ $country->maxNumPhone }}"
                                                        data-max="{{ $country->maxNumPhone }}"
                                                        value="{{ $country->id }}" @if(!empty($corporate) && $corporate->country_id == $country->id) selected @endif>{{  $country->CountryName }}({{ $country->phonecode }})</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country'))
                                            <label class="text-danger">{{ $errors->first('country') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.phone")<span class="text-danger">*</span>
                                        </label>
{{--                                        {{p($corporate->Country->phonecode)}}--}}
                                        <input type="text" class="form-control" id="user_phone"
                                               name="phone" value="{{old('corporate_name',isset($corporate->corporate_phone) ? str_replace($corporate->Country->phonecode,"",$corporate->corporate_phone) : NULL)}}"
                                               placeholder="" required>
                                        @if ($errors->has('phone'))
                                            <label class="text-danger">{{ $errors->first('phone') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.email")<span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="email"
                                               name="email" placeholder="" value="{{old('corporate_name',isset($corporate->email) ? $corporate->email : NULL)}}"
                                               required>
                                        @if ($errors->has('email'))
                                            <label class="text-danger">{{ $errors->first('email') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.address")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="address"
                                               name="address"
                                               placeholder="" value="{{old('corporate_address',isset($corporate->corporate_address) ? $corporate->corporate_address : NULL)}}"
                                               required>
                                        @if ($errors->has('address'))
                                            <label class="text-danger">{{ $errors->first('address') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.logo")<span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="corporate_logo"
                                               name="corporate_logo"
                                                {{$required}}>
                                        @if ($errors->has('corporate_logo'))
                                            <label class="text-danger">{{ $errors->first('corporate_logo') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.password")<span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control" id="password"
                                               name="password" placeholder=""
                                                {{$required}}>
                                        @if ($errors->has('password'))
                                            <label class="text-danger">{{ $errors->first('password') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.confirm_password")<span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                               name="password_confirmation" placeholder=""
                                               {{$required}}>
                                        @if ($errors->has('password_confirmation'))
                                            <label class="text-danger">{{ $errors->first('password_confirmation') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="wb-check-circle"></i> @lang("$string_file.save")
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection