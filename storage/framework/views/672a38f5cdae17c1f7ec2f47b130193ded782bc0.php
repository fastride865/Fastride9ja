<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content container-fluid">
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
                        <?php if(Auth::user('merchant')->can('create_refer')): ?>
                            <a href="<?php echo e(route('referral-system.create')); ?>">
                                <button type="button" data-toggle="tooltip" class="btn btn-icon btn-success float-right"
                                        style="margin: 10px;">
                                    <i class="wb-plus"
                                       title="<?php echo app('translator')->get("$string_file.add_referral_system"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="fa-television" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.referral_system"); ?>
                    </h3>
                </header>

                <div class="panel-body container-fluid">
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.country"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.application"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.start_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.end_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.discount_applicable"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.offer_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.offer_value"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.offer_condition"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <?php if(Auth::user('merchant')->can('edit_refer') || Auth::user('merchant')->can('delete_refer')): ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $referral_systems->firstItem() ?>
                        <?php $__currentLoopData = $referral_systems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $refer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e(!empty($refer->country_id) ? $refer->Country->CountryName : ""); ?></td>
                                <td><?php echo e(!empty($refer->country_area_id) ? $refer->CountryArea->CountryAreaName : ""); ?></td>
                                <td>
                                    <?php switch($refer->application):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.user"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.driver"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($refer->start_date, null, null, $refer->Merchant, 2); ?>

                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($refer->end_date, null, null, $refer->Merchant, 2); ?>

                                </td>
                                <td>
                                    <?php switch($refer->offer_applicable):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.sender"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.receiver"); ?>
                                        <?php break; ?>
                                        <?php case (3): ?>
                                        <?php echo app('translator')->get("$string_file.both"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php switch($refer->offer_type):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.fixed_amount"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.discount"); ?>
                                        <?php break; ?>
                                        <?php case (3): ?>
                                        <?php echo app('translator')->get("$string_file.according_to_commission_fare"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php switch($refer->offer_type):
                                        case (1): ?>
                                        <?php echo e($refer->Country->isoCode." ".$refer->offer_value); ?>

                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo e($refer->offer_value); ?> %
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php $offer_condition = getReferralSystemOfferCondition($string_file) ?>
                                    <?php echo e($offer_condition[$refer->offer_condition]); ?>

                                </td>
                                <td>
                                    <?php if($refer->status == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php elseif($refer->status == 2): ?>
                                        <span class="badge badge-default"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php elseif($refer->status == 3): ?>
                                        <span class="badge badge-dark"><?php echo app('translator')->get("$string_file.expired"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.deleted"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if(Auth::user('merchant')->can('edit_refer') || Auth::user('merchant')->can('delete_refer')): ?>
                                    <td>
                                        <?php if(Auth::user('merchant')->can('edit_refer') && in_array($refer->status,[1,2])): ?>
                                            <?php if($refer->status == 1): ?>
                                                <a href="<?php echo e(route('referral-system.change-status',['id'=>$refer->id,'status'=>2])); ?>"
                                                   data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>"
                                                   data-toggle="tooltip" data-placement="top"
                                                   class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                            class="fa fa-eye-slash"></i> </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('referral-system.change-status',['id'=>$refer->id,'status'=>1])); ?>"
                                                   data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>"
                                                   data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                            class="fa fa-eye"></i> </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('referral-system.create',['id'=>$refer->id])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i> </a>
                                        <?php endif; ?>
                                        <?php if(Auth::user('merchant')->can('delete_refer') && $refer->status != 4): ?>
                                            <a href="#" class="btn btn-sm btn-danger menu-icon"
                                               data-original-title="Delete"
                                               data-toggle="tooltip"
                                               data-placement="top" data-Id="<?php echo e($refer->id); ?>" onclick="EditDoc(this)"> <i
                                                        class="fa fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $referral_systems, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditDOc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> <?php echo app('translator')->get("$string_file.referral_system"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('referral-system.delete')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body text-center">
                        <label><b class="text-danger"><?php echo app('translator')->get("$string_file.delete_warning"); ?></b></label>
                        <input type="hidden" id="referral_system_id" name="referral_system_id">
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-sm btn-danger" value="<?php echo app('translator')->get("$string_file.delete"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function EditDoc(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #referral_system_id").val(ID);
            $('#EditDOc').modal('show');
        }

        $(document).ready(function () {
            $('#dataTable2').DataTable({
                searching: false,
                paging: false,
                info: false,
                "bSort": false,
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/referral_system/index.blade.php ENDPATH**/ ?>