<!-- Footer -->
@php $loggedin_user = Auth::user('business-segment');
$merchant = $loggedin_user->Merchant;
$merchant_id = $merchant->id;
$business_segment_id = $loggedin_user->id;
 @endphp
<footer class="site-footer">
    <div class="site-footer-legal">{{$loggedin_user->full_name}} © {{ date('Y')}} </div>
    <div class="site-footer-right"></div>
</footer>

<!-- Core  -->
<script src="{{ asset('global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ asset('global/vendor/jquery/jquery.js') }}"></script>
<script src="{{ asset('global/vendor/popper-js/umd/popper.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('global/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ asset('global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ asset('global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<script src="{{ asset('global/vendor/ashoverscroll/jquery-asHoverScroll.js') }}"></script>

<!-- Plugins -->
<script src="{{ asset('global/vendor/intro-js/intro.js') }}"></script>
<script src="{{ asset('global/vendor/screenfull/screenfull.js') }}"></script>
<script src="{{ asset('global/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
<script src="{{ asset('global/vendor/chartist/chartist.min.js') }}"></script>
<script src="{{ asset('global/vendor/aspieprogress/jquery-asPieProgress.js') }}"></script>
<script src="{{ asset('global/vendor/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('global/vendor/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('global/vendor/bootbox/bootbox.js') }}"></script>
<script src="{{ asset('global/vendor/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ asset('global/vendor/multi-select/jquery.multi-select.js') }}"></script>

<!-- Scripts -->
<script src="{{ asset('global/js/Component.js') }}"></script>
<script src="{{ asset('global/js/Plugin.js') }}"></script>
<script src="{{ asset('global/js/Base.js') }}"></script>
<script src="{{ asset('global/js/Config.js') }}"></script>
{{--<script>Config.set('theme', '{{ asset('theme') }}');</script>--}}

<script src="{{ asset('theme/js/Section/Menubar.js' ) }}"></script>
<script src="{{ asset('theme/js/Section/GridMenu.js' ) }}"></script>
<script src="{{ asset('theme/js/Section/Sidebar.js' ) }}"></script>
<script src="{{ asset('theme/js/Section/PageAside.js' ) }}"></script>
<script src="{{ asset('theme/js/Plugin/menu.js' ) }}"></script>

<script src="{{ asset('global/js/config/colors.js') }}"></script>
{{--<script src="{{ asset('theme/js/config/tour.js') }}"></script>--}}
<!-- Page -->
<script src="{{ asset('theme/js/Site.js') }}"></script>
<script src="{{ asset('global/js/Plugin/asscrollable.js') }}"></script>
<script src="{{ asset('global/js/Plugin/slidepanel.js') }}"></script>
<script src="{{ asset('global/js/Plugin/switchery.js') }}"></script>
<script src="{{ asset('global/vendor/asspinner/jquery-asSpinner.js') }}"></script>
<script src="{{ asset('global/vendor/asspinner/jquery-asSpinner.min.js') }}"></script>
<script src="{{ asset('global/js/Plugin/aspieprogress.js') }}"></script>
<script src="{{ asset('global/js/Plugin/bootstrap-touchspin.js')}}"></script>
<script src="{{ asset('global/js/Plugin/jquery-placeholder.js')}}"></script>
{{--<script src="{{ asset('theme/examples/js/tables/datatable.js') }}"></script>--}}
<script src="{{ asset('global/js/Plugin/datatables.js') }}"></script>
<script src="{{ asset('global/js/Plugin/select2.js')}}"></script>
<script src="{{ asset('global/js/Plugin/icheck.js')}}"></script>
<script src="{{ asset('global/js/Plugin/bootstrap-select.js')}}"></script>
<script src="{{ asset('global/js/Plugin/ascolorpicker.js')}}"></script>
<script src="{{ asset('global/js/Plugin/multi-select.js')}}"></script>
{{--<script src="{{ asset('global/js/Plugin/bootstrap-datepicker.js') }}"></script>--}}
<script src="{{ asset('global/vendor/typeahead-js/bloodhound.min.js') }}"></script>
<script src="{{ asset('global/vendor/typeahead-js/typeahead.jquery.min.js') }}"></script>

<!-- For Taxi Purpose -->

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js"></script>
<script src="{{ asset('global/vendor/clockpicker/bootstrap-clockpicker.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="https://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>

{{--web push notification--}}
<script src="{{ asset('global/js/Plugin/soundmanager2.js') }}" type="text/javascript"></script>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>

