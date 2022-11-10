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
                        <?php if(Auth::user('merchant')->can('create_promotion')): ?>
                            <a href="<?php echo e(route('promotions.create')); ?>">
                                <button type="button"
                                        title="<?php echo app('translator')->get("$string_file.notification"); ?>"
                                        class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                            class="wb-plus"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('excel.promotionnotifications',$search_param)); ?>">
                            <button type="button" class="btn btn-icon btn-primary float-right"
                                    style="margin:10px"
                                    data-original-title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"
                                    data-toggle="tooltip">
                                <i class="icon fa-download"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-bell" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.promotional_notification"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="get" action="<?php echo e(route('promotions.search')); ?>">
                        <div class="table_search">
                            <div class="row">

                                <div class="col-md-4  form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="title" value="<?php echo e(isset($search_param['title']) ? $search_param['title'] : ""); ?>"
                                               placeholder="<?php echo app('translator')->get("$string_file.title"); ?>"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <?php echo Form::select('application',[''=>trans("$string_file.application"),1=>trans("$string_file.driver"),2=>trans("$string_file.user")],isset($search_param['application']) ? $search_param['application'] : NULL,['class'=>'form-control','id'=>'application']); ?>







                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="date" value="<?php echo e(isset($search_param['date']) ? $search_param['date'] : ""); ?>"
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
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.title"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.message"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.url"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.application"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.receiver"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.show_in_promotion"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.expire_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <?php if(Auth::user('merchant')->can('edit_promotion') || Auth::user('merchant')->can('delete_promotion')): ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $promotions->firstItem() ?>
                        <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php if($promotion->country_area_id): ?>
                                        <?php echo e($promotion->CountryArea->CountryAreaName); ?>

                                    <?php else: ?>
                                        ------
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($promotion->title); ?>

                                </td>
                                <td>
                                    <span class="map_address"><?php echo e($promotion->message); ?></span>
                                </td>
                                <td>
                                    <img src="<?php echo e(get_image($promotion->image, 'promotions')); ?>"
                                         align="center" width="100px" height="80px"
                                         class="img-radius"
                                         alt="Promotion Notification Image">
                                </td>
                                <td>
                                    <a title="<?php echo e($promotion->url); ?>"
                                       href="<?php echo e($promotion->url); ?>" class="btn btn-icon btn-success ml-20"><i class="icon wb-link"></i></a>
                                </td>
                                <?php switch($promotion->application):
                                    case (1): ?>
                                    <td><?php echo app('translator')->get("$string_file.driver"); ?></td>
                                    <?php break; ?>
                                    <?php case (2): ?>
                                    <td><?php echo app('translator')->get("$string_file.user"); ?></td>
                                    <?php break; ?>
                                <?php endswitch; ?>
                                <?php if(Auth::user()->demo == 1): ?>
                                    <td>
                                        <?php switch($promotion->application):
                                            case (1): ?>
                                            <?php if($promotion->driver_id == 0): ?>
                                                <?php echo app('translator')->get("$string_file.all_drivers"); ?>
                                            <?php else: ?>
                                                <?php echo e("********".substr($promotion->Driver->last_name, -2)); ?>

                                                <br>
                                                <?php echo e("********".substr($promotion->Driver->phoneNumber, -2)); ?>

                                                <br>
                                                <?php echo e("********".substr($promotion->Driver->email, -2)); ?>

                                            <?php endif; ?>
                                            <?php break; ?>
                                            <?php case (2): ?>
                                            <?php if($promotion->user_id == 0): ?>
                                                <?php echo app('translator')->get("$string_file.all_users"); ?>
                                            <?php else: ?>
                                                <?php echo e("********".substr($promotion->User->UserName, -2)); ?>

                                                <br>
                                                <?php echo e("********".substr($promotion->User->UserPhone, -2)); ?>

                                                <br>
                                                <?php echo e("********".substr($promotion->User->email, -2)); ?>

                                            <?php endif; ?>
                                            <?php break; ?>
                                        <?php endswitch; ?>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php switch($promotion->application):
                                            case (1): ?>
                                            <?php if($promotion->driver_id == 0): ?>
                                                <?php echo app('translator')->get("$string_file.all_drivers"); ?>
                                            <?php else: ?>
                                                <?php if($promotion->Driver): ?>
                                                    <?php echo e($promotion->Driver->first_name." ".$promotion->Driver->last_name); ?>

                                                    <br>
                                                    <?php echo e($promotion->Driver->phoneNumber); ?>

                                                    <br>
                                                    <?php echo e($promotion->Driver->email); ?>

                                                <?php else: ?>
                                                    ---
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php break; ?>
                                            <?php case (2): ?>
                                            <?php if($promotion->user_id == 0): ?>
                                                <?php echo app('translator')->get("$string_file.all_users"); ?>
                                            <?php else: ?>
                                                <?php if($promotion->User): ?>
                                                    <?php echo e($promotion->User->UserName); ?>

                                                    <br>
                                                    <?php echo e($promotion->User->UserPhone); ?>

                                                    <br>
                                                    <?php echo e($promotion->User->email); ?>

                                                <?php else: ?>
                                                    -----
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php break; ?>
                                        <?php endswitch; ?>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php if($promotion->show_promotion == 1): ?>
                                        <?php echo app('translator')->get("$string_file.yes"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.no"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($promotion->show_promotion == 1): ?>
                                        <?php if(isset($promotion->CountryArea->timezone)): ?>
                                            <?php echo convertTimeToUSERzone($promotion->expiry_date, $promotion->CountryArea->timezone, null, $promotion->Merchant, 2); ?>

                                        <?php else: ?>
                                            <?php echo convertTimeToUSERzone($promotion->expiry_date, null, null, $promotion->Merchant, 2); ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                        -----
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(isset($promotion->CountryArea->timezone)): ?>
                                        <?php echo convertTimeToUSERzone($promotion->created_at, $promotion->CountryArea->timezone, null, $promotion->Merchant); ?>

                                    <?php else: ?>
                                        <?php echo convertTimeToUSERzone($promotion->created_at, null, null, $promotion->Merchant); ?>

                                    <?php endif; ?>
                                </td>
                                <?php if(Auth::user('merchant')->can('edit_promotion') || Auth::user('merchant')->can('delete_promotion')): ?>
                                    <td>
                                        <?php if(Auth::user('merchant')->can('edit_promotion')): ?>
                                            <a href="<?php echo e(route('promotions.edit',$promotion->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i> </a>
                                        <?php endif; ?>
                                        <?php if(Auth::user('merchant')->can('delete_promotion')): ?>
                                            <a href="<?php echo e(route('promotions.delete',$promotion->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                                <i class="fa fa-trash"></i> </a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $promotions, 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/promotion/index.blade.php ENDPATH**/ ?>