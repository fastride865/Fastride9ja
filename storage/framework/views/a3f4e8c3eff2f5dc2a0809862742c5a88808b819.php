<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="<?php echo e(route('merchant.segment.price_card')); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.add_price_card"); ?>
                    </h3>
                </header>
                <?php $id = NULL; ?>
                <?php if(isset($data['price_card']['id'])): ?>
                    <?php $id = $data['price_card']['id'];
                    ?>
                <?php endif; ?>
                <?php echo Form::hidden('segment_price_card_id',$id,['id' =>'segment_price_card_id']); ?>

                <?php $min_hour_req = false; $service_type_req = false; ?>
                <?php if($id != NULL && $data['price_card']['price_type'] == 2): ?>
                    <?php $min_hour_req = true; ?>
                <?php else: ?>
                    <?php $service_type_req = true; ?>
                <?php endif; ?>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <?php echo Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("segment.price_card.save",$id)]); ?>

                        <?php echo Form::hidden('id',$id); ?>

                        <fieldset>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name"><?php echo app('translator')->get("$string_file.service_area"); ?> <span class="text-danger">*</span>
                                            </label>
                                            <?php echo Form::select('country_area_id',add_blank_option($data['arr_areas'],trans("$string_file.select")),old('country_area_id',isset($data['price_card']['country_area_id']) ? $data['price_card']['country_area_id'] :NULL),['class'=>'form-control','required'=>true,'id'=>'country_area_id','onChange'=>"getSegment()"]); ?>

                                            <?php if($errors->has('country_area_id')): ?>
                                                <span class="help-block">
                                                    <strong><?php echo e($errors->first('country_area_id')); ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo app('translator')->get("$string_file.segment"); ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <?php echo Form::select('segment_id',add_blank_option($data['arr_segment'],trans("$string_file.select")),old('segment_id',isset($data['price_card']['segment_id']) ? $data['price_card']['segment_id'] :NULL),["class"=>"form-control","id"=>"area_segment","required"=>true,'onChange'=>"getService()"]); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo app('translator')->get("$string_file.price_type"); ?> (<?php echo app('translator')->get("$string_file.service_charges"); ?>)<span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <?php echo Form::select('price_type',$data['arr_type'],old('price_type',isset($data['price_card']['price_type']) ? $data['price_card']['price_type'] :NULL),['class'=>'form-control','required'=>true,'id'=>'price_type']); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.minimum_booking_amount"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <?php echo Form::number('minimum_booking_amount',old('minimum_booking_amount',isset($data['price_card']['minimum_booking_amount']) ? $data['price_card']['minimum_booking_amount'] : ''),['class'=>'form-control','id'=>'minimum_booking_amount','placeholder'=>"","required"=>$min_hour_req,'min'=>0, 'step' => 'any']); ?>

                                            <?php if($errors->has('minimum_booking_amount')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('minimum_booking_amount')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo app('translator')->get("$string_file.status"); ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <?php echo Form::select('status',$data['arr_status'],old('service_type_id',isset($data['price_card']['status']) ? $data['price_card']['status'] :NULL),['class'=>'form-control','required'=>true,'id'=>'status']); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="<?php if($id == NULL || (!empty($id) && $data['price_card']['price_type'] == 2)): ?> custom-hidden <?php endif; ?>" id="service_type_div">
                                    <?php echo $data['arr_services']; ?>

                                </div>
                                    <?php $hourly_required = false; ?>
                                    <?php if(!empty($id) && $data['price_card']['price_type'] == 2): ?>
                                    <?php $hourly_required = true; ?>
                                    <?php endif; ?>
                                <div class="<?php if($id == NULL || (!empty($id) && $data['price_card']['price_type'] == 1)): ?> custom-hidden <?php endif; ?>" id="hourly_charges_div">
                                    <h5><?php echo app('translator')->get("$string_file.set_charges_as_hourly"); ?></h5>
                                    <hr>
                                    <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.per_hour_amount"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <?php echo Form::number('hourly_amount',old('amount',isset($data['price_card']['amount']) ? $data['price_card']['amount'] : ''),['class'=>'form-control','id'=>'hourly_amount','placeholder'=>"",'required'=>$hourly_required,'min'=>0]); ?>

                                            <?php if($errors->has('hourly_amount')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('hourly_amount')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </fieldset>
                        <div class="form-actions float-right">
                            <?php if(!$is_demo): ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-square-o"></i><?php echo $data['submit_button']; ?>

                                    </button>
                            <?php else: ?>
                                <span style="color: red"> <?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php echo Form::close(); ?>

                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script type="text/javascript">
        function getSegment() {
            $("#area_segment").empty();
            $("#area_segment").append('<option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>');
            $("#service_type_id").empty();
            $("#service_type_id").append('<option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>');
            var area_id = $("#country_area_id option:selected").val();
            if (area_id != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('get.area.segment') ?>',
                    data: {area_id: area_id,segment_group_id:2},
                    success: function (data) {
                        $("#area_segment").empty();
                        $('#area_segment').html(data);
                    }
                });
                $("#loader1").hide();
            }
        }
        function getService() {
            var area_id = $("#country_area_id option:selected").val();
            var segment_id = $("#area_segment option:selected").val();
            var price_type = $("#price_type option:selected").val();
            var segment_price_card_id = $("#segment_price_card_id").val();
            // console.log(area_id);
            // setPriceTypeSetting(price_type);
            $('#service_type_div').html("");
            $("#service_type_div").hide();
            if (area_id != "" && price_type == 1) {
                // $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('segment.price_card.services') ?>',
                    data: {area_id: area_id,segment_id:segment_id,segment_group:2,segment_price_card_id:segment_price_card_id},
                    success: function (data) {
                        $('#service_type_div').html(data);
                        $("#service_type_div").show();
                    }
                });
                // $("#loader1").hide();
            }
        }
            $(document).on("change","#price_type",function(e){
                var val = $(this).val();
                setPriceTypeSetting(val)

            });
        function setPriceTypeSetting(val)
        {
            getService();
            $("#service_type_div").hide();
            $("#hourly_charges_div").hide();
            if(val == 1)
            {
                $("#service_type_div").show();
            }
            else if(val == 2)
            {
                $("#hourly_charges_div").show();
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/segment-pricecard/form.blade.php ENDPATH**/ ?>