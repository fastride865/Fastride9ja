<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php if($merchant_file_exist == false): ?>
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i> <?php echo e(trans("$string_file.string_file_not_found")); ?>

                </div>
            <?php endif; ?>
            <div class="alert dark alert-icon alert-warning" role="alert">
                <i class="icon fa-warning" aria-hidden="true"></i> Note- Please don't change/remove <b>: & a word attached to it</b>. For example <b>:area,  :NUM, :OBJECT,:AMOUNT, :FROM, :count, :ID, :successfully, :delivery, :., :TAX</b> etc.
            </div>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                            <?php echo app('translator')->get("$string_file.translation_status"); ?>
                            <i class="fa fa-info red-900"></i>: <?php echo app('translator')->get("$string_file.pending"); ?> | <i class="fa fa-check green-500"></i>: <?php echo app('translator')->get("$string_file.done"); ?>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.string_translation"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="<?php echo e(route('merchant.module-string.submit')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <?php $i = 1; $translation_done = "";?>
                                <?php $__currentLoopData = $project_strings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $string): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $string_val = "";
                                    $title = $string;
                                    $translation_done = "red-900";
                                      $text_icon = '<i class="fa fa-info" title="'.trans("$string_file.translation_pending").'"></i>';
                                    ?>
                                   <?php if(isset($merchant_lang_file[$key])): ?>
                                     <?php  $string =  $merchant_lang_file[$key];
                                      $translation_done = "green-500";
                                      $text_icon = '<i class="fa fa-check" title="'.trans("$string_file.translation_done").'"></i>';
                                     ?>
                                   <?php endif; ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                              <?php echo e($i); ?>)  <?php echo e($title); ?><span class="<?php echo e($translation_done); ?>"> <?php echo $text_icon; ?> </span>
                                            </label>
                                            <?php echo e(Form::text("name[$key]",$string,["class"=>"form-control","place_holder"=>$string,'required'=>true])); ?>

                                            <?php if($errors->has('name')): ?>
                                                <label class="danger"><?php echo e($errors->first('name')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php $i++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="form-actions right" style="margin-bottom: 3%">


                                <?php if(Auth::user('merchant')->can('edit_language_strings')): ?>
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/language-file/module-strings.blade.php ENDPATH**/ ?>