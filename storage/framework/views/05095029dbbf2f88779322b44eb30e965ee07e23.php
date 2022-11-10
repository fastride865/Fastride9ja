<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                       
                                <a href="<?php echo e(route('merchant.driver-agency.add')); ?>">
                                    <button type="button" title="<?php echo app('translator')->get("$string_file.add_driver_agency"); ?>"
                                            class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                                    </button>
                                </a>
                       
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-building" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.driver_agency"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.driver_agency"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.logo"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.country"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.login_url"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.address"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.bank_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.no_of_driver"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.wallet_money"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.transactions"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = 1 ?>
                            <?php $__currentLoopData = $driver_agency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->name, -2)); ?> <?php else: ?> <b><?php echo e($agency->name); ?></b> <?php endif; ?> <br>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->email, -2)); ?> <?php else: ?> <i><?php echo e($agency->email); ?></i> <?php endif; ?> <br>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->phone, -2)); ?> <?php else: ?> <?php echo e($agency->phone); ?> <?php endif; ?>
                                    </td>
                                    <td>
                                        <img src="<?php echo e(get_image($agency->logo,'agency_logo',$agency->merchant_id)); ?>" width="50px" height="50px">
                                    </td>
                                    <td>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->country->CountryName, -2)); ?> <?php else: ?> <?php echo e($agency->country->CountryName); ?> <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(config('app.url')); ?>driver-agency/admin/<?php echo e($merchant->alias_name); ?>/<?php echo e($agency->alias_name); ?>/login"
                                           target="_blank" rel="noopener noreferrer"  class="btn btn-icon btn-info btn_eye action_btn"><i class="icon fa-sign-in"></i></a>
                                    </td>
                                    <td>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->address, -2)); ?> <?php else: ?> <?php echo e($agency->address); ?> <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->bank_name, -2)); ?> <?php else: ?> <?php echo e($agency->bank_name); ?> <?php endif; ?>,
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->account_holder_name, -2)); ?> <?php else: ?> <?php echo e($agency->account_holder_name); ?> <?php endif; ?> <br>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->account_number, -2)); ?> <?php else: ?> <?php echo e($agency->account_number); ?> <?php endif; ?>,
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr((isset($agency->AccountType->LangAccountTypeSingle->name) ? $agency->AccountType->LangAccountTypeSingle->name : 'Unknown'), -2)); ?> <?php else: ?> <?php echo e((isset($agency->AccountType->LangAccountTypeSingle->name) ? $agency->AccountType->LangAccountTypeSingle->name : 'Unknown')); ?> <?php endif; ?> <br>
                                        <?php if(Auth::user()->demo == 1): ?> <?php echo e("********".substr($agency->online_transaction, -2)); ?> <?php else: ?> <?php echo e($agency->online_transaction); ?> <?php endif; ?>
                                    </td>
                                    <td> <?php echo e(isset($agency->Driver) ? $agency->Driver->count() : 0); ?> </td>
                                    <td>
                                        <?php echo e(isset($agency->wallet_balance) ? $agency->wallet_balance : 0); ?>

                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('merchant.driver-agency.transactions',[$agency->id])); ?>"><i class="fa fa-random"></i> </a>
                                    </td>
                                    <td>
                                        <?php if($agency->status == 1): ?>
                                            <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td  style="width: 100px;float: left">
                                       
                                            <span data-toggle="modal"
                                            data-target="#examplePositionCenter">
                                                <a href="#"
                                               onclick="AddWalletMoneyMod(this)"
                                               data-ID="<?php echo e($agency->id); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.add_money"); ?>"
                                               data-toggle="tooltip"

                                               data-placement="top"
                                               class="btn text-white btn-sm btn-success">
                                                <i class="icon fa-money"></i>
                                                </a></span>
                                            <a href="<?php echo e(route('merchant.driver-agency.wallet.show',$agency->id)); ?>"
                                               data-original-title="Wallet Transaction" data-toggle="tooltip"
                                               class="btn btn-sm menu-icon btn-primary btn_money action_btn">
                                                <i class="icon fa-window-maximize">
                                                         </i>
                                            </a>
                                            <?php if(Auth::user()->demo != 1): ?>
                                                <a href="<?php echo e(route('merchant.driver-agency.add',$agency->id)); ?>"
                                                   data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="wb-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if($agency->status == 1): ?>
                                                <a href="<?php echo e(route('driver-agency.status',['id'=>$agency->id,'status'=>2])); ?>"
                                                   data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                            class="fa fa-eye-slash"></i> </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('driver-agency.status',['id'=>$agency->id,'status'=>1])); ?>"
                                                   data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                            class="icon fa-eye"></i> </a>
                                            <?php endif; ?>
                                        
                                    </td>
                                </tr>
                                <?php $sr++  ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $driver_agency, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="examplePositionCenter" aria-hidden="true" aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.add_money_in_wallet"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.payment_method"); ?>: </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1"><?php echo app('translator')->get("$string_file.cash"); ?></option>
                                <option value="2"><?php echo app('translator')->get("$string_file.no_cash"); ?></option>
                            </select>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.receipt_number"); ?>: </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" id="receipt_number"
                                   class="form-control" required>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.amount"); ?>: </label>
                        <div class="form-group">
                            <input type="number" name="amount" id="amount" placeholder=""
                                   class="form-control" required min="1">
                            <input type="hidden" name="add_money_driver_agency_id" id="add_money_driver_agency_id">
                        </div>

                        <label><?php echo app('translator')->get("$string_file.description"); ?>: </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="button" id="add_money_button" class="btn btn-primary" value="<?php echo app('translator')->get("$string_file.add"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function AddWalletMoneyMod(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #add_money_driver_agency_id").val(ID);
            $('#addWalletMoneyModel').modal('show');
        }
        $('#add_money_button').on('click', function () {
            $('#add_money_button').prop('disabled',true);
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
            var token = $('[name="_token"]').val();
            var payment_method = document.getElementById('payment_method').value;
            var receipt_number = document.getElementById('receipt_number').value;
            var amount = document.getElementById('amount').value;
            var desc = document.getElementById('title1').value;
            var driver_agency_id = document.getElementById('add_money_driver_agency_id').value;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: "POST",
                data: {payment_method_id : payment_method,receipt_number : receipt_number,amount : amount,description : desc,driver_agency_id : driver_agency_id},
                url: "<?php echo e(route('driver-agency.add-wallet')); ?>",
                success: function (data) {
                    console.log(data);
                    if(data.result == 1){
                        $('#myLoader').removeClass('d-flex');
                        $('#myLoader').addClass('d-none');
                        swal({
                            title: "<?php echo app('translator')->get("$string_file.driver_agency_account"); ?>",
                            text: "<?php echo app('translator')->get("$string_file.amount_added_successfully"); ?>",
                            icon: "success",
                            buttons: true,
                            dangerMode: true,
                        }).then((isConfirm) => {
                            window.location.href = "<?php echo e(route('merchant.driver-agency')); ?>";

                        });
                    }
                }, error: function (err) {
                    $('#myLoader').removeClass('d-flex');
                    $('#myLoader').addClass('d-none');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver-agency/index.blade.php ENDPATH**/ ?>