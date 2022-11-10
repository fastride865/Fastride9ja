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
                        <?php if(Auth::user('merchant')->can('create_vehicle_type')): ?>
                            <button type="button" class="btn btn-icon btn-success float-right"
                                    title="<?php echo app('translator')->get("$string_file.add_vehicle_type"); ?>"
                                    data-toggle="modal"
                                    data-target="#inlineForm" style="margin:10px">
                                <i class="wb-plus"></i>
                            </button>
                            <a href="<?php echo e(route('excel.vehicle-types',$arr_vehicle_type['arr_search'])); ?>">
                                <button type="button" data-toggle="tooltip"
                                        data-original-title="<?php echo app('translator')->get("$string_file.export"); ?>"
                                        class="btn btn-icon btn-primary float-right" style="margin:10px"><i
                                            class="wb-download"
                                            title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if(Auth::user('merchant')->can('view_vehicle_type')): ?>
                        <h3 class="panel-title"><i class="icon fa-taxi" aria-hidden="true"></i>
                            <?php echo app('translator')->get("$string_file.vehicle_type"); ?>
                        </h3>
                    <?php endif; ?>
                </header>
                <div class="panel-body container-fluid">
                    <?php
                        $vehicle_type = isset($arr_vehicle_type['vehicle_type']) ? $arr_vehicle_type['vehicle_type'] : "";
                    ?>
                    <?php echo Form::open(['name'=>'','url'=>$arr_vehicle_type['search_route'],'method'=>'GET']); ?>

                    <div class="table_search row">
                        <div class="col-md-3 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="vehicle_type" value="<?php echo e($vehicle_type); ?>" placeholder="<?php echo app('translator')->get("$string_file.vehicle_type"); ?>" class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            <a href="<?php echo e($arr_vehicle_type['search_route']); ?>" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>


                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.rank"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.map_icon"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.description"); ?></th>
                            <?php if(in_array(5,$merchant->Service)): ?>
                                <th><?php echo app('translator')->get("$string_file.pool_availability"); ?></th> <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.sequence"); ?></th>
                            <?php if($vehicle_model_expire == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.model_expire_year"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <?php if(Auth::user('merchant')->can('edit_vehicle_type')): ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tfoot></tfoot>
                        <tbody>
                        <?php $sr = $vehicles->firstItem() ?>
                        <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($vehicle->VehicleTypeName); ?>

                                </td>
                                <td><?php echo e($vehicle->vehicleTypeRank); ?></td>
                                <td><img src="<?php echo e(get_image($vehicle->vehicleTypeImage, 'vehicle')); ?>"
                                         align="center" width="100px" height="60px"
                                         class="img-radius"
                                         alt="User-Profile-Image"></td>
                                <td>
                                    <img src="<?php echo e(view_config_image($vehicle->vehicleTypeMapImage)); ?>"
                                         align="center" width="50px" height="50px"
                                         class="img-radius"
                                         alt="User-Profile-Image"></td>

                                <td> <span class="map_address"><?php echo e($vehicle->VehicleTypeDescription); ?></span>
                                </td>
                                <?php if(in_array(5,$merchant->Service)): ?>
                                    <td>
                                        <?php if($vehicle->pool_enable == 1): ?>
                                            <label class="label_success"><?php echo app('translator')->get("$string_file.yes"); ?></label>
                                        <?php else: ?>
                                            <label class="label_danger"><?php echo app('translator')->get("$string_file.no"); ?></label>
                                        <?php endif; ?>
                                    </td> <?php endif; ?>
                                <td>
                                    <?php echo e($vehicle->sequence); ?>


                                </td>
                                <td>
                                    <?php if($vehicle_model_expire == 1): ?>
                                    <?php echo e($vehicle->model_expire_year); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($vehicle->vehicleTypeStatus == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(Auth::user('merchant')->can('edit_vehicle_type')): ?>
                                        <a href="<?php echo e(route('vehicletype.edit',$vehicle->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i></a>
                                        <button onclick="DeleteEvent(<?php echo e($vehicle->id); ?>)"
                                                type="submit"
                                                data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <?php endif; ?>

                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $vehicles, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> <?php echo app('translator')->get("$string_file.add_vehicle"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('vehicletype.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                             <label><?php echo app('translator')->get("$string_file.vehicle_type"); ?> <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="vehicle_name" name="vehicle_name"
                                           placeholder="" required>
                                </div>
                            </div>
                        <div class="col-md-4">
                            <label><?php echo app('translator')->get("$string_file.vehicle_rank"); ?><span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input type="number" class="form-control" id="vehicle_rank" name="vehicle_rank" min="1"
                                       placeholder="" required>
                            </div>
                        </div>
                            <div class="col-md-4">
                                <label><?php echo app('translator')->get("$string_file.sequence"); ?> <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <input type="number" class="form-control" id="sequence" name="sequence" min="1"
                                           placeholder="" required>
                                </div>
                            </div>
                            <?php if($vehicle_model_expire == 1): ?>
                            <div class="col-md-4">
                                <label><?php echo app('translator')->get("$string_file.model_expire_year"); ?> <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <input type="number" class="form-control" id="model_expire_year" name="model_expire_year" min="1" max="50" placeholder="" required>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <label>  <?php echo app('translator')->get("$string_file.image"); ?><span class="text-danger">*</span> </label><span style="color: blue">(<?php echo app('translator')->get("$string_file.size"); ?> 60*60 px)</span>
                                <i class="fa fa-info-circle fa-1" data-toggle="tooltip" data-placement="top" title=""></i>
                                <div class="form-group">
                                    <input style="height: 0%" type="file" class="form-control" id="vehicle_image" name="vehicle_image" placeholder="" required>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label> <?php echo app('translator')->get("$string_file.description"); ?>
                                    <span class="text-danger">*</span></label>
                                <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder=""></textarea>
                                </div>
                            </div>
                            <div class="col-md-4 mt-md-15">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" value="1" name="ride_now"
                                           id="ride_now"/>
                                    <label class="font-weight-400"><?php echo app('translator')->get("$string_file.ride_now"); ?></label>
                                    <br>
                                    <input type="checkbox" value="1" name="ride_later"
                                           id="ride_later"/>
                                    <label class="font-weight-400"><?php echo app('translator')->get("$string_file.ride_later"); ?></label>
                                    <br>
                                    <?php if(in_array(5,$merchant->Service)): ?>
                                        <input type="checkbox" value="1" name="pool_enable"
                                               id="pool_enable"/>
                                        <label class="font-weight-400"><?php echo app('translator')->get("$string_file.pool_enable"); ?></label>
                                        <br>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <br>
                        <label> <?php echo app('translator')->get("$string_file.map_image"); ?>
                            <span class="text-danger">*</span> </label><span style="color: blue">(<?php echo app('translator')->get("$string_file.size"); ?> 100*100px)</span><i
                                class="fa fa-info-circle fa-1" data-toggle="tooltip" data-placement="top" title=""></i>
                        <div class="form-group"> <div class="row">
                                <?php $__currentLoopData = get_config_image('map_icon'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-4">
                                        <input type="radio" name="vehicle_map_image" value="<?php echo e($path); ?>"
                                               id="male-radio-<?php echo e($path); ?>"><label for="male-radio-<?php echo e($path); ?>">
                                            <img src="<?php echo e(view_config_image($path)); ?>" class="w-p10 h-p10" >
                                            <?php echo e(explode_image_path($path)); ?>

                                        </label>
                                    </div>
                                    <br>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.reset"); ?>">
                        <input type="submit" class="btn btn-outline-primary btn" value="<?php echo app('translator')->get("$string_file.submit"); ?>">
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
    url: "<?php echo e(route('merchant.vehicletype.delete')); ?>",
    }).done(function (data) {
    swal({
    title: "<?php echo app('translator')->get("$string_file.deleted"); ?>",
    text: data,
    type: "success",
    });
    window.location.href = "<?php echo e(route('vehicletype.index')); ?>";
    });
    } else {
    swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
    }
    });
    }
</script>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/vehicletype/index.blade.php ENDPATH**/ ?>