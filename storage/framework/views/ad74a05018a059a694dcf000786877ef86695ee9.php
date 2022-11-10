<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
           <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="<?php echo e(route('excel.ridefailed')); ?>" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin: 10px;">
                                    <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                        </div>
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.failed_rides"); ?></h3>
                    </header>
                <div class="panel-body container-fluid">
                    <form action="<?php echo e(route('merchant.failride.search',['slug' => $url_slug])); ?>" method="get">
                        <div class="table_search row ">
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <?php echo app('translator')->get("$string_file.search_by"); ?>:
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="booking_id"
                                           placeholder="<?php echo app('translator')->get("$string_file.ride_id"); ?>"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="rider"
                                           placeholder="<?php echo app('translator')->get("$string_file.user_details"); ?>"
                                           class="form-control col-md-12 col-xs-12">
                                </div>

                            </div>
                            <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"
                                        name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.ride_type"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.ride_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.pickup_location"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.failed_reason"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.date"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = $bookings->firstItem() ?>
                            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php echo e($sr); ?>

                                    </td>
                                    <td><?php echo e($booking->id); ?>

                                    </td>
                                    <td>
                                        <?php if($booking->booking_type == 1): ?>
                                            <?php echo app('translator')->get("$string_file.ride_now"); ?>
                                        <?php else: ?>
                                            <?php echo app('translator')->get("$string_file.ride_later"); ?> <br>(
                                            <?php echo convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2); ?><br>
                                            
                                            <?php echo e($booking->later_booking_time); ?> )
                                        <?php endif; ?>
                                    </td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <td>
                                                                 <span class="long_text">
                                                               <?php echo e("********".substr($booking->User->UserName,-2)); ?>

                                                            <br>
                                                            <?php echo e("********".substr($booking->User->UserPhone,-2)); ?>

                                                            <br>
                                                            <?php echo e("********".substr($booking->User->email,-2)); ?>

                                                                </span>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                                                 <span class="long_text">
                                                                <?php echo e($booking->User->UserName); ?>

                                                                <br>
                                                                <?php echo e($booking->User->UserPhone); ?>

                                                                <br>
                                                                <?php echo e($booking->User->email); ?>

                                                                </span>
                                        </td>
                                    <?php endif; ?>

                                    <td>
                                        <?php echo e($booking->CountryArea->LanguageSingle == "" ? $booking->CountryArea->LanguageAny->AreaName : $booking->CountryArea->LanguageSingle->AreaName); ?>

                                        <br>
                                        <?php
                                            $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : ($booking->deliveryType ? $booking->deliveryType->name : '---' );
                                        ?>
                                        <?php echo e($service_text); ?><br>
                                        <?php if($booking->VehicleType): ?> <?php echo e($booking->VehicleType->VehicleTypeName); ?> <?php else: ?>
                                            ------- <?php endif; ?></td>
                                    <td><?php if(!empty($booking->pickup_location)): ?>
                                            <a title="<?php echo e($booking->pickup_location); ?>"
                                               target="_blank"
                                               href="https://www.google.com/maps/place/<?php echo e($booking->pickup_location); ?>"class="btn btn-icon btn-success ml-40"><i class="icon wb-map"></i></a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($booking->failreason == 1): ?>
                                            <?php echo app('translator')->get("$string_file.configuration_not_found"); ?>
                                        <?php else: ?>
                                            <?php echo app('translator')->get("$string_file.driver_not_found"); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant); ?>




                                    </td>
                                </tr>
                                <?php $sr++ ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/booking/fail.blade.php ENDPATH**/ ?>