<h5 class="form-section col-md-12" style="color: black;">
    <i class="fa fa-file"></i> <?php echo app('translator')->get("$string_file.personal_document"); ?>
</h5>
<hr>
<?php
    $arr_uploaded_doc = [];
?>
<?php if(isset($driver['driver_document']) && !empty($driver['driver_document'])): ?>
    <?php
        $arr_uploaded_doc = array_column($driver['driver_document'],NULL,'document_id');
    ?>
<?php endif; ?>
<?php $__currentLoopData = $areas->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php $expire_date = null;
$document_file = null; ?>
<?php if(isset($arr_uploaded_doc[$document['id']])): ?>
    <?php
        $expire_date = $arr_uploaded_doc[$document['id']]['expire_date'];
        $document_file = $arr_uploaded_doc[$document['id']]['document_file'];
        $document_number = $arr_uploaded_doc[$document['id']]['document_number'];
    ?>
<?php endif; ?>
<?php echo Form::hidden('all_doc[]',$document['id']); ?>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="emailAddress5">
                <?php echo e($document->DocumentName); ?>:
                <span class="text-danger">*</span>
            </label>
            <?php if(in_array($document['pivot']['document_id'],array_keys($arr_uploaded_doc))): ?>
                <a href="<?php echo e(get_image($document_file,'driver_document')); ?>"
                   target="_blank"><?php echo app('translator')->get("$string_file.view"); ?> </a>
            <?php endif; ?>
            <input type="file" class="form-control" id="document"
                   name="document[<?php echo e($document['id']); ?>]"
                   placeholder=""
                   <?php if($document['documentNeed'] == 1 && empty($document_file)): ?>)
                   required <?php endif; ?>>
            <?php if($errors->has('documentname')): ?>
                <label class="text-danger"><?php echo e($errors->first('documentname')); ?></label>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-4">
        <?php if($document->expire_date == 1): ?>
            <div class="form-group">
                <label for="datepicker"><?php echo app('translator')->get("$string_file.expire_date"); ?>
                    <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="icon wb-calendar"
                                                          aria-hidden="true"></i></span>
                    </div>
                    <input type="text"
                           class="form-control customDatePicker1"
                           name="expiredate[<?php echo e($document->id); ?>]"
                           value="<?php echo e($expire_date); ?>"
                           placeholder="<?php echo app('translator')->get("$string_file.expire_date"); ?>  "
                           <?php if($document['expire_date'] == 1 && empty($expire_date)): ?> required
                           <?php endif; ?>
                           autocomplete="off">
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if($document->document_number_required == 1): ?>
        <div class="col-md-4">
            <div class="form-group">
                <label for="emailAddress5">
                    <?php echo app('translator')->get("$string_file.document_number"); ?> :
                    <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="document_number"
                       name="document_number[<?php echo e($document['id']); ?>]"
                       placeholder="<?php echo app('translator')->get("$string_file.document_number"); ?>"
                       value="<?php echo e(isset($document_number) ? $document_number : ''); ?>"
                       required>
                <?php if($errors->has('document_number')): ?>
                    <label class="text-danger"><?php echo e($errors->first('document_number')); ?></label>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/personal-document.blade.php ENDPATH**/ ?>