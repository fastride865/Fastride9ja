@php
    $order_id = isset($arr_search['order_id']) ? $arr_search['order_id'] : "";
    $rider = isset($arr_search['rider']) ? $arr_search['rider'] : "";
    $start = isset($arr_search['start']) ? $arr_search['start'] : "";
    $product = isset($arr_search['product']) ? $arr_search['product'] : "";
    $end = isset($arr_search['end']) ? $arr_search['end'] : "";
    $driver = isset($arr_search['driver']) ? $arr_search['driver'] : "";
    $arr_segment = isset($arr_search['arr_segment']) ? $arr_search['arr_segment'] : [];
    $arr_bs = isset($arr_search['arr_bs']) ? $arr_search['arr_bs'] : [];
    $segment_id = isset($arr_search['segment_id']) ? $arr_search['segment_id'] : NULL;
    $driver_id = isset($arr_search['driver_id']) ? $arr_search['driver_id'] : NULL;
    $business_segment_id = isset($arr_search['business_segment_id']) ? $arr_search['business_segment_id'] : NULL;
    $calling_view = isset($arr_search['calling_view']) ? $arr_search['calling_view'] : "";
    $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : "";
@endphp
{!! Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']) !!}
<div class="table_search row">
    @if(!empty($arr_segment))
        <div class="col-md-2 col-xs-12 form-group active-margin-top">
            <div class="input-group">
                {!! Form::select('segment_id',add_blank_option($arr_segment,trans("$string_file.segment")),$segment_id,['class'=>'form-control']) !!}
            </div>
        </div>
    @endif
        @if(!empty($arr_bs))
            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                <div class="input-group">
                    {!! Form::select('business_segment_id',add_blank_option($arr_bs,trans("$string_file.store")),$business_segment_id,['class'=>'form-control']) !!}
                </div>
            </div>
        @endif
    @if($calling_view == "earning")
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="order_id" value="{{$order_id}}"
                   placeholder="@lang("$string_file.order_id")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    @else
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="product" value="{{$product}}"
                   placeholder="@lang("$string_file.product_name")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="rider" value="{{$rider}}"
                   placeholder="@lang("$string_file.user_details")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="driver" value="{{$driver}}"
                   placeholder="@lang("$string_file.driver_details")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    @endif
    <div class="col-md-4 col-xs-12 form-group active-margin-top">
        <div class="input-daterange" data-plugin="datepicker">
            <div class="input-group">
                <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="icon wb-calendar" aria-hidden="true"></i>
                          </span>
                </div>
                <input type="text" class="form-control" name="start" value="{{$start}}" />
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">to</span>
                </div>
                <input type="text" class="form-control" name="end" value="{{$end}}" />
            </div>
        </div>
        {!! Form::hidden('driver_id',$driver_id,['class'=>'form-control']) !!}
{{--        <div class="input-group">--}}
{{--            <input type="text" id="" name="date" value="{{$date}}"--}}
{{--                   placeholder="@lang("$string_file.date"): {{date('Y-m-d')}}"--}}
{{--                   class="form-control col-md-12 col-xs-12 customDatePicker2"--}}
{{--                   id="datepickersearch" autocomplete="off">--}}
{{--            <br>--}}
{{--        </div>--}}
    </div>
    <div class="col-sm-2  col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
        <a href="{{$search_route}}" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
    </div>
</div>
{!! Form::close() !!}