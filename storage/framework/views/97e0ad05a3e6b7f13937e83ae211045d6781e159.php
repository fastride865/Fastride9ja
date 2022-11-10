<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('driver.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_driver"); ?>"></i>
                            </button>
                        </a>
                        <a href="<?php echo e(route('excel.rejecteddriver')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.rejected_drivers"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th> <?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.reject_reason"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.updated_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $drivers->firstItem() ?>
                            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td><a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                           class="address_link"><?php echo e($driver->merchant_driver_id); ?></a></td>
                                    <td><?php echo e($driver->CountryArea->CountryAreaName); ?></td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <td>
                                            <?php echo e("********".substr($driver->last_name, -2)); ?><br>
                                            <?php echo e("********".substr($driver->phoneNumber, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($driver->email, -2)); ?>


                                        </td>
                                    <?php else: ?>
                                        <td><?php echo e($driver->first_name." ".$driver->last_name); ?><br>
                                            <?php echo e($driver->email); ?><br>
                                            <?php echo e($driver->phoneNumber); ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <?php $__currentLoopData = $driver->DriverVehicle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($vehicle->vehicle_number); ?>,
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td><?php echo e($driver->admin_msg); ?></td>
                                    <td>
                                        <?php echo convertTimeToUSERzone($driver->created_at, $driver->CountryArea->timezone, null, $driver->Merchant); ?>

                                    </td>
                                    <td>
                                        <?php echo convertTimeToUSERzone($driver->updated_at, $driver->CountryArea->timezone, null, $driver->Merchant); ?>

                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                           class="btn btn-sm btn-info menu-icon btn_detail action_btn">
                                            <span class="fa fa-list-alt" title="View Driver Profile"></span>
                                        </a>
                                        <button type="button" onclick="EditDoc(this)" data-ID="<?php echo e($driver->id); ?>" class="btn btn-sm btn-success"
                                                data-toggle="tooltip" data-placement="bottom" title="Move To Pending">
                                            <span class="fa fa-eyedropper"></span>
                                        </button>
                                        <button onclick="DeleteEvent(<?php echo e($driver->id); ?>)"
                                                type="submit"
                                                data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php $sr++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="moveToPending" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><?php echo app('translator')->get('admin_x.auth_required'); ?></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="<?php echo e(route('merchant.driver.move-to-pending')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <h1 class="text-danger text-center" style="font-size:60px"><i class="fa fa-exclamation-circle"></i></h1>
                        <h5 class="text-danger text-center"><?php echo app('translator')->get('admin_x.confirmation_to_move'); ?></h5><br>
                        <input type="hidden" id="docId" name="driver_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo app('translator')->get("$string_file.close"); ?></button>
                        <button type="submit" class="btn btn-success"><?php echo app('translator')->get('admin_x.submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $('.toast').toast('show');
        function EditDoc(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #docId").val(ID);
            $('#moveToPending').modal('show');
        }
    </script>
    <script>
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
                            request_from:"rejected"
                        },
                        url: "<?php echo e(route('driverDelete')); ?>",
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "<?php echo e(route('merchant.driver.rejected')); ?>";
                    });
                } else {
                    swal("<?php echo app('translator')->get('admin_x.message893'); ?>");
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/rejected.blade.php ENDPATH**/ ?>