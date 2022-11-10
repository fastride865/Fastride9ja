@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('updated'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.updated')
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('goods.index') }}">
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
                          action="{{ route('goods.update',$goods->id) }}">
                        @csrf
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.name")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="name"
                                           name="name"
                                           value="{{ $goods->GoodName }}"
                                           placeholder="@lang("$string_file.name")" required>
                                    @if ($errors->has('name'))
                                        <label class="danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.category_type')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="select2 form-control" name="delivery_type[]" multiple>
                                        @foreach($delivery_types as $type)
                                            <option value="{{$type->id}}"
                                                    @if(in_array($type->id, array_pluck($goods->deliveryTypes,'id'))) selected @endif>{{$type->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('delivery_type'))
                                        <label class="text-danger">{{ $errors->first('delivery_type') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.current_status")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="status">
                                        <option value="0"
                                                @if($goods->status==0)selected @endif>@lang("$string_file.inactive")</option>
                                        <option value="1"
                                                @if($goods->status==1)selected @endif>@lang("$string_file.active")</option>
                                    </select>
                                    @if ($errors->has('description'))
                                        <label class="danger">{{ $errors->first('category') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.description")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control"
                                              id="description"
                                              name="description"
                                              placeholder="@lang("$string_file.description")">{{ $goods->GoodDescription }}
                                    </textarea>
                                </div>
                            </div>

                        </div>
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
@endsection


