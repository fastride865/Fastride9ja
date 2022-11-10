<?php $__env->startSection('content'); ?>
    <style>
        .impo-text {
            color: red;
            font-size: 15px;
            text-wrap: normal;
            display: none;
        }
    </style>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="<?php echo e(route('documents.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin-left:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-add-file" aria-hidden="true"></i>
                        <?php echo $document['title']; ?>

                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'','url'=>$document['submit_url'],'class'=>'steps-validation wizard-notification']); ?>

                    <?php
                        $old_expire_status =  NULL;
                        $old_mandatory_status =  NULL;
                        $document_id =  NULL;
                    ?>
                    <?php if(isset($document['data']->id) && !empty($document['data']->id)): ?>
                        <?php
                            $document_id = $document['data']->id;
                            $old_expire_status =  $document['data']->expire_date;
                            $old_mandatory_status =  $document['data']->documentNeed;
                        ?>
                    <?php endif; ?>
                    <?php echo Form::hidden('document_id',$document_id,['id'=>'document_id','readonly'=>true]); ?>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="package_duation_name">
                                    <?php echo app('translator')->get("$string_file.name"); ?> :
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('documentname',old('documentname',isset($document['data']->LanguageSingle->documentname) ? $document['data']->LanguageSingle->documentname : ''),['id'=>'documentname','class'=>'form-control','required'=>true,'placeholder'=>'']); ?>

                                <?php if($errors->has('name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label><?php echo app('translator')->get("$string_file.mandatory"); ?>? : <span
                                        class="text-danger">*</span></label>
                            <div class="form-group">
                                <?php echo Form::select('documentNeed',$document['document_status'],old('documentNeed',$old_mandatory_status),['id'=>'documentNeed','class'=>'form-control','required'=>true,'old-mandatory-status'=>$old_mandatory_status]); ?>

                                <?php if($errors->has('documentNeed')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('documentNeed')); ?></label>
                                <?php endif; ?>
                                <div class="impo-text" id="document_mandatory_text_div">
                                    <?php echo app('translator')->get("$string_file.document_mandatory_text"); ?>;
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label><?php echo app('translator')->get("$string_file.expire_date"); ?> ?<span
                                        class="text-danger">*</span></label>
                            <div class="form-group">
                                <?php echo Form::select('expire_date',$document['document_status'],old('expire_date',$old_expire_status),['id'=>'expire_date','class'=>'form-control','required'=>true,'old-expiry-status'=>$old_expire_status]); ?>

                                <?php if($errors->has('expire_date')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('expire_date')); ?></label>
                                <?php endif; ?>
                                <div class="impo-text" id="expire_date_text_div">
                                    <?php echo app('translator')->get("$string_file.expire_date_text"); ?>;
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="expire_date_value_div" style="display: none">
                            <div class="form-group">
                                <label for="expire_date">
                                    <?php echo app('translator')->get("$string_file.default_expire_date"); ?> :
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('expire_date_value',old('expire_date_value'),['id'=>'expire_date_value','class'=>'form-control customDatePicker1','placeholder'=>trans('admin.expire_date'),'autocomplete'=>'off']); ?>

                                <?php if($errors->has('expire_date_value')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('expire_date_value')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label><?php echo app('translator')->get("$string_file.document_number_required"); ?> ? : <span
                                        class="text-danger">*</span></label>
                            <div class="form-group">
                                <?php $document_no_option = add_blank_option(get_status(true,$string_file),trans("$string_file.select")); ?>
                                <?php echo Form::select('document_number_required',$document_no_option,old('document_number_required',isset($document['data']->document_number_required) ? $document['data']->document_number_required : ''),['id'=>'document_number_required','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('document_number_required')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('document_number_required')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions float-right">
                        <?php if(!$is_demo): ?>
                        <?php echo Form::submit($document['submit_button'],['class'=>'btn btn-primary','id'=>'']); ?>

                        <?php else: ?>
                            <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php echo Form::close(); ?>

                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/document/form.blade.php ENDPATH**/ ?>