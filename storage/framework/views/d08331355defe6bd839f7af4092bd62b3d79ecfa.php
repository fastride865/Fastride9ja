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
                        <?php if(Auth::user('merchant')->can('add_banner')): ?>
                            <a href="<?php echo e(route('advertisement.create')); ?>">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus"
                                       title="<?php echo app('translator')->get("$string_file.add_banner"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.banner_management"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sequence"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.url"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.validity"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.activate_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.expire_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.banner_for"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.is_display_on_home_screen"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $banners->firstItem() ?>
                        <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($banner->name); ?></td>
                                <td><?php echo e($banner->sequence); ?></td>
                                <td><?php echo e($banner->redirect_url); ?></td>
                                <td>
                                    <?php if($banner->validity == 1): ?>
                                        <?php echo app('translator')->get("$string_file.unlimited"); ?>
                                    <?php elseif($banner->validity == 2): ?>
                                        <?php echo app('translator')->get("$string_file.limited"); ?>
                                    <?php else: ?>
                                        ----
                                    <?php endif; ?>
                                </td>
                                <?php $activate_date = convertTimeToUSERzone($banner->activate_date, null, null, $banner->Merchant,2); ?>
                                <td><?php echo $activate_date; ?></td>
                                <td>
                                    <?php if($banner->validity == 2): ?>
                                        <?php $expire_date = convertTimeToUSERzone($banner->expire_date, null, null, $banner->Merchant,2); ?>
                                        <?php echo $expire_date; ?>

                                    <?php else: ?>
                                        ----
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($banner->banner_for == 1): ?>
                                        <?php echo app('translator')->get("$string_file.user"); ?>
                                    <?php elseif($banner->banner_for == 2): ?>
                                        <?php echo app('translator')->get("$string_file.driver"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.both"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($status[$banner->home_screen]); ?>

                                </td>
                                <td>
                                    <?php if($banner->segment_id): ?>
                                        <?php echo e($arr_segment[$banner->segment_id]); ?>

                                    <?php endif; ?>
                                </td>
                                <?php $created_at = convertTimeToUSERzone($banner->created_at, null, null, $banner->Merchant,2); ?>
                                <td><?php echo $created_at; ?></td>
                                
                                
                                
                                
                                <td>
                                    <?php if($banner->status  == 1): ?>
                                        <label class="label_success"><?php echo app('translator')->get("$string_file.active"); ?></label>
                                    <?php else: ?>
                                        <label class="label_danger"><?php echo app('translator')->get("$string_file.inactive"); ?></label>
                                    <?php endif; ?>
                                </td>
                                <td><a href="<?php echo e(get_image($banner->image,'banners',$banner->merchant_id)); ?>"
                                       target="_blank"><img
                                                src="<?php echo e(get_image($banner->image,'banners',$banner->merchant_id)); ?>"
                                                height="60" width="60" class="img-responsive"/></a></td>
                                <td>
                                    <?php if(Auth::user('merchant')->can('add_banner')): ?>
                                        <?php if(empty($banner->expire_date) || ($banner->expire_date >= date('Y-m-d'))): ?>
                                            <a href="<?php echo e(route('advertisement.create',$banner->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if($banner->status == 1): ?>
                                        <a href="<?php echo e(route('advertisement.active.deactive',['id'=>$banner->id,'status'=>2])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                    class="fa fa-eye-slash"></i> </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('advertisement.active.deactive',['id'=>$banner->id,'status'=>1])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                    class="fa fa-eye"></i> </a>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('delete_banner')): ?>
                                        <button onclick="DeleteEvent(<?php echo e($banner->id); ?>)"
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
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $banners, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "<?php echo app('translator')->get("$string_file.are_you_sure"); ?>",
                text: "<?php echo app('translator')->get("$string_file.delete_banner"); ?>",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "GET",
                        data: {
                            id: id,
                        },
                        url: "<?php echo e(route('advertisement.delete')); ?>",
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "<?php echo e(route('advertisement.index')); ?>";
                    });
                } else {
                    swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/advertisement_banner/index.blade.php ENDPATH**/ ?>