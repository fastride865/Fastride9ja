<h5 class="form-section"><i class="fa fa-paperclip"></i><?php echo app('translator')->get("$string_file.upload_document"); ?>
</h5>
<?php
    $arr_uploaded_doc =[];
    $expire_date = null;
    $document_file = null;
?>
<?php if(isset($vehicle_details->DriverVehicleDocument) && count($vehicle_details->DriverVehicleDocument->toArray()) > 0): ?>
    <?php
        $arr_uploaded_doc =  $vehicle_details->DriverVehicleDocument->toArray();
        $arr_uploaded_doc = array_column($arr_uploaded_doc,NULL, 'document_id');
        $arr_doc_id = array_column($arr_uploaded_doc,'document_id');
    ?>
<?php endif; ?>
<?php $__currentLoopData = $docs->VehicleDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $expire_date = null; $document_file = null;$document_number = NULL ?>
    <?php if(isset($arr_uploaded_doc[$doc['pivot']['document_id']])): ?>
        <?php
            $expire_date = $arr_uploaded_doc[$doc['pivot']['document_id']]['expire_date'];
            $document_file = $arr_uploaded_doc[$doc['pivot']['document_id']]['document'];
            $document_number = $arr_uploaded_doc[$doc['pivot']['document_id']]['document_number'];
        ?>
    <?php endif; ?>
    <?php echo Form::hidden('all_doc[]',$doc['id']); ?>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="location3">
                    <?php if(empty($doc->LanguageSingle)): ?>
                        <?php echo e($doc->LanguageAny->documentname); ?>

                    <?php else: ?>
                        <?php echo e($doc->LanguageSingle->documentname); ?>

                    <?php endif; ?>
                        <span class="text-danger">*</span>:</label>
                <?php if(in_array($doc['pivot']['document_id'],array_keys($arr_uploaded_doc))): ?>
                    <a href="<?php echo e(get_image($document_file,'vehicle_document')); ?>"
                       target="_blank"><?php echo app('translator')->get("$string_file.view"); ?> </a>
                <?php endif; ?>
                <input type="file" class="form-control"
                       name="document[<?php echo e($doc->id); ?>]"
                       placeholder=""
                       <?php if($doc->documentNeed == 1 && empty($document_file)): ?>) required <?php endif; ?>>
            </div>
        </div>
        <?php if($doc->expire_date == 1): ?>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="location3">
                        <?php echo app('translator')->get("$string_file.expire_date"); ?>  <span class="text-danger">*</span>
                        :</label>
                    <input type="text"
                           class="form-control customDatePicker1"
                           name="expiredate[<?php echo e($doc->id); ?>]" value="<?php echo e($expire_date); ?>"
                           placeholder=""
                           <?php if($doc['expire_date'] == 1 && empty($expire_date)): ?> required <?php endif; ?>
                           autocomplete="off">
                </div>
            </div>
        <?php endif; ?>
        <?php if($doc->document_number_required == 1): ?>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="emailAddress5">
                        <?php echo app('translator')->get("$string_file.document_number"); ?> :
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="document_number"
                           name="document_number[<?php echo e($doc['id']); ?>]"
                           placeholder="<?php echo app('translator')->get("$string_file.document_number"); ?>"
                           value="<?php echo e($document_number); ?>"
                           required>
                    <?php if($errors->has('document_number')): ?>
                        <label class="text-danger"><?php echo e($errors->first('document_number')); ?></label>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<hr>
<h5 class="form-section"><i class="fa fa-paperclip"></i> <?php echo app('translator')->get("$string_file.segment"); ?> & <?php echo app('translator')->get("$string_file.services_configuration"); ?>
</h5>

<?php $__currentLoopData = $arr_segment_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $segment_id = $key; ?>
    <?php if(count($segment['arr_services']) > 0): ?>
     <div class="border rounded p-4 mt-10 shadow-sm bg-light">
        <div class="border rounded p-4 mb-2 bg-white">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong><?php echo $segment['name']; ?>'s <br></strong><?php echo app('translator')->get("$string_file.services"); ?>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <?php $__currentLoopData = $segment['arr_services']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key_inner=>$service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $service_type_id = $service['id']; $checked  = "";?>
                                    <?php if(in_array($service_type_id,$selected_services)): ?>
                                        <?php $checked = 'checked'; ?>
                                    <?php endif; ?>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input name="segment_service_type[<?php echo e($key); ?>][]" value="<?php echo $service_type_id; ?>" id="<?php echo $service_type_id; ?>" class="form-group mr-20 mt-5 ml-20 area_segment" type="checkbox" <?php echo e($checked); ?>><?php echo $service['locale_service_name']; ?>

                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/vehicle-document-segment.blade.php ENDPATH**/ ?>