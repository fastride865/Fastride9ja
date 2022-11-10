<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="<?php echo e(route('country.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.edit_country"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('country.update', $country->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.name"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name"
                                           name="name"
                                           placeholder=""
                                           value="<?php if(!empty($country->LanguageCountrySingle)): ?> <?php echo e($country->LanguageCountrySingle->name); ?> <?php endif; ?>"
                                           required>
                                    <?php if($errors->has('name')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('name')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ProfileImage">
                                        <?php echo app('translator')->get("$string_file.isd_code"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                                                    <span class="input-group-text"
                                                                          id="basic-addon1">+</span>
                                        </div>
                                        <input type="number" class="form-control" readonly
                                               id="phonecode"
                                               name="phonecode"
                                               value="<?php echo e(str_replace("+","",$country->phonecode)); ?>"
                                               placeholder="<?php echo app('translator')->get("$string_file.isd_code"); ?>"
                                               required>
                                    </div>
                                    <?php if($errors->has('phonecode')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('phonecode')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        <?php echo app('translator')->get("$string_file.iso_code_detail"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="isocode"
                                           name="isoCode"

                                           value="<?php echo e($country->isoCode); ?>"
                                           placeholder=""
                                           required>
                                    <?php if($errors->has('isoCode')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('isoCode')); ?></label>
                                    <?php endif; ?>
                                    <label class="text-danger">Eg:ISO code of $ is USD</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        <?php echo app('translator')->get("$string_file.country_code"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="country_code" readonly
                                           name="country_code"
                                           value="<?php echo e(old('country_code',$country->country_code)); ?>"
                                           placeholder="<?php echo app('translator')->get('admin.country_code'); ?>" required>
                                    <?php if($errors->has('country_code')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('country_code')); ?></label>
                                    <?php endif; ?>
                                    <label class="text-danger">Eg:Country code of India is IN</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.distance_unit"); ?>
                                    </label>
                                    <select class="c-select form-control"
                                            id="distance_unit"
                                            name="distance_unit" required>
                                        <option value="1"
                                                <?php if($country->distance_unit == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.km"); ?></option>
                                        <option value="2"
                                                <?php if($country->distance_unit == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.miles"); ?></option>
                                    </select>
                                    <?php if($errors->has('distance_unit')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('distance_unit')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ProfileImage">
                                        <?php echo app('translator')->get("$string_file.min_digits"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="min_digits"
                                           name="minNumPhone"
                                           placeholder=""
                                           value="<?php echo e($country->minNumPhone); ?>" required
                                           min="1" max="25">
                                    <?php if($errors->has('minNumPhone')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('minNumPhone')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_digits">
                                        <?php echo app('translator')->get("$string_file.max_digits"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control"
                                           id="max_digits"
                                           name="maxNumPhone"
                                           placeholder=""
                                           value="<?php echo e($country->maxNumPhone); ?>" required
                                           min="1" max="25">
                                    <?php if($errors->has('maxNumPhone')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('maxNumPhone')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="online_transaction">
                                        <?php echo app('translator')->get("$string_file.online_transaction_code"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="online_transaction"
                                           name="online_transaction" required
                                           value="<?php echo e($country->transaction_code); ?>"
                                           placeholder="">
                                    <?php if($errors->has('online_transaction')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('online_transaction')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sequance">
                                        <?php echo app('translator')->get("$string_file.sequence"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="sequance"
                                           name="sequance" required value="<?php echo e($country->sequance); ?>"
                                           placeholder="">
                                    <?php if($errors->has('sequance')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('sequance')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if($applicationConfig->user_document == 1): ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <h4><?php echo app('translator')->get("$string_file.document_configuration"); ?></h4>
                                    <hr>
                                    <div class="form-group">
                                        <label for="Documents">
                                            <?php echo app('translator')->get("$string_file.document_for_user"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="select2 form-control" name="document[]"
                                                id="document"
                                                data-placeholder="<?php echo app('translator')->get("$string_file.select_document"); ?>"
                                                multiple="multiple">
                                            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option
                                                        <?php if(in_array($document->id, array_pluck($country->documents,'id'))): ?> selected <?php endif; ?>
                                                value="<?php echo e($document->id); ?>"
                                                >
                                                    <?php echo e($document->DocumentName); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('document')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('document')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.update"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(
            function () {
                $("input[name=additional_details]").each(function () {
                    // console.log($(this).attr('value'));
                    if($(this).is(':checked')) {
                        if($(this).attr('value') == 1)
                        {
                            $('#parameter_name').attr('required',true);
                            $('#parameter_name').attr('disabled',false);
                            $('#parameter_name').parent().parent().removeClass('hide');
                            $('#placeholder').attr('required',true);
                            $('#placeholder').attr('disabled',false);
                            $('#placeholder').parent().parent().removeClass('hide');

                        }
                        //console.log("IN IF: "+$(this).attr('id')+' '+$(this).attr('value'));
                        // $(this).removeAttr('required');
                    }
                });
            });

        function extraparameters(data)
        {
            //console.log(data);
            if(data == 1)
            {
                $('#parameter_name').attr('required',true);
                $('#parameter_name').attr('disabled',false);
                $('#parameter_name').parent().parent().removeClass('hide');
                $('#placeholder').attr('required',true);
                $('#placeholder').attr('disabled',false);
                $('#placeholder').parent().parent().removeClass('hide');

            }else{
                $('#parameter_name').attr('required',false);
                $('#parameter_name').attr('disabled',true);
                $('#parameter_name').parent().parent().addClass('hide');
                $('#placeholder').attr('required',false);
                $('#placeholder').attr('disabled',true);
                $('#placeholder').parent().parent().addClass('hide');
            }
        }
        $(document).ready(function () {
            $('form#countryForm').submit(function () {
                $(this).find(':input[type=submit]').prop('disabled', true);
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/country/edit.blade.php ENDPATH**/ ?>