<?php $__env->startSection('content'); ?>
    <style>
        em {
            color: red;
        }
    </style>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php if(Session::has('handyman-document-expire-warning')): ?>
                <p class="alert alert-info"><?php echo e(Session::get('handyman-document-expire-warning')); ?></p>
            <?php endif; ?>
            <?php if(Session::has('handyman-document-expired-error')): ?>
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i> <?php echo e(Session::get('handyman-document-expired-error')); ?>

                </div>
            <?php endif; ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('driver.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                         <?php echo app('translator')->get("$string_file.driver_name"); ?> : <?php echo e($driver->first_name .' '.$driver->last_name); ?> ->  <?php echo app('translator')->get("$string_file.handyman_services_configuration"); ?>
                    </h3>
                </header>
                <?php $display = true; $selected_doc = []; $id = NULL ?>
                <?php if(isset($driver->id) && !empty($driver->id)): ?>
                    <?php $display = false;
                    $id =  $driver->id;
                    ?>
                <?php endif; ?>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'driver-handyman-segment','files'=>true,'url'=>route('merchant.driver.handyman.segment.save',$id)]); ?>

                    <?php echo Form::hidden("id",$id,['class'=>'','id'=>'id']); ?>







                     <?php $__currentLoopData = $arr_segment_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <?php $segment_id = $key; ?>
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
                                                            <?php $service_type_id = $service['id'];
                                                            $arr_selected_services = isset($arr_selected_segment_service[$key]) ? $arr_selected_segment_service[$key] : [];
                                                            $checked = '';
                                                            ?>
                                                            <?php if(in_array($service_type_id,$arr_selected_services)): ?>
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

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="location3"><?php echo app('translator')->get("$string_file.document"); ?></label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                        <?php $arr_uploaded_doc = []; $document_file = ""; $expire_date = NULL; $document_number = "";?>
                                        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <?php if($segment_id == $document['pivot']->segment_id): ?>
                                             <?php $uploaded_document = isset($arr_segment_selected_document[$segment_id][$document->id]) ? $arr_segment_selected_document[$segment_id][$document->id] : NULL; ?>
                                             <?php if($uploaded_document): ?>
                                                 <?php
                                                 $document_file = $uploaded_document->document_file;
                                                 $expire_date = $uploaded_document->expire_date;
                                                 $document_number = $uploaded_document->document_number;
                                                 ?>
                                             <?php endif; ?>
                                            <div class="row">
                                            <?php echo Form::hidden('segment_document_id['.$segment_id.'][]',$document->id); ?>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="emailAddress5">
                                                        <?php echo e($document->DocumentName); ?>:
                                                        <span class="text-danger">*</span>
                                                        <?php if(!empty($uploaded_document)): ?>)
                                                            <a href="<?php echo e(get_image($document_file,'segment_document')); ?>" target="_blank"><?php echo app('translator')->get("$string_file.view"); ?> </a>
                                                        <?php endif; ?>
                                                    </label>
                                                    <input type="file" class="form-control" id="document"
                                                           name="segment_document[<?php echo e($segment_id); ?>][<?php echo e($document['id']); ?>]"
                                                           placeholder=""
                                                           <?php if($document['documentNeed'] == 1 && empty($document_file)): ?>  <?php endif; ?>>
                                                    <?php if($errors->has('documentname')): ?>
                                                        <label class="text-danger"><?php echo e($errors->first('documentname')); ?>

                                                        </label>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if($document->expire_date == 1): ?>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="datepicker"><?php echo app('translator')->get("$string_file.expire_date"); ?>
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="icon wb-calendar" aria-hidden="true"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control customDatePicker1" name="expire_date[<?php echo e($segment_id); ?>][<?php echo e($document->id); ?>]"
                                                                   placeholder="<?php echo app('translator')->get("$string_file.expire_date"); ?>  " value="<?php echo e(isset($expire_date) ? $expire_date : ''); ?>"
                                                                   <?php if($document['expire_date'] == 1 && empty($expire_date)): ?>  <?php endif; ?> autocomplete="off" >
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($document->document_number_required == 1): ?>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="emailAddress5">
                                                        <?php echo app('translator')->get("$string_file.document_number"); ?> :
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="document_number"
                                                           name="document_number[<?php echo e($segment_id); ?>][<?php echo e($document['id']); ?>]"
                                                           placeholder="<?php echo app('translator')->get("$string_file.document_number"); ?>"
                                                           value="<?php echo e($document_number); ?>">

                                                    <?php if($errors->has('document_number')): ?>
                                                        <label class="text-danger"><?php echo e($errors->first('document_number')); ?></label>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                         </div>
                                         <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i><?php echo app('translator')->get("$string_file.save"); ?>
                        </button>
                    </div>
                    <?php echo Form::close(); ?>

                </div>
            </div>
        </div>
    </div>
        <?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script>
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/handyman-segment.blade.php ENDPATH**/ ?>