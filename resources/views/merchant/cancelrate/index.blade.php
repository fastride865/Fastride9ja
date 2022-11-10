@extends('merchant.layouts.main')
@section('content')
  <div class="app-content content">
    <div class="container-fluid ">
      <div class=" content-wrapper">

        <div class="content-body">
          <section id="horizontal">
            <div class="row">
              @if(session('cancelrate'))
                <div class="row container mx-auto">
                  <div class="col-md-12 alert alert-icon-right alert-info alert-dismissible mb-2"
                       role="alert">
                    <span class="alert-icon"><i class="fa fa-info"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                    <strong>{{ session('cancelrate') }}</strong>
                  </div>
                </div>
              @endif

              <div class="col-12">
                <div class="card">
                  <div class="card-header py-3">
                    <div class="content-header row">
                      <div class="content-header-left col-md-4 col-12 mb-2">
                        <h3 class="content-header-title mb-0 d-inline-block">
                          <i class=" fa fa-taxi" aria-hidden="true"></i>
                          @lang('admin.cancel.rate.table')</h3>
                      </div>
                      <div class="content-header-right col-md-8 col-12">
                        <div class="btn-group float-md-right">
                          <div class="heading-elements">
                            <a href="{{ route('merchant.cancelrate.create') }}">
                              <button class="btn btn-secondary btn-sm"
                                      style="position:relative;">
                                <i class="fa fa-plus"></i> @lang("$string_file.add")

                              </button>
                            </a>

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-header py-3">
                    <div class="">

                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="dataTable"
                             class="w-100 table table-striped table-bordered ">

                        <thead>
                        <tr>
                          <th>@lang("$string_file.start_range")</th>
                          <th>@lang("$string_file.end_range")</th>
                          <th>@lang('admin.charge')</th>
                          <th>@lang('admin.charge.type')</th>
                          <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($cancel_rates as $rate)
                          <tr>
                            <td>
                              {{ $rate->start_range }}
                            </td>

                            <td>
                              {{ $rate->end_range }}
                            </td>

                            <td>
                              {{ $rate->charge }}
                            </td>

                            <td>
                              {{ ($rate->charge_type == 1) ? __('admin.nominal') : __('admin.percentage')  }}
                            </td>

                            <td>
                              <a class="btn btn-sm" href="{{route('merchant.cancelrate.edit' , ['id' => $rate->id])}}">
                                <span class="fas fa-edit"></span>
                              </a>
                              <button class="btn btn-sm"
                                      onclick="
                                        if (confirm('Do you want to delete ?')) {
                                        $('#delete-rate-{{$rate->id}}').submit();
                                        }
                                        "
                              >
                                <span class="fas fa-trash text-danger"></span>
                              </button>
                              <form id="delete-rate-{{$rate->id}}" method="post" action="{{route('merchant.cancelrate.destroy' , ['id' => $rate->id])}}">
                                @csrf
                              </form>
                            </td>

                          </tr>
                        @endforeach
                        </tbody>
                      </table>

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
