<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('excel.ridenow',$arr_search)); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-download"
                                   title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.ongoing_rides"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="tab-content pt-20">
                        <div class="tab-pane active" id="exampleTabsLineTopOne" role="tabpanel">
                            <?php echo $search_view; ?>

                            <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.ride_type"); ?></th>
                                    <?php if(isset($bookingConfig->ride_otp) && $bookingConfig->ride_otp == 1): ?>
                                        <th><?php echo app('translator')->get("$string_file.otp"); ?></th>
                                    <?php endif; ?>
                                    <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.request_from"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.ride_details"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.estimate_bill"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.payment_method"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.date"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $sr = $all_bookings->firstItem() ?>
                                <?php $__currentLoopData = $all_bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php echo e($sr); ?>

                                        </td>
                                        <td>
                                            <a class="address_link"
                                               href="<?php echo e(route('merchant.booking.details',$booking->id)); ?>"><?php echo e($booking->merchant_booking_id); ?> </a>
                                        </td>
                                        <td>
                                            <?php if($booking->booking_type == 1): ?>
                                                <?php echo app('translator')->get("$string_file.ride_now"); ?>
                                            <?php else: ?>
                                                <?php echo app('translator')->get("$string_file.ride_later"); ?><br>(
                                                <?php echo date(getDateTimeFormat($booking->Merchant->datetime_format,2),strtotime($booking->later_booking_date)); ?>


                                                <br>
                                                
                                                <?php echo e($booking->later_booking_time); ?> )
                                            <?php endif; ?>
                                        </td>
                                        <?php if(isset($bookingConfig->ride_otp) && $bookingConfig->ride_otp == 1): ?>
                                            <td><?php echo e(isset($booking->ride_otp) ? $booking->ride_otp : '--'); ?></td>
                                        <?php endif; ?>
                                        <td style="text-align: center">
                                            <?php if(!empty($arr_booking_status)): ?>
                                                <?php echo isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""; ?>

                                                <br>
                                                <?php echo convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3); ?>

                                                
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(Auth::user()->demo == 1): ?>
                                                <span class="long_text">
                                                        <?php echo e("********".substr($booking->User->UserName, -2)); ?>

                                                        <br>
                                                        <?php echo e("********".substr($booking->User->UserPhone, -2)); ?>

                                                        <br>
                                                        <?php echo e("********".substr($booking->User->email, -2)); ?>

                                                    </span>
                                            <?php else: ?>
                                                <span class="long_text">
                                                        <?php echo e($booking->User->UserName); ?>

                                                        <br>
                                                        <?php echo e($booking->User->UserPhone); ?>

                                                        <br>
                                                        <?php echo e($booking->User->email); ?>

                                                    </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                                             <span class="long_text">
                                                                <?php if($booking->Driver): ?>
                                                                     <?php if(Auth::user()->demo == 1): ?>
                                                                         <?php echo e("********".substr($booking->Driver->first_name.' '.$booking->Driver->last_name, -2)); ?>

                                                                         <br>
                                                                         <?php echo e("********".substr($booking->Driver->phoneNumber, -2)); ?>

                                                                         <br>
                                                                         <?php echo e("********".substr($booking->Driver->email, -2)); ?>

                                                                     <?php else: ?>
                                                                         <?php echo e($booking->Driver->first_name.' '.$booking->Driver->last_name); ?>

                                                                         <br>
                                                                         <?php echo e($booking->Driver->phoneNumber); ?>

                                                                         <br>
                                                                         <?php echo e($booking->Driver->email); ?>

                                                                     <?php endif; ?>
                                                                 <?php else: ?>
                                                                     <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                                                 <?php endif; ?>
                                                            </span>
                                        </td>
                                        <td>
                                            <?php switch($booking->platform):
                                                case (1): ?>
                                                <?php echo app('translator')->get("$string_file.application"); ?>
                                                <?php break; ?>
                                                <?php case (2): ?>
                                                <?php echo app('translator')->get("$string_file.admin"); ?>
                                                <?php break; ?>
                                                <?php case (3): ?>
                                                <?php echo app('translator')->get("$string_file.web"); ?>
                                                <?php break; ?>
                                            <?php endswitch; ?>
                                        </td>
                                        <?php
                                            $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : ($booking->deliveryType ? $booking->deliveryType->name : '---' );
                                        ?>
                                        <td><?php echo nl2br($booking->CountryArea->CountryAreaName ."\n". $service_text."\n".$booking->VehicleType->VehicleTypeName); ?></td>

                                        <td><a title="<?php echo e($booking->pickup_location); ?>"
                                               href="https://www.google.com/maps/place/<?php echo e($booking->pickup_location); ?>"
                                               class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                            <a title="<?php echo e($booking->drop_location); ?>"
                                               href="https://www.google.com/maps/place/<?php echo e($booking->drop_location); ?>"
                                               class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                        </td>
                                        <td>
                                            <?php echo e($booking->estimate_distance); ?><br>
                                            <?php echo e($booking->CountryArea->Country->isoCode . $booking->estimate_bill); ?>

                                        </td>
                                        <td>
                                            <?php echo e($booking->PaymentMethod->payment_method); ?>

                                        </td>

                                        <td>
                                            <?php echo convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant); ?>

                                            
                                            
                                            
                                        </td>
                                        <td>
                                            <a target="_blank"
                                               title=""
                                               href="<?php echo e(route('merchant.ride-requests',$booking->id)); ?>"
                                               class="btn btn-sm btn-primary menu-icon btn_detail action_btn"><span
                                                        class="fa fa-list-alt"
                                                        data-original-title="<?php echo app('translator')->get("$string_file.requested_drivers"); ?>"
                                                        data-toggle="tooltip"
                                                        data-placement="top"></span></a>
                                            <a target="_blank"
                                               title=""
                                               href="<?php echo e(route('merchant.booking.details',$booking->id)); ?>"
                                               class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                        class="fa fa-info-circle"
                                                        data-original-title="<?php echo app('translator')->get("$string_file.ride_details"); ?>"
                                                        data-toggle="tooltip"
                                                        data-placement="top"></span></a>
                                            <?php if(!in_array($booking->booking_status,[1005])): ?>
                                                <span data-target="#cancelbooking"
                                                      data-toggle="modal"
                                                      id="<?php echo e($booking->id); ?>"><a
                                                            data-original-title="<?php echo app('translator')->get("$string_file.cancel_ride"); ?>"
                                                            data-toggle="tooltip"
                                                            id="<?php echo e($booking->id); ?>"
                                                            data-placement="top"
                                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                                class="fa fa-times"></i> </a></span>
                                            <?php endif; ?>
                                            <span data-target="#completebooking"
                                                  data-toggle="modal"
                                                  id="<?php echo e($booking->id); ?>"><a
                                                        data-original-title="<?php echo app('translator')->get("$string_file.complete_ride"); ?>"
                                                        data-toggle="tooltip"
                                                        id="<?php echo e($booking->id); ?>"
                                                        data-placement="top"
                                                        class="btn btn-sm btn-success menu-icon btn_delete action_btn"> <i
                                                            class="fa fa-check"></i> </a></span>
                                            <?php if(!in_array($booking->booking_status,[1001,1012])): ?>
                                                <a target="_blank"
                                                   title=""
                                                   href="<?php echo e(route('merchant.activeride.track',$booking->id)); ?>"
                                                   class="btn btn-sm btn-success menu-icon btn_money action_btn"><span
                                                            class="fa fa-map-marker"
                                                            data-original-title="<?php echo app('translator')->get("$string_file.track_ride"); ?>"
                                                            data-toggle="tooltip"
                                                            data-placement="top"></span></a>
                                            <?php endif; ?>
                                            <?php if($booking->booking_status == 1005 && $booking->booking_closure != 1): ?>
                                                <span data-target="#ratebooking"
                                                      data-toggle="modal"
                                                      id="<?php echo e($booking->id); ?>"><a
                                                            data-original-title="<?php echo app('translator')->get("$string_file.rating"); ?> "
                                                            data-toggle="tooltip"
                                                            id="<?php echo e($booking->id); ?>"
                                                            data-placement="top"
                                                            class="btn btn-sm btn-info menu-icon action_btn rating_btn"> <i
                                                                class="fa fa-star-half-empty"></i> </a></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php $sr++ ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $all_bookings, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="cancelbooking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><?php echo app('translator')->get("$string_file.cancel_ride"); ?></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('merchant.cancelbooking')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <select class="form-control" name="cancel_reason_id" required>
                                <option value=""><?php echo app('translator')->get("$string_file.cancel_reason"); ?></option>
                                <?php $__currentLoopData = $cancelreasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cancelreason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cancelreason->id); ?>"><?php echo e($cancelreason->ReasonName); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.additional_notes"); ?>: </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                        <input type="hidden" name="booking_id" id="booking_id" value="">

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-primary" value="<?php echo app('translator')->get("$string_file.save"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="completebooking" tabindex="-1" role="dialog" aria-labelledby="myModalLabelCompleteBooking"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabelCompleteBooking"><?php echo app('translator')->get("$string_file.complete_ride"); ?></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('merchant.completebooking')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="text-center">Are You Sure?</h3>
                                    <h4 class="text-center">You want to complete this Ride?</h4>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="booking_id" id="booking_id" value="">
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-danger" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.no"); ?>">
                        <input type="submit" class="btn btn-success" value="<?php echo app('translator')->get("$string_file.yes"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="ratebooking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><?php echo app('translator')->get("$string_file.rating"); ?></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('merchant.booking.rating')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="number" class="form-control" id="rating" name="rating"
                                   placeholder=""/>
                        </div>
                        <label><?php echo app('translator')->get("$string_file.comment"); ?> : </label>
                        <div class="form-group">
                            <textarea class="form-control" id="comment" rows="3" name="comment"
                                      placeholder=""></textarea>
                        </div>
                        <input type="hidden" name="rating_booking_id" id="rating_booking_id" value="">
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-primary" value="<?php echo app('translator')->get("$string_file.save"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="examplePositionSidebar" aria-labelledby="examplePositionSidebar"
         role="dialog" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-simple modal-sidebar modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    
                </div>
                <div class="modal-body">
                    <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                        <?php echo $info_setting->view_text; ?>

                    <?php else: ?>
                        <p>No information content found...</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function () {
            $('#dataTable2').DataTable({
                searching: false,
                paging: false,
                info: false,
                "bSort": false,
            });
            $(".rating_btn").on('click', function () {
                $('#rating_booking_id').val($(this).attr('id'));
            });
        });
        $('#completebooking').on('show.bs.modal', function (e) {
            let $modal = $(this),
                esseyId = e.relatedTarget.id;
            $modal.find('#booking_id').val(esseyId);
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/booking/active.blade.php ENDPATH**/ ?>