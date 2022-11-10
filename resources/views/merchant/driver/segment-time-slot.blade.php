@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                         @lang("$string_file.driver_name") : {{$driver->first_name .' '.$driver->last_name}} ->  @lang("$string_file.service_time_slots")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                @php $display = true; $selected_doc = []; $id = NULL @endphp
                @if(isset($driver->id) && !empty($driver->id))
                    @php $display = false;
                    $id =  $driver->id;
                    @endphp
                @endif
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'driver-segment-time-slot','url'=>route('merchant.driver.segment.time-slot.save',$id)]) !!}
                    {!! Form::hidden("id",$id,['class'=>'','id'=>'id']) !!}
                     @foreach($arr_segment as $key=>$segment)
                         @php $segment_id = $segment->id; @endphp
                            <div class="border rounded p-4 mt-10 shadow-sm bg-light">
                                <div class="border rounded p-4 mb-2 bg-white">
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <strong>{!! $segment->Name($merchant_id) !!}'s @lang("$string_file.time_slot")</strong>
                                        </div>
                                        </div>
                                    </div>
                                    @foreach($segment_time_slot['time_slots'] as $day_slot)
                                      @if($day_slot['segment_id'] == $segment_id && count($day_slot['service_time_slot']) > 0)
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="location3">{!! $day_slot['day_title'] !!}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                            @php $arr_uploaded_doc = []; @endphp
                                            @foreach($day_slot['service_time_slot'] as $key=> $time_slot)
                                             <div class="col-md-4">
                                               <label for="ProfileImage"></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input time-slot-checkbox" id="{{$time_slot['id']}}" name="arr_time_slot[{{$segment_id}}][]" value="{{$time_slot['id']}}" {{$time_slot['selected'] == 1 ? "checked" : ""}} >
                                                                <label class="custom-control-label" for="{{$time_slot['id']}}"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                      <input value="{{$time_slot['slot_time_text']}}" class="form-control" id="time-slot-{{$time_slot['id']}}" name="" placeholder="" aria-describedby="" disabled>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                            </div>
                                        </div>
                                        <hr>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                     @endforeach
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i>@lang("$string_file.save")
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
        @endsection
@section('js')
<script>
    $(document).on("click",".time-slot-checkbox",function(e){
        var val = $(this).val();
        if ($(this).is(':checked')) {
        }
    })
</script>
@endsection