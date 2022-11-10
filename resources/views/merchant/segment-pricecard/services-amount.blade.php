<h5>@lang("$string_file.set_service_charges_as_fixed")</h5>
<hr>
<div class="row">
    @foreach($arr_services as $service)
        @php
            $amount = isset($service->SegmentPriceCardDetail) ? $service->SegmentPriceCardDetail->amount : NULL;
            $detail_id = isset($service->SegmentPriceCardDetail) ? $service->SegmentPriceCardDetail->id : NULL;
        @endphp
    <div class="col-md-4">
        <div class="form-group">
            <label for="firstName3">
               {{!empty($service->serviceName($merchant_id)) ? $service->serviceName($merchant_id) : $service->serviceName}}
                <span class="text-danger">*</span>
            </label>
            {!! Form::hidden('detail_id['.$service->id.']',old('detail_id',$detail_id),['class'=>'form-control','id'=>'detail_id','placeholder'=>"",'required'=>true]) !!}
            {!! Form::number('fixed_amount['.$service->id.']',old('fixed_amount',$amount),['class'=>'form-control','id'=>'sequence_number','placeholder'=>"",'required'=>true,'min'=>0]) !!}
            @if ($errors->has('fixed_amount'))
                <label class="text-danger">{{ $errors->first('fixed_amount') }}</label>
            @endif
        </div>
    </div>
@endforeach
</div>
