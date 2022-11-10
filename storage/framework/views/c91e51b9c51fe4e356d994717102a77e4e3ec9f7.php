<div class="row">
    <div class="col-md-12">
        <form action="<?php echo e($search_route); ?>" method="get">
            <div class="table_search row">
                <?php
                    $segment_id = isset($arr_search['segment_id']) ? $arr_search['segment_id'] : NULL;
                    $country_area_id = isset($arr_search['country_area_id']) ? $arr_search['country_area_id'] : NULL;
                ?>
                <?php if($segment_list > 1): ?>
                    <div class="col-md-3">
                        <?php echo Form::select('segment_id',add_blank_option($segment_list,trans("$string_file.segment")),$segment_id,['class'=>'form-control','id'=>'segment_id']); ?>

                    </div>
                <?php endif; ?>

                <div class="col-md-3 col-xs-12 form-group active-margin-top">
                    <div class="">
                        <?php echo Form::select('country_area_id',add_blank_option($country_area,trans("$string_file.service_area")),$country_area_id,['class'=>'form-control','id'=>'country_area_id']); ?>

                    </div>
                </div>
                <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                    <button class="btn btn-primary" type="submit" name="search"><i
                                class="fa fa-search" aria-hidden="true"></i></button>
                    <a href="<?php echo e($search_route); ?>">
                        <button class="btn btn-success" type="button">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </button>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>


<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/segment-pricecard/search.blade.php ENDPATH**/ ?>