@php
    /** $order_id = isset($arr_search['order_id']) ? $arr_search['order_id'] : "";
    $rider = isset($arr_search['rider']) ? $arr_search['rider'] : "";
    $product = isset($arr_search['product']) ? $arr_search['product'] : "";
    $driver = isset($arr_search['driver']) ? $arr_search['driver'] : "";
    $arr_segment = isset($arr_search['arr_segment']) ? $arr_search['arr_segment'] : [];
    $arr_bs = isset($arr_search['arr_bs']) ? $arr_search['arr_bs'] : [];
    $segment_id = isset($arr_search['segment_id']) ? $arr_search['segment_id'] : NULL;
    $driver_id = isset($arr_search['driver_id']) ? $arr_search['driver_id'] : NULL;
    $business_segment_id = isset($arr_search['business_segment_id']) ? $arr_search['business_segment_id'] : NULL;**/
    $start = isset($arr_search['start']) ? $arr_search['start'] : "";
    $end = isset($arr_search['end']) ? $arr_search['end'] : "";
    $calling_view = isset($arr_search['calling_view']) ? $arr_search['calling_view'] : "";
    $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : "";
@endphp
{!! Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']) !!}
<div class="table_search row">
   
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
    </div>
    <div class="col-sm-2  col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
        <a href="{{$search_route}}" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
    </div>
</div>
{!! Form::close() !!}