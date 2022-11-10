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
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.wallet_recharge"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="<?php echo e(route('Wallet.recharge.details')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.receiver_account"); ?> <span
                                                    class="text-danger">*</span></label>
                                        <?php echo Form::select('application',$receiver,old('application'),['id'=>'application','class'=>'form-control','required'=>true, 'onchange' => "findReceiver()"]); ?>

                                        <?php if($errors->has('application')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('application')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 position-relative">
                                    <div id="loader-img"
                                         style="position: absolute;width:100%;z-index: 1;top: 19px;display:none;">
                                        <img src="<?php echo e(url('/basic-images/loader2.gif')); ?>" width="60px"
                                             style="position:relative;left:35%;" alt="Image"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.search_by"); ?><span
                                                    class="text-danger">*</span></label>
                                        <?php echo Form::select('receiver_id',add_blank_option([],trans("$string_file.select")),old('receiver_id'),['id'=>'receiver_id','class'=>'form-control select2','required'=>true]); ?>

                                        <?php if($errors->has('receiver_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('receiver_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <button type="button" id="serach_info" onclick="checkDetails()"
                                                class="btn btn-primary"><?php echo app('translator')->get("$string_file.search"); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <b><?php echo app('translator')->get("$string_file.name"); ?> : </b><i id="full_name">---</i><br>
                                    <b><?php echo app('translator')->get("$string_file.phone"); ?> : </b><i id="phone">---</i>
                                </div>
                                <div class="col-md-6">
                                    <b><?php echo app('translator')->get("$string_file.email"); ?> : </b><i id="email">---</i><br>
                                    <b><?php echo app('translator')->get("$string_file.wallet_money"); ?> : </b><i
                                            id="wallet">---</i>
                                </div>
                            </div>
                            <div class="row mt-5 mb-5">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.payment_method"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="payment_method" id="payment_method" required
                                                disabled>
                                            <option value="1"><?php echo app('translator')->get("$string_file.cash"); ?></option>
                                            <option value="2"><?php echo app('translator')->get("$string_file.non_cash"); ?></option>
                                        </select>
                                        <?php if($errors->has('payment_method')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('payment_method')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="profile_image">
                                            <?php echo app('translator')->get("$string_file.receipt_number"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="receipt_number" name="receipt_number"
                                               placeholder="<?php echo app('translator')->get("$string_file.receipt_number"); ?>"
                                               class="form-control" required disabled>
                                        <?php if($errors->has('receipt_number')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('receipt_number')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="transaction_type">
                                            <?php echo app('translator')->get("$string_file.transaction_type"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <select id="transaction_type" name="transaction_type" class="form-control" required disabled>
                                            <option value="1"><?php echo app('translator')->get("$string_file.credit"); ?></option>
                                            <option value="2"><?php echo app('translator')->get("$string_file.debit"); ?></option>
                                        </select>
                                        <?php if($errors->has('transaction_type')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('transaction_type')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.amount"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="amount" name="amount"
                                               placeholder="<?php echo app('translator')->get("$string_file.amount"); ?>"
                                               class="form-control" required disabled>
                                        <?php if($errors->has('amount')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('amount')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.description"); ?><span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="title1" rows="3" name="description"
                                                  placeholder="<?php echo app('translator')->get("$string_file.description"); ?>" disabled></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                <button type="submit" id="sub" class="btn btn-primary float-right" disabled>
                                    <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.wallet_recharge"); ?>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function sweetalert(msg) {
            swal({
                title: "<?php echo app('translator')->get("$string_file.error"); ?>",
                text: msg,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            });
        }

        function checkDetails() {
            var application = document.getElementById('application').value;
            var receiver_id = document.getElementById('receiver_id').value;
            if (application == "") {
                sweetalert("<?php echo app('translator')->get("$string_file.select_receiver"); ?>");
                return false;
            }
            if (receiver_id == "") {
                sweetalert("<?php echo app('translator')->get("$string_file.select_receiver_account"); ?>");
                return false;
            }

            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "getDetails",
                data: {application: application, receiver_id: receiver_id},
                success: function (data) {
                    if (data.result == 'success') {
                        $('#full_name').html(data.data.full_name);
                        $('#phone').html(data.data.phone);
                        $('#email').html(data.data.email);
                        $('#wallet').html(data.data.wallet);
                        $('#payment_method').prop('disabled', false);
                        $('#receipt_number').prop('disabled', false);
                        $('#transaction_type').prop('disabled', false);
                        $('#amount').prop('disabled', false);
                        $('#title1').prop('disabled', false);
                        $('#sub').prop('disabled', false);
                    } else {
                        alert('<?php echo app('translator')->get("$string_file.data_not_found"); ?>');
                        $('#full_name').html('---');
                        $('#phone').html('---');
                        $('#email').html('---');
                        $('#wallet').html('---');
                        $('#payment_method').prop('disabled', false);
                        $('#receipt_number').prop('disabled', false);
                        $('#transaction_type').prop('disabled', false);
                        $('#amount').prop('disabled', false);
                        $('#title1').prop('disabled', false);
                        $('#sub').prop('disabled', false);
                    }
                }, error: function (e) {
                    console.log(e);
                    $('#full_name').html('---');
                    $('#phone').html('---');
                    $('#email').html('---');
                    $('#wallet').html('---');
                    $('#payment_method').prop('disabled', false);
                    $('#receipt_number').prop('disabled', false);
                    $('#transaction_type').prop('disabled', false);
                    $('#amount').prop('disabled', false);
                    $('#title1').prop('disabled', false);
                    $('#sub').prop('disabled', false);
                }

            });
        }

        function findReceiver() {
            var application = document.getElementById('application').value;

            $.ajax({
                method: 'GET',
                url: "<?php echo e(route('wallet.getReceivers')); ?>",
                data: {application: application},
                beforeSend: function () {
                    // Handle the beforeSend event
                    $('#loader-img').show();
                },
                success: function (data) {
                    console.log(data.data);
                    if (data.result == 'success') {
                        $('#receiver_id').html(data.data);
                        $('#loader-img').hide();
                    } else {
                        $('#receiver_id').html([]);
                        $('#loader-img').hide();
                        alert('<?php echo app('translator')->get("$string_file.data_not_found"); ?>');
                    }
                }, error: function (e) {
                    console.log(e);
                }
            });
            $('#full_name').html('---');
            $('#phone').html('---');
            $('#email').html('---');
            $('#wallet').html('---');
            $('#payment_method').prop('disabled', true);
            $('#receipt_number').prop('disabled', true);
            $('#transaction_type').prop('disabled', true);
            $('#amount').prop('disabled', true);
            $('#title1').prop('disabled', true);
            $('#sub').prop('disabled', true);
        }

        $('#sub').on('click', function () {
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/random/wallet_recharge.blade.php ENDPATH**/ ?>