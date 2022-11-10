$(document).ready(function () {
    $("#expery_check_two").on("change", function () {
        if ($(this).is(':checked')) {
            $('#datepicker-backend').prop('disabled', false);
        } else {
            $('#datepicker-backend').prop('disabled', true);
        }
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    }

    $('#country').on('change', function () {
        var phoneCode = $('option:selected', this).attr('data-ISD');
        $("#isd").val(phoneCode);
        // var max = $('option:selected', this).attr('data-max');
        // var min = $('option:selected', this).attr('data-min');
        // $("#user_phone").attr({'maxlength': max});
        // $("#user_phone").attr({'minlength': min});

    });

    //$('.mytimepicker').clockTimePicker();

    $('.mytimepicker').clockpicker({
        uiLibrary: 'materialdesign',
    });

    $('.mytimepicker-pricecard-start').clockpicker({
        uiLibrary: 'materialdesign',
    });

    $('.mytimepicker-pricecard-end').clockpicker({
        uiLibrary: 'materialdesign'
    });

    $('#resetForm').on('click', function () {
        window.location.reload(true);
    });
    $('#settlementBill').on('show.bs.modal', function (e) {
        let $modal = $(this),
            esseyId = e.relatedTarget.id;
        $modal.find('#bill_id').val(esseyId);
    });
    $('#sendNotificationModel').on('show.bs.modal', function (e) {
        let $modal = $(this),
            esseyId = e.relatedTarget.id;
        $modal.find('#persion_id').val(esseyId);
    });
    $('#sendNotificationModelUser').on('show.bs.modal', function (e) {
        let $modal = $(this),
            esseyId = e.relatedTarget.id;
        $modal.find('#persion_id').val(esseyId);
    });
    $('#cancelbooking').on('show.bs.modal', function (e) {
        let $modal = $(this),
            esseyId = e.relatedTarget.id;
        $modal.find('#booking_id').val(esseyId);
    });

    $('#addMoneyModel').on('show.bs.modal', function (e) {
        let $modal = $(this),
            esseyId = e.relatedTarget.id;
        $modal.find('#add_money_driver_id').val(esseyId);
    });

    $("#removeButton").on("click", function () {
        $(this).parent().parent().remove();
    });

    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        onRender: function (date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
        }
    }).on("changeDate", function(dateText) {
        if ($.isFunction(ForManual)) {
            ForManual(this.value);
        }
        // console.log('Change Event');
        // console.log("Selected date: " + dateText + ", Current Selected Value= " + this.value);
    });

    $('.docs_datepicker').datepicker({
        format: 'yyyy-mm-dd',
        onRender: function (date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
        }
    });

    $('.datepickersearch').datepicker({
        format: 'yyyy-mm-dd',
        onRender: function (date) {
            return date.valueOf() > now.valueOf() ? 'disabled' : '';
        }
    });

    $(".openPopup").click(function () {
        var data = $(this).attr('data-href');
        var number = $(this).attr('data-id');
        $.ajax({
            method: 'GET',
            url: "transactions/billdetails",
            data: {booking_id: number},
            success: function (dataResponse) {
                let data1 = JSON.parse(dataResponse);
                var table = document.createElement('table');
                table.className = "table display nowrap table-striped table-bordered scroll-horizontal";
                for (var i in data1) {
                    var parameter = data1[i].name;
                    var amount = data1[i].amount;
                    var tr = document.createElement('tr');
                    var td1 = document.createElement('th');
                    var td2 = document.createElement('td');
                    var text1 = document.createTextNode(parameter);
                    var text2 = document.createTextNode(amount);
                    td1.appendChild(text1);
                    td2.appendChild(text2);
                    tr.appendChild(td1);
                    tr.appendChild(td2);

                    table.appendChild(tr);
                }
                $('#detailBooking').modal({show: true});
                $('#detailBooking').find('.modal-body').html(table);
            }
        });


    });

    $(".select2").select2();

    $('select[name=first_logic]').on('change', function () {
        var self = this;
        $('select[name=second_logic]').find('option').prop('disabled', function () {
            return this.value == self.value
        });
    });


    $('select[name=second_logic]').on('change', function () {
        var firstVal = $('#first_logic').find(":selected").attr("id");
        var secondval = $('#second_logic').find(":selected").attr("id");
        $("select#third_logic option").prop('disabled', false).filter("#" + firstVal + " ,#" + secondval).prop('disabled', true);
    });


    $('select[name=third_logic]').on('change', function () {
        var firstVal = $('#first_logic').find(":selected").attr("id");
        var secondval = $('#second_logic').find(":selected").attr("id");
        var thirdval = $('#third_logic').find(":selected").attr("id");
        $("select#fourth_logic option").prop('disabled', false).filter("#" + firstVal + " ,#" + secondval + ",#" + thirdval).prop('disabled', true);
    });



    $('#cancel_charges').on('change', function () {
        if (this.value == "1") {
            $("#cancel_first").show();
            $("#cancel_second").show();
            $("#cancel_third").show();
        } else {
            $("#cancel_first").hide();
            $("#cancel_second").hide();
            $("#cancel_third").hide();
        }
    });

    /*$("#ride_radius_driver").on('change', function () { });*/
    $("#ride_radius_driver").blur(function () {
        var radius = this.value;
        var pickup_latitude = $('[name="pickup_latitude"]').val();
        var manual_area = $('[name="manual_area"]').val();
        var pickup_longitude = $('[name="pickup_longitude"]').val();
        var drop_latitude = $('[name="drop_latitude"]').val();
        var drop_longitude = $('[name="drop_longitude"]').val();
        var service = $('[name="service"]').val();
        var vehicle_type = $('[name="vehicle_type"]').val();
        var driver_gender = $('[name="driver_gender"]').val();
        var distance_unit = document.getElementById("distance_unit").value;
        var riders_num = '';
        if(document.getElementById('riders_num')){
            riders_num = document.getElementById('riders_num').value;
        }

        if (pickup_latitude == "" && drop_latitude == "") {
            alert("Enter Pickup And Drop Location");
            //$('#ride_radius_driver').prop('selectedIndex', 0);
            $('#ride_radius_driver').val(0);
            return false;
        } else {
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "checkDriver",
                data: {
                    manual_area: manual_area,
                    radius: radius,
                    pickup_latitude: pickup_latitude,
                    pickup_longitude: pickup_longitude,
                    drop_latitude: drop_latitude,
                    drop_longitude: drop_longitude,
                    service: service,
                    vehicle_type: vehicle_type,
                    driver_gender: driver_gender,
                    distance_unit: distance_unit,
                    riders_num: riders_num
                },
                success: function (data) {
                    $('#number_of_drivers').text(data)
                }
            });
        }
    });

    $("#check_ride_estimate").click(function () {
        var area = $('[name="area"]').val();
        var distance = $('[name="distance"]').val();
        var distance_unit = $('[name="distance_unit"]').val();
        var ride_time = $('[name="ride_time"]').val();
        var service = $('[name="service"]').val();
        var vehicle_type = $('[name="vehicle_type"]').val();
        if (service == 2 || service == 3) {
            if (service == "" || vehicle_type == "" || area == "") {
                alert("Required Field Missing");
                return false;
            }
        } else {
            if (distance == "" || ride_time == "" || service == "" || vehicle_type == "" || area == "") {
                alert("Required Field Missing");
                return false;
            }
        }
        var token = $('[name="_token"]').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': token
            },
            method: 'POST',
            url: "estimatePrice",
            data: {
                distance: distance,
                ride_time: ride_time,
                service: service,
                vehicle_type: vehicle_type,
                area: area,
                distance_unit : distance_unit
            },
            success: function (data) {
                var estimate = "Fare Estimate: " + data;
                $('#estimate_fare_ride').html(estimate);
                $('#estimate_fare').val(data);
            }
        });

    });

    // $("#manual_area").on('change', function () {
    //     var area_id = this.value;
    //     if (area_id != "") {
    //         var token = $('[name="_token"]').val();
    //         $.ajax({
    //             headers: {
    //                 'X-CSRF-TOKEN': token
    //             },
    //             method: 'POST',
    //             url: "getServices",
    //             data: {area_id: area_id},
    //             success: function (data) {
    //                 $("#service").html(data);
    //             }
    //         });
    //     }
    // });


    $('#manual_user_type').on('change', function () {
        var user = this.value;
        if (user == 1) {
            $("#gender_new_div").show();
            $("#corporate_div").hide();
            $("#email_new_div").show();
            $("#first_name_new_div").show();
            $("#last_name_new_div").show();
            $("#new_user_details_div").show();
            // $("#check_user_details_div").hide();
            $("#full_details_div").hide();
        } else if (user == 2) {
            $("#gender_new_div").hide();
            $("#corporate_div").hide();
            $("#email_new_div").hide();
            $("#first_name_new_div").hide();
            $("#last_name_new_div").hide();
            $("#new_user_details_div").hide();
            // $("#check_user_details_div").show();
            $("#full_details_div").hide();
        } else {
            $("#gender_new_div").hide();
            $("#corporate_div").show();
            $("#email_new_div").hide();
            $("#first_name_new_div").hide();
            $("#last_name_new_div").hide();
            $("#new_user_details_div").hide();
            // $("#check_user_details_div").show();
            $("#full_details_div").hide();
        }
    });

    $("#new_user_details").click(function () {
        var phone_code = $('option:selected','[name="country"]').attr('data-phone');
        var country_id = $('[name="country"]').val();
        var new_user_first_name = $('[name="new_user_first_name"]').val();
        var new_user_last_name = $('[name="new_user_last_name"]').val();
        var gender = $('[name="new_user_gender"]').val();
        var new_user_phone = $('[name="user_phone"]').val();
        var new_user_email = $('[name="new_user_email"]').val();
        if (new_user_first_name === "" || new_user_last_name === "" || new_user_phone === "" || gender === "") {
            swal('Please enter Rider details..');
            return false;
        } else {
            $("#loader1").show();
            var token = $('[name="_token"]').val();
            let fullNumber = phone_code + new_user_phone;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "AddManualUser",
                data: {
                    first_name: new_user_first_name,
                    last_name: new_user_last_name,
                    new_user_phone: fullNumber,
                    new_user_email: new_user_email,
                    gender : gender,
                    country_id : country_id
                },
                success: function (data) {
                    console.log(data);
                    if (data.result == 0){
                        alert(data.message);
                        return false;
                    }
                    $('#user_id').val(data.id);
                    $("#new_user_details").prop('disabled', true);
                    $("#distance_unit").val(data.distance_unit);
                    $("#multi_destination").val(data.multi_destination);
                    $("#isocode").val(data.iso);
                    $("#max__multi_count").val(data.max_multi_count);
                    if (data.user_gender == 1){
                        $('#driver_gender_div').hide();
                    }
                    alert("Rider Register Successfully");
                }, error: function (err) {
                    $('#user_id').val('');
                    var errors = err.responseJSON;
                    alert(errors.message);
                }
            });
            $("#loader1").hide();
        }
    });


    $('#all_driver_booking').click(function () {
        $("#ride_radius_driver").prop('disabled', false);
        $("#ride_radius_manual_driver").prop('disabled', true);
        $("#driver_id").prop('disabled', true);
        $("#favourite_list").prop('disabled', true);
    });

    $('#favourite_driver').click(function () {
        $("#ride_radius_driver").prop('disabled', true);
        $("#driver_id").prop('disabled', true);
        $("#favourite_list").prop('disabled', false);

        var area = $('[name="area"]').val();
        var service = $('[name="service"]').val();
        var vehicle_type = $('[name="vehicle_type"]').val();
        var user_id = $('[name="user_id"]').val();
        var pickup_latitude = $('[name="pickup_latitude"]').val();
        var pickup_longitude = $('[name="pickup_longitude"]').val();
        if (pickup_latitude == "" && pickup_longitude == "") {
            alert("Enter Pickup Location");
            return false;
        } else {
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "getFavouriteDriver",
                data: {
                    manual_area: area,
                    service: service,
                    vehicle_type: vehicle_type,
                    user_id: user_id,
                    pickup_latitude: pickup_latitude,
                    pickup_longitude: pickup_longitude,
                },
                success: function (data) {
                    $('#favourite_list').html(data)
                }
            });
        }

    });
    var x = 1;
    $('#addScnt').click(function () {
        var max_fields = 4;
        var wrapper = $("#multiple_location");
        if (x < max_fields) {
            x++;
            $(wrapper).append('<div class="col-md-8"><div class="form-group"><input type="text" class="form-control" id="drop_location" name="drop_location" placeholder="Enter Drop Location"></div> </div><div class="col-md-4"><div class="form-group"><a href="#" id="remScnt">Remove</a> </div> </div>'); //add input box
        }
    });

    // $('#service').on("change", function () {
    //     var service = $(this).val();
    //     var vehicle_type = $('#vehicle_type').val();
    //     var manual_area = $('#manual_area').val();
    //     var package_id = $('#package').val();
    //     var token = $('[name="_token"]').val();
    //     console.log('vehicle_type :'+vehicle_type);
    //     console.log('Service_Type :'+service);
    //     if (vehicle_type === "" || service === "" || manual_area === "") {
    //         alert("Some Data Missing");
    //         //$('#vehicle_type').prop('selectedIndex', 0);
    //         return false;
    //     }
    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': token
    //         },
    //         method: 'POST',
    //         url: "checkPriceCard",
    //         data: {
    //             vehicle_type: vehicle_type,
    //             service: service,
    //             area: manual_area,
    //             package_id: package_id,
    //         },
    //         success: function (data) {
    //             if (data.result == 0) {
    //                 alert(data.message);
    //                 return false;
    //             }
    //         }
    //     });
    //     $("#loader1").hide();
    // });

    // $('#vehicle_type').on("change", function () {
    //     var vehicle_type = $(this).val();
    //     console.log('vehicle_type :'+vehicle_type);
    //     var service = $('#service').val();
    //     var manual_area = $('#manual_area').val();
    //     var package_id = $('#package').val();
    //     var token = $('[name="_token"]').val();
    //     if (vehicle_type === "" || service === "" || manual_area === "") {
    //         alert("Some Data Missing");
    //         $('#vehicle_type').prop('selectedIndex', 0);
    //         return false;
    //     }
    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': token
    //         },
    //         method: 'POST',
    //         url: "checkPriceCard",
    //         data: {
    //             vehicle_type: vehicle_type,
    //             service: service,
    //             area: manual_area,
    //             package_id: package_id,
    //         },
    //         success: function (data) {
    //             //alert(data);
    //             if ($.trim(data)) {
    //             } else {
    //                 alert("No Price Card Found");
    //                 $('#vehicle_type').prop('selectedIndex', 0);
    //                 return false;
    //             }
    //         }
    //     });
    //     $("#loader1").hide();
    // });

    // $('#package').on("change", function () {
    //     var service = $('#service').val();
    //     var manual_area = $('#manual_area').val();
    //     var package_id = $('#package').val();
    //     var token = $('[name="_token"]').val();
    //     if (package_id != "") {
    //         $("#loader1").show();
    //         $.ajax({
    //             headers: {
    //                 'X-CSRF-TOKEN': token
    //             },
    //             method: 'POST',
    //             url: "getVehicle",
    //             data: {
    //                 package_id: package_id,
    //                 service: service,
    //                 area: manual_area,
    //             },
    //             success: function (data) {
    //                 if (data) {
    //                     $("#vehicle_type_id").show();
    //                     $("#vehicle_type").html(data);
    //                 } else {
    //                     alert("No Price Card For This Package");
    //                 }
    //             }
    //         });
    //         $("#loader1").hide();
    //     }
    // });

    $('#payment_method_id').on('change',function () {
        var eta = $('#estimate_fare').val();
        $('#old_eta').val(eta);
    });

    function setFare(){
        var old_eta = $('#old_eta').val();
        var iso = $('#isocode').val();
        var estimate = "Fare Estimate: "+iso+" "+ old_eta;
        $('#estimate_fare_ride').html(estimate);
        $('#estimate_fare').val(old_eta);
    }

    $('#promo_code').on('change',function () {
        var payment_method = $('#payment_method_id').val();
        if (payment_method == ""){
            sweetalert('Please Select Any Payment Method First !!');
            $('#promo_code').prop('selectedIndex', 0);
            return false;
        }
        var code = this.value;
        var promo_id = $('#promo_id').val();
        if (code == ""){
            setFare();
            return false;
        }
        if (code != promo_id){
            setFare();
        }
        var eta = $('#estimate_fare').val();
        $('#promo_id').val(code);
        console.log(code);
        console.log(eta);
        if (eta == ""){
            sweetalert("Estimate Fare is not found");
            $('#promo_code').prop('selectedIndex', 0);
            return false;
        }else{
            var token = $('[name = "_token"]').val();
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : token
                },
                method : 'POST',
                url : 'getPromoCodeEta',
                data : {
                    promocode_id : code,
                    estimate_fare : eta,
                },
                success: function (data) {
                    console.log(data);
                    var iso = document.getElementById("isocode").value;
                    var estimate = "Fare Estimate : "+iso+" "+ data;
                    $('#estimate_fare_ride').html(estimate);
                    $('#estimate_fare').val(data);
                }
            });
        }
    });

    $('#manually_driver').click(function () {
        $("#ride_radius_driver").prop('disabled', true);
        $("#ride_radius_manual_driver").prop('disabled', false);
        $("#driver_id").prop('disabled', false);
        $("#favourite_list").prop('disabled', true);
        var pickup_latitude = $('[name="pickup_latitude"]').val();
        var manual_area = $('[name="area"]').val();
        var pickup_longitude = $('[name="pickup_longitude"]').val();
        var drop_latitude = $('[name="drop_latitude"]').val();
        var drop_longitude = $('[name="drop_longitude"]').val();
        var service = $('[name="service"]').val();
        var vehicle_type = $('[name="vehicle_type"]').val();
        /*if (pickup_latitude == "" && pickup_longitude == "" && service == "" && vehicle_type == "") {
            alert("Required Field Missing")
            return false;
        } else {
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "getallDriver",
                data: {
                    manual_area: manual_area,
                    pickup_latitude: pickup_latitude,
                    pickup_longitude: pickup_longitude,
                    drop_latitude: drop_latitude,
                    drop_longitude: drop_longitude,
                    service: service,
                    vehicle_type: vehicle_type,
                },
                success: function (data) {
                    $('#driver_id').html(data)
                }
            });
        }*/

    });

    /*$("#ride_radius_manual_driver").on('change', function () { });*/
    $("#ride_radius_manual_driver").blur(function () {
        var radius = this.value;
        var pickup_latitude = $('[name="pickup_latitude"]').val();
        var manual_area = $('[name="manual_area"]').val();
        console.log('area_id :'+manual_area);
        var pickup_longitude = $('[name="pickup_longitude"]').val();
        var drop_latitude = $('[name="drop_latitude"]').val();
        var drop_longitude = $('[name="drop_longitude"]').val();
        var service = $('[name="service"]').val();
        var vehicle_type = $('[name="vehicle_type"]').val();
        var driver_gender = $('[name="driver_gender"]').val();
        var distance_unit = document.getElementById("distance_unit").value;
        if (pickup_latitude == "" && drop_latitude == "" && service == "" && vehicle_type == "") {
            alert("Required Field Missing");
            //$('#ride_radius_manual_driver').prop('selectedIndex', 0);
            $('#ride_radius_manual_driver').val(0);
            return false;
        } else {
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "getallDriverForManual",
                data: {
                    radius: radius,
                    manual_area: manual_area,
                    pickup_latitude: pickup_latitude,
                    pickup_longitude: pickup_longitude,
                    drop_latitude: drop_latitude,
                    drop_longitude: drop_longitude,
                    service: service,
                    vehicle_type: vehicle_type,
                    driver_gender : driver_gender,
                    distance_unit: distance_unit,
                },
                success: function (data) {
                    $('#driver_id').html(data)
                }
            });
        }
    });


    // $("#multi_step_form").validate();
    // $('#first_step').click(function () {
    //     var formValida = $("#multi_step_form").valid();
    //     if (formValida == true) {
    //         var token = $('[name="_token"]').val();
    //         var fullname = $('[name="fullname"]').val();
    //         var email = $('[name="email"]').val();
    //         var phone = $('[name="phone"]').val();
    //         var password = $('[name="password"]').val();
    //         var area = $('[name="area"]').val();
    //
    //         $.ajax({
    //             headers: {
    //                         'X-CSRF-TOKEN': token
    //                      },
    //             method:'POST',
    //             url:"firststep",
    //             data: {fullname: fullname,email:email,phone:phone,password:password,area:area},
    //             success:function(data){
    //                // $("#second_step_input").html(data);
    //                 $("#first_step_div").hide();
    //                 $("#second_step_div").show();
    //                 $('#third_step_div').hide();
    //                 $("#second_step_input").after(data);
    //             },error:function(err){
    //                 var errors = err.responseJSON;
    //                 alert(errors.message);
    //             }
    //         });
    //     }
    // });

    // $('#second_step').click(function(){
    //     var formValida = $("#multi_step_form").valid();
    //     if(formValida == true){
    //         var token = $('[name="_token"]').val();
    //         var values = $("input[name='documents[]']").map(function(){return $(this).val();}).get();
    //         alert(values);
    //         // var image = $("input[name='documents[]']")[0].files[0];
    //         // var driver_id = $("input[name='driver_id']").val();
    //         // // console.log(values);
    //         // var form = new FormData();
    //         // form.append('id', driver_id);
    //         // form.append('image', image);
    //         // $.ajax({
    //         //     headers: {
    //         //         'X-CSRF-TOKEN': token
    //         //     },
    //         //     method:'POST',
    //         //     url:"secondstep",
    //         //    // data: $('#multi_step_form').serialize(),
    //         //     data: form,
    //         //     cache:false,
    //         //     contentType: false,
    //         //     processData: false,
    //         //     success:function(data){
    //         //        console.log(data);
    //         //     },error:function(err){
    //         //         console.log(err);
    //         //     }
    //         // });
    //     }
    //
    // });
    var url = window.location;
    $('ul#accordionSidebar a').filter(function () {
        return this.href == url;
        var k = 5;
    }).parent().addClass('active');
    $('ul#accordionSidebar a').filter(function () {
        return this.href == url;
    }).parentsUntil("#accordionSidebar > .treeview-menu").addClass('active');

    /*-------------------Other One -----------------------*/
    var a = window.location.pathname;
    $('#accordionSidebar > li > a[href="http://localhost' + a + '"').parent().addClass("active");
    $('#accordionSidebar ul li').find('a').each(function () {
        var link = new RegExp($(this).attr('href')); //Check if some menu compares inside your the browsers link
        if (link.test(document.location.href)) {
            if (!$(this).parents().hasClass('active')) {
                $(this).parents().addClass("active open");
                $(this).parents('li div a').addClass('open');
                $(this).addClass("active"); //Add this too
            }
        }
    });
    $('.loader1').hide();

    var url = window.location;
    // for sidebar menu entirely but not cover treeview
    $('ul.sidebar-dark a').filter(function () {
        if (this.href == url) {
            $(this).addClass("active");
        }
        return this.href == url;

        var k = 5;
    }).parent().addClass('active');
    // for treeview
    $('div.collapse-inner a').filter(function () {
        return this.href == url;
    }).parentsUntil(".sidebar-dark > .collapse-inner").addClass('show');

    /*-------------------Other One -----------------------*/
    var a = window.location.pathname;
    $('.sidebar-dark > div > a[href="http://localhost' + a + '"').parent().addClass("active");
    $('.collapse div').find('a').each(function () {
        var link = new RegExp($(this).attr('href')); //Check if some menu compares inside your the browsers link
        <!-- alert("a+link: "+a+' '+link); -->
        if (link.test(document.location.href)) {
            <!-- alert("THIS: "+this); -->
            if (!$(this).hasClass('active')) {
                $(this).parents('div').addClass('show');
                $(this).parents().addClass("active");
                //$(this).addClass("active"); //Add this too
            }
        }
    });
    // $(document).ready(function(){
    //   $("#accordionSidebar li a").on('click', function(e){
    //     $("#accordionSidebar .active").removeClass('active');
    //     $(this).parent().addClass('active');
    //     // e.preventDefault();
    // });
    // });
    $(document).on('change',"#package_type",function(){
        $("#package_price").val(null);
        $("#package_price").prop('disabled', false);
        if($(this).val() == 1)
        {
            $("#package_price").val(0);
            $("#package_price").prop('disabled', true);
        }
    });
});
// document module
$(document).on('change',"#expire_date",function(){
    var document_id =  $("#document_id").val();
    $("#expire_date_value").attr('required',false);;
    if(document_id !='') {
        var old_expire_status = $(this).attr("old-expiry-status");
        var current_expire_status = $(this).val();
        $("#expire_date_text_div").hide();
        $("#expire_date_value_div").hide();
        if (old_expire_status == 2 && current_expire_status == 1) {
            $("#expire_date_text_div").show();
            $("#expire_date_value_div").show();
            $("#expire_date_value").attr('required',true);
        }
    }
});
$(document).on('change',"#documentNeed",function(){
    var document_id =  $("#document_id").val();
    if(document_id !='') {
        var old_mandatory_status = $(this).attr("old-mandatory-status");
        var current_mandatory_status = $(this).val();
        $("#document_mandatory_text_div").hide();
        if (old_mandatory_status == 2 && current_mandatory_status == 1) {
            $("#document_mandatory_text_div").show();
        }
    }
});

