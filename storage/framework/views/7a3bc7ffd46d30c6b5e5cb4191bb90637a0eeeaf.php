<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="mr--10 ml--10">
                        <div class="row" style="margin-right: 0rem;margin-left: 0rem">
                            <div class="col-md-4 col-sm-4 col-4">
                            <h3 class="panel-title">
                                <i class="fa fa-language"></i>
                                <?php echo app('translator')->get("$string_file.customize_string"); ?>
                            </h3>
                            </div>
                            <?php echo Form::open(['url'=>route('admin-app-string'),'id'=>'filter','class'=>'','method'=>'GET']); ?>

                            <div class="col-md-12 col-sm-12 col-12">
                                <div class="row">
                                    <input type="hidden" value="<?php echo e(app()->getLocale()); ?>" name="loc">
                               <div class="col-md-4 col-sm-4 ">
                                   <?php echo Form::select('platform',[''=>'--'.trans("$string_file.app").'--','android'=>trans("$string_file.android"),'ios'=>trans("$string_file.ios")],isset($searched_param['platform']) ? $searched_param['platform'] : NULL,["class"=>"form-control mt-10","required"=>true]); ?>






                                <?php if($errors->has('platform')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('platform')); ?></label>
                                <?php endif; ?>
                                </div>
                                    <div class="col-md-4 col-sm-4">
                                        <?php echo Form::select('app',[''=> '--'.trans("$string_file.application"),'USER'=>trans("$string_file.user"),'DRIVER'=>trans("$string_file.driver")],isset($searched_param['app']) ? $searched_param['app'] : NULL,["class"=>"form-control mt-10","required"=>true]); ?>






                                <?php if($errors->has('app')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('app')); ?></label>
                                <?php endif; ?>
                                    </div>
                                    <div class="col-md-2">
                                    <button type="submit" id="search_data" class="btn btn-primary mt-10"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-md-2">
                                <a href="<?php echo e(route('applicationstring.index')); ?>">
                                    <button type="button" class="btn btn-success mt-10"><i class="fa fa-reply"></i>
                                    </button>
                                </a>
                                    </div>
                                </div>
                            </div>
                            <?php echo Form::close(); ?>

                        </div>
                    </div>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="<?php echo e(route('customSave')); ?>">
                        <?php echo csrf_field(); ?>
                        <div id="show_val">
                            <?php echo $final_text; ?>


                        </div><br>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if($result): ?>
                                <input type="hidden" value="<?php echo e(app()->getLocale()); ?>" name="loc">
                                <button type="submit" id="save_data" class="btn btn-primary"><i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?></button>
                            <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function sweetalert(msg) {
            swal({
                title: "Error",
                text: msg,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            });
        }

        function getKeyVal(obj) {
            var application = document.getElementById('app').value;
            var platform = document.getElementById('platform').value;
            var loc = "<?php echo e(app()->getLocale()); ?>";
            if (platform == "") {
                sweetalert("Please Select Any Platform");
                return false;
            }
            if (application == "") {
                sweetalert("Please Select Any Application");
                return false;
            }


            $.ajax({
                method: 'GET',
                url: "getStringVal",
                data: {application: application,platform:platform,loc:loc},
                success: function (data) {
                    if(data){
                        $('#show_val').html(data);
                        $('#save_data').prop('hidden',false);
                    }else{
                        $('#show_val').text("");
                        alert('No Data Found');
                        return false;
                    }
                }, error: function (e) {
                    console.log(e);
                }
            });
        }

    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/application_string/edit.blade.php ENDPATH**/ ?>