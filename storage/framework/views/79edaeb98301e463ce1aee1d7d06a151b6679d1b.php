<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
















            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('business-segment.product.index')); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.product"); ?>"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i><?php echo app('translator')->get("$string_file.product_inventory"); ?></h3>
                </header>
                <div class="panel-body">
                    <div class="example-wrap">
                        <form class="form-inline"
                              type="post" action="<?php echo e(route('business-segment.product.inventory.index')); ?>"
                        >
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label class="sr-only" for="inputUnlabelUsername"><?php echo app('translator')->get("$string_file.product"); ?></label>
                                <?php echo Form::select('id',add_blank_option($product_list,trans("$string_file.select_product")),old('id'),['id'=>'id','class'=>'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="inputUnlabelPassword"><?php echo app('translator')->get("$string_file.product_variant"); ?> </label>
                                <?php echo Form::select('product_variant_cid',add_blank_option($product_variant_list,trans("$string_file.select_variant")),old('id'),['id'=>'product_variant_id','class'=>'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-outline"><?php echo app('translator')->get("$string_file.search"); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="exampleFooEditing" class="table table-bordered table-hover toggle-circle"
                               data-paging="true" data-filtering="false" data-sorting="true" data-dropdown-toggle="false"
                               data-editing-allow-delete="false" data-editing-allow-add="false">
                            <thead>
                            <tr>
                                <th data-name="id" data-type="number"
                                    data-breakpoints="xs"><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                                <th data-name="product"><?php echo app('translator')->get("$string_file.product"); ?></th>
                                <th data-name="product_variant"><?php echo app('translator')->get("$string_file.product_variant"); ?> </th>
                                <th data-name="current_stock" data-type="string"><?php echo app('translator')->get("$string_file.current_stock"); ?> </th>
                                <th data-name="product_cost" data-type="number"><?php echo app('translator')->get("$string_file.product_cost"); ?></th>
                                <th data-name="product_selling_price"
                                    data-type="number"><?php echo app('translator')->get("$string_file.selling_price"); ?></th>
                                <th data-name="action" data-visible="false"
                                    data-filterable="false"><?php echo app('translator')->get("$string_file.action"); ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = 1 ?>
                            <?php if(!empty($product_variants)): ?>
                                <?php $__currentLoopData = $product_variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product_variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($product_variant->id); ?></td>
                                        <td><?php echo e($product_variant->Product->Name($product_variant->Product->merchant_id).' ('.$product_variant->Product->sku_id.')'); ?></td>
                                        <td><?php echo e($product_variant->product_title.' ('.$product_variant->sku_id.')'); ?></td>
                                        <td><?php echo e(isset($product_variant->ProductInventory->current_stock) ? $product_variant->ProductInventory->current_stock : trans("$string_file.no").' '.trans($string_file.".stock")); ?></td>
                                        <td><?php echo e(isset($product_variant->ProductInventory->product_cost) ? $product_variant->ProductInventory->product_cost : 0); ?></td>
                                        <td><?php echo e(isset($product_variant->ProductInventory->product_selling_price) ? $product_variant->ProductInventory->product_selling_price : $product_variant->product_price); ?></td>
                                        <td>
                                            <div class="badge badge-table badge-success">Paid</div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <!-- End Panel Editing Rows -->
                        <div class="modal fade" id="editor-modal" tabindex="-1" role="dialog"
                             aria-labelledby="editor-title">
                            <div class="modal-dialog modal-simple" role="document">
                                <form class="modal-content form-horizontal" id="editor" type="post">
                                    <?php echo csrf_field(); ?>
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title"
                                            id="editor-title">
                                            <?php echo app('translator')->get("$string_file.edit_product_inventory"); ?>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <input type="number" id="id" name="id" class="hidden" style="display:none;"/>
                                        <dl class="dl-horizontal row">
                                            <dt class="col-sm-3"><?php echo app('translator')->get("$string_file.product"); ?> :</dt>
                                            <dd class="col-sm-3" id="product"></dd>
                                            <dt class="col-sm-3"><?php echo app('translator')->get("$string_file.product_variant"); ?>  :</dt>
                                            <dd class="col-sm-3" id="product_variant"></dd>
                                        </dl>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price"
                                                           class=" control-label"><?php echo app('translator')->get("$string_file.current_stock"); ?> </label>
                                                    <input type="number" class="form-control" id="current_stock"
                                                           name="current_stock"
                                                           placeholder="<?php echo app('translator')->get("$string_file.current_stock"); ?> "
                                                           required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price"
                                                           class=" control-label"> <?php echo app('translator')->get("$string_file.new_stock"); ?></label>
                                                    <input type="number" class="form-control" id="new_stock"
                                                           name="new_stock"
                                                           placeholder="" value=""
                                                           required>
                                                    <input type="hidden" id="last_new_stock" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price"
                                                           class=" control-label"> <?php echo app('translator')->get("$string_file.updated_current_stock"); ?></label>
                                                    <input type="number" class="form-control" id="updated_current_stock"
                                                           name="updated_current_stock"
                                                           placeholder=""
                                                           required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount"
                                                           class=" control-label"><?php echo app('translator')->get("$string_file.product_cost"); ?></label>
                                                        <input type="number" class="form-control" id="product_cost" step="any"
                                                               name="product_cost"
                                                               placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount"
                                                           class=" control-label"><?php echo app('translator')->get("$string_file.selling_price"); ?></label>
                                                        <input type="number" class="form-control" id="product_selling_price"
                                                               name="product_selling_price" step="any"
                                                               placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                     <?php if(!$is_demo): ?>
                                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("$string_file.save"); ?> </button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo app('translator')->get("$string_file.cancel"); ?> </button>
                                        <?php else: ?>
                                            <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/footable/footable.core.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/tables/footable.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('global/vendor/footable/footable.min.js')); ?>"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function () {
            var $modal = $('#editor-modal'),
                $editor = $('#editor'),
                $editorTitle = $('#editor-title'),
                ft = FooTable.init('#exampleFooEditing', {
                    editing: {
                        enabled: true,
                        editRow: function (row) {
                            var values = row.val();
                            $editor.find('#id').val(values.id);
                            $editor.find('#product').text(values.product);
                            $editor.find('#product_variant').text(values.product_variant);
                            if(values.current_stock > 0){
                                $editor.find('#current_stock').val(values.current_stock);
                                $editor.find('#updated_current_stock').val(values.current_stock);
                            }else{
                                $editor.find('#current_stock').val(0);
                                $editor.find('#updated_current_stock').val(0);
                            }
                            $editor.find('#product_cost').val(values.product_cost);
                            $editor.find('#product_selling_price').val(values.product_selling_price);

                            $modal.data('row', row);
                            $editorTitle.text('Edit row #' + values.id);
                            $modal.modal('show');
                        },
                    }
                }),
                uid = 10;
            $editor.on('submit', function (e) {
                if (this.checkValidity && !this.checkValidity()) return;
                e.preventDefault();
                $.ajax({
                    url: "<?php echo e(route('business-segment.product.inventory.save')); ?>",
                    data: $editor.serialize(),
                    type: "POST",
                }).done(function (result) {
                    if (typeof (result.success) != "undefined" && result.success !== null) {
                        var row = $modal.data('row'),
                            values = {
                                id: $editor.find('#id').val(),
                                product: $editor.find('#product').val(),
                                product_variant: $editor.find('#product_variant').val(),
                                current_stock: $editor.find('#current_stock').val(),
                                product_cost: $editor.find('#product_cost').val(),
                                product_selling_price: $editor.find('#product_selling_price').val(),
                                new_stock: $editor.find('#new_stock').val(),
                            };
                        if (row instanceof FooTable.Row) {
                            row.val(values);
                        } else {
                            values.id = uid++;
                            ft.rows.add(values);
                        }
                        $modal.modal('hide');
                        alert('success : ' + result.success);
                        window.location.href = result.route;

                    } else {
                        alert('error : ' + result.error);
                    }
                });
            });
            $('#new_stock').change(function(){
                if($('#new_stock').val() > 0){
                    var updated_current_stock = (isNaN(parseInt($('#updated_current_stock').val()))) ? 0 : parseInt($('#updated_current_stock').val());
                    var new_stock = (isNaN(parseInt($('#new_stock').val()))) ? 0 : parseInt($('#new_stock').val());
                    var last_new_stock = (isNaN(parseInt($('#last_new_stock').val()))) ? 0 : parseInt($('#last_new_stock').val());
                    $('#last_new_stock').val(new_stock);
                    var total_stock = (updated_current_stock - last_new_stock) + new_stock;
                    $('#updated_current_stock').val(total_stock);
                }else{
                    var last_new_stock = (isNaN(parseInt($('#last_new_stock').val()))) ? 0 : parseInt($('#last_new_stock').val());
                    var updated_current_stock = (isNaN(parseInt($('#updated_current_stock').val()))) ? 0 : parseInt($('#updated_current_stock').val());
                    var last_stock = updated_current_stock - last_new_stock;
                    $('#updated_current_stock').val(last_stock);
                    $('#last_new_stock').val(0);
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/product-inventory/index.blade.php ENDPATH**/ ?>