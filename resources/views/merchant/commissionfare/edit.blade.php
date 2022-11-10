@extends('merchant.layouts.main')
@section('content')
  <div class="app-content content">
    <div class="container-fluid ">
      <div class="content-wrapper">
        <div class="content-body">
          <section id="validation">
            <div class="row">
              <div class="col-12">
                <div class="card shadow h-100">
                  <div class="card-header py-3">
                    <div class="content-header row">
                      <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                        <h3 class="content-header-title mb-0 d-inline-block">
                          <i class=" fa fa-user-plus" aria-hidden="true"></i>
                          @lang('admin.commissionfare.edit')</h3>
                      </div>
                      <div class="content-header-right col-md-4 col-12">
                        <div class="btn-group float-md-right">
                          <a href="{{ route('merchant.driver.commission.fare') }}">
                            <button type="button" class="btn btn-icon btn-success mr-1"><i
                                      class="fa fa-reply"></i>
                            </button>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="">
                    @if(session('driverCommission'))
                      <div class="row container mx-auto">
                        <div class="col-md-12 alert alert-icon-right alert-info alert-dismissible mb-2"
                             role="alert">
                          <span class="alert-icon"><i class="fa fa-info"></i></span>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                          <strong>{{ session('driverCommission') }}</strong>
                        </div>
                      </div>
                    @endif
                    <a class="heading-elements-toggle"><i
                              class="ft-ellipsis-h font-medium-3"></i></a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="card-content collapse show">
                    <div class="card-body">
                      <form method="POST" action="{{ route('merchant.driver.commissionfare.update' , ['id' => $commissionfare]) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="location3">@lang("$string_file.start_range")
                                :</label>
                              <input type="text" class="form-control"
                                     name="start_range" value="{{ $commissionfare->start_range }}"
                              >
                              @if ($errors->has('start_range'))
                                <label class="text-danger">{{ $errors->first('start_range') }}</label>
                              @endif
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="location3">@lang("$string_file.end_range")
                                :</label>
                              <input type="text" class="form-control"
                                     name="end_range" value="{{ $commissionfare->end_range }}"
                              >
                              @if ($errors->has('end_range'))
                                <label class="text-danger">{{ $errors->first('end_range') }}</label>
                              @endif
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="emailAddress5">
                                @lang("$string_file.commission") :
                              </label>
                              <input type="number" step=0.01 min=0 class="form-control" id="isd"
                                     name="commission" value="{{ $commissionfare->commission }}">
                              @if ($errors->has('commission'))
                                <label class="text-danger">{{ $errors->first('commission') }}</label>
                              @endif
                            </div>
                          </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                          <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> Save
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
      <br>
    </div>
  </div>

@endsection
