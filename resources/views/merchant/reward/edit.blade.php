@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">

                <div class="content-header row">
                    <div class="col-md-6 col-12">
                        @if(session('reward'))
                            <div class="col-md-6 alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                                <span class="alert-icon"><i class="fa fa-info"></i></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>{{session('reward')}}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="content-body">
                    <section id="validation">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="content-header-title mb-0 d-inline-block"><i
                                                    class="fas fa-user-plus"></i> @lang('admin.reward.edit')</h3>
                                        <div class="btn-group float-md-right">
                                            <a href="{{ route('reward-points.index') }}">
                                                <button type="button" class="btn btn-icon btn-success mr-1"><i
                                                            class="fa fa-reply"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <form method="POST"
                                                  class="steps-validation wizard-notification"
                                                  action="{{ route('reward-points.update' , ['id' => $reward->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <fieldset>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="location3" class="text-capitalize">@lang('admin.area.select')</label>
                                                                <select class="form-control" name="country_area">
                                                                    <option value=""> @lang("$string_file.select") </option>
                                                                    @foreach($country_areas as $area)
                                                                        <option value="{{$area->id}}"
                                                                                {{($reward->country_area_id == $area->id) ? ' selected' : ''}}
                                                                        >
                                                                            {{ $area->CountryAreaName  }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>
                                                                <span class="text-danger">{{ $errors->first('country_area')  }}</span>

                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="location3" class="text-capitalize">@lang('admin.registration.enable')</label>
                                                                <select class="form-control" name="registration_enable"
                                                                        onchange="switchDisabled(this.value , 'registration-switch')"
                                                                >
                                                                    <option value="2"> @lang("$string_file.disable") </option>
                                                                    <option value="1" {{($reward->registration_enable == 1) ? 'selected' : ''}}> @lang("$string_file.enable") </option>

                                                                </select>

                                                            </div>
                                                        </div>


                                                      <div class="col-md-6">
                                                          <div class="form-group">
                                                              <label for="location3" class="text-capitalize">@lang('admin.user.registration.reward')</label>
                                                              <input type="number" max="1000000" class="registration-switch form-control" value="{{$reward->user_registration_reward}}" name="user_registration_reward"
                                                                {{($reward->registration_enable == 1) ? '' : 'disabled'}}
                                                              />
                                                              @if ($errors->has('user_registration_reward'))
                                                                  <label class="danger">{{ $errors->first('user_registration_reward') }}</label>
                                                              @endif
                                                          </div>
                                                      </div>

                                                      <div class="col-md-6">
                                                          <div class="form-group">
                                                              <label for="location3" class="text-capitalize">@lang('admin.driver.registration.reward')</label>
                                                              <input type="number" max="1000000" class="registration-switch form-control" value="{{$reward->driver_registration_reward}}" name="driver_registration_reward"
                                                                {{($reward->registration_enable == 1) ? '' : 'disabled'}}
                                                              />
                                                              @if ($errors->has('driver_registration_reward'))
                                                                  <label class="danger">{{ $errors->first('driver_registration_reward') }}</label>
                                                              @endif
                                                          </div>
                                                      </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="location3" class="text-capitalize">@lang('admin.referral.enable')</label>
                                                                <select class="form-control required"
                                                                        name="referral_enable"
                                                                        onchange="switchDisabled(this.value , 'referral-switch')">
                                                                        <option value="2"> @lang("$string_file.disable") </option>
                                                                        <option value="1" {{($reward->referral_enable == 1) ? 'selected' : ''}}> @lang("$string_file.enable") </option>

                                                                </select>

                                                            </div>
                                                        </div>

                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label class="text-capitalize">
                                                            @lang('admin.user.referral.reward')
                                                          </label>
                                                          <input type="number" class="referral-switch form-control" max="10000000000" name="user_referral_reward"
                                                                 value="{{$reward->user_referral_reward}}"
                                                                 {{($reward->referral_enable == 1) ? '' : 'disabled'}}
                                                          />
                                                          @if($errors->first('user_referral_reward'))
                                                            <label class="text-danger">
                                                              {{$errors->first('user_referral_reward')}}
                                                            </label>
                                                          @endif
                                                        </div>
                                                      </div>


                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label class="text-capitalize">
                                                            @lang('admin.driver.referral.reward')
                                                          </label>
                                                          <input type="number" class="referral-switch form-control" max="10000000000" name="driver_referral_reward"
                                                                 value="{{$reward->driver_referral_reward}}"
                                                            {{($reward->referral_enable == 1) ? '' : 'disabled'}}
                                                          />
                                                          @if($errors->first('driver_referral_reward'))
                                                            <label class="text-danger">
                                                              {{$errors->first('driver_referral_reward')}}
                                                            </label>
                                                          @endif
                                                        </div>
                                                      </div>

                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label class="text-capitalize">
                                                            @lang('admin.value.equals')
                                                          </label>
                                                          <input type="number" class="form-control" max="10000000000" name="value_equals"
                                                                 value="{{$reward->value_equals}}" placeholder="@lang('admin.enter_reward')"
                                                          />
                                                          @if($errors->first('value_equals'))
                                                            <label class="text-danger">
                                                              {{$errors->first('value_equals')}}
                                                            </label>
                                                          @endif
                                                        </div>
                                                      </div>

                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label class="text-capitalize">
                                                            @lang('admin.max.redeem')
                                                          </label>
                                                          <input type="number" class="form-control" max="10000000000" name="max_redeem"
                                                                 value="{{$reward->max_redeem}}"
                                                          />
                                                          @if($errors->first('max_redeem'))
                                                            <label class="text-danger">
                                                              {{$errors->first('max_redeem')}}
                                                            </label>
                                                          @endif
                                                        </div>
                                                      </div>

                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label class="text-capitalize">
                                                            @lang('admin.trips.count')
                                                          </label>
                                                          <input type="number" class="form-control" max="10000000000" name="trips_count"
                                                                 value="{{$reward->trips_count}}"
                                                          />
                                                          @if($errors->first('trips_count'))
                                                            <label class="text-danger">
                                                              {{$errors->first('trips_count')}}
                                                            </label>
                                                          @endif
                                                        </div>
                                                      </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="location3" class="text-capitalize">@lang("$string_file.status")</label>
                                                                <select class="form-control required"
                                                                        name="active"
                                                                        required>
                                                                        <option value="2" class="text-danger"> @lang("$string_file.inactive") </option>
                                                                        <option class="text-success" value="1" {{($reward->active == 1) ? 'selected' : ''}}> @lang("$string_file.active") </option>

                                                                </select>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <div class="form-actions right" style="margin-bottom: 3%">
                                                    <button type="submit" class="btn btn-primary float-right">
                                                        <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
  <script>
      function switchDisabled (value , target) {
          if (value == 1) {
              $('.'+target).prop('disabled' , false)
              return
          }
          $('.'+target).prop('disabled' , true)
      }
  </script>
@endsection
