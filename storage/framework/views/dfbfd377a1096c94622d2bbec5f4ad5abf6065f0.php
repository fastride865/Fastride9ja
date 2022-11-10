<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('excel.customersupports')); ?>">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-toggle="tooltip">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.customer_support"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="<?php echo e(route('merchant.customer_support.search')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="table_search row">
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <select class="form-control" name="application"
                                            id="application">
                                        <option value="">--<?php echo app('translator')->get("$string_file.application"); ?>--</option>
                                        <option value="2"><?php echo app('translator')->get("$string_file.driver"); ?></option>
                                        <option value="1"><?php echo app('translator')->get("$string_file.user"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="name"
                                           placeholder="<?php echo app('translator')->get("$string_file.name"); ?>"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="date"
                                           placeholder="<?php echo app('translator')->get("$string_file.date"); ?>"
                                           class="form-control col-md-12 col-xs-12 datepickersearch"
                                           id="datepickersearch">
                                </div>
                            </div>
                            <div class="col-sm-2  col-xs-12 form-group ">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                        <th><?php echo app('translator')->get("$string_file.application"); ?></th>
                        <th><?php echo app('translator')->get("$string_file.details"); ?></th>
                        
                        
                        
                        <th><?php echo app('translator')->get("$string_file.query"); ?></th>
                        <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                        </thead>
                        <tbody>
                        <?php $sr = $customer_supports->firstItem() ?>
                        <?php $__currentLoopData = $customer_supports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer_support): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo e($sr); ?>

                                </td>
                                <td>
                                    <?php if($customer_support->application == 1): ?>
                                        <?php echo app('translator')->get("$string_file.user"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.driver"); ?>
                                    <?php endif; ?>
                                </td>

                                <?php if(Auth::user()->demo == 1): ?>
                                    <td>
                                        <span class="long_text">
                                            <?php echo e("********".substr($customer_support->name, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($customer_support->email, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($customer_support->phone, -2)); ?>

                                        </span>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <span class="long_text">
                                            <?php echo e($customer_support->name); ?>

                                            <br>
                                            <?php echo e($customer_support->email); ?>

                                            <br>
                                            <?php echo e($customer_support->phone); ?>

                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <span class="long_text"><?php echo e($customer_support->query); ?></span>
                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($customer_support->created_at, null, null, $customer_support->Merchant); ?>

                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <div class="pagination1 float-right"><?php echo e($customer_supports->links()); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>







<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/random/customer_support.blade.php ENDPATH**/ ?>