<script src="{{ asset('js/my-script.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('.customDatePicker').datepicker({
            format: 'yyyy-mm-dd',
            onRender: function (date) {
                return date.valueOf() < now.valueOf() ? 'disabled' : '';
            }
        });
        var dateToday = new Date();
        $('.customDatePicker1').datepicker({
            format: 'yyyy-mm-dd',
            startDate: dateToday,
            onRender: function (date) {
                return date.valueOf() < now.valueOf() ? 'disabled' : '';
            }
        });
        $('.customDatePicker2').datepicker({
            format: 'yyyy-mm-dd',
            endDate: dateToday,
            onRender: function (date) {
                return date.valueOf() < now.valueOf() ? 'disabled' : '';
            }
        });
    });
    $(document).ready(function() {
        var rowCount = $('#customDataTable >tbody >tr').length;
        $("#customDataTable >tbody >tr>td").addClass("custom_datatable_width");
        if(rowCount == 1)
        {
            $("#customDataTable >tbody >tr>td").addClass("custom_datatable_padding");
        }
        $('#customDataTable').DataTable( {
            "aoColumnDefs": [{
                'bSortable': false,
                'aTargets': [-1]
            }],
            //"iDisplayLength": 5,
            "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            //"sDom": '<"dt-panelmenu clearfix"Bfr>t<"dt-panelfooter clearfix"ip>',
            //"buttons": ['copy', 'excel', 'csv', 'pdf', 'print'],
            "scrollX": true,
            "paging":   false,
            "bInfo" : false,

        });
    });
    $(document).ready(function() {
        $('#customDataTable2').DataTable( {
            "aoColumnDefs": [{
                'bSortable': false,
                'aTargets': [-1]
            }],
            //"iDisplayLength": 10,
            "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            //"sDom": '<"dt-panelmenu clearfix"Bfr>t<"dt-panelfooter clearfix"ip>',
            //"buttons": ['copy', 'excel', 'csv', 'pdf', 'print'],
            "scrollX": true,
            "paging":   false,
            "bInfo" : false,
        });
    });
    (function(document, window, $){
        'use strict';
        var Site = window.Site;
        $(document).ready(function(){
            Site.run();
        });
    })(document, window, jQuery);

    function myFunction() {
        var input, filter, ul, li, a, i;
        filter = $("#mySearch").val();
        ul = $("#myMenu");
        $('#myMenu li').each(function(i)
        {
            var a = jQuery(this).children("a");
            var href = a.attr('href');
            var text = a.children('span').html();

        });
    }

    $(document).ready(function(){
        /** add active class and stay opened when selected */
        var url = window.location;
        $('ul.site-menu a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');

        setTimeout(function(){
            //your code here
            $('ul.site-menu li.has-sub a').filter(function () {
                return this.href == url;
            }).parents('li:eq(1)').addClass("open");
        }, 500);
    });

    // web notification to display icon on panel
  @if(isset($loggedin_user->OneSignal) && !is_null($loggedin_user->OneSignal->application_key))
    var OneSignal = window.OneSignal || [];
//151be084-a66d-4db1-b09a-6901b77be856 for food and grcoery
    OneSignal.push(function () {
        OneSignal.init({
            appId: "{{$loggedin_user->OneSignal->application_key }}",
            welcomeNotification: {
                title: "{{$loggedin_user->full_name}}",
            },
            notifyButton: {
                enable: true,
                colors: { // Customize the colors of the main button and dialog popup button
                    'circle.background': 'rgb(21, 136, 193)',
                    'circle.foreground': 'white',
                    'badge.background': 'rgb(21, 136, 193)',
                    'badge.foreground': 'white',
                    'badge.bordercolor': 'white',
                    'pulse.color': 'white',
                    'dialog.button.background.hovering': 'rgb(77, 101, 113)',
                    'dialog.button.background.active': 'rgb(70, 92, 103)',
                    'dialog.button.background': 'rgb(84,110,123)',
                    'dialog.button.foreground': 'white'
                },
            },
        });

    });

    OneSignal.push(function () {
        OneSignal.on('notificationDisplay', function (event) {
            var mySound = soundManager.createSound('mySound', '{{asset('sound/notify.mp3')}}');
            mySound.muted = true;
            mySound.play();
            console.log(event);
            @if($merchant->Configuration->sweet_alert_admin == 1)
            swal({
                title: event.heading,
                text: event.content,
                icon: "success",
                buttons: ["@lang("$string_file.skip")", "@lang("$string_file.okay")"],
            }).then((isConfirm) => {
                if (isConfirm) {
                    if(event.url !="")
                    {
                     location.href = event.url;
                    }
                } else {
                    swal("@lang("$string_file.ok_thanks")");
                }
            });
            @endif
        });
    });

    OneSignal.push(function () {
        OneSignal.on('subscriptionChange', function (isSubscribed) {
            console.log("The admin subscription STATE is now:", isSubscribed);
            OneSignal.push(function () {
                OneSignal.getUserId(function (userId) {
                   var status = 0;
                    var player_id = userId;
                    if (isSubscribed) {
                        status = 1;
                    }
                    // call ajax
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        url: "<?php echo route('merchant-business-segment-playerid.onesignal') ?>",
                        cache: false,
                        method: "POST",
                        //dataType: "json",
                        data: {
                            status:status,
                            player_id: player_id,
                            merchant_id: "{{$merchant_id}}",
                            business_segment_id:"{{$business_segment_id}}",
                        },
                        success: function (data) {
                            //jQuery("#showloader").hide();
                            //data-toggle="modal" data-user="'+response[f]['id']+'" data-id="'+response[f]['days']+'" data-target="#leave"
                            //window.location.href = data;
                        }
                    });

                });
            });
        });
    });
    @endif

</script>
@yield('js')
</body>
</html>