<?php $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : route('driver.index');?>
<?php echo Form::open(['name'=>'','class'=>'','url'=>$search_route,"onsubmit"=>"return selectSearchFields()",'method'=>"GET","id"=>"driver-search"]); ?>

<?php $searched_segment = []; $searched_param = NULL; $searched_area = NULL; $searched_text = "";
$driver_status = NULL; $arr_status_list = arr_driver_search_status($string_file);
 ?>

<?php if(!empty($arr_search)): ?>
    <?php $searched_segment = isset($arr_search['segment_id']) ? $arr_search['segment_id'] : [] ;
     $searched_param = isset($arr_search['parameter']) ? $arr_search['parameter'] : NULL;
     $searched_area = isset($arr_search['area_id']) ? $arr_search['area_id'] : NULL;
     $searched_country = isset($arr_search['country_id']) ? $arr_search['country_id'] : NULL;
     $searched_text = isset($arr_search['keyword']) ? $arr_search['keyword'] : "";
     $online_offline = isset($arr_search['online_offline']) ? $arr_search['online_offline'] : "";
     $driver_status = isset($arr_search['driver_status']) ? $arr_search['driver_status'] : "";
    ?>
<?php endif; ?>
<div class="table_search row p-3 ">

    <?php if(count($arr_segment) > 1): ?>
        <div class="col-md-2">
            <?php echo Form::select('segment_id[]',$arr_segment,$searched_segment,['class'=>'form-control select2','multiple'=>true,'id'=>'segment_id','data-placeholder'=>trans("$string_file.segment")]); ?>

        </div>
    <?php endif; ?>
    <div class="col-md-2">
        <?php echo Form::select('driver_status',$arr_status_list,$driver_status,['class'=>'form-control','id'=>'segment_id']); ?>

    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <?php echo Form::select('country_id',add_blank_option($countries,trans("$string_file.country")),$searched_country,['class'=>'form-control select2','id'=>'country_id']); ?>

    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <?php echo Form::select('area_id',add_blank_option($areas,trans("$string_file.area")),$searched_area,['class'=>'form-control select2','id'=>'area_id']); ?>

    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <?php echo Form::select('parameter',add_blank_option($search_param,trans("$string_file.select_by")),$searched_param,['class'=>'form-control','id'=>'by_param']); ?>

    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <input id="keyword" name="keyword" placeholder="<?php echo app('translator')->get("$string_file.enter_text"); ?>" value="<?php echo e($searched_text); ?>"
               class="form-control" type="text">
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i>
        </button>
        <a href="<?php echo e($search_route); ?>">
            <button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i>
            </button>
        </a>
    </div>
</div>
<hr>
<?php echo Form::close(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/driver-search.blade.php ENDPATH**/ ?>