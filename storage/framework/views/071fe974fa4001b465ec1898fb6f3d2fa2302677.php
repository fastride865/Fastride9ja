<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
        <div class="panel panel-bordered">
            <!--<header class="panel-heading">-->
            <!--    <div class="row">-->
            <!--        <div class="col-md-3">-->
            <!--        <h3 class="panel-title">-->
            <!--            <?php echo app('translator')->get("$string_file.import_bulk_products"); ?>-->
            <!--        </h3>-->
            <!--        </div>-->
            <!--        <div class="col-md-9">-->
            <!--            <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>-->
            <!--                <button class="btn btn-icon btn-primary float-right" style="margin:10px"-->
            <!--                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">-->
            <!--                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>-->
            <!--                </button>-->
            <!--            <?php endif; ?>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <div class="row">-->
            <!--        <div class="col-md-4">-->
            <!--        <a href="<?php echo e(route('business-segment.category.export',$arr_category_search)); ?>">-->
            <!--            <button type="button" title="<?php echo app('translator')->get("$string_file.export_category"); ?>"-->
            <!--                    class="btn btn-icon btn-success" style="margin:10px"> 1. <?php echo app('translator')->get("$string_file.export_category"); ?>-->
            <!--                <?php echo app('translator')->get("$string_file.categories"); ?> <i class="wb-upload"></i>-->
            <!--            </button>-->
            <!--        </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <a href="<?php echo e(asset('basic-images/product_import_sheet.xlsx')); ?>">-->
            <!--            <button type="button" title="<?php echo app('translator')->get("$string_file.download_product_excel_sample"); ?>"-->
            <!--                    class="btn btn-icon btn-info" style="margin:10px">-->
            <!--                2. <?php echo app('translator')->get("$string_file.download_product_excel_sample"); ?> <i class="wb-upload"></i>-->
            <!--            </button>-->
            <!--        </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <button type="button" class="btn btn-icon btn-success"-->
            <!--                title="<?php echo app('translator')->get("$string_file.import_bulk_products"); ?>"-->
            <!--                data-toggle="modal"-->
            <!--                data-target="#importProduct" style="margin:10px"> 3. <?php echo app('translator')->get("$string_file.import_bulk_products"); ?> <i class="wb-download"></i>-->
            <!--        </button>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--            <a href="<?php echo e(route('business-segment.weight.export')); ?>">-->
            <!--                <button type="button" title="<?php echo app('translator')->get("$string_file.export_weight_unit"); ?>"-->
            <!--                        class="btn btn-icon btn-info" style="margin:10px">-->
            <!--                    4. <?php echo app('translator')->get("$string_file.export_weight_unit"); ?> <i class="wb-upload"></i>-->
            <!--                </button>-->
            <!--            </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <a href="<?php echo e(route('business-segment-product-export-variant')); ?>">-->
            <!--            <button type="button" title="<?php echo app('translator')->get("$string_file.export_product_variant"); ?>"-->
            <!--                    class="btn btn-icon btn-info" style="margin:10px">-->
            <!--                5. <?php echo app('translator')->get("$string_file.export_product_variant"); ?> <i class="wb-upload"></i>-->
            <!--            </button>-->
            <!--        </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <button type="button" class="btn btn-icon btn-success"-->
            <!--                title="<?php echo app('translator')->get("$string_file.import_product_variants"); ?>"-->
            <!--                data-toggle="modal"-->
            <!--                data-target="#importProductVariant" style="margin:10px">-->
            <!--            6. <?php echo app('translator')->get("$string_file.import_product_variants"); ?> <i class="wb-download"></i>-->
            <!--        </button>-->
            <!--        </div>-->




            <!--        <a href="<?php echo e(route('business-segment-product-import')); ?>">-->
            <!--        <button type="button" title="<?php echo app('translator')->get("$string_file.download_product_excel_sample"); ?>"-->
            <!--        class="btn btn-icon btn-info float-right" style="margin:10px"><i-->
            <!--        class="wb-upload"></i>-->
            <!--        </button>-->
            <!--        </a>-->
            <!--    </div>-->

            <!--</header>-->
        </div>
        </div>
            <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('business-segment.product.basic.add')); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.add_product"); ?>"
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                            </button>
                        </a>

                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i><?php echo app('translator')->get("$string_file.products"); ?>
                    </h3>
                </header>
                <div class="panel-body">
                    <?php echo $search_view; ?>

                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable2"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sku_no"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.product_name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.description"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ingredients"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.category"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sequence"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.cover_image"); ?></th>
                            <?php if($segment->Segment->slag == 'FOOD'): ?>
                                <th><?php echo app('translator')->get("$string_file.preparation_time"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.variant_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.inventory_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1; ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <tr>
                                <?php $lang_data = $product->langData($product->merchant_id); ?>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($product->sku_id); ?></td>
                                <td><?php echo e($lang_data->name); ?></td>
                                <td><?php echo e($lang_data->description); ?></td>
                                <td><?php echo e($lang_data->ingredients); ?></td>
                                <td><?php echo e($product->Category->Name($product->merchant_id)); ?></td>
                                <td><?php echo e($product->sequence); ?></td>
                                <td>
                                    <img src="<?php echo e(get_image($product->product_cover_image,'product_cover_image',$product->merchant_id)); ?>"
                                         width="80px" height="80px">
                                </td>
                                <?php if($segment->Segment->slag == 'FOOD'): ?>
                                    <td><?php echo e($product->product_preparation_time); ?></td>
                                <?php endif; ?>
                                <td><?php if($product->status == 1): ?>
                                        <span class="badge badge-success"><?php echo e(isset($product_status[$product->status]) ? $product_status[$product->status] : ""); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo e(isset($product_status[$product->status]) ? $product_status[$product->status] : ""); ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php $product_variant_count = $product->ProductVariant->count(); ?>
                                <td><?php if($product_variant_count > 0): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.added"); ?> (<?php echo e($product_variant_count); ?> - <?php echo app('translator')->get("$string_file.variants"); ?>)</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.not_added"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php
                                        $inventory_available_status = 0;
                                        if($product->manage_inventory == 1){
                                            $product_variant_inventory_count = 0;
                                            foreach($product->ProductVariant as $product_variant){
                                                if(isset($product_variant->ProductInventory) && $product_variant->ProductInventory->count() > 0){
                                                    $product_variant_inventory_count++;
                                                }
                                            }
                                            if($product_variant_count > 0){
                                                if($product_variant_count == $product_variant_inventory_count){
                                                    $inventory_available_status = 1;
                                                }elseif($product_variant_inventory_count > 0){
                                                    $inventory_available_status = 2;
                                                }
                                            }
                                        }
                                    ?>
                                    <?php if($product->manage_inventory == 1): ?>
                                        <span class="badge badge-success"><?php echo e(isset($inventory_status[$product->manage_inventory]) ? $inventory_status[$product->manage_inventory] : ""); ?></span>
                                        <?php switch($inventory_available_status):
                                            case (0): ?>
                                            <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.not_added"); ?></span>
                                            <?php break; ?>;
                                            <?php case (1): ?>
                                            <span class="badge badge-success"><?php echo app('translator')->get("$string_file.added"); ?></span>
                                            <?php break; ?>;
                                            <?php case (2): ?>
                                            <span class="badge badge-info"><?php echo app('translator')->get("$string_file.add"); ?> : <?php echo e($product_variant_inventory_count); ?> - <?php echo app('translator')->get("$string_file.variant"); ?> | <?php echo app('translator')->get("$string_file.inventory"); ?></span>
                                            <?php break; ?>;
                                        <?php endswitch; ?>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo e(isset($inventory_status[$product->manage_inventory]) ? $inventory_status[$product->manage_inventory] : ""); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo route('business-segment.product.basic.add',$product->id); ?>"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn" title="<?php echo app('translator')->get("$string_file.edit"); ?>"><i
                                                class="wb-edit"></i></a>
                                    <?php echo csrf_field(); ?>
                                    <button onclick="DeleteEvent(<?php echo e($product->id); ?>)"
                                            type="button"
                                            data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i></button>
                                    <a href="<?php echo route('business-segment.product.variant.index',$product->id); ?>"
                                       title="Manage Variant"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"><i
                                                class="wb-eye"></i></a>
                                    <?php if(isset($product->manage_inventory) && $product->manage_inventory == 1): ?>
                                        <a href="<?php echo route('business-segment.product.inventory.index',['id' => $product->id]); ?>"
                                           title="Manage Product Inventory"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn"><i
                                                    class="wb-book"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $data, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="importProduct" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> <?php echo app('translator')->get("$string_file.import_bulk_products"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('business-segment-product-import')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label><?php echo app('translator')->get("$string_file.product_excel"); ?> <span class="text-danger">*</span> </label>
                                <i class="fa fa-info-circle fa-1" data-toggle="tooltip" data-placement="top" title=""></i>
                                <div class="form-group">
                                    <input style="height: 0%" type="file" class="form-control" id="product_import_sheet"
                                           name="product_import_sheet" placeholder="" required>
                                </div>
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

    <div class="modal fade text-left" id="importProductVariant" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> <?php echo app('translator')->get("$string_file.import_product_variants"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('business-segment-product-variant-import')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label><?php echo app('translator')->get("$string_file.product_excel"); ?> <span class="text-danger">*</span> </label>
                                <i class="fa fa-info-circle fa-1" data-toggle="tooltip" data-placement="top" title=""></i>
                                <div class="form-group">
                                    <input style="height: 0%" type="file" class="form-control" id="product_variant_import_sheet"
                                           name="product_variant_import_sheet" placeholder="" required>
                                </div>
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
<?php $__env->startSection('js'); ?>
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
                        type: "POST",
                        data: {
                            id: id,
                        },
                        url: "<?php echo e(route('business-segment.product.destroy')); ?>",
                    })
                        .done(function (data) {
                            swal({
                                title: "DELETED!",
                                text: data,
                                type: "success",
                            });
                            window.location.href = "<?php echo e(route('business-segment.product.index')); ?>";
                        });
                } else {
                    swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                }
            });
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/product/index.blade.php ENDPATH**/ ?>