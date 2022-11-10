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
                        <?php if(Auth::user('merchant')->can('create_rider')): ?>
                            <a href="<?php echo e(route('excel.user',$data['export_search'])); ?>" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                            <a href="<?php echo e(route('users.create')); ?>">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="<?php echo app('translator')->get("$string_file.add_user"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="wb-users" aria-hidden="true"></i>
                       <?php echo app('translator')->get("$string_file.user_management"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="<?php echo e(route('merchant.user.search')); ?>" method="get">
                        <div class="table_search row p-3">
                            <div class="col-md-2 col-xs-6 active-margin-top"><?php echo app('translator')->get("$string_file.search_by"); ?> :</div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <select class="form-control" name="parameter"
                                            id="parameter"
                                            required>
                                        <option value="1"><?php echo app('translator')->get("$string_file.name"); ?></option>
                                        <option value="2"><?php echo app('translator')->get("$string_file.email"); ?></option>
                                        <option value="3"><?php echo app('translator')->get("$string_file.phone"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" name="keyword"
                                           placeholder="<?php echo app('translator')->get("$string_file.enter_text"); ?>"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <select class="form-control" name="country_id"
                                            id="country_id">
                                        <option value="">--<?php echo app('translator')->get("$string_file.country"); ?>--</option>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>"> <?php echo e($country->CountryName); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"><i
                                            class="fa fa-search"
                                            aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                            <?php if($config->gender == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.gender"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.service_statistics"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.wallet_money"); ?></th>

                            <th><?php echo app('translator')->get("$string_file.signup_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $users->firstItem() ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?>  </td>
                                <td><?php echo e($user->user_merchant_id); ?></td>
                                <?php if(Auth::user()->demo == 1): ?>
                                    <td>
                                        <span class="long_text">   <?php echo nl2br("********".substr($user->last_name, -2)."\n"."********".substr($user->UserPhone, -2)."\n"."********".substr($user->email, -2)); ?></span>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <span class="long_text">   <?php echo nl2br($user->first_name." ".$user->last_name."\n".$user->UserPhone."\n".$user->email); ?></span>
                                    </td>
                                <?php endif; ?>
                                <?php if($config->gender == 1): ?>
                                    <?php switch($user->user_gender):
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
                                    <?php if($user->total_trips): ?>
                                        <?php echo e($user->total_trips); ?>  <?php echo app('translator')->get("$string_file.rides"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.no_ride"); ?>
                                    <?php endif; ?>
                                    <br>
                                    <?php echo app('translator')->get("$string_file.rating"); ?> :
                                    <?php if(!empty($user->rating) && $user->rating > 0): ?>
                                        <?php while($user->rating>0): ?>
                                            <?php if($user->rating >0.5): ?>
                                                <img src="<?php echo e(view_config_image("static-images/star.png")); ?>"
                                                     alt='Whole Star'>
                                            <?php else: ?>
                                                <img src="<?php echo e(view_config_image('static-images/halfstar.png')); ?>"
                                                     alt='Half Star'>
                                            <?php endif; ?>
                                            <?php $user->rating--; ?>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                     <?php echo app('translator')->get("$string_file.not_rated_yet"); ?>
                                    <?php endif; ?>
                                </td>
                                <td><a class="hyperLink"
                                       href="<?php echo e(route('merchant.user.wallet',$user->id)); ?>" j>
                                        <?php if($user->wallet_balance): ?>
                                            <?php echo e($user->wallet_balance); ?>

                                        <?php else: ?>
                                            0.00
                                        <?php endif; ?>
                                    </a>
                                </td>
                                
                                <td>
                                    <?php echo app('translator')->get("$string_file.user_type"); ?> :
                                    <?php if($user->user_type == 1): ?>
                                     <?php echo app('translator')->get("$string_file.corporate_user"); ?>
                                    <?php else: ?>
                                     <?php echo app('translator')->get("$string_file.retail"); ?>
                                    <?php endif; ?>
                                    <br>
                                        <?php echo app('translator')->get("$string_file.signup_type"); ?> :
                                    <?php switch($user->UserSignupType):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.app"); ?>/<?php echo app('translator')->get("$string_file.admin"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.google"); ?>
                                        <?php break; ?>
                                        <?php case (3): ?>
                                        <?php echo app('translator')->get("$string_file.facebook"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                    <br>
                                    <?php echo app('translator')->get("$string_file.signup_from"); ?> :
                                    <?php switch($user->UserSignupFrom):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.application"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.admin"); ?>
                                        <?php break; ?>
                                        <?php case (3): ?>
                                        <?php echo app('translator')->get("$string_file.web"); ?>
                                        <?php break; ?>
                                        <?php case (4): ?>
                                        <?php echo app('translator')->get("$string_file.whatsapp"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php if(isset($user->CountryArea->timezone)): ?>
                                        <?php echo convertTimeToUSERzone($user->created_at, $user->CountryArea->timezone, null, $user->Merchant); ?>

                                    <?php else: ?>
                                        <?php echo convertTimeToUSERzone($user->created_at, null, null, $user->Merchant); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($user->UserStatus == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="button-margin">
                                        <?php if(Auth::user('merchant')->can('edit_rider')): ?>
                                            <a href="<?php echo e(route('users.edit',$user->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('users.show',$user->id)); ?>"
                                           class="btn btn-sm btn-info menu-icon btn_delete action_btn"
                                           data-original-title="<?php echo app('translator')->get("$string_file.details"); ?>"
                                           data-toggle="tooltip"
                                           data-placement="top"><span class="fa fa-user"></span>
                                        </a>
                                        <?php if(Auth::user('merchant')->can('edit_rider')): ?>
                                            <?php if($user->UserStatus == 1): ?>
                                                <a href="<?php echo e(route('merchant.user.active-deactive',['id'=>$user->id,'status'=>2])); ?>"
                                                   title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip" data-placement="top"
                                                   class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                    <i class="fa fa-eye-slash"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('merchant.user.active-deactive',['id'=>$user->id,'status'=>1])); ?>"
                                                   title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip" data-placement="top"
                                                   class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(Auth::user()->demo != 1): ?>
                                            <?php if(Auth::user('merchant')->can('delete_rider')): ?>
                                                <button onclick="DeleteEvent(<?php echo e($user->id); ?>)"
                                                        type="submit" title="<?php echo app('translator')->get("$string_file.delete"); ?>" data-toggle="tooltip"
                                                        data-placement="top" class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                            <?php if(Auth::user('merchant')->can('create_promotion')): ?>

                                                <span data-target="#sendNotificationModelUser"
                                                      data-toggle="modal"
                                                      id="<?php echo e($user->id); ?>"><a
                                                            title="<?php echo app('translator')->get("$string_file.send_notification"); ?>"
                                                            data-toggle="tooltip"
                                                            id="<?php echo e($user->id); ?>"
                                                            data-placement="top"
                                                            class="btn  text-white btn-sm btn-warning menu-icon btn_eye action_btn">
                                                    <i class="wb-bell"></i> </a></span>

                                            <?php endif; ?>
                                            <?php if($config->user_wallet_status == 1): ?>
                                                <span data-target="#addMoneyModel" data-toggle="modal" id="<?php echo e($user->id); ?>">
                                                    <a title="<?php echo app('translator')->get("$string_file.add_money"); ?>"
                                                       id="<?php echo e($user->id); ?>" data-placement="top"
                                                       class="btn text-white btn-sm btn-success menu-icon btn_eye action_btn" role="menuitem">
                                                        <i class="icon fa-money"></i>
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                            <?php if($config->user_wallet_status == 1): ?>
                                                <a href="<?php echo e(route('merchant.user.wallet',$user->id)); ?>"
                                                   title="<?php echo app('translator')->get("$string_file.wallet_transaction"); ?>" data-placement="top"
                                                   class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem">
                                                    <i class="icon fa-window-maximize"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('merchant.user.favourite-location',$user->id)); ?>"
                                               class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem"
                                               title="<?php echo app('translator')->get("$string_file.saved_address"); ?>"
                                               data-placement="top"><i class="icon fa fa-location-arrow"></i>
                                            </a>
                                            <?php if(isset($merchant) && $merchant->ApplicationConfiguration->favourite_driver_module == 1): ?>
                                                <a href="<?php echo e(route('merchant.user.favourite-driver',$user->id)); ?>"
                                                   class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem"
                                                   title="<?php echo app('translator')->get("$string_file.favourite_drivers"); ?>" data-placement="top">
                                                    <i class="icon fa fa-id-card"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if($config->user_document == 1): ?>
                                                <a href="<?php echo e(route('merchant.user.documents',['id'=>$user->id])); ?>"
                                                   title="<?php echo app('translator')->get("$string_file.documents"); ?>" data-placement="top"
                                                   class="btn text-white btn-sm btn-info menu-icon btn_eye action_btn" role="menuitem">
                                                    <i class="icon fa fa-file"></i></a>
                                            <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $users, 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="sendNotificationModelUser" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.send_notification"); ?> </b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('merchant.sendsingle-user')); ?>" enctype="multipart/form-data" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.title"); ?> </label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="title"
                                   name="title"
                                   placeholder="" required>
                        </div>

                        <label><?php echo app('translator')->get("$string_file.message"); ?> </label>
                        <div class="form-group">
                           <textarea class="form-control" id="message" name="message"
                                     rows="3"
                                     placeholder=""></textarea>
                        </div>
                        <label><?php echo app('translator')->get("$string_file.image"); ?> </label>
                        <div class="form-group">
                            <input type="file" class="form-control" id="image"
                                   name="image"
                                   placeholder="<?php echo app('translator')->get("$string_file.image"); ?>">
                            <input type="hidden" name="persion_id" id="persion_id" required>
                        </div>
                        <label><?php echo app('translator')->get("$string_file.show_in_promotion"); ?> </label>
                        <div class="form-group">
                            <input type="checkbox" value="1" name="expery_check"
                                   id="expery_check_two">
                        </div>
                        <label><?php echo app('translator')->get("$string_file.expire_date"); ?> </label>
                        <div class="input-group">
                            <input type="text" class="form-control customDatePicker1 bg-this-color"
                                   id="datepicker" name="date" readonly
                                   placeholder="">
                        </div>

                        <label><?php echo app('translator')->get("$string_file.url"); ?> </label>
                        <div class="form-group">
                            <input type="url" class="form-control" id="url"
                                   name="url"
                                   placeholder="<?php echo app('translator')->get("$string_file.url"); ?>(<?php echo app('translator')->get("$string_file.optional"); ?>)">
                            <label class="danger"><?php echo app('translator')->get("$string_file.example"); ?> :  https://www.google.com/</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal" value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-outline-primary btn" value="<?php echo app('translator')->get("$string_file.send"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade text-left" id="addMoneyModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.add_money_in_wallet"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('merchant.user.add.wallet')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.payment_method"); ?> </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1"><?php echo app('translator')->get("$string_file.cash"); ?></option>
                                <option value="2"><?php echo app('translator')->get("$string_file.non_cash"); ?></option>
                            </select>
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

                        <label><?php echo app('translator')->get("$string_file.amount"); ?> </label>
                        <div class="form-group">
                            <input type="text" name="amount" placeholder=""
                                   class="form-control" required>
                            <input type="hidden" name="add_money_user_id" id="add_money_driver_id">
                        </div>

                        <label><?php echo app('translator')->get("$string_file.receipt_number"); ?> </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" placeholder=""
                                   class="form-control" required>
                        </div>
                        <label><?php echo app('translator')->get("$string_file.description"); ?> </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" id="sub" class="btn btn-primary" value="<?php echo app('translator')->get("$string_file.save"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $('#sub').on('click', function () {
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
        });
    </script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
                        type: "GET",
                        url: "<?php echo e(route('merchant.user.delete')); ?>/" + id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "<?php echo e(route('users.index')); ?>";
                    });
                } else {
                    swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/user/index.blade.php ENDPATH**/ ?>