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
                            <a href="<?php echo e(route('promocode.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.promo_code"); ?>
                    </h3>
                </header>
                <?php $id = isset($promocode->id) ? $promocode->id : NULL;?>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="<?php echo e(route('promocode.store',$id)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo Form::hidden('id',$id); ?>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.service_area"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php if(!empty($id)): ?>
                                            <?php echo Form::text('area_id',$promocode->CountryArea->CountryAreaName,['class'=>"form-control",'disabled'=>true]); ?>

                                            <?php echo Form::hidden('area',$promocode->country_area_id,[]); ?>

                                        <?php else: ?>
                                            <select class="form-control" name="area" id="area"
                                                    onchange="getSegment(this.value)"
                                                    
                                                    required>
                                                <option value="">--<?php echo app('translator')->get("$string_file.select"); ?>--</option>
                                                <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option id="<?php echo e($area->id); ?>"
                                                            value="<?php echo e($area->id); ?>"><?php echo e($area->CountryAreaName); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php if($errors->has('area')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('area')); ?></label>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.segment"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php if(!empty($id)): ?>
                                            <?php echo Form::text('seg_id',($promocode->segment_id != NULL) ? $segment_list[$promocode->segment_id] : "---",['class'=>"form-control",'disabled'=>true]); ?>

                                            <?php echo Form::hidden('segment_id',$promocode->segment_id,[]); ?>

                                        <?php else: ?>
                                            <?php echo Form::select('segment_id',add_blank_option($segment_list,trans("$string_file.select")),old('segment_id'),array('class' => 'form-control','required'=>true,'id'=>'segment_id')); ?>

                                            <?php if($errors->has('segment_id')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('segment_id')); ?></label>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.promo_code"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="promocode"
                                               name="promocode" placeholder=""
                                               value="<?php echo e(old('promocode',isset($promocode->promoCode) ? $promocode->promoCode : NULL)); ?>"
                                               required>
                                        <?php if($errors->has('promocode')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promocode')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.type"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control"
                                                name="promo_code_value_type"
                                                id="promo_code_value_type"
                                                onchange="changeText(this.value)"
                                                required>
                                            <option id="1" value="1"
                                                    <?php if(!empty($id) && $promocode->promo_code_value_type == 1): ?> selected <?php endif; ?>> <?php echo app('translator')->get("$string_file.flat"); ?></option>
                                            <option id="2" value="2"
                                                    <?php if(!empty($id) && $promocode->promo_code_value_type == 2): ?> selected <?php endif; ?>> <?php echo app('translator')->get("$string_file.percentage"); ?></option>
                                        </select>
                                        <?php if($errors->has('promo_code_value_type')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promo_code_value_type')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.discount"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <input type="number" step=0.01 min=0 class="form-control"
                                               id="promo_code_value" name="promo_code_value"
                                               placeholder=""
                                               value="<?php echo e(old('promo_code_value',isset($promocode->promo_code_value) ? $promocode->promo_code_value : NULL)); ?>"
                                               required>
                                        <?php if($errors->has('promo_code_value')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promo_code_value')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.description"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="promo_code_description"
                                                  name="promo_code_description" placeholder=""
                                                  required><?php echo e(old('promo_code_description',isset($promocode->promo_code_description) ? $promocode->promo_code_description : "")); ?></textarea>
                                        <?php if($errors->has('promo_code_description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promo_code_description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.validity"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="icheckbox_minimal checked hover active"
                                             style="position: relative;">
                                            <input type="radio"
                                                   id="promo_code_validity_permanent" value="1"
                                                   name="promo_code_validity"
                                                   onclick="javascript:yesnoCheck()" checked
                                                   <?php if(!empty($id) && $promocode->promo_code_validity == 1): ?> checked <?php endif; ?>>
                                            <label for="promo_code_validity_permanent"
                                                   class=""><?php echo app('translator')->get("$string_file.permanent"); ?></label>
                                            <input type="radio" id="promo_code_validity_custom"
                                                   value="2" name="promo_code_validity"
                                                   onclick="javascript:yesnoCheck()"
                                                   style="margin-left: 20px;"
                                                   <?php if(!empty($id) && $promocode->promo_code_validity == 2): ?> checked <?php endif; ?>>
                                            <label for="promo_code_validity_custom"
                                                   class=""><?php echo app('translator')->get("$string_file.custom"); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group custom-hidden" id="start-div">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.start_date"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control customDatePicker1"
                                               name="start_date" placeholder=""
                                               value="<?php echo e(old('start_date', isset($promocode->start_date) ? $promocode->start_date : NULL)); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php if(empty($id) || (!empty($id) && $promocode->promo_code_validity == 1)): ?> custom-hidden <?php endif; ?>"
                                         id="end-div">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.end_date"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control customDatePicker1" name="end_date"
                                               placeholder=""
                                               value="<?php echo e(old('end_date', isset($promocode->end_date) ? $promocode->end_date : NULL)); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.applicable_for"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="applicable_for" id="applicable_for"
                                                onchange="UserType(this.value)" required>
                                            <option value="1"
                                                    <?php if(!empty($id) && $promocode->applicable_for == 1): ?> selected <?php endif; ?>> <?php echo app('translator')->get("$string_file.all_users"); ?></option>
                                            <option value="2"
                                                    <?php if(!empty($id) && $promocode->applicable_for == 2): ?> selected <?php endif; ?>> <?php echo app('translator')->get("$string_file.new_user"); ?></option>
                                            <?php if($config->corporate_admin == 1): ?>
                                                <option value="3"
                                                        <?php if(!empty($id) && $promocode->applicable_for == 3): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.corporate_users"); ?></option>
                                            <?php endif; ?>
                                        </select>
                                        <?php if($errors->has('applicable_for')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('applicable_for')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.limit"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control"
                                               id="promo_code_limit"
                                               name="promo_code_limit"
                                               placeholder=""
                                               value="<?php echo e(old('promo_code_limit',isset($promocode->promo_code_limit) ? $promocode->promo_code_limit : NULL)); ?>"
                                               required>
                                        <?php if($errors->has('promo_code_limit')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promo_code_limit')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.limit_per_user"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="promo_code_limit_per_user"
                                               name="promo_code_limit_per_user" placeholder=""
                                               value="<?php echo e(old('promo_code_limit_per_user',isset($promocode->promo_code_limit_per_user) ? $promocode->promo_code_limit_per_user : NULL)); ?>"
                                               required>
                                        <?php if($errors->has('promo_code_limit_per_user')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promo_code_limit_per_user')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.minimum_bill_amount"); ?>

                                        </label>
                                        <input type="number" class="form-control" id="order_minimum_amount"
                                               name="order_minimum_amount" placeholder=""
                                               value="<?php echo e(old('order_minimum_amount',isset($promocode->order_minimum_amount) ? $promocode->order_minimum_amount : NULL)); ?>">
                                        <?php if($errors->has('order_minimum_amount')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('order_minimum_amount')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.promo_percentage_maximum_discount"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control"
                                               id="promo_percentage_maximum_discount"
                                               name="promo_percentage_maximum_discount"
                                               placeholder=""
                                               value="<?php echo e(old('promo_percentage_maximum_discount',isset($promocode->promo_percentage_maximum_discount) ? $promocode->promo_percentage_maximum_discount:NULL)); ?>"
                                               disabled>
                                        <?php if($errors->has('promo_percentage_maximum_discount')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promo_percentage_maximum_discount')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.promo_code_parameter"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="promo_code_name"
                                               name="promo_code_name" placeholder=""
                                               value="<?php echo e(old('promo_code_name',!empty($id) ? $promocode->PromoName : NULL)); ?>"
                                               required>
                                        <?php if($errors->has('promo_code_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('promo_code_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row <?php if(empty($id) || !empty($id) && $promocode->applicable_for != 3): ?> custom-hidden <?php endif; ?>"
                                 id="corporate_div">
                                <div class="col-md-4 corporate_inr">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.corporate_name"); ?></label>
                                        <select class="form-control" name="corporate_id"
                                                id="corporate_id">
                                            <option value="">--<?php echo app('translator')->get("$string_file.select"); ?>--</option>
                                            <?php $__currentLoopData = $corporates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $corporate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($corporate->id); ?>"><?php echo e($corporate->corporate_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('rider_type')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('rider_type')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle" onclick="return Validate()"></i>
                                    <?php echo app('translator')->get("$string_file.save"); ?>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        function Validate() {
            var promo_code_value_type = document.getElementById('promo_code_value_type').value;
            var promo_code_value = document.getElementById('promo_code_value').value;
            if (promo_code_value_type == 2 && promo_code_value > 100) {
                alert('Enter Value Less Then 100');
                return false;
            }
        }

        function changeText(val) {
            let firstmsg = "";
            let firstmsg2 = "";
            if (val == "2") {
                $('#promo_percentage_maximum_discount').prop("disabled", false);
                $('#promo_code_value').attr("placeholder", firstmsg2);
            } else {
                $('#promo_percentage_maximum_discount').prop("disabled", true);
                $('#promo_code_value').attr("placeholder", firstmsg);
            }
        }

        function UserType(val) {
            if (val == "3") {
                document.getElementById('corporate_div').style.display = 'block';
            } else {
                document.getElementById('corporate_div').style.display = 'none';
            }
        }

        function yesnoCheck() {
            if (document.getElementById('promo_code_validity_permanent').checked) {
                document.getElementById('start-div').style.display = 'none';
                document.getElementById('end-div').style.display = 'none';
            } else {
                document.getElementById('start-div').style.display = 'block';
                document.getElementById('end-div').style.display = 'block';
            }
        }

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        

        function getSegment(val) {
            console.log(val);
            $("#segment_id").empty();
            var area_id = val;
            var data = {area_id: area_id, segment_group_id: 1};
            $("#segment_id").append('<option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>');
            <?php if($handyman_apply_promocode): ?>
                data = {area_id: area_id};
            <?php endif; ?>
            if (area_id != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('get.area.segment') ?>',
                    data: data,
                    success: function (data) {
                        $("#segment_id").empty();
                        $('#segment_id').html(data);
                    }
                });
                $("#loader1").hide();
            }
        }
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/promocode/create.blade.php ENDPATH**/ ?>