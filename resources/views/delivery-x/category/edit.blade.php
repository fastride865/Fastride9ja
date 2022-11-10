@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('categoeyadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message596')
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('categories.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                    <i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang('admin.message595')
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('categories.update',$categories->id) }}">
                        @csrf
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.message590')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="name"
                                           name="name"
                                           value="{{ $categories->CategoryName }}"
                                           placeholder="@lang('admin.message590')" required>
                                    @if ($errors->has('name'))
                                        <label class="danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.delivery_type')<span class="text-danger">*</span>
                                    </label>
                                    <select id="delivery_name" name="delivery_name" class="form-control">
                                        <option value="">-- Select One --</option>
                                        @foreach($delivery_types as $delivery_type)
                                            <option value="{{$delivery_type->id}}" @if($delivery_type->id == $categories->delivery_type_id) selected @endif>{{$delivery_type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.message206')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control"
                                              id="description"
                                              name="description"
                                              value="{{ $categories->CategoryDescription }}"
                                              placeholder="@lang('admin.message206')"
                                              required>{{ $categories->CategoryDescription }}</textarea>
                                    @if ($errors->has('description'))
                                        <label class="danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> @lang('admin.update')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection