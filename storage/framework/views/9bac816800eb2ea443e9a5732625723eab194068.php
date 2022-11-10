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
                        <a href="<?php echo e(route('business-segment.category.add')); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.add_category"); ?>"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-plus"></i>
                            </button>
                        </a>
                        <button type="button" title="<?php echo app('translator')->get("$string_file.delete_category"); ?>" onclick="DeleteEvent('group')"
                                class="btn btn-icon btn-danger float-right" style="margin:10px"><i
                                    class="wb-trash"></i>
                        </button>
                            <a href="<?php echo e(route('merchant.category.export',$arr_search)); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.export_category"); ?>"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-download"></i>
                            </button>
                            </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-building" aria-hidden="true"></i><?php echo app('translator')->get("$string_file.category"); ?></h3>
                </header>
                <div class="panel-body">
                    <?php
                        $category = isset($arr_search['category']) ? $arr_search['category'] : "";
                    ?>
                    <?php echo Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']); ?>

                    <div class="table_search row">
                        <div class="col-md-3 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="category" value="<?php echo e($category); ?>"
                                       placeholder="<?php echo app('translator')->get("$string_file.category"); ?>"
                                       class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            <a href="<?php echo e($search_route); ?>" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>

                    <hr>
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th class="w-50">
                            <span class="checkbox-custom checkbox-primary">
                              <input class="example-select-all" type="checkbox" name="select_all" value="1" id="example-select-all">
                              <label></label>
                            </span>
                            </th>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.parent_category"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sequence"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $sr = 1; $arr_data = (!empty($data['parent_category']) && isset($data['parent_category'])) ? $data['parent_category'] : NULL;
                            unset($data['parent_category']);
                        ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="checkbox-custom checkbox-primary">
                                      <input class="selectable-item" type="checkbox" id="ids[]"
                                             name="ids[]" value="<?php echo e($category->id); ?>">
                                      <label for="row-<?php echo e($category->id); ?>"></label>
                                    </span>
                                </td>
                                <td><?php echo e($sr); ?></td>
                                <td>

                                    <?php $__currentLoopData = $category->Segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($segment->Name($category->merchant_id)); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>
                                    <?php echo e($category->Name($category->merchant_id)); ?>


                                </td>
                                <td>
                                    <?php if(!empty($category->category_parent_id)): ?>
                                        <?php $parent_category = $arr_data->where('id',$category->category_parent_id);
                                        $parent_category = collect($parent_category->values());
                                         ?>
                                        <?php if(isset($parent_category[0]) && !empty($parent_category[0]->id)): ?>
                                        <?php echo e($parent_category[0]->Name($category->merchant_id)); ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.none"); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php if($category->status == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($category->sequence); ?></td>
                                <td>
                                    <?php if(!empty($data) && !empty($category['category_image'])): ?>
                                        <?php $image = get_image($category->category_image,'category',$category->merchant_id); ?>
                                        <a href="<?php echo e($image); ?>" target="_blank">
                                            <img src="<?php echo e($image); ?>" height="30" width="30">
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <?php $created_at = convertTimeToUSERzone($category->created_at, null, null, $category->Merchant,2); ?>
                                <td><?php echo $created_at; ?></td>



                                <td>

                                    <a href="<?php echo route('business-segment.category.add',$category->id); ?>"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="wb-edit"></i>
                                    </a>

                                    <?php echo csrf_field(); ?>
                                    <button onclick="DeleteEvent(<?php echo e($category->id); ?>)"
                                            type="button"
                                            data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $data, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id) {
            var is_ok = true;
            var token = $('[name="_token"]').val();
            if(id == 'group'){
                var searchIDs = $("#customDataTable input:checkbox:checked").map(function(){
                    return $(this).val();
                }).get();
                id = searchIDs
                if(id.length == 0){
                    is_ok = false;
                    swal("<?php echo app('translator')->get("$string_file.select_at_least_one_record"); ?>");
                }
            }
            if(is_ok){
                swal({
                    title: "<?php echo app('translator')->get("$string_file.are_you_sure"); ?>",
                    text: "<?php echo app('translator')->get("$string_file.delete_category"); ?>",
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
                            url: "<?php echo e(route('business-segment.category.destroy')); ?>",
                        })
                            .done(function (data) {
                                swal({
                                    title: "DELETED!",
                                    text: data,
                                    type: "success",
                                });
                                window.location.href = "<?php echo e(route('merchant.category')); ?>";
                            });
                    } else {
                        swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                    }
                });
            }
        }
        $(document).ready(function(){
            // Handle click on "Select all" control
            $('#example-select-all').on('click', function(){
                // Get all rows with search applied
                var table = $('#customDataTable').DataTable();
                var rows = table.rows({ 'search': 'applied' }).nodes();
                // Check/uncheck checkboxes for all rows in the table
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            // Handle click on checkbox to set state of "Select all" control
            $('.selectable-item').on('click', function(){
                // If checkbox is not checked
                if(!this.checked){
                    var el = $('#example-select-all').get(0);
                    // If "Select all" control is checked and has 'indeterminate' property
                    // if(el && el.checked && ('indeterminate' in el)){
                    if(el && el.checked){
                        // Set visual state of "Select all" control
                        // as 'indeterminate'
                        el.indeterminate = true;
                        $('#example-select-all').prop('checked', false);
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/category/index.blade.php ENDPATH**/ ?>