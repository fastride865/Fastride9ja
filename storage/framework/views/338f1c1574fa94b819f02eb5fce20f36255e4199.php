<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content container-fluid">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('referral-system')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                                <?php echo app('translator')->get("$string_file.add_referral_system"); ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" enctype="multipart/form-data" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="<?php echo e(route('referral-system.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="location3"><?php echo app('translator')->get("$string_file.country"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select("country_id",add_blank_option($countries),old("country_id"),
                                    ["class"=>"form-control select2 search-input","id"=>"country_id",
                                    "onchange"=>"getAreaList(this)","required"=>true]); ?>

                                    <?php if($errors->has('country_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('country_id')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="country_area_id"><?php echo app('translator')->get("$string_file.area"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select("country_area_id",add_blank_option([]),old("country_area_id"),["class"=>"form-control select2 search-input","id"=>"country_area_id","required"=>true]); ?>

                                    <?php if($errors->has('country_area_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('country_area_id')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="application">
                                        <?php echo app('translator')->get("$string_file.referral_for"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select("application",add_blank_option([1=>trans("$string_file.user"),2=>trans("$string_file.driver")]),old("application"),["class"=>"form-control select2 search-input","id"=>"application"]); ?>

                                    <?php if($errors->has('application')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('application')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary check-referral"
                                        onclick="checkReferralSystem()"><i
                                            class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.check_referral"); ?>
                                </button>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-primary" onclick="resetReferralSystem()"><i
                                            class="fa fa-close"></i> <?php echo app('translator')->get("$string_file.reset"); ?>
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row" id="segment_id_div">
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="datepicker"> <?php echo app('translator')->get("$string_file.start_date"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon wb-calendar"
                                                                              aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" class="form-control customDatePicker1" name="start_date"
                                               id="start_date"
                                               value="<?php echo e(old("start_date",isset($referral_system->start_date) ? $referral_system->start_date : "")); ?>"
                                               placeholder="" autocomplete="off" readonly>
                                        <?php if($errors->has('start_date')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('start_date')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="datepicker"><?php echo app('translator')->get("$string_file.end_date"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon wb-calendar"
                                                                              aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" class="form-control customDatePicker1" name="end_date"
                                               id="end_date"
                                               value="<?php echo e(old("end_date",isset($referral_system->end_date) ? $referral_system->end_date : "")); ?>"
                                               placeholder="" autocomplete="off" readonly>
                                        <?php if($errors->has('end_date')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('end_date')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="location3">
                                        <?php echo app('translator')->get("$string_file.discount_applicable"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select("offer_applicable",add_blank_option([1=>trans("$string_file.sender"),2=>trans("$string_file.receiver"),3=>trans("$string_file.both")]),old("offer_applicable",isset($referral_system->offer_applicable) ? $referral_system->offer_applicable : ""),["class"=>"form-control select2","id"=>"offer_applicable","required"]); ?>

                                    <?php if($errors->has('offer_applicable')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('offer_applicable')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="location3"><?php echo app('translator')->get("$string_file.offer_type"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="offer_type" id="offer_type" class="form-control select2" required
                                            onchange="changeOfferType()">
                                        <option value="1"
                                                id="offer_type_fixed_amount"><?php echo app('translator')->get("$string_file.fixed_amount"); ?></option>
                                        <option value="2"
                                                id="offer_type_discount"><?php echo app('translator')->get("$string_file.discount"); ?></option>
                                    </select>
                                    
                                    <?php if($errors->has('offer_type')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('offer_type')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="location3"><?php echo app('translator')->get("$string_file.offer_value"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="offer_value_symbol"></span>
                                        </div>
                                        <input type="number" step=0.01 min=0 class="form-control" id="offer_value"
                                               name="offer_value"
                                               value="<?php echo e(old("offer_value",isset($referral_system->offer_value) ? $referral_system->offer_value : "")); ?>"
                                               placeholder="" autocomplete="off" required/>
                                    </div>
                                    <?php if($errors->has('offer_value')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('offer_value')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="maximum_offer_amount"><?php echo app('translator')->get("$string_file.maximum_offer_amount"); ?>
                                    </label>
                                    <input type="number" step=0.01 min=0 class="form-control" id="maximum_offer_amount"
                                           name="maximum_offer_amount"
                                           value="<?php echo e(old("offer_value",isset($referral_system->maximum_offer_amount) ? $referral_system->maximum_offer_amount : "")); ?>"
                                           placeholder="" autocomplete="off" required/>
                                    <?php if($errors->has('maximum_offer_amount')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('maximum_offer_amount')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"><?php echo app('translator')->get("$string_file.offer_condition"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="offer_condition" class="form-control select2" id="offer_condition"
                                            required>
                                        <?php $__currentLoopData = add_blank_option(getReferralSystemOfferCondition($string_file)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" id="offer_condition_<?php echo e($key); ?>"><?php echo e($item); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="custom-hidden" id="limited_offer_div">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-control-label"
                                               for="limit_usage"><?php echo app('translator')->get("$string_file.no_of_uses"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step=0.01 min=0 class="form-control" id="limit_usage"
                                               name="limit_usage"
                                               placeholder="" autocomplete="off"/>
                                        <?php if($errors->has('limit_usage')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('limit_usage')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-control-label"
                                               for="day_limit"><?php echo app('translator')->get("$string_file.no_of_days"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step=0.01 min=0 class="form-control" id="day_limit"
                                               name="day_limit"
                                               placeholder="" autocomplete="off" required/>
                                        <?php if($errors->has('day_limit')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('day_limit')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-control-label"
                                               for="day_count"><?php echo app('translator')->get("$string_file.days_count_start"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::select("day_count",add_blank_option([1=>trans("$string_file.after_signup"),2=>trans("$string_file.after_financial_transaction")]),old("day_count"),["class"=>"form-control select2","id"=>"day_count"]); ?>

                                        <?php if($errors->has('day_count')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('day_count')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-hidden" id="conditional_offer_driver_div">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-control-label"
                                               for="conditional_no_driver"><?php echo app('translator')->get("$string_file.no_of_drivers"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step=0.01 min=0 class="form-control"
                                               id="conditional_no_driver"
                                               name="conditional_no_driver"
                                               placeholder="" autocomplete="off"/>
                                        <?php if($errors->has('conditional_no_driver')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('conditional_no_driver')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-control-label"
                                               for="conditional_driver_rule">Rule <?php echo app('translator')->get("$string_file.for_driver"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::select("conditional_driver_rule",add_blank_option(getReferralSystemDriverCondition($string_file)),old("conditional_driver_rule"),["class"=>"form-control select2","id"=>"conditional_driver_rule","onchange" => "ruleForDriver(this)"]); ?>

                                        <?php if($errors->has('conditional_driver_rule')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('conditional_driver_rule')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-control-label"
                                               for="conditional_no_services"><?php echo app('translator')->get("$string_file.no_of_services"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step=0.01 min=0 class="form-control"
                                               id="conditional_no_services"
                                               name="conditional_no_services" disabled
                                               placeholder="" autocomplete="off" required/>
                                        <?php if($errors->has('conditional_no_services')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('conditional_no_services')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right" style="margin-bottom: 1%">
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="currency" value=""/>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function () {
            disableAllControl();
        });

        function limitedOfferCondition(is_display) {
            // console.log("limitedOfferCondition : " + is_display);
            if (is_display == 1) {
                // console.log("Show");
                $('#limited_offer_div').show();
                $('#no_usage').prop('required', 'true');
                $('#day_limit').prop('required', 'true');
                $('#day_count').prop('required', 'true');
            } else {
                // console.log("Hide");
                $('#limited_offer_div').hide();
                $('#no_usage').removeAttr('required');
                $('#day_limit').removeAttr('required');
                $('#day_count').removeAttr('required');
            }
        }

        function driverConditionalOfferCondition(is_display) {
            // console.log("driverConditionalOfferCondition : " + is_display);
            if (is_display == 1) {
                // console.log("Show");
                $('#conditional_offer_driver_div').show();
                $('#conditional_no_driver').prop('required', 'true');
                $('#conditional_driver_rule').prop('required', 'true');
                $('#conditional_no_services').prop('required', 'true');
            } else {
                // console.log("Show");
                $('#conditional_offer_driver_div').hide();
                $('#conditional_no_driver').removeAttr('required');
                $('#conditional_driver_rule').removeAttr('required');
                $('#conditional_no_services').removeAttr('required');
            }
        }

        $(document).on('change', '#offer_condition', function () {
            var offer_condition = $(this).val();
            offerCondition(offer_condition);
            onChangeOfferCondition(offer_condition);
        });

        $(document).on('change', '.search-input', function () {
            enableSearchControl();
            disableAllControl();
            $("#segment_id_div").empty();
            onChangeApplication();
        });

        function offerCondition(offer_condition) {
            // console.log("offer_condition : " + offer_condition);
            limitedOfferCondition(2);
            driverConditionalOfferCondition(2);
            switch (offer_condition) {
                case "1":
                    limitedOfferCondition(1);
                    driverConditionalOfferCondition(2);
                    break;
                case "4":
                    limitedOfferCondition(2);
                    driverConditionalOfferCondition(1);
                    break;
                case "2":
                case "3":
                    limitedOfferCondition(2);
                    driverConditionalOfferCondition(2);
                    break;
            }
        }

        function getAreaList(obj) {
            var id = obj.options[obj.selectedIndex].getAttribute('value');
            $('#country_area_id').empty();
            $("#segment_id_div").empty();
            $("#loader1").show();
            $.ajax({
                method: 'GET',
                url: "<?php echo e(route('merchant.country.arealist')); ?>",
                data: {country_id: id},
                success: function (data) {
                    $('#country_area_id').empty();
                    $('#country_area_id').html(data);
                }
            });
            $("#loader1").hide();
        }

        function getSegments(country_area_id) {
            $("#loader1").show();
            var token = $('[name="_token"]').val();
            $("#segment_id_div").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: '<?php echo route('get.area.segment') ?>',
                data: {area_id: country_area_id, option_type: "CHECK-BOX"},
                success: function (data) {
                    $("#segment_id_div").empty();
                    $('#segment_id_div').html(data);
                }
            });
            $("#loader1").hide();
        }

        function checkReferralSystem() {
            var country_id = $("#country_id").val();
            var country_area_id = $("#country_area_id").val();
            var application = $("#application").val();
            disableAllControl();
            $("#loader1").show();
            $.ajax({
                method: 'GET',
                url: '<?php echo route('referral-system.check-referral') ?>',
                data: {
                    country_area_id: country_area_id,
                    country_id: country_id,
                    application: application
                },
                success: function (data) {
                    if (data.status == "success") {
                        enableAllControl();
                        disableSearchControl();
                        getSegments(country_area_id);
                        $("#currency").val(data.currency);
                    } else {
                        enableSearchControl();
                        disableAllControl();
                        alert("Referral already exist");
                        $("#currency").val(null);
                    }
                }
            });
            $("#loader1").hide();
        }

        function resetReferralSystem() {
            disableAllControl();
            enableSearchControl();
        }

        function disableAllControl() {
            $("#start_date").attr("disabled", true);
            $("#end_date").attr("disabled", true);
            $("#offer_applicable").attr("disabled", true);
            $("#offer_applicable").val(null).trigger("change");
            $("#offer_type").attr("disabled", true);
            $("#offer_type").val(null).trigger("change");
            $("#offer_value").attr("disabled", true);
            $("#maximum_offer_amount").attr("disabled", true);
            $("#offer_condition").attr("disabled", true);
            $("#offer_condition").val(null).trigger("change");
            offerCondition("NA");
        }

        function enableAllControl() {
            $("#start_date").attr("disabled", false);
            $("#end_date").attr("disabled", false);
            $("#offer_applicable").attr("disabled", false);
            $("#offer_applicable").val(null).trigger("change");
            $("#offer_type").attr("disabled", false);
            $("#offer_type").val(null).trigger("change");
            $("#offer_value").attr("disabled", false);
            $("#offer_condition").attr("disabled", false);
            $("#offer_condition").val(null).trigger("change");
            offerCondition("NA");
        }

        function disableSearchControl() {
            $(".check-referral").attr("disabled", true);
        }

        function enableSearchControl() {
            $(".check-referral").attr("disabled", false);
        }

        function changeOfferType() {
            var offer_type = $("#offer_type").val();
            if (offer_type == 2) {
                $("#maximum_offer_amount").attr("disabled", false);
                $("#offer_value_symbol").html("%");
            } else if (offer_type == 1) {
                $("#maximum_offer_amount").attr("disabled", true);
                $("#offer_value_symbol").html($("#currency").val());
            }else{
                $("#maximum_offer_amount").attr("disabled", true);
            }
        }

        function onChangeApplication() {
            var id = $("#application").val();
            switch (id) {
                case "1":
                    $('#offer_condition_4').prop('disabled', true);
                    $('#application').select2();
                    break;
                case "2":
                    $('#offer_condition_4').prop('disabled', false);
                    $('#application').select2();
                    break;
            }
        }

        function ruleForDriver(obj) {
            var offer_type = obj.options[obj.selectedIndex].getAttribute('value');
            if (offer_type == 3) {
                $("#conditional_no_services").attr("disabled", false);
            } else {
                $("#conditional_no_services").attr("disabled", true);
            }
            onChangeConditionalDriverRule(offer_type);
        }

        function onChangeOfferCondition(offer_condition) {
            switch (offer_condition) {
                case "3":
                    $('#offer_type_discount').prop('disabled', true);
                    $('#offer_type').select2();
                    break;
                default:
                    $('#offer_type_discount').prop('disabled', false);
                    $('#offer_type').select2();
                    break;
            }
            $("#offer_type").val(null).trigger("change");
        }

        function onChangeConditionalDriverRule(offer_condition) {
            switch (offer_condition) {
                case "3":
                    $('#offer_type_discount').prop('disabled', false);
                    $('#offer_type').select2();
                    break;
                default:
                    $('#offer_type_discount').prop('disabled', true);
                    $('#offer_type').select2();
                    break;
            }
            $("#offer_type").val(null).trigger("change");
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/referral_system/create.blade.php ENDPATH**/ ?>