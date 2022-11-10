@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('questionadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.question_added')
                </div>

            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('questions.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        @lang('admin.Addquestions')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('questions.store') }}">
                            @csrf
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang('admin.questions') :
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="question"
                                                   name="question"
                                                   placeholder="@lang('admin.questions')" required>
                                            @if ($errors->has('question'))
                                                <label class="danger">{{ $errors->first('question') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </fieldset>
                            <div class="form-actions float-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="wb-check-square-o"></i> @lang("$string_file.save")
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
