<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a class="heading-elements-toggle"><i
                                    class="ft-ellipsis-h font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa-globe" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.website_driver_headings"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="<?php echo e(route('website-driver-home-headings.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <h5 class="form-section col-md-12" ><i class="wb-add-file"></i> <?php echo app('translator')->get('admin.website_driver_main'); ?>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="banner_image">
                                            <?php echo app('translator')->get("$string_file.banner_image"); ?> (1500x1000px):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="banner_image"
                                               name="banner_image"
                                               placeholder="">
                                        <?php if($errors->has('banner_image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('banner_image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_heading">
                                            <?php echo app('translator')->get("$string_file.driver_heading"); ?>:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_heading"
                                               name="driver_heading"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->DriverHeading); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('driver_heading')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('driver_heading')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_sub_heading">
                                            <?php echo app('translator')->get("$string_file.driver_sub_heading"); ?>:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_sub_heading"
                                               name="driver_sub_heading"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->subHeading); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('driver_sub_heading')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('driver_sub_heading')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_buttonText">
                                            <?php echo app('translator')->get("$string_file.driver_button_text"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" maxlength="200" class="form-control" id="driver_buttonText"
                                               name="driver_buttonText"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->driverButtonText); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('driver_buttonText')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('driver_buttonText')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo app('translator')->get("$string_file.login"); ?></label>
                                        <input type="file" class="form-control" name="driver_login_bg_image"/>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> <?php echo app('translator')->get("$string_file.features"); ?>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_title[<?php echo e($features[0]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.section_one_title"); ?>  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="features_title[<?php echo e($features[0]['id']); ?>]"
                                               name="features[<?php echo e($features[0]['id']); ?>][title]"
                                               placeholder="Book Button Title" value="<?php echo e($features[0]->FeatureTitle); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[<?php echo e($features[0]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.section_three_description"); ?>  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[<?php echo e($features[0]['id']); ?>]" name="features[<?php echo e($features[0]['id']); ?>][description]"
                                                  rows="3"
                                                  placeholder="" required><?php echo e($features[0]->FeatureDiscription); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_title[<?php echo e($features[1]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.section_two_title"); ?>  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="features_title[<?php echo e($features[1]['id']); ?>]"
                                               name="features[<?php echo e($features[1]['id']); ?>][title]"
                                               placeholder="" value="<?php echo e($features[1]->FeatureTitle); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[<?php echo e($features[1]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.section_two_description"); ?>  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[<?php echo e($features[0]['id']); ?>]"
                                                  name="features[<?php echo e($features[1]['id']); ?>][description]"
                                                  rows="3"
                                                  placeholder="" required><?php echo e($features[1]->FeatureDiscription); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_title[<?php echo e($features[2]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.section_three_title"); ?>  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="features_title[<?php echo e($features[2]['id']); ?>]"
                                               name="features[<?php echo e($features[2]['id']); ?>][title]"
                                               placeholder="Book Button Title" value="<?php echo e($features[2]->FeatureTitle); ?>" required>
                                        <?php if($errors->has('estimate_btn_title')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('estimate_btn_title')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[<?php echo e($features[2]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.section_three_description"); ?>  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[<?php echo e($features[1]['id']); ?>]"
                                                  name="features[<?php echo e($features[2]['id']); ?>][description]"
                                                  rows="3"
                                                  placeholder="" required><?php echo e($features[2]->FeatureDiscription); ?></textarea>
                                        <?php if($errors->has('estimate_description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('estimate_description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> <?php echo app('translator')->get("$string_file.how_app_works"); ?>
                            </h5>
                            <hr>
                            <?php $i =1; ?>
                            <?php for($i=0;$i<=4;$i++){?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" value=<?php echo e($i); ?> name="position[]" />
                                        <label for="app_image">
                                            <?php echo app('translator')->get("$string_file.app_image"); ?> (90 x 190) :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="app_image"
                                               name="data[<?php echo e($i); ?>][image]"
                                               placeholder="">
                                        <?php if($errors->has('app_image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('app_image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_title">
                                            <?php echo app('translator')->get("$string_file.title"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="app_title"
                                               name="data[<?php echo e($i); ?>][title]"
                                               placeholder="" value="<?php echo e(isset($app_detil[$i]->FeatureTitle) ? $app_detil[$i]->FeatureTitle : NULL); ?>" required>
                                        <?php if($errors->has('app_title')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('app_title')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_description">
                                            <?php echo app('translator')->get("$string_file.description"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" max="200" class="form-control" id="app_description"
                                               name="data[<?php echo e($i); ?>][description]"
                                               placeholder="" value="<?php echo e(isset($app_detil[$i]->FeatureDiscription) ? $app_detil[$i]->FeatureDiscription : NULL); ?>" required>
                                        <?php if($errors->has('app_description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('app_description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> <?php echo app('translator')->get("$string_file.driver_footer"); ?>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="driver_footer_image">
                                           <?php echo app('translator')->get("$string_file.footer_image"); ?> (200 x 200) :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="driver_footer_image"
                                               name="driver_footer_image"
                                               placeholder="">
                                        <?php if($errors->has('driver_footer_image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('driver_footer_image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_title">
                                            <?php echo app('translator')->get("$string_file.driver_footer_heading"); ?>:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="footer_heading"
                                               name="footer_heading"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->FooterHeading); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('footer_heading')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('footer_heading')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="footer_sub_heading">
                                            <?php echo app('translator')->get("$string_file.footer_sub_heading"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="footer_sub_heading"
                                               name="footer_sub_heading"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->FooterSubHeading); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('footer_sub_heading')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('footer_sub_heading')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions float-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-square-o"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/website/driver_headings.blade.php ENDPATH**/ ?>