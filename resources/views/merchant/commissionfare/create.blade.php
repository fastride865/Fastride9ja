@extends('merchant.layouts.main')
@section('content')
  <div class="page">
    <div class="page-content">
      @include('merchant.shared.errors-and-messages')
      <div class="panel panel-bordered">
        <div class="panel-heading">
          <div class="panel-actions">
            <div class="btn-group float-right" >
              <a href="{{ route('merchant.driver.commission.fare') }}">
                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                          class="wb-reply"></i>
                </button>
              </a>
            </div>
          </div>
          <h3 class="panel-title">
            <i class=" wb-user-plus" aria-hidden="true"></i>
            @if(isset($commissionfare) && !empty($commissionfare))
              @lang("$string_file.edit")
            @else
              @lang("$string_file.add")
            @endif
            @lang("$string_file.commission_fare")
          </h3>
        </div>
        <div class="panel-body container-fluid">
          @if(isset($commissionfare) && !empty($commissionfare))
            <form method="POST" action="{{ route('merchant.driver.commissionfare.store',['id' => $commissionfare->id]) }}">
              @else
                <form method="POST" action="{{ route('merchant.driver.commissionfare.store') }}">
                  @endif
                  @csrf
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="location3">@lang("$string_file.title")
                          :</label>
                        {{ Form::text('name', old('name', isset($commissionfare) ? $commissionfare->name : ''), ['class' => 'form-control','required'=>true])  }}
                        @if ($errors->has('name'))
                          <label class="text-danger">{{ $errors->first('name') }}</label>
                        @endif
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="location3">@lang("$string_file.start_range")
                          :</label>
                        {{ Form::text('start_range', old('start_range', isset($commissionfare) ? $commissionfare->start_range : ''), ['class' => 'form-control','required'=>true])  }}
                        @if ($errors->has('start_range'))
                          <label class="text-danger">{{ $errors->first('start_range') }}</label>
                        @endif
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="location3">@lang("$string_file.end_range")
                          :</label>
                        {{ Form::text('end_range', old('end_range', isset($commissionfare) ? $commissionfare->end_range : ''), ['class' => 'form-control','required'=>true])  }}
                        @if ($errors->has('end_range'))
                          <label class="text-danger">{{ $errors->first('end_range') }}</label>
                        @endif
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="emailAddress5">
                          @lang("$string_file.commission")
                        </label>
                        {{ Form::number('commission', old('commission', isset($commissionfare) ? $commissionfare->commission : ''), ['class' => 'form-control', 'step' => '0.01', 'id' => "isd",'required'=>true])  }}
                        @if ($errors->has('commission'))
                          <label class="text-danger">{{ $errors->first('commission') }}</label>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="form-actions d-flex flex-row-reverse p-2">
                    <button type="submit" class="btn btn-primary">
                      <i class="fa fa-check-circle"></i>
                      @if(isset($commissionfare))
                        @lang("$string_file.update")
                      @else
                        @lang("$string_file.save")
                      @endif
                    </button>
                  </div>
                </form>
            </form>
        </div>
      </div>
    </div>
  </div>
@endsection

