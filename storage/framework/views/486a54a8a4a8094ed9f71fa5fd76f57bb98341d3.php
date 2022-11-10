<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php if(session('document-message')): ?>
                <div class="col-md-8 alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-info"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong> <?php echo e(session()->get('document-message')); ?> </strong>
                </div>
            <?php endif; ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('users.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                        <?php if($user->signup_status==1): ?>
                            <a href="<?php echo e(route('merchant.user.AlldocumentStatus' , ['id' => $user->id,'status'=>2])); ?>">
                                <button type="button" class="btn btn-icon btn-warning">Approve All Documents
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="wb-file" aria-hidden="true">
                        </i> <?php echo e($user->first_name." ".$user->last_name); ?>'s <?php echo app('translator')->get("$string_file.documents"); ?> </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <?php if(empty($user->UserDocuments)): ?>
                            <div class="container text-center">
                                <h4> <?php echo app('translator')->get('admin.noDocuments'); ?> </h4>
                            </div>
                        <?php else: ?>
                            <?php $__currentLoopData = $user->UserDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col col-12 col-sm-6 col-md-4">
                                    <div class="card" style="margin-top:20px;">
                                        <?php if($doc->pivot->document_verification_status == 1): ?>
                                            <div class="dropdown">
                                                <button type="button" style="right:0;" class="btn btn-secondary position-absolute btn-sm rounded-0" data-toggle="dropdown">
                                                    <?php echo app('translator')->get("$string_file.action"); ?> <span class="fa fa-chevron-down"></span>
                                                </button>
                                                <div class="dropdown-menu pb-0" style="min-width:265px;">
                                                    
                                                    <a class="dropdown-item text-danger" href="#" data-toggle="collapse" onclick="$('#reason-collapse-<?php echo e($doc->id); ?>').toggle();event.stopPropagation();"> <strong>Reject</strong> </a>

                                                    <div id="reason-collapse-<?php echo e($doc->id); ?>" class="collapse">
                                                        <form class="" action="<?php echo e(route('merchant.user.documentStatus')); ?>" method="get">
                                                            <div class="form-group text-center">
                                                                <input type="hidden" name="id" value="<?php echo e($doc->pivot->id); ?>">
                                                                <input type="hidden" name="status" value="3">

                                                                <select class="select2 form-control" name="reject_reason_id"
                                                                        id="timezone" required>
                                                                    <option value="" selected disabled><?php echo app('translator')->get('admin.selectRejectReason'); ?><option>
                                                                    <?php $__currentLoopData = $rejectReasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <option value="<?php echo e($reason->id); ?>"> <?php echo e($reason->LanguageSingle->title); ?></option>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </select>
                                                                <?php if($errors->has('reject_reason_id')): ?>
                                                                    <label class="danger d-block"><?php echo e($errors->first('reject_reason_id')); ?></label>
                                                                <?php endif; ?>

                                                                <button type="submit" class="btn btn-danger btn-sm text-white mt-2"><?php echo app('translator')->get("$string_file.rejected"); ?> </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif($doc->pivot->document_verification_status == 2): ?>
                                            <a class="btn btn-success position-absolute btn-sm rounded-0 text-white" style="right:0;"> <?php echo app('translator')->get("$string_file.approved"); ?></a>
                                        <?php elseif($doc->pivot->document_verification_status == 3): ?>
                                            <a class="btn btn-danger position-absolute btn-sm rounded-0 text-white" style="right:0;"> <?php echo app('translator')->get("$string_file.rejected"); ?></a>
                                        <?php endif; ?>
                                        <div class="card-body bg-light">
                                            <h4 class="card-title"> <?php echo e($doc->LanguageSingle->documentname); ?> (<?php echo e($doc->pivot->document_number); ?>)</h4>
                                            <?php if(!empty($doc->pivot->expire_date)): ?>
                                                <h6 class="text-muted card-subtitle mb-2"><?php echo app('translator')->get('admin.expiryDate'); ?> : <?php echo e($doc->pivot->expire_date); ?></h6>
                                            <?php endif; ?>
                                            <img src="<?php echo e(get_image($doc->pivot->document_file,'user_document')); ?>" style="width:300px;height:250px;">
                                            <p class="card-text"></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/user/document.blade.php ENDPATH**/ ?>