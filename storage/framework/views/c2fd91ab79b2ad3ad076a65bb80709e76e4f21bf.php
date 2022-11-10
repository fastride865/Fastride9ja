<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="mr--10 ml--10">
                        <div class="row " style="margin-right: 0rem;margin-left: 0rem">
                            <div class="col-md-2 col-sm-5">
                                <h3 class="panel-title"><i class="fa-users" aria-hidden="true"></i>
                                    <?php echo app('translator')->get("$string_file.all_driver"); ?></h3>
                            </div>
                            <div class="col-md-10 col-sm-7">
                                <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                                    <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                            data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                        <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                    </button>
                                <?php endif; ?>
                                <?php if(Auth::user('merchant')->can('create_drivers')): ?>
                                    <a href="<?php echo e(route('driver.add')); ?>">
                                        <button type="button" class="btn btn-icon btn-success float-right"
                                                style="margin:10px">
                                            <i class="wb-plus"
                                               title="<?php echo app('translator')->get("$string_file.add_driver"); ?>"></i>
                                        </button>
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('excel.driver',$arr_search)); ?>" data-toggle="tooltip">
                                    <button type="button" class="btn btn-icon btn-primary float-right"
                                            style="margin:10px">
                                        <i class="wb-download"
                                           title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="row " style="margin-right: 0rem;margin-left: 0rem">
                            <div class="col-md-12 col-sm-7">
                                <?php if(Auth::user('merchant')->can('basic_driver_signup')): ?>
                                    <a href="<?php echo e(route('merchant.driver.basic')); ?>">
                                        <button type="button" class="btn btn-icon btn-primary float-right"
                                                style="margin:10px">
                                            <?php echo app('translator')->get("$string_file.basic_signup_completed"); ?>
                                            <span class="badge badge-pill"><?php echo e($basicDriver); ?>

                                    </span>
                                        </button>
                                    </a>
                                <?php endif; ?>
                                <?php if(Auth::user('merchant')->can('pending_drivers_approval')): ?>
                                    <a href="<?php echo e(route('merchant.driver.temp-doc-pending.show')); ?>">
                                        <button type="button" class="btn btn-icon btn-info float-right"
                                                style="margin:10px">
                                            <?php echo app('translator')->get("$string_file.temp_doc_approve"); ?>
                                            <span class="badge badge-pill"><?php echo e($tempDocUploaded); ?>

                                    </span>
                                        </button>
                                    </a>
                                    <a href="<?php echo e(route('merchant.driver.pending.show')); ?>">
                                        <button type="button"
                                                class="btn btn-icon btn-warning float-right" style="margin:10px">
                                            <?php echo app('translator')->get("$string_file.pending_driver_approval"); ?>
                                            <span class="badge badge-pill"><?php echo e($pendingdrivers); ?>

                                        </span>
                                        </button>
                                    </a>
                                <?php endif; ?>
                                <?php if(Auth::user('merchant')->can('rejected_drivers')): ?>
                                    <a href="<?php echo e(route('merchant.driver.rejected')); ?>">
                                        <button type="button" class="btn btn-icon btn-danger float-right"
                                                style="margin:10px">
                                            <?php echo app('translator')->get("$string_file.rejected_drivers"); ?>
                                            <span class="badge badge-pill"><?php echo e($rejecteddrivers); ?>

                                        </span>
                                        </button>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th> <?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <?php if($config->gender == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.gender"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.service_statistics"); ?></th>
                            
                            <th><?php echo app('translator')->get("$string_file.transaction_amount"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.last_location_updated"); ?>  </th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $drivers->firstItem() ?>
                        <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                       class="hyperLink"><?php echo e($driver->merchant_driver_id); ?></a>
                                </td>
                                <td><?php echo e($driver->CountryArea->CountryAreaName); ?></td>
                                <td>
                                        <span class="long_text">
                                            <?php echo e(is_demo_data($driver->first_name,$driver->Merchant)." ".is_demo_data($driver->last_name,$driver->Merchant)); ?>

                                            <br>
                                            <?php echo e(is_demo_data($driver->phoneNumber,$driver->Merchant)); ?>

                                            <br>
                                            <?php echo e(is_demo_data($driver->email,$driver->Merchant)); ?>

                                        </span>
                                </td>
                                <?php if($config->gender == 1): ?>
                                    <?php switch($driver->driver_gender):
                                        case (1): ?>
                                        <td><?php echo app('translator')->get("$string_file.male"); ?></td>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <td><?php echo app('translator')->get("$string_file.female"); ?></td>
                                        <?php break; ?>
                                        <?php default: ?>
                                        <td>------</td>
                                    <?php endswitch; ?>
                                <?php endif; ?>
                                <td>
                                    <?php if($driver->segment_group_id == 1): ?>
                                        <?php
                                            $arr_segment = array_pluck($driver->Segment,'slag');

                                        ?>
                                        <?php if(array_intersect($arr_segment,$booking_segment)): ?>
                                            <?php
                                                $bookings = $driver->Booking->where('booking_status',1005)->count();

                                            ?>
                                            <a href="<?php echo e(route('merchant.driver.jobs',['booking',$driver->id])); ?>">
                                                <span class="badge badge-success font-weight-100"><?php echo app('translator')->get("$string_file.rides"); ?> : <?php echo e($bookings); ?></span>
                                            </a>
                                            <br>
                                        <?php endif; ?>
                                        <?php if(array_intersect($arr_segment,$order_segment)): ?>
                                            <?php
                                                $orders = $driver->Order->where('order_status',11)->count();
                                            ?>
                                            <a href="<?php echo e(route('merchant.driver.jobs',['order',$driver->id])); ?>">
                                                <span class="badge badge-success font-weight-100"><?php echo app('translator')->get("$string_file.orders"); ?> : <?php echo e($orders); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php
                                            $handyman_orders = isset($driver->HandymanOrder) ? $driver->HandymanOrder->where('order_status',7)->count() : 0;
                                        ?>
                                        <a href="<?php echo e(route('merchant.driver.jobs',['handyman-order',$driver->id])); ?>">
                                            <span class="badge badge-success font-weight-100"><?php echo app('translator')->get("$string_file.bookings"); ?> : <?php echo e($handyman_orders); ?></span>
                                        </a>
                                    <?php endif; ?>
                                    <br>
                                    <?php echo app('translator')->get("$string_file.rating"); ?> :
                                    <?php if(!empty($driver->rating) && $driver->rating>0): ?>
                                        <?php while($driver->rating>0): ?>
                                            <?php if($driver->rating >0.5): ?>
                                                <img src="<?php echo e(view_config_image('static-images/star.png')); ?>"
                                                     alt='Whole Star'>
                                            <?php else: ?>
                                                <img src="<?php echo e(view_config_image('static-images/halfstar.png')); ?>"
                                                     alt='Half Star'>
                                            <?php endif; ?>
                                            <?php $driver->rating--; ?>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.not_rated_yet"); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="width:250px;float:left">
                                    <?php if($driver->total_earnings): ?>
                                        <?php echo app('translator')->get("$string_file.earning"); ?>
                                        :- <?php echo e($driver->CountryArea->Country->isoCode." ". $driver->total_earnings); ?>

                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.earning"); ?>
                                        :- <?php echo app('translator')->get("$string_file.no_services"); ?>
                                    <?php endif; ?>
                                    <br>
                                    <?php if($driver->total_comany_earning): ?>
                                        <?php echo app('translator')->get("$string_file.company_profit"); ?>
                                        :- <?php echo e($driver->CountryArea->Country->isoCode." ".$driver->total_comany_earning); ?>

                                    <?php else: ?>
                                        ---
                                    <?php endif; ?>
                                    <br>
                                    <?php if($config->driver_wallet_status == 1): ?>
                                        <?php if($driver->wallet_money): ?>
                                            <?php echo app('translator')->get("$string_file.wallet_money"); ?> :- <a
                                                    href="<?php echo e(route('merchant.driver.wallet.show',$driver->id)); ?>"><?php echo e($driver->wallet_money); ?></a>
                                        <?php else: ?>
                                            <?php echo app('translator')->get("$string_file.wallet_money"); ?> :- ------
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <br>
                                <td><?php echo convertTimeToUSERzone($driver->created_at, $driver->CountryArea->timezone, null, $driver->Merchant); ?></td>
                                <td>
                                    <?php if($driver->driver_admin_status == 1): ?>
                                        <?php if($driver->login_logout == 1): ?>
                                            <?php if($driver->online_offline == 1): ?>
                                                <?php if($driver->free_busy == 1): ?>
                                                    <span class="badge badge-info"><?php echo app('translator')->get("$string_file.busy"); ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-success"><?php echo app('translator')->get("$string_file.online"); ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-info"><?php echo app('translator')->get("$string_file.offline"); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?php echo app('translator')->get("$string_file.logout"); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($driver->segment_group_id) && $driver->segment_group_id == 2): ?>
                                        <?php
                                         $latitude = "";
                                          if(!empty($driver->WorkShopArea))
                                              {
                                                $address = $driver->WorkShopArea; // workshop area of driver
                                                $updated = $address->updated_at;
                                                $latitude = $address->latitude;
                                                $longitude = $address->longitude;
                                              }
                                     ?>
                                         <?php if(!empty($latitude)): ?>
                                            <?php $last_location_update_time = convertTimeToUSERzone($updated, $driver->CountryArea->timezone, null, $driver->Merchant); ?>
                                            <a class="map_address hyperLink " target="_blank"
                                               href="https://www.google.com/maps/place/<?php echo e($latitude); ?>,<?php echo e($longitude); ?>">
                                                <?php echo $last_location_update_time; ?>

                                            </a>

                                         <?php else: ?>
                                          <?php echo app('translator')->get("$string_file.workshop_area_not_found"); ?>
                                         <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(!empty($driver->ats_id) && !empty($socket_enable) && $driver->ats_id !="NA"): ?>
                                            <button class="badge badge-info border-0 view_current_location"
                                                    id="<?php echo e($driver->ats_id); ?>" driver_id="<?php echo e($driver->id); ?>"
                                                    timezone="<?php echo e($driver->CountryArea->timezone); ?>"><?php echo app('translator')->get("$string_file.current_location"); ?></button>
                                            <div id="<?php echo e($driver->id); ?>"></div>
                                        <?php else: ?>
                                            <?php if(!empty($driver->current_latitude)): ?>
                                                <?php $last_location_update_time = convertTimeToUSERzone($driver->last_location_update_time, $driver->CountryArea->timezone, null, $driver->Merchant); ?>
                                                <a class="map_address hyperLink " target="_blank"
                                                   href="https://www.google.com/maps/place/<?php echo e($driver->current_latitude); ?>,<?php echo e($driver->current_longitude); ?>">
                                                    <?php echo $last_location_update_time; ?>

                                                </a>
                                            <?php else: ?>
                                                ----------
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="button-margin">
                                        <?php if(Auth::user('merchant')->can('edit_drivers')): ?>
                                            <a href="<?php echo e(route('driver.add',$driver->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                <i class="wb-edit"></i> </a>
                                        <?php endif; ?>

                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        

                                        <?php if($config->subscription_package == 1 && Auth::user('merchant')->can('view_drivers')): ?>
                                            <a href="<?php echo e(route('driver.activated_subscription',$driver->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.active_subscription"); ?>"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-info menu-icon btn_edit action_btn">
                                                <i class="wb-indent-increase"></i> </a>
                                        <?php endif; ?>

                                        <?php if(Auth::user('merchant')->can('create_promotion')): ?>
                                            <span data-target="#sendNotificationModel"
                                                  data-toggle="modal"
                                                  id="<?php echo e($driver->id); ?>"><a
                                                        data-original-title="<?php echo app('translator')->get("$string_file.send_notification"); ?>"
                                                        data-toggle="tooltip"
                                                        id="<?php echo e($driver->id); ?>"
                                                        data-placement="top"
                                                        class="btn  text-white btn-sm btn-warning menu-icon btn_eye action_btn">
                                                    <i class="wb-bell"></i> </a></span>
                                        <?php endif; ?>

                                        <?php if($config->driver_wallet_status == 1 && Auth::user('merchant')->can('delete_drivers')): ?>
                                            <a onclick="AddWalletMoneyMod(this)"
                                               data-ID="<?php echo e($driver->id); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.add_money"); ?>"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn text-white btn-sm btn-success">
                                                <i class="fa fa-money"></i> </a>
                                            <a href="<?php echo e(route('merchant.driver.wallet.show',$driver->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.wallet_transaction"); ?>"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm menu-icon btn-primary btn_money action_btn">
                                                <span class="icon fa-window-maximize"></span></a>
                                        <?php endif; ?>

                                        <?php if(Auth::user('merchant')->can('view_drivers')): ?>
                                            <a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.view_profile"); ?>"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-info menu-icon btn_detail action_btn">
                                                <span class="wb-user"></span></a>
                                        <?php endif; ?>

                                        <?php if($driver->segment_group_id == 1 && Auth::user('merchant')->can('view_drivers')): ?>
                                            <a href="<?php echo e(route('merchant.driver-vehicle',$driver->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.view_vehicles"); ?>"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                                <i class="fa fa-car"></i></a>
                                        <?php endif; ?>
                                        <?php if($driver->driver_admin_status == 1 && Auth::user('merchant')->can('edit_drivers')): ?>
                                            <a href="<?php echo e(route('merchant.driver.active.deactive',['id'=>$driver->id,'status'=>2])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                <i class="fa fa-eye-slash"></i> </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('merchant.driver.active.deactive',['id'=>$driver->id,'status'=>1])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($driver->login_logout == 1 && Auth::user('merchant')->can('edit_drivers')): ?>
                                            <a href="<?php echo e(route('merchant.driver.logout',$driver->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.logout"); ?>"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-secondary menu-icon btn_delete action_btn">
                                                <i
                                                        class="fa fa-sign-out"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if(Auth::user()->demo != 1): ?>
                                            <?php if(Auth::user('merchant')->can('delete_drivers')): ?>
                                                <button onclick="DeleteEvent(<?php echo e($driver->id); ?>)"
                                                        type="submit"
                                                        data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                                    <i
                                                            class="fa fa-trash"></i></button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($config->stripe_connect_enable) && $config->stripe_connect_enable == 1): ?>
                                            <a href="<?php echo e(route('merchant.driver.stripe_connect',$driver->id)); ?>"
                                               data-original-title="Stripe Connect View"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-secondary menu-icon action_btn">
                                                <i class="icon wb-grid-9"></i>
                                            </a>
                                            <a href="<?php echo e(route('merchant.driver.stripe_connect.sync',$driver->id)); ?>"
                                               data-original-title="Stripe Connect Sync"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-secondary menu-icon action_btn">
                                                <i class="wb wb-refresh"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php $sr++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="sendNotificationModel" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.send_notification"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('merchant.sendsingle-driver')); ?>" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.title"); ?>: </label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="title"
                                   name="title"
                                   placeholder="" required>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.message"); ?>: </label>
                        <div class="form-group">
                           <textarea class="form-control" id="message" name="message"
                                     rows="3"
                                     placeholder=""></textarea>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.image"); ?>: </label>
                        <div class="form-group">
                            <input type="file" class="form-control" id="image"
                                   name="image"
                                   placeholder="">
                            <input type="hidden" name="persion_id" id="persion_id">
                        </div>
                        <label><?php echo app('translator')->get("$string_file.show_in_promotion"); ?>
                            : </label>
                        <div class="form-group">
                            <input type="checkbox" value="1" name="expery_check"
                                   id="expery_check_two">
                        </div>

                        <label><?php echo app('translator')->get("$string_file.expire_date"); ?>:</label>
                        <div class="form-group">
                            <input type="text" class="form-control customDatePicker1"
                                   id="datepicker-backend" name="date"
                                   placeholder="" disabled readonly>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.url"); ?> (<?php echo app('translator')->get("$string_file.optional"); ?>): </label>
                        <div class="form-group">
                            <input type="url" class="form-control" id="url"
                                   name="url"
                                   placeholder="">
                            <label class="danger"><?php echo app('translator')->get("$string_file.example"); ?> : https://www.google.com/</label>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="<?php echo app('translator')->get("$string_file.send"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="addWalletMoneyModel" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.add_money_in_driver_wallet"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>

                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.payment_method"); ?>: </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1"><?php echo app('translator')->get("$string_file.cash"); ?></option>
                                <option value="2"><?php echo app('translator')->get("$string_file.non_cash"); ?></option>
                            </select>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.receipt_number"); ?>: </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" id="receipt_number" placeholder="" class="form-control" required>
                        </div>

                        <label for="transaction_type">
                            <?php echo app('translator')->get("$string_file.transaction_type"); ?><span
                                    class="text-danger">*</span>
                        </label>
                        <div class="form-group">
                            <select id="transaction_type" name="transaction_type" class="form-control" required>
                                <option value="1"><?php echo app('translator')->get("$string_file.credit"); ?></option>
                                <option value="2"><?php echo app('translator')->get("$string_file.debit"); ?></option>
                            </select>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.amount"); ?>: </label>
                        <div class="form-group">
                            <input type="number" name="amount" id="amount" placeholder="<?php echo app('translator')->get("$string_file.amount"); ?>"
                                   class="form-control" required>
                        </div>
                        <input type="hidden" name="add_money_driver_id" id="add_money_driver_id">


                        <label><?php echo app('translator')->get("$string_file.description"); ?>: </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" id="add_money_button" class="btn btn-primary"
                               value="<?php echo app('translator')->get("$string_file.save"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>

    <script>
        $('#export-excel').on('click', function () {
            var action = '<?php echo e(route("excel.driver")); ?>';
            var arr_param = [];
            var arr_param = $("#driver-search").serializeArray();
            $.ajax({
                type: "GET",
                data: {arr_param},
                url: action,
                success: function (data) {
                    console.log(data);
                }, error: function (err) {
                }
            });
        });
        $('#add_money_button').on('click', function () {
            $('#add_money_button').prop('disabled', true);
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
            var token = $('[name="_token"]').val();
            var payment_method = document.getElementById('payment_method').value;
            var receipt_number = document.getElementById('receipt_number').value;
            var amount = document.getElementById('amount').value;
            var transaction_type = document.getElementById('transaction_type').value;
            var desc = document.getElementById('title1').value;
            var driver_id = document.getElementById('add_money_driver_id').value;
            if (amount > 0) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: "POST",
                    data: {
                        payment_method_id: payment_method,
                        receipt_number: receipt_number,
                        amount: amount,
                        transaction_type: transaction_type,
                        description: desc,
                        driver_id: driver_id
                    },
                    url: "<?php echo e(route('merchant.AddMoney')); ?>",
                    success: function (data) {
                        console.log(data);
                        if (data.result == 1) {
                            $('#myLoader').removeClass('d-flex');
                            $('#myLoader').addClass('d-none');
                            swal({
                                title: "<?php echo app('translator')->get("$string_file.driver_account"); ?>",
                                text: "<?php echo app('translator')->get("$string_file.money_added_successfully"); ?>",
                                icon: "success",
                                buttons: true,
                                dangerMode: true,
                            }).then((isConfirm) => {
                                if (isConfirm) {
                                    window.location.href = "<?php echo e(route('driver.index')); ?>";
                                } else {
                                    window.location.href = "<?php echo e(route('driver.index')); ?>";
                                }
                            });
                        }
                    }, error: function (err) {
                        $('#myLoader').removeClass('d-flex');
                        $('#myLoader').addClass('d-none');
                    }
                });
            } else {
                $('#amount_error').removeClass('d-none');
                $('#add_money_button').prop('disabled', false);
            }

        });

        function AddWalletMoneyMod(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #add_money_driver_id").val(ID);
            $('#addWalletMoneyModel form')[0].reset();
            $('#amount_error').addClass('d-none');
            $('#addWalletMoneyModel').modal('show');
        }

    </script>

    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "<?php echo app('translator')->get("$string_file.are_you_sure"); ?>",
                text: "<?php echo app('translator')->get("$string_file.delete_warning"); ?>",
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
                        url: "<?php echo e(route('driverDelete')); ?>",
                    }).done(function (data) {
                        swal({
                            title: "<?php echo app('translator')->get("$string_file.deleted"); ?>",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "<?php echo e(route('driver.index')); ?>";
                    });
                } else {
                    swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                }
            });
        }

        function selectSearchFields() {
            var segment_id = $('#segment_id').val();
            var area_id = $('#area_id').val();
            var by = $('#by_param').val();
            var by_text = $('#keyword').val();
            if (segment_id.length == 0 && area_id == "" && by == "" && by_text == "" && driver_status == "") {
                alert("Please select at least one search field");
                return false;
            } else if (by != "" && by_text == "") {
                alert("Please enter text according to selected parameter");
                return false;
            } else if (by_text != "" && by == "") {
                alert("Please select parameter according to entered text");
                return false;
            }
        }

        // get location
        $('.view_current_location').on('click', function () {
            var ats_id = $(this).attr("id");
            var driver_id = $(this).attr("driver_id");
            var timezone = $(this).attr("timezone");

            if (ats_id == "" || ats_id == "NA") {
                alert("<?php echo app('translator')->get("$string_file.ats_id_error"); ?>");
                return true;
            }
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: "POST",
                data: {
                    "ats_id": ats_id,
                    "driver_timezone": timezone,
                },
                url: "<?php echo e(route('merchant.get-lat-long')); ?>",
                success: function (data) {
                    console.log(data);
                    $("#" + driver_id).html(data);
                }, error: function (err) {
                    $("#" + driver_id).text("No Data");
                }
            });
        });

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/index.blade.php ENDPATH**/ ?>