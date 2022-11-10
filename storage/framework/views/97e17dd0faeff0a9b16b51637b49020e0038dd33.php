<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('business-segment.product.index')); ?>">
                            <button type="button" title="Back"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i><?php echo e($data->Name($data->merchant_id)); ?>

                        - <?php echo app('translator')->get("$string_file.product_variant"); ?> </h3>
                </header>
                <div class="panel-body">
                    <table id="exampleFooEditing" class="table table-bordered table-hover toggle-circle"
                           data-paging="true" data-filtering="false" data-sorting="true">
                        <thead>
                        <tr>
                            <th data-name="sn" data-type="number" data-breakpoints="xs"><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th data-name="id" data-type="number" data-breakpoints="xs"> <?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th data-name="sku_id" data-breakpoints="xs"><?php echo app('translator')->get("$string_file.sku_no"); ?></th>
                            <th data-name="title"><?php echo app('translator')->get("$string_file.title"); ?></th>
                            <th data-name="price"><?php echo app('translator')->get("$string_file.price"); ?></th>
                            <th data-name="discount"><?php echo app('translator')->get("$string_file.discount"); ?></th>
                            <th data-name="weight_unit"><?php echo app('translator')->get("$string_file.weight_unit"); ?> </th>
                            <th data-name="weight_unit_value" data-visible="false"><?php echo app('translator')->get("$string_file.weight_unit"); ?> </th>
                            <th data-name="weight"><?php echo app('translator')->get("$string_file.weight"); ?></th>
                            <th data-name="is_title_show"><?php echo app('translator')->get("$string_file.is_title_show"); ?></th>
                            <th data-name="is_title_show_value" data-visible="false"><?php echo app('translator')->get("$string_file.is_title_show"); ?></th>
                            <th data-name="status"><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th data-name="status_value" data-visible="false"><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th data-name="variant_inventory"><?php echo app('translator')->get("$string_file.inventory_status"); ?></th>
                            <th data-name="action" data-visible="false"
                                data-filterable="false"><?php echo app('translator')->get("$string_file.action"); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1 ?>
                        <?php $__currentLoopData = $product_variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product_variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($product_variant->id); ?></td>
                                <td><?php echo e($product_variant->sku_id); ?></td>
                                <td><?php echo e($product_variant->is_title_show == 1 ? $product_variant->Name($merchant_id) : $product_variant->Product->Name($merchant_id)); ?></td>
                                <td><?php echo e(custom_number_format($product_variant->product_price,$trip_calculation_method)); ?></td>
                                <td><?php echo e(custom_number_format($product_variant->discount,$trip_calculation_method)); ?></td>
                                <td><?php if(!empty($product_variant->weight_unit_id)): ?><?php echo e($product_variant->WeightUnit->WeightUnitName); ?><?php endif; ?></td>
                                <td><?php echo e($product_variant->weight_unit_id); ?></td>
                                <td><?php echo e($product_variant->weight); ?></td>
                                <td><?php if($product_variant->is_title_show == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.yes"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.no"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($product_variant->is_title_show); ?></td>
                                <td><?php if($product_variant->status == 1): ?>
                                        <span class="badge badge-success"><?php echo e($product_status[$product_variant->status]); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo e($product_status[$product_variant->status]); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($product_variant->status); ?></td>
                                <td><?php if(isset($product_variant->ProductInventory) && $product_variant->ProductInventory->count() > 0): ?>
                                        <span class="badge badge-success"><?php echo e($product_status[1]); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo e($product_status[2]); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td></td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <?php if(isset($data->manage_inventory) && $data->manage_inventory == 1): ?>
                            <a href="<?php echo e(route('business-segment.product.inventory.index',['id' => $data->id])); ?>">
                                <button class="btn btn-primary" <?php if($product_variants->count() == 0): ?> disabled <?php endif; ?>>
                                    <i class="fa fa-check-circle"></i>
                                    <?php echo app('translator')->get("$string_file.continue_to_product_inventory"); ?>
                                </button>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('business-segment.product.index')); ?>">
                                <button class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i>
                                    <?php echo app('translator')->get("$string_file.finish"); ?>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($arr_option_type) && $arr_option_type->count() > 0): ?>
                    <hr>
                    <?php $sn = 1; ?>
                    <h4><?php echo app('translator')->get("$string_file.option_management"); ?> : </h4>
                        <?php echo Form::open(["name"=>"","url"=>route("business-segment.product.options.save")]); ?>

                        <input type="hidden" name="product_id" value="<?php echo e($data->id); ?>">
                        <?php $__currentLoopData = $arr_option_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $form_field_type = $option_type->charges_type == 2 ? "text" : "checkbox"; ?>
                            <div class="row">
                                <div class="col-md-12">
                                 <h5>
                                <?php echo e($sn.'. '.$option_type->Type($merchant_id)); ?>

                                 </h5>
                                    <div class="row">
                                        <?php $__currentLoopData = $option_type->Option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $checked = false; $amount = NULL; $disabled= true; ?>
                                            <?php $__currentLoopData = $option->Product; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product_pivot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(!empty($product_pivot->pivot->product_id) && ($product_pivot->pivot->product_id == $data->id)): ?>
                                                <?php $disabled =false; $checked = true; $amount = $product_pivot->pivot->option_amount; ?>
                                            <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="sku_id">
                                                        <?php echo e($option->Name($bs_id)); ?>

                                                        <span class="text-danger"></span>
                                                    </label>
                                                    <?php if($form_field_type == "text"): ?>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <?php echo Form::checkbox('arr_option['.$option->id.']',NULL,$checked,['id'=>$option->id,'class'=>'option_checkbox']); ?>

                                                            </span>
                                                            <?php echo Form::text('option_amount['.$option->id.']',old('option_amount',$amount),['id'=>'','class'=>'form-control option'.$option->id,'placeholder'=>trans("$string_file.amount"),'disabled'=>$disabled]); ?>

                                                        </div>
                                                    <?php else: ?>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <?php echo Form::checkbox('arr_option['.$option->id.']',NULL,$checked,['id'=>$option->id,'class'=>'option_checkbox']); ?>

                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <?php $sn++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!$is_demo): ?>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <?php if(isset($data->manage_inventory) && $data->manage_inventory == 1): ?>
                                <a href="<?php echo e(route('business-segment.product.inventory.index',['id' => $data->id])); ?>">
                                    <button class="btn btn-primary" <?php if($product_variants->count() == 0): ?> disabled <?php endif; ?>>
                                        <i class="fa fa-check-circle"></i>
                                        <?php echo app('translator')->get("$string_file.save"); ?> & <?php echo app('translator')->get("$string_file.continue_to_product_inventory"); ?>
                                    </button>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('business-segment.product.index')); ?>">
                                    <button class="btn btn-primary">
                                        <i class="fa fa-check-circle"></i>
                                        <?php echo app('translator')->get("$string_file.save"); ?>
                                    </button>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                        <?php endif; ?>
                        <?php echo Form::close(); ?>

                    <?php endif; ?>


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
                                    <h4 class="modal-title" id="editor-title"><?php echo app('translator')->get("$string_file.edit_product_variant"); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="number" id="id" name="id" class="hidden"
                                                   style="display:none;"/>
                                            <input type="hidden" name="product_id" value="<?php echo e($data->id); ?>">
                                            <div class="form-group required">
                                                <label for="price"
                                                       class="control-label"><?php echo app('translator')->get("$string_file.sku_no"); ?></label>
                                                <input type="text" class="form-control" id="sku_id" name="sku_id"
                                                       placeholder="<?php echo app('translator')->get("$string_file.sku_no"); ?>"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="hidden" name="product_variant_id" id="product_variant_id"
                                                   value="">
                                            <div class="form-group">
                                                <label for="dob"
                                                       class=" control-label"><?php echo app('translator')->get("$string_file.status"); ?></label>
                                                <?php echo Form::select('status',$product_status,old('status'),['id'=>'status','class'=>'form-control','autocomplete'=>'off','required']); ?>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group required">
                                                <label for="title"
                                                       class="control-label"><?php echo app('translator')->get("$string_file.product_name"); ?></label>
                                                <input type="text" class="form-control" id="title" name="title" readonly
                                                       placeholder="<?php echo app('translator')->get("$string_file.title"); ?>" value="<?php echo e($data->Name($data->merchant_id)); ?>"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="checkbox-custom checkbox-default">
                                                    <input type="checkbox" id="is_title_show" onclick="ShowHideDiv(this)" name="is_title_show" autocomplete="off">
                                                    <label for="is_title_show"><?php echo app('translator')->get("$string_file.is_title_show"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group required">
                                                <label for="price"
                                                       class="control-label"><?php echo app('translator')->get("$string_file.price"); ?></label>
                                                <input type="number" class="form-control" id="price" name="price" step="<?php echo e($step_value); ?>" min="0"
                                                       placeholder="<?php echo app('translator')->get("$string_file.price"); ?>"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount"
                                                       class=" control-label"><?php echo app('translator')->get("$string_file.discount"); ?></label>
                                                <input type="number" class="form-control" id="discount" name="discount" step="<?php echo e($step_value); ?>" min="0"
                                                       placeholder="<?php echo app('translator')->get("$string_file.discount"); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weight_unit"
                                                       class=" control-label"><?php echo app('translator')->get("$string_file.weight_unit"); ?> </label>
                                                <?php echo Form::select('weight_unit',$arr_weight_unit,old('weight_unit'),['id'=>'weight_unit','class'=>'form-control','autocomplete'=>'off']); ?>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount"
                                                       class=" control-label"><?php echo app('translator')->get("$string_file.weight"); ?></label>
                                                <input type="text" class="form-control" id="weight" name="weight"
                                                       placeholder="<?php echo app('translator')->get("$string_file.weight"); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if(!$is_demo): ?>
                                    <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("$string_file.save"); ?> </button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo app('translator')->get("$string_file.cancel"); ?></button>
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/footable/footable.core.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/tables/footable.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('global/vendor/footable/footable.min.js')); ?>"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        function ShowHideDiv(ss) {
            var checkValue = ss.checked ? 1 : 0;
            console.log(checkValue);
            if(checkValue == 1){
                $('#title').attr('readonly', false);
            }else{
                $('#title').attr('readonly', true);
            }
        }
        $(document).ready(function () {

            var $modal = $('#editor-modal'),
                $editor = $('#editor'),
                $editorTitle = $('#editor-title'),
                ft = FooTable.init('#exampleFooEditing', {
                    editing: {
                        enabled: true,
                        addRow: function () {
                            $modal.removeData('row');
                            $editor[0].reset();
                            $editor.find('#is_title_show').prop("checked", false );
                            $editor.find('#title').prop('readonly',true);
                            $editorTitle.text('<?php echo app('translator')->get("$string_file.product_variant"); ?>');
                            $modal.modal('show');
                        },
                        editRow: function (row) {
                            var values = row.val();
                            console.log(values);
                            $editor.find('#id').val(values.id);
                            $editor.find('#sku_id').val(values.sku_id);
                            $editor.find('#title').val(values.title);
                            $editor.find('#price').val(values.price);
                            $editor.find('#discount').val(values.discount);
                            $editor.find('#weight_unit').val(values.weight_unit_value);
                            $editor.find('#weight').val(values.weight);
                            $editor.find('#status').val(values.status_value);
                            if(values.is_title_show_value == 1){
                                $editor.find('#is_title_show').prop("checked", true );
                                $editor.find('#title').prop('readonly',false);
                            }else{
                                $editor.find('#is_title_show').prop("checked", false );
                                $editor.find('#title').prop('readonly',true);
                            }

                            $modal.data('row', row);
                            $editorTitle.text('Edit row #' + values.id);
                            $modal.modal('show');
                        },
                        deleteRow: function (row) {
                            if (confirm('Are you sure you want to delete the row?')) {
                                var values = row.val();
                                $.get("<?php echo e(route('business-segment.product.variant.destroy')); ?>", {id: values.id}, function (data, status) {
                                    if (data.result == 'success') {
                                        row.delete();
                                    } else {
                                        alert(data.data);
                                    }
                                });
                            }
                        }
                    }
                }),
                uid = 10;

            $editor.on('submit', function (e) {
                if (this.checkValidity && !this.checkValidity()) return;
                e.preventDefault();
                $.ajax({
                    url: "<?php echo e(route('business-segment.product.variant.save')); ?>",
                    data: $editor.serialize(),
                    type: "POST",
                }).done(function (result) {
                    if (typeof (result.success) != "undefined" && result.success !== null) {
                        var row = $modal.data('row'),
                            values = {
                                id: $editor.find('#id').val(),
                                sku_id: $editor.find('#sku_id').val(),
                                title: $editor.find('#title').val(),
                                price: $editor.find('#price').val(),
                                discount: $editor.find('#discount').val(),
                                weight_unit: $editor.find('#weight_unit').val(),
                                weight: $editor.find('#weight').val(),
                                status: $editor.find('#status').val()
                            };
                        if (row instanceof FooTable.Row) {
                            row.val(values);
                        } else {
                            values.id = uid++;
                            ft.rows.add(values);
                        }
                        $modal.modal('hide');
                        window.location.href = result.route;
                    } else {
                        alert('error : ' + result.error);
                    }
                });
            });
        });
        $(document).ready(function () {
            $(".option_checkbox").click(function () {
               var id = $(this).attr("id");
               if($(this).is(':checked'))
               {
                 $(".option"+id).prop("disabled",false);
                 // $(".option"+id).prop("required",true);
               }
               else
               {
                   $(".option"+id).prop("disabled",true);
                   // $(".option"+id).prop("required",false);
               }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/product/product_variant.blade.php ENDPATH**/ ?>