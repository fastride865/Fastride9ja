<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php if(session('vehiclemakeadded')): ?>
                <div class="alert dark alert-icon alert-info alert-dismissible"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i><?php echo app('translator')->get("$string_file.vehicle_make_added"); ?>
                </div>
            <?php endif; ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('create_vehicle_make')): ?>
                            <a href="<?php echo e(route('excel.vehicle.make',$arr_vehicle_make['arr_search'])); ?>" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"
                                    title="<?php echo app('translator')->get("$string_file.vehicle_make"); ?>" data-toggle="modal"
                                    data-target="#examplePositionCenter">
                                <i class="fa fa-plus"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa-car" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.vehicle_make"); ?> </h3>
                </header>
                <div class="panel-body container-fluid">

                    <?php
                        $vehicle_make = isset($arr_vehicle_make['vehicle_type']) ? $arr_vehicle_make['vehicle_type'] : "";
                    ?>
                    <?php echo Form::open(['name'=>'','url'=>$arr_vehicle_make['search_route'],'method'=>'GET']); ?>

                    <div class="table_search row">
                        <div class="col-md-3 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="vehicle_make" value="<?php echo e($vehicle_make); ?>" placeholder="<?php echo app('translator')->get("$string_file.vehicle_make"); ?>" class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            <a href="<?php echo e($arr_vehicle_make['search_route']); ?>" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>


                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_make"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.logo"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.description"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <?php if(Auth::user('merchant')->can('edit_vehicle_make')): ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $vehiclemakes->firstItem() ?>
                        <?php $__currentLoopData = $vehiclemakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehiclemake): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php if(empty($vehiclemake->LanguageVehicleMakeSingle)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary">( In <?php echo e($vehiclemake->LanguageVehicleMakeAny->LanguageName->name); ?>

                                                                : <?php echo e($vehiclemake->LanguageVehicleMakeAny->vehicleMakeName); ?>

                                                                )</span>
                                    <?php else: ?>
                                        <?php echo e($vehiclemake->VehicleMakeName); ?>

                                    <?php endif; ?>
                                </td>

                                <td><img src="<?php echo get_image($vehiclemake->vehicleMakeLogo,'vehicle'); ?>"
                                         align="center" width="100px" class="img-radius"
                                         alt="User-Profile-Image"></td>
                                <td><?php if(empty($vehiclemake->LanguageVehicleMakeSingle)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary long_text">( In <?php echo e($vehiclemake->LanguageVehicleMakeAny->LanguageName->name); ?>

                                                                : <?php echo e($vehiclemake->LanguageVehicleMakeAny->vehicleMakeDescription); ?>

                                                                )</span>
                                    <?php else: ?>
                                        <span class="map_address long_text"><?php echo e($vehiclemake->LanguageVehicleMakeSingle->vehicleMakeDescription); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($vehiclemake->vehicleMakeStatus == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if(Auth::user('merchant')->can('edit_vehicle_make')): ?>
                                <td>
                                    <a href="<?php echo e(route('vehiclemake.edit',$vehiclemake->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                    <button onclick="DeleteEvent(<?php echo e($vehiclemake->id); ?>)"
                                            type="submit"
                                            data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $vehiclemakes, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="examplePositionCenter" aria-hidden="true" aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> <?php echo app('translator')->get("$string_file.vehicle_make"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('vehiclemake.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.vehicle_make"); ?>  <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="vehicle_make" name="vehicle_make"
                                   placeholder="" required>
                        </div>

                        <label> <?php echo app('translator')->get("$string_file.description"); ?>
                            <span class="text-danger">*</span> </label>
                        <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder=""></textarea>
                        </div>

                        <label>  <?php echo app('translator')->get("$string_file.logo"); ?>
                            <span class="text-danger">*</span> </label><span style="color: blue">(<?php echo app('translator')->get("$string_file.logo_size"); ?>)</span><i
                                class="fa fa-info-circle fa-1"
                                data-toggle="tooltip"
                                data-placement="top"
                                title=""></i>
                        <div class="form-group">
                            <input type="file" class="form-control" id="vehicle_make_logo" name="vehicle_make_logo"
                                   placeholder="" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-outline-primary btn" value="<?php echo app('translator')->get("$string_file.add"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<script type="">
    function DeleteEvent(id) {
        var token = $('[name="_token"]').val();
        swal({
            title: "<?php echo app('translator')->get("$string_file.are_you_sure"); ?>",
            text: "<?php echo app('translator')->get("$string_file.delete_warning"); ?>",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: "POST",
                    data: {
                        id: id,
                    },
                    url: "<?php echo e(route('merchant.vehiclemake.delete')); ?>",
                }).done(function (data) {
                    swal({
                        title: "<?php echo app('translator')->get("$string_file.deleted"); ?>",
                        text: data,
                        type: "success",
                    });
                    window.location.href = "<?php echo e(route('vehiclemake.index')); ?>";
                });
            } else {
                swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
            }
        });
    }
</script>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/vehiclemake/index.blade.php ENDPATH**/ ?>