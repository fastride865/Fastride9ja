@php
    $full_name = isset($arr_search['full_name']) ? $arr_search['full_name'] : "";
    $email = isset($arr_search['email']) ? $arr_search['email'] : "";
    $phone_number = isset($arr_search['phone_number']) ? $arr_search['phone_number'] : "";
    $country_area_id = isset($arr_search['country_area_id']) ? $arr_search['country_area_id'] : "";
    $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : "";
@endphp
{!! Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']) !!}
<div class="table_search row">

    <div class="col-md-3 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="full_name" value="{{$full_name}}"
                   placeholder="@lang("$string_file.name")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-3 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="email" value="{{$email}}"
                   placeholder="@lang("$string_file.email")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="phone_number" value="{{$phone_number}}"
                   placeholder="@lang("$string_file.phone")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            {!! Form::select('country_area_id',add_blank_option($arr_area,trans("$string_file.service_area")),$country_area_id,['class'=>'form-control','id'=>'country_area_id']) !!}
        </div>
    </div>
    <div class="col-sm-2  col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
        <a href="{{$search_route}}" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
    </div>
</div>
{!! Form::close() !!}