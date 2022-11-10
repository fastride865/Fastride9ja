<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <?php if(Auth::user()->demo == 1): ?>





                            <?php else: ?>
                                <a href="<?php echo e(route('transaction.wallet-report.export',$data)); ?>">
                                    <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="<?php echo app('translator')->get("$string_file.export_excel"); ?>" data-toggle="tooltip"><i
                                                class="fa fa-download"></i>
                                    </button>
                                </a>
                            <?php endif; ?>
                        </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        <?php echo e($page_title); ?>

                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'','url'=>route("transaction.wallet-report",["slug" => $slug]),'method'=>'GET']); ?>

                    <?php echo Form::hidden("slug",$slug); ?>

                    <div class="table_search row">
                        <div class="col-md-4 col-xs-12 form-group active-margin-top">
                            <div class="input-daterange" data-plugin="datepicker">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="icon wb-calendar" aria-hidden="true"></i>
                          </span>
                                    </div>
                                    <input type="text" class="form-control" name="start" value="<?php echo e(old("start", isset($data['start']) ? $data['start'] : "")); ?>" />
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">to</span>
                                    </div>
                                    <input type="text" class="form-control" name="end" value="<?php echo e(old("end", isset($data['end']) ? $data['end'] : "")); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            <a href="<?php echo e(route("transaction.wallet-report",["slug" => $slug])); ?>" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>

                                <th><?php echo app('translator')->get("$string_file.receiver_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.amount"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.transaction_for"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.transaction_type"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.date"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.transaction_from"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.narration"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.transaction_by"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = $wallet_transactions->firstItem() ?>
                            <?php $__currentLoopData = $wallet_transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>

                                    <?php if(Auth::user()->demo == 1): ?>
                                        <td>
                                            <?php echo e("********".substr($transaction->user_name, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr( $transaction->user_phone, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($transaction->user_email, -2)); ?>

                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <?php echo e($transaction->user_name); ?>

                                            <br>
                                            <?php echo e($transaction->user_phone); ?>

                                            <br>
                                            <?php echo e($transaction->user_email); ?>

                                        </td>
                                    <?php endif; ?>
                                    <td><?php echo e($transaction->amount); ?></td>
                                    <td>
                                        <?php if(isset($transaction->booking_id) && !empty($transaction->booking_id)): ?>
                                            <?php echo app('translator')->get("$string_file.ride_id"); ?> : <a target="_blank" title="<?php echo app('translator')->get("$string_file.ride_details"); ?>" href="<?php echo e(route('merchant.booking.details',$transaction->booking_id)); ?>"><?php echo e($transaction->booking_id); ?></a>
                                        <?php elseif(isset($transaction->order_id) && !empty($transaction->order_id)): ?>
                                            <?php echo app('translator')->get("$string_file.order_id"); ?> : <a target="_blank" title="<?php echo app('translator')->get("$string_file.order_details"); ?>" href="<?php echo e(route('driver.order.detail',$transaction->order_id)); ?>"><?php echo e($transaction->order_id); ?></a>
                                        <?php elseif(isset($transaction->handyman_order_id) && !empty($transaction->handyman_order_id)): ?>
                                            <?php echo app('translator')->get("$string_file.booking_id"); ?> : <a target="_blank" title="<?php echo app('translator')->get("$string_file.booking_details"); ?>" href="<?php echo e(route('merchant.handyman.order.detail',$transaction->handyman_order_id)); ?>"><?php echo e($transaction->handyman_order_id); ?></a>
                                        <?php else: ?>
                                            ---
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($transaction->transaction_type); ?></td>
                                    <td><?php echo e(convertTimeToUSERzone($transaction->created_at, null,null,$transaction->Merchant)); ?>

                                    <td><?php echo e($transaction->platform); ?>

                                    <td><?php echo e($transaction->narration); ?>

                                    <td><?php echo e($transaction->action_merchant_name); ?>

                                </tr>
                                <?php $sr++  ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $wallet_transactions, 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/report/wallet_report.blade.php ENDPATH**/ ?>