<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content container-fluid">
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
                        <button type="button" title="<?php echo app('translator')->get("$string_file.add_product"); ?>"
                                class="btn btn-icon btn-success float-right" style="margin:10px" data-toggle="modal"
                                data-target="#exampleModal">
                            <i class="wb-plus"></i>
                        </button>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-product-hunt" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.delivery_product"); ?>
                    </h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.product_name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.weight_unit"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $delivery_products->firstItem() ?>
                        <?php $__currentLoopData = $delivery_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($delivery_product->ProductName); ?></td>
                                <td><?php echo e($delivery_product->WeightUnit->WeightUnitName); ?></td>
                                <td><?php echo convertTimeToUSERzone($delivery_product->created_at, null,null,$delivery_product->Merchant, 2); ?></td>
                                <td>
                                    <?php if($delivery_product->status == 1): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Deactivate</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($delivery_product->status == 1): ?>
                                        <a class="btn btn-sm btn-danger"
                                           href="<?php echo e(route('delivery_product.change_status',[$delivery_product->id,2])); ?>"><i
                                                    class="fa fa-eye-slash"></i></a>
                                    <?php else: ?>
                                        <a class="btn btn-sm btn-success"
                                           href="<?php echo e(route('delivery_product.change_status',[$delivery_product->id,1])); ?>"><i
                                                    class="fa fa-eye"></i></a>
                                    <?php endif; ?>
                                    <a href="<?php echo route('delivery_product.edit',$delivery_product->id); ?>"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="wb-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $delivery_products, 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel"><?php echo app('translator')->get("$string_file.add_product"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('delivery_product.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span><?php echo app('translator')->get("$string_file.product_name"); ?></span>
                                    <input type="text" name="product_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span><?php echo app('translator')->get("$string_file.weight_unit"); ?></span>
                                    <select name="weight_unit" class="form-control" required>
                                        <option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                        <?php $__currentLoopData = $weight_units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weight_unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($weight_unit->id); ?>"><?php echo e($weight_unit->WeightUnitName); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?php echo app('translator')->get("$string_file.reset"); ?></button>
                        <button type="submit" class="btn btn-success"><?php echo app('translator')->get("$string_file.save"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/delivery_product/index.blade.php ENDPATH**/ ?>