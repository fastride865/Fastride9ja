<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
          <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <h4 class="panel-title">
                        <i class=" icon fa-gears" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.email_configuration"); ?>
                    </h4>
                </header>
                <div class="panel-body container-fluid">
                    <h5>
                        <i class="icon fa-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.sender_details"); ?>
                    </h5>
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('merchant.emailtemplate.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="host">
                                        <?php echo app('translator')->get("$string_file.host_name"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           name="host"
                                           placeholder=""
                                           value="<?php if($configuration): ?><?php echo e($configuration['host']); ?><?php endif; ?>"
                                           required>
                                    <?php if($errors->has('host')): ?>
                                        <label class="danger"><?php echo e($errors->first('host')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.email"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control"
                                           name="username"
                                           placeholder=""
                                           value="<?php if($configuration): ?><?php echo e($configuration['username']); ?><?php endif; ?>"
                                           required>
                                    <?php if($errors->has('username')): ?>
                                        <label class="danger"><?php echo e($errors->first('username')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">
                                        <?php echo app('translator')->get("$string_file.password"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control"
                                           name="password"
                                           placeholder=""
                                           value="<?php if($configuration): ?><?php echo e($configuration['password']); ?><?php endif; ?>"
                                           required>
                                    <?php if($errors->has('password')): ?>
                                        <label class="danger"><?php echo e($errors->first('password')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="encryption">
                                        <?php echo app('translator')->get("$string_file.encryption"); ?><span class="text-danger">*</span><span
                                                class="text-primary"> ( tls/ssl )</span>
                                    </label>
                                    <select class="form-control" required name="encryption">
                                        <option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                        <option value="ssl"
                                                <?php if(isset($configuration['encryption']) && $configuration['encryption'] == 'ssl'): ?>selected <?php endif; ?>>
                                            SSL
                                        </option>
                                        <option value="tls"
                                                <?php if(isset($configuration['encryption']) && $configuration['encryption'] == 'tls'): ?>selected <?php endif; ?>>
                                            TLS
                                        </option>
                                    </select>
                                    <?php if($errors->has('encryption')): ?>
                                        <label class="danger"><?php echo e($errors->first('encryption')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="port">
                                        <?php echo app('translator')->get("$string_file.port_number"); ?> <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" required name="port">
                                        <option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                        <option value="465"
                                                <?php if(isset($configuration['port']) && $configuration['port'] == '465'): ?>selected <?php endif; ?>>
                                            465(SSL)
                                        </option>
                                        <option value="587"
                                                <?php if(isset($configuration['port']) && $configuration['port'] == '587'): ?>selected <?php endif; ?>>
                                            587(TLS)
                                        </option>
                                    </select>
                                    <?php if($errors->has('port')): ?>
                                        <label class="danger"><?php echo e($errors->first('port')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <h5>
                            <i class="icon fa-envelope" aria-hidden="true"></i>
                            <?php echo app('translator')->get("$string_file.welcome_email_configuration"); ?>
                        </h5>
                        <div class="row">
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.image"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input style="height: 0%" type="file" class="form-control"
                                           name="image"
                                           placeholder="<?php echo app('translator')->get('admin.message149'); ?>"
                                           value=""
                                           <?php if(empty($template['event']['welcome']['image'])): ?> required <?php endif; ?>>
                                    <?php if($errors->has('number_of_driver_user_map')): ?>
                                        <label class="danger"><?php echo e($errors->first('number_of_driver_user_map')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <?php if(!empty($template['event']['welcome']['image'])): ?>
                                    <img class="rounded img-bordered img-bordered-primary" width="150" height="150"
                                         src="<?php echo e(get_image($template['event']['welcome']['image'],'email')); ?>"
                                         alt="...">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.heading"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           name="heading"
                                           placeholder="Heading"
                                           value="<?php if(!empty($welcome->Heading)): ?> <?php echo e($welcome->Heading); ?> <?php endif; ?>"
                                           required>
                                    <?php if($errors->has('location_update_timeband')): ?>
                                        <label class="danger"><?php echo e($errors->first('location_update_timeband')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.sub_heading"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           name="subheading"
                                           placeholder="Sub heading "
                                           value="<?php if(!empty($welcome->Subheading)): ?> <?php echo e($welcome->Subheading); ?> <?php endif; ?>"
                                           required
                                    >
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.message"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="textmessage"
                                           name="textmessage"
                                           value="<?php if(!empty($welcome->Message)): ?> <?php echo e($welcome->Message); ?> <?php endif; ?>"
                                           placeholder="Message"
                                           required
                                    >
                                </div>
                            </div>
                        </div>
                        <h5>
                            <i class="icon fa-file" aria-hidden="true"></i>
                            <?php echo app('translator')->get("$string_file.invoice_email_configuration"); ?>
                        </h5>
                        <div class="row">
                            
                            <?php $socialLinks = \Illuminate\Support\Facades\Config::get('custom.social_links'); ?>
                            <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $socialLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3"> <?php echo e($socialLink); ?> Link</label>
                                        <input type="text" name="socialLinks[<?php echo e($key); ?>]"
                                               value="<?php if(isset($template['event']['invoice']['social_links'])): ?><?php echo e($template['event']['invoice']['social_links']->$key); ?><?php endif; ?>"
                                               class="form-control" placeholder="Enter <?php echo e($socialLink); ?> Link">
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <?php if(Auth::user('merchant')->can('edit_email_configurations')): ?>
                            <?php if(!$is_demo): ?>
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-square-o"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                </button>
                            <?php else: ?>
                                <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                
                
                
                
                
                
                
                

                
                
                
                
                
                
                
                
                
                
                
                
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/random/emailtemplate.blade.php ENDPATH**/ ?>