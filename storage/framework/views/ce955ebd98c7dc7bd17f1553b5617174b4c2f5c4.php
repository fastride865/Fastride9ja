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
                        <?php if(Auth::user('merchant')->can('create-account-types')): ?>
                            <a href="<?php echo e(route('account-types.create')); ?>">
                                <button type="button"
                                        title="<?php echo app('translator')->get("$string_file.add_account_type"); ?>"
                                        class="btn btn-icon btn-success float-right" style="margin-top:10px">
                                    <i class="wb-plus"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.account_type_management"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.account_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $account_types->firstItem() ?>
                        <?php $__currentLoopData = $account_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php if(empty($account_type->LangAccountTypeSingle)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary"><?php if(!empty($account_type->LangAccountTypeAny->LanguageName->name)): ?>
                                                ( In <?php echo e($account_type->LangAccountTypeAny->LanguageName->name); ?>

                                                : <?php echo e($account_type->LangAccountTypeAny->name); ?>

                                                )<?php endif; ?> </span>
                                    <?php else: ?>
                                        <?php echo e($account_type->LangAccountTypeSingle->name); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($account_type->status  == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(Auth::user('merchant')->can('edit-account-types')): ?>
                                        <a href="<?php echo e(route('account-types.edit',$account_type->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>

                                        <?php if($account_type->status == 1): ?>
                                            <a href="<?php echo e(route('account-types.changestatus',['id'=>$account_type->id,'status'=>0])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('account-types.changestatus',['id'=>$account_type->id,'status'=>1])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i> </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php echo csrf_field(); ?>

                                    <?php if(Auth::user('merchant')->can('delete-account-types')): ?>
                                        <button onclick="DeleteEvent(<?php echo e($account_type->id); ?>)" type="submit"
                                                data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-dangeu-icon btn_delete action_btn"><i
                                                    class="fa fa-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $account_types, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
                        type: "DELETE",
                        url: 'account-types/' + id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "<?php echo e(route('account-types.index')); ?>";
                    });
                } else {
                    swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/account_types/index.blade.php ENDPATH**/ ?>