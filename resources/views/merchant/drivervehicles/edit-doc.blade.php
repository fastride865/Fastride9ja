@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid">
            <div class="content-wrapper">

                <div class="content-body">
                    <section id="validation">
                        <div class="row">
                            @include('merchant.shared.errors-and-messages')
                            <div class="col-12">
                                <div class="card shadow h-100">
                                    <div class="card-header">
                                        <div class="content-header row">
                                            <div class="content-header-left col-md-4 col-12 mb-2 breadcrumb-new">
                                                <h3 class="content-header-title mb-0 d-inline-block">
                                                    <i class=" fa fa-plus" aria-hidden="true"></i>
                                                    @lang('admin.editvehicleDOc')</h3>
                                            </div>

                                            <div class="content-header-right col-md-8 col-12">
                                                <div class="btn-group float-md-right">
                                                    <a href="{{ route('driver.index') }}">
                                                        <button type="button" class="btn btn-icon btn-success mr-1"><i
                                                                    class="fa fa-reply"></i>
                                                        </button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
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
                                            <form method="POST" class="steps-validation wizard-notification"
                                                  enctype="multipart/form-data"
                                                  action="{{ route('merchant.driver.allvehicles.update',$drivervehicle->id) }}">
                                                @csrf
                                                <fieldset>
                                                    @foreach($documents as $doc)
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="location3">
                                                                        {{ $doc->DocumentName }}
                                                                        :</label>
                                                                    <input type="file" class="form-control"
                                                                           name="document[{{$doc->id}}]"
                                                                           placeholder=""
                                                                           @if($doc->documentNeed == 1) required @endif>
                                                                </div>
                                                            </div>
                                                            @if($doc->expire_date == 1)
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="location3">
                                                                            @lang("$string_file.expire_date")
                                                                            :</label>
                                                                        <input type="text"
                                                                               class="form-control docs_datepicker"
                                                                               name="expiredate[{{$doc->id}}]"
                                                                               placeholder="@lang("$string_file.expire_date")  "
                                                                               @if($doc->documentNeed == 1) required
                                                                               @endif
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach

                                                </fieldset>

                                                <div class="form-actions d-flex flex-row-reverse p-2 float-right">
                                                    <button type="submit"
                                                            class="btn btn-primary">
                                                        <i class="fa fa-check-circle"></i> @lang("$string_file.save")
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