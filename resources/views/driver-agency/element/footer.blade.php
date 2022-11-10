<!-- Footer -->
<footer class="site-footer">
    <div class="site-footer-legal">© {{ date('Y').' '.Auth::user('driver-agency')->name }}</div>
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
<script src="{{ asset('theme/js/config/tour.js') }}"></script>
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
<script src="{{ asset('global/vendor/summernote/summernote.min.js') }}"></script>


<!-- For Taxi Purpose -->
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js"></script>
<script src="{{ asset('global/vendor/clockpicker/bootstrap-clockpicker.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/my-script.js')}}" type="text/javascript"></script>s

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
        $('#customDataTable').DataTable( {
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
        });
    });

    (function(document, window, $){
        'use strict';
        var Site = window.Site;
        $(document).ready(function(){
            Site.run();
        });
    })(document, window, jQuery);

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
</script>
@yield('js')
</body>
</html>