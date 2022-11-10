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
                        <a href="<?php echo e(route('excel.basicsignupdriver')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.basic_signup_completed"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.profile_image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.update"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $drivers->firstItem() ?>
                            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td><?php echo e(!empty($driver->country_area_id) ? $driver->CountryArea->CountryAreaName : ""); ?></td>
                                    <td class="text-center">
                                        <img
                                                src="<?php echo e(get_image($driver->profile_image,'driver')); ?>"
                                                alt="avatar" style="width: 100px;height: 100px;">
                                    </td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <td>
                                                            <span class="long_text">
                                                                <?php echo e("********".substr($driver->last_name, -2)); ?><br>
                                                                <?php echo e("********".substr($driver->phoneNumber, -2)); ?> <br>
                                                                <?php echo e("********".substr($driver->email, -2)); ?>

                                                            </span>
                                        </td>
                                    <?php else: ?>
                                        <td><?php echo e($driver->first_name." ".$driver->last_name); ?><br>
                                            <?php echo e($driver->phoneNumber); ?><br>
                                            <?php echo e($driver->email); ?>

                                        </td>
                                    <?php endif; ?>
                                    <?php $created_at = $driver->created_at; $updated_at = $driver->updated_at; ?>
                                    <?php if(!empty($driver->CountryArea->timezone)): ?>
                                        <?php
                                            $created_at = convertTimeToUSERzone($created_at, $driver->CountryArea->timezone, null, $driver->Merchant);
                                            $updated_at = convertTimeToUSERzone($updated_at, $driver->CountryArea->timezone, null, $driver->Merchant);
                                        ?>
                                    <?php endif; ?>
                                    <td><?php echo $created_at; ?></td>
                                    <td><?php echo $updated_at; ?></td>
                                    <td>
                                        <?php if(Auth::user('merchant')->can('edit_drivers')): ?>
                                            <a href="<?php echo e(route('driver.add',$driver->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.complete_signup"); ?>"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                <i
                                                        class="fa fa-edit"></i> </a>
                                        <?php endif; ?>
                                        <?php if(Auth::user('merchant')->can('delete_drivers')): ?>
                                            <button onclick="DeleteEvent(<?php echo e($driver->id); ?>)"
                                                    type="submit"
                                                    data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    class="btn menu-icon btn-sm btn-danger action_btn"><i
                                                        class="fa fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
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
    <form>
        <?php echo csrf_field(); ?>
    </form>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            console.log(token);
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
                        url: "<?php echo e(route('driverDelete')); ?>",
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "<?php echo e(route('merchant.driver.basic')); ?>";
                    });
                } else {
                    swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                }
            });
        }
    </script>
    <br>
    <br>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/basic.blade.php ENDPATH**/ ?>