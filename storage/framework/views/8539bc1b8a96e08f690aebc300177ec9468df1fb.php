<div class="modal fade" id="addVehicle" tabindex="-1" role="upload" aria-labelledby="examplePositionTops" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'country-area-step2','files'=>true,'url'=>route('countryareas.save.step2',$area->id)]); ?>

            <?php echo Form::hidden("id",$area->id,['class'=>'','id'=>'id']); ?>

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo app('translator')->get("$string_file.vehicle_configuration"); ?></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="vehicle-modal-body">
                <div class="border rounded p-4 mt-10 shadow-sm bg-light" id="vehicle_count">
                    <?php $vehicle_type_id = isset($vehicle_type_id) ? $vehicle_type_id : NULL;
                    ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="vehicle"><?php echo app('translator')->get("$string_file.vehicle_type"); ?><span class="text-danger">*</span> </label>
                                <?php echo e(Form::select('vehicle_type',add_blank_option($vehicles,trans("$string_file.select")), old('vehicle_type',isset($vehicle_type_id) ? $vehicle_type_id : NULL), ['class'=>'form-control segment_vehicle','id' =>'','required'=>true])); ?>

                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_document"); ?><span class="text-danger">*</span></label>
                                <?php echo Form::select('vehicle_document[]',$documents,old('vehicle_document',isset($arr_vehicle_selected_document[$vehicle_type_id]) ? $arr_vehicle_selected_document[$vehicle_type_id] : []),["class"=>"select2 form-control","id"=>"vehicle_doc","multiple"=>true,"required"=>true]); ?>

                                <?php if($errors->has('vehicle_document')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('vehicle_document')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded p-4 mb-2 bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <?php $__currentLoopData = $arr_segment_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>$segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $arr_selected_segments = isset($arr_selected_vehicle_service[$vehicle_type_id]) ? $arr_selected_vehicle_service[$vehicle_type_id] : [] ; ?>
                                        <?php $arr_selected_services = !empty($arr_selected_segments)  && isset($arr_selected_segments[$key]) ? $arr_selected_segments[$key] : [];?>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <strong><?php echo $segment['name']; ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <?php $__currentLoopData = $segment['arr_services']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key_inner=>$service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php $service_type_id = $service['id'];  $checked = ''; ?>

                                                        <?php if(in_array($service_type_id,$arr_selected_services)): ?>
                                                            <?php $checked = 'checked'; ?>
                                                        <?php endif; ?>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input name="vehicle_service_type[<?php echo e($key); ?>][]" value="<?php echo $service_type_id; ?>" class="form-group mr-20 mt-5 ml-20" type="checkbox" id="<?php echo e($service_type_id); ?>" <?php echo e($checked); ?>><?php echo $service['locale_service_name']; ?>

                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($vehicle_type_id)): ?>
                        <span class="text-danger"><?php echo app('translator')->get("$string_file.note"); ?> :- <?php echo app('translator')->get("$string_file.service_area_document_warning"); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <?php if(!$is_demo): ?>
                <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo app('translator')->get("$string_file.cancel"); ?></button>
                <input type="submit" class="btn btn-primary btn" value="<?php echo app('translator')->get("$string_file.submit"); ?>">
                <?php else: ?>
                    <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                <?php endif; ?>
            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
</div>


<?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/area/edit-vehicle-config.blade.php ENDPATH**/ ?>