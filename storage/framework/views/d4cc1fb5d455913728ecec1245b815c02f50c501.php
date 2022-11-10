<?php $__env->startSection('content'); ?>
    <style>
        @media  print {
            body * {
                visibility: hidden;
            }
            #section-to-print, #section-to-print * {
                visibility: visible;
            }
        }
    </style>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">


                            <button class="btn btn-icon btn-warning float-right print_invoice" style="margin:10px;width:115px;" ><i class="icon wb-print" aria-hidden="true"></i>
                                <?php echo app('translator')->get("$string_file.print"); ?>
                            </button>

                        <a href="<?php echo e(route('admin.sendinvoice',$booking->id)); ?>">
                            <button class="btn btn-icon btn-warning float-right" style="margin:10px;width:115px;"><i class="icon fa-send"></i>
                                <?php echo app('translator')->get("$string_file.invoice"); ?>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-flag" aria-hidden="true"></i>
                       <?php echo app('translator')->get("$string_file.invoice"); ?>
                    </h3>
                </header>
                <?php if(Auth::user()->tax): ?>
                    <div class="panel-heading"> <?php $a = json_decode(Auth::user()->tax,true);echo $a['name'] ?>
                        <strong><?php $a = json_decode(Auth::user()->tax,true);echo $a['tax_number'] ?> </strong>
                    </div>
                <?php endif; ?>
                <div id="section-to-print" class="panel">
                    <div class="panel-body container-fluid printableArea">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <img class="mr-5" src="<?php echo e(get_image(Auth::user()->BusinessLogo,'business_logo')); ?>" title="<?php echo e((Auth::user()->BusinessName)); ?>"
                                     width="40" height="40" alt="..."><br><h4><?php echo e((Auth::user()->BusinessName)); ?></h4>
                                <?php if(Auth::user()->demo == 1): ?>
                                    <address>
                                        <?php echo e("********".substr($booking->Merchant->BusinessName, -2)); ?>

                                        <br><?php echo e("********".substr($booking->Merchant->merchantAddress, -2)); ?><br>
                                        <?php echo e($booking->Merchant->merchantFirstName); ?> <?php echo e($booking->Merchant->merchantLastName); ?>

                                        <br>
                                        <abbr title="Mail"><?php echo app('translator')->get("$string_file.email"); ?></abbr><?php echo e("********".substr($booking->Merchant->email, -2)); ?>

                                        <br>
                                        <abbr title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?></abbr><?php echo e("********".substr($booking->Merchant->merchantPhone, -2)); ?>

                                        <br>
                                    </address>
                                <?php else: ?>
                                    <address>
                                        <?php echo e($booking->Merchant->merchantFirstName); ?> <?php echo e($booking->Merchant->merchantLastName); ?><br>
                                        <?php echo e($booking->Merchant->merchantAddress); ?>

                                        <br>
                                        <abbr title="Mail"><?php echo app('translator')->get("$string_file.email"); ?>: </abbr><?php echo e($booking->Merchant->email); ?>

                                        <br>
                                        <abbr title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>: </abbr><?php echo e($booking->Merchant->merchantPhone); ?>

                                        <br>
                                    </address>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-3 offset-lg-6 text-right">
                                <a class="font-size-18" href="javascript:void(0)"><?php echo app('translator')->get("$string_file.ride_id"); ?>#<?php echo e($booking->merchant_booking_id); ?></a>
                                <br><b><?php echo app('translator')->get("$string_file.f_cap_to"); ?>:</b>
                                <br>

                                <?php if(Auth::user()->demo == 1): ?>
                                    <?php echo e("********".substr($booking->User->UserName, -2)); ?>

                                    <br>
                                    <address>
                                        <abbr title="Mail"><?php echo app('translator')->get("$string_file.email"); ?>: </abbr><?php echo e("********".substr($booking->User->email, -2)); ?>

                                        <br>
                                        <abbr title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>: </abbr><?php echo e("********".substr($booking->User->UserPhone, -2)); ?>

                                        <br>
                                    </address>
                                <?php else: ?>
                                    <span class="font-size-16"><?php echo e($booking->User->UserName); ?></span> <br>
                                    <address>
                                        <abbr title="Mail"><?php echo app('translator')->get("$string_file.email"); ?>: </abbr><?php echo e($booking->User->email); ?>

                                        <br>
                                        <abbr title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>: </abbr><?php echo e($booking->User->UserPhone); ?>

                                        <br>
                                    </address>
                                <?php endif; ?>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <h4 class="font-size-16"><?php echo app('translator')->get("$string_file.ride_details"); ?></h4>
                                <hr>
                                <div class="row mt-40 mb-10">
                                    <div class="col-xl-6 col-md-6 col-sm-6">
                                        <p><img class="location_marker"
                                                src="<?php echo e(view_config_image('static-images/pinup.png')); ?>"
                                                width="30">
                                            <?php echo e($booking->BookingDetail->start_location); ?></p>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6">
                                        <p><img class="location_marker"
                                                src="<?php echo e(view_config_image('static-images/pindown.png')); ?>"
                                                width="30">
                                            <?php echo e($booking->BookingDetail->end_location); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <h4 class="font-size-16"><?php echo app('translator')->get("$string_file.vehicle_details"); ?></h4>
                                <hr>
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 text-center">
                                        <img class="profile_img" style="border-radius: 100%;"
                                             src="<?php if($booking->Driver->profile_image): ?> <?php echo e(get_image($booking->Driver->profile_image,'driver')); ?> <?php else: ?> <?php echo e(get_image(null, 'driver')); ?> <?php endif; ?>"
                                             width="100" height="100">
                                        <?php if(Auth::user()->demo == 1): ?>
                                            <h5 class="profile_name"><?php echo e("********".substr($booking->Driver->fullName, -2)); ?></h5>>
                                            <?php echo e("********".substr($booking->Driver->email, -2)); ?>

                                            <?php echo e("********".substr($booking->Driver->phoneNumber, -2)); ?>

                                        <?php else: ?>
                                            <h5 class="profile_name"><?php echo e($booking->Driver->fullName); ?></h5>
                                            <?php echo e($booking->Driver->email); ?>

                                            <?php echo e($booking->Driver->phoneNumber); ?>

                                        <?php endif; ?>
                                        <br>
                                        <?php if($booking->Driver->rating == "0.0"): ?>
                                            <?php echo app('translator')->get("$string_file.not_rated_yet"); ?>
                                        <?php else: ?>
                                            <?php while($booking->Driver->rating >0): ?>
                                                <?php if($booking->Driver->rating >0.5): ?>
                                                    <img src="<?php echo e(view_config_image("static-images/star.png")); ?>"
                                                         alt='Whole Star'>
                                                <?php else: ?>
                                                    <img src="<?php echo e(view_config_image('static-images/halfstar.png')); ?>"
                                                         alt='Half Star'>
                                                <?php endif; ?>
                                                <?php $booking->Driver->rating--; ?>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 text-center">
                                        <img src="<?php echo e(get_image($booking->VehicleType->vehicleTypeImage,'vehicle')); ?>"
                                             width="100" height="100">
                                        <h5  class="vehicle_name"><?php echo e($booking->VehicleType->LanguageVehicleTypeSingle == "" ?
                                         $booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName); ?></h5>
                                        <td align="center"
                                            class="vehicle_number"><?php echo e($booking->DriverVehicle->vehicle_number); ?>

                                        </td>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <h4 class="font-size-16"><?php echo app('translator')->get("$string_file.payment_details"); ?></h4>
                                <hr>
                                <div class="row mb-10">
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="">
                                                    <?php echo app('translator')->get("$string_file.payment_method"); ?> : <?php echo e($booking->PaymentMethod->payment_method); ?>

                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="">
                                                    <?php echo app('translator')->get("$string_file.grand_total"); ?> :
                                                    <?php echo e($booking->CountryArea->Country->isoCode.''.$booking->final_amount_paid); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="">
                                                    <?php if($final_bill_calculation == 1): ?>
                                                        <h6><?php echo app('translator')->get("$string_file.final_equal_actual"); ?></h6>
                                                    <?php else: ?>
                                                        <h6><?php echo app('translator')->get("$string_file.final_equal_estimate"); ?></h6>
                                                    <?php endif; ?>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-20 mb-20">
                            <div class="col-lg-6 col-md-6 col-sm-12 mt-25">
                                <img class="map_img" style="border-radius: 20px;"
                                     src="<?php echo e($booking->map_image); ?>"
                                     width="100%" height="300"/>
                                <div class="mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-default" id="dataTable" >
                                            <tfoot>
                                            <tr>
                                                <th width="20%"><b><?php echo app('translator')->get("$string_file.travelled_distance"); ?></b></th>
                                                <th width="30%" class="address">
                                                    <b> <?php echo e($booking->travel_distance); ?></b>
                                                </th>
                                                <th width="20%"><b><?php echo app('translator')->get("$string_file.total_time"); ?></b></th>
                                                <th width="30%"
                                                    class="address"> <b><?php echo e($booking->travel_time); ?></b>
                                                </th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mt-5">
                                <div class="table-responsive">
                                    <table class="table table-default table-hover" id="dataTable" >
                                        <thead>
                                        <tr>
                                            <th class="left"><?php echo app('translator')->get("$string_file.description"); ?></th>
                                            <th class="right"><?php echo app('translator')->get("$string_file.price"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $booking->holder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="left"><?php echo e($b['highlighted_text']); ?></td>
                                                <td class="right"><?php echo e($b['value_text']); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>






                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/jquery.PrintArea.js')); ?>" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $(".print_invoice").click(function(){
                var mode = 'popup'; //popup
                var close = mode == "popup";
                var options = { mode : mode, popClose : true, popHt : 900, popWd: 900, };
                $(".printableArea").printArea( options );
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/booking/invoice.blade.php ENDPATH**/ ?>