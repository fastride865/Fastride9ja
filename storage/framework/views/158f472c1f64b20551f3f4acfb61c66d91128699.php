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
                        <?php if(Auth::user('merchant')->can('create_pricing_parameter')): ?>
                            <a href="<?php echo e(route('priceparameter.add')); ?>">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus"
                                       title="<?php echo app('translator')->get("$string_file.add_pricing_parameter"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.pricing_parameter_management"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.application_name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sequence"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.applicable_for"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $parameters->firstItem();
                             $arr_price_type = get_price_parameter($string_file, "edit");
                        ?>
                        <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parameter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php if(!empty($parameter->Segment)): ?>
                                        <?php echo implode(',',array_pluck($parameter->Segment,'slag')); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    
                                        
                                        
                                                                
                                                                
                                    
                                        
                                    

                                    <?php echo e($parameter->ParameterName); ?>

                                </td>
                                <td>
                                    <?php echo e($parameter->ParameterApplication); ?>

                                    
                                        
                                        
                                    
                                        
                                    
                                </td>
                                <td>
                                    <?php echo isset($arr_price_type[$parameter->parameterType]) ? $arr_price_type[$parameter->parameterType] : ''; ?>

                                </td>
                                <td><?php echo e($parameter->sequence_number); ?></td>
                                <td>
                                    <?php $__currentLoopData = $parameter->PricingType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php switch($value->price_type):
                                            case (1): ?>
                                            <?php echo app('translator')->get("$string_file.variable"); ?>,
                                            <?php break; ?>
                                            <?php case (2): ?>
                                            <?php echo app('translator')->get("$string_file.fixed_price"); ?>,
                                            <?php break; ?>
                                            <?php case (3): ?>
                                            <?php echo app('translator')->get("$string_file.input_by_driver"); ?>,
                                            <?php break; ?>
                                        <?php endswitch; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>
                                    <?php if($parameter->parameterStatus == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(Auth::user('merchant')->can('edit_pricing_parameter')): ?>
                                        <a href="<?php echo e(route('priceparameter.add',$parameter->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i>
                                        </a>
                                    <?php endif; ?>

                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $parameters, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/pricingparameter/index.blade.php ENDPATH**/ ?>