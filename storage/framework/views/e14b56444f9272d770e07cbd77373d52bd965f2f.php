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
                        <?php echo app('translator')->get("$string_file.website_headings"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="<?php echo e(route('website-user-home-headings.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <h5 class="form-section col-md-12" ><i class="wb-add-file"></i> <?php echo app('translator')->get("$string_file.general_configuration"); ?>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_logo">
                                            <?php echo app('translator')->get("$string_file.app_logo"); ?> (512x512):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="app_logo"
                                               name="app_logo"
                                               placeholder="">

                                        <?php if($errors->has('app_logo')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('app_logo')); ?></label>
                                        <?php endif; ?>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_logo">
                                            <?php echo app('translator')->get("$string_file.login_background_image"); ?> 
                                            (1500x1000):
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="user_login_bg_image"
                                               name="user_login_bg_image"
                                               placeholder="">
                                        <?php if($errors->has('user_login_bg_image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('user_login_bg_image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="banner_image">
                                            <?php echo app('translator')->get("$string_file.banner_image"); ?> (1500x1000):
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
                                        <label for="start_address_hint">
                                            <?php echo app('translator')->get("$string_file.pickup_location"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="start_address_hint"
                                               name="start_address_hint"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->StartAddress); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('start_address_hint')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('start_address_hint')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_address_hint">
                                            <?php echo app('translator')->get("$string_file.drop_off_location"); ?>:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="end_address_hint"
                                               name="end_address_hint"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->EndAddress); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('end_address_hint')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('end_address_hint')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="book_btn_title">
                                           <?php echo app('translator')->get("$string_file.book_button_title"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="book_btn_title"
                                               name="book_btn_title"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details->BookingButton); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('book_btn_title')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('book_btn_title')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="book_btn_title">
                                            <?php echo app('translator')->get("$string_file.estimate_button_title"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="estimate_btn_title"
                                               name="estimate_btn_title"
                                               placeholder="Book Button Title" value="<?php if(!empty($details)): ?> <?php echo e($details->EstimateButton); ?> <?php endif; ?>" required>
                                        <?php if($errors->has('estimate_btn_title')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('estimate_btn_title')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo app('translator')->get("$string_file.left_image"); ?></label>
                                        <span class="text-danger">*</span>
                                        <input type="file" class="form-control" name="estimate_image" />
                                        <?php if($errors->has('estimate_image')): ?>
                                            <label class="text-danger"> <?php echo e($errors->first('estimate_image')); ?> </label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estimate_description">
                                            <label><?php echo app('translator')->get("$string_file.estimate_description"); ?></label>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="estimate_description" name="estimate_description"
                                                  rows="3"
                                                  placeholder="" required><?php if(!empty($details)): ?> <?php echo e($details->EstimateDescription); ?> <?php endif; ?></textarea>
                                        <?php if($errors->has('estimate_description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('estimate_description')); ?></label>
                                        <?php endif; ?>
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
                                               placeholder="" value="<?php if(!empty($features[0])): ?> <?php echo e($features[0]->FeatureTitle); ?> <?php endif; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="features_description[<?php echo e($features[0]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.section_one_description"); ?>  :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" maxlength="200" id="features_description[<?php echo e($features[0]['id']); ?>]" name="features[<?php echo e($features[0]['id']); ?>][description]"
                                                  rows="3"
                                                  placeholder="" required><?php if(!empty($features[0])): ?> <?php echo e($features[0]->FeatureDiscription); ?> <?php endif; ?></textarea>
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
                                               placeholder="Book Button Title" value="<?php if(!empty($features[1])): ?> <?php echo e($features[1]->FeatureTitle); ?> <?php endif; ?>" required>
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
                                                  placeholder="" required><?php if(!empty($features[1])): ?> <?php echo e($features[1]->FeatureDiscription); ?> <?php endif; ?></textarea>
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
                                               placeholder="Book Button Title" value="<?php if(!empty($features[2])): ?> <?php echo e($features[2]->FeatureTitle); ?> <?php endif; ?>" required>
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
                                                  placeholder="" required><?php if(!empty($features[2])): ?> <?php echo e($features[2]->FeatureDiscription); ?> <?php endif; ?></textarea>
                                        <?php if($errors->has('estimate_description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('estimate_description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> <?php echo app('translator')->get("$string_file.feature_components"); ?>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo app('translator')->get("$string_file.main_image"); ?></label>
                                        <input type="file" class="form-control" name="featured_component_main_image" />
                                    </div>
                                </div>
                            </div>
                            <?php for($i=0;$i<=4;$i++){?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" value="<?php echo e($i); ?>" name="position[]" />
                                        <label for="featre_compnt_image">
                                            <?php echo app('translator')->get("$string_file.image"); ?> (200 x 200) :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="featre_compnt_image"
                                               name="data[<?php echo e($i); ?>][featre_compnt_image]"
                                               placeholder="">
                                        <?php if($errors->has('featre_compnt_image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('featre_compnt_image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_title">
                                            <?php echo app('translator')->get("$string_file.title"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="featre_compnt_title"
                                               name="data[<?php echo e($i); ?>][featre_compnt_title]"
                                               placeholder="" value="<?php echo isset($features_component) ? $features_component[$i]->FeatureTitle : ""; ?>" required>
                                        <?php if($errors->has('featre_compnt_title')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('featre_compnt_title')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="app_description">
                                            <?php echo app('translator')->get("$string_file.description"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" maxlength="200" class="form-control" id="featre_compnt_description"
                                               name="data[<?php echo e($i); ?>][featre_compnt_description]"
                                               placeholder="" value="<?php echo isset($features_component) ? $features_component[$i]->FeatureDiscription :""; ?>" required>
                                        <?php if($errors->has('featre_compnt_description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('featre_compnt_description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="android_link[<?php echo e($features[0]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.android_app_link"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="android_link"
                                               name="android_link"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details['android_link']); ?> <?php endif; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ios_link[<?php echo e($features[0]['id']); ?>]">
                                            <?php echo app('translator')->get("$string_file.ios_app_link"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="ios_link"
                                               name="ios_link"
                                               placeholder="" value="<?php if(!empty($details)): ?> <?php echo e($details['ios_link']); ?> <?php endif; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="form-section col-md-12"><i class="wb-add-file"></i> <?php echo app('translator')->get("$string_file.driver_footer"); ?>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group px-2">
                                        <label><?php echo app('translator')->get("$string_file.background_color"); ?>:</label>
                                        <input type="color" class="form-control" name="footer_bg_color" value="<?php echo e((!empty($details) && $details['footer_bgcolor']) ? $details['footer_bgcolor'] : '#ffffff'); ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group px-2">
                                        <label><?php echo app('translator')->get("$string_file.text_color"); ?>:</label>
                                        <input type="color" class="form-control" name="footer_text_color" value="<?php echo e((!empty($details) && $details['footer_text_color']) ? $details['footer_text_color'] : '#000000'); ?>"/>
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
<?php $__env->startSection('js'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/website/user_headings.blade.php ENDPATH**/ ?>