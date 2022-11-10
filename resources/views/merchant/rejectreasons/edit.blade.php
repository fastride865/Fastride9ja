@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid ">
        <div class="content-wrapper">
            @if(session('reject'))
                <div class="box no-border">
                    <div class="box-tools">
                        <p class="alert alert-info alert-dismissible">
                            <strong>{{ session('reject') }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                        </p>
                    </div>
                </div>
            @endif
            <div class="content-body">
                <section id="validation">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header">
								     <div class="content-header row">
										<div class="content-header-left col-md-10 col-12 mb-2">
											<h3 class="content-header-title mb-0 d-inline-block">@lang('admin.message706')
												(@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
										</div>
										<div class="content-header-right col-md-2 col-12">
											<div class="btn-group float-md-right">
												<a href="{{ route('rejectreason.index') }}">
													<button type="button" class="btn btn-icon btn-success mr-1"><i class="fa fa-reply"></i>
													</button>
												</a>
											</div>
										</div>
									</div>
                                    <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
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
                                              action="{{route('rejectreason.update', $reason->id)}}">
                                            {{method_field('PUT')}}
                                            @csrf
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="firstName3">
                                                                @lang("$string_file.title") :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="title" name="title"
                                                                   placeholder="@lang('admin.message627')" value="@if($reason->LanguageSingle){{ $reason->LanguageSingle->title }}@endif"/>
                                                            @if ($errors->has('title'))
                                                                <label class="danger">{{ $errors->first('title') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="firstName3">
                                                                @lang('admin.message703') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <textarea class="form-control" id="action" name="action" rows="3"
                                                                      placeholder="@lang('admin.message704')">@if($reason->LanguageSingle){{ $reason->LanguageSingle->action }}@endif</textarea>
                                                            @if ($errors->has('action'))
                                                                <label class="danger">{{ $errors->first('action') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <div class="form-actions right">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-check-square-o"></i> @lang("$string_file.update")
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