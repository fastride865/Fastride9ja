<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('create_countries')): ?>
                            <a href="<?php echo e(route('country.create')); ?>">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus"
                                       title="<?php echo app('translator')->get("$string_file.add_country"); ?>"></i>
                                </button>
                            </a>
                            <a href="<?php echo e(route('excel.countriesexport')); ?>" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download"
                                       title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.country_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="<?php echo e(route('country.index')); ?>" method="GET">
                        <div class="table_search row p-3">
                            <div class="col-md-2 active-margin-top"><?php echo app('translator')->get("$string_file.search_by"); ?>
                                :
                            </div>
                            <div class="col-md-4 col-xs-12 form-group active-margin-top">
                                <select class="form-control select2" name="country_id" id="country_id">
                                    <option value=""><?php echo app('translator')->get("$string_file.name"); ?></option>
                                    <?php $__currentLoopData = $search_countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->id); ?>"
                                                <?php if(!empty($search_data['country_id']) == $country->id): ?> selected <?php endif; ?>> <?php echo e($country->CountryName); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-6 form-group active-margin-top">
                                <?php echo Form::text('phonecode',isset($search_data['phonecode']) ? $search_data['phonecode'] : NULL,['class'=>'form-control','placeholder'=>trans("$string_file.isd_code")]); ?>

                            </div>
                            <div class="col-md-2 col-xs-6 form-group active-margin-top">
                                <?php echo Form::text('isoCode',isset($search_data['isoCode']) ? $search_data['isoCode'] : NULL,['class'=>'form-control','placeholder'=>trans("$string_file.iso_code")]); ?>

                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"
                                                                                 aria-hidden="true"></i></button>
                            </div>
                            <div class="col-md-1">
                                <a href="<?php echo e(route('country.index')); ?>">
                                    <button class="btn btn-small btn-success" type="button"><i
                                                class="fa fa-refresh"></i></button>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sequence"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.isd_code"); ?></th>
                            
                            <th><?php echo app('translator')->get("$string_file.iso_code"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.country_code"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.distance_unit"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.phone_length"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <?php if(Auth::user('merchant')->can('edit_countries')): ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $countries->firstItem() ?>
                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php if(empty($country->LanguageCountrySingle)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary">( In <?php echo e(isset($country->LanguageCountryAny->LanguageName->name) ? $country->LanguageCountryAny->LanguageName->name : ''); ?>

                                                            : <?php echo e($country->LanguageCountryAny->name); ?>

                                                            )</span>
                                    <?php else: ?>
                                        <?php echo e($country->LanguageCountrySingle->name); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($country->sequance); ?></td>
                                <td><?php echo e($country->phonecode); ?></td>
                                <td><?php echo e($country->isoCode); ?></td>
                                <td><?php echo e($country->country_code); ?></td>
                                <?php switch($country->distance_unit):
                                    case (1): ?>
                                    <td><?php echo app('translator')->get("$string_file.km"); ?></td>
                                    <?php break; ?>
                                    <?php case (2): ?>
                                    <td><?php echo app('translator')->get("$string_file.miles"); ?></td>
                                    <?php break; ?>
                                <?php endswitch; ?>
                                
                                <td><?php echo app('translator')->get("$string_file.min"); ?>: <?php echo e($country->minNumPhone); ?><br>
                                    <?php echo app('translator')->get("$string_file.max"); ?>: <?php echo e($country->maxNumPhone); ?></td>
                                <td>
                                    <?php if($country->country_status  == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if(Auth::user('merchant')->can('edit_countries')): ?>
                                    <td style="width:100px; float:left">
                                        <a href="<?php echo e(route('country.edit',$country->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="wb-edit"></i></a>
                                        <?php if($country->country_status == 1): ?>
                                            <a href="<?php echo e(route('merchant.country.active-deactive',['id'=>$country->id,'status'=>2])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn mr-1"> <i
                                                        class="fa fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('merchant.country.active-deactive',['id'=>$country->id,'status'=>1])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                                <i class="wb-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $countries, 'data' => $search_data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/country/index.blade.php ENDPATH**/ ?>