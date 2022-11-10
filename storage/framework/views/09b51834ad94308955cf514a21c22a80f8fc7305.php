<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo csrf_field(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(count($merchant_segments) > 1): ?>
                            <div class="d-inline-block mt-5">
                                <div class="form-group mt-5">
                                    <?php echo Form::select('segment_id',$merchant_segments,[],['class'=>'form-control', 'id'=>'segment_id','data-placeholder'=>trans("$string_file.segment"), 'onchange'=>"getPoints()"]); ?>

                                </div>
                            </div>
                        <?php else: ?>
                            <?php echo Form::hidden('segment_id',array_key_first($merchant_segments)); ?>

                        <?php endif; ?>
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <button type="button"
                                class="btn btn-outline-primary btn-min-width box-shadow-1 mr-1 mb-1 float-right" style="margin:10px"
                                onclick="changeGradient()"><?php echo app('translator')->get("$string_file.change_gradient"); ?>
                        </button>
                        <button type="button"
                                class="btn btn-outline-danger btn-min-width box-shadow-1 mr-1 mb-1 float-right" style="margin:10px"
                                onclick="changeRadius()"><?php echo app('translator')->get("$string_file.change_radius"); ?>
                        </button>
                    </div>
                    <h3 class="panel-title"><i class=" wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.heat_map"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div id="context-menu" style="width: 100%;height: 550px;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>

        var map, heatmap;
        function initMap() {
            map = new google.maps.Map(document.getElementById('context-menu'), {
                zoom: 2,
                center: {lat: 28.7041, lng: 77.1025},
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            // heatmap = new google.maps.visualization.HeatmapLayer({
            //     data: getPoints(),
            //     map: map
            // });
            getPoints();
            
        }

        function toggleHeatmap() {
            heatmap.setMap(heatmap.getMap() ? null : map);
        }

        function changeGradient() {
            var gradient = [
                'rgba(0, 255, 255, 0)',
                'rgba(0, 255, 255, 1)',
                'rgba(0, 191, 255, 1)',
                'rgba(0, 127, 255, 1)',
                'rgba(0, 63, 255, 1)',
                'rgba(0, 0, 255, 1)',
                'rgba(0, 0, 223, 1)',
                'rgba(0, 0, 191, 1)',
                'rgba(0, 0, 159, 1)',
                'rgba(0, 0, 127, 1)',
                'rgba(63, 0, 91, 1)',
                'rgba(127, 0, 63, 1)',
                'rgba(191, 0, 31, 1)',
                'rgba(255, 0, 0, 1)'
            ]
            heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
        }

        function changeRadius() {
            heatmap.set('radius', heatmap.get('radius') ? null : 20);
        }

        function changeOpacity() {
            heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);
        }

        function getPoints() {
            var arr=[];
            var token = $('[name="_token"]').val();
            var segment_id = null;
            var driver_status = '';
            // if ($('#driver_status').val() != '') {
            //     driver_status = $('#driver_status').val();
            // }
            if( heatmap){
                 heatmap.setMap(null);
            }
            if ($('#segment_id').val() != '') {
                segment_id = [$('#segment_id').val()];
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "<?php echo e(route('getBookingsOnHeatMap')); ?>",
                data: {
                    driver_status: driver_status,
                    segment_id: segment_id,
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    if(data!=''){
                        for(i=0;i<data.length;i++){
                            arr.push(new google.maps.LatLng(data[i].drop_latitude,data[i].drop_longitude));
                        }
                        heatmap = new google.maps.visualization.HeatmapLayer({
                        data: arr,
                        map: map
                        });
                    } 

                }, error: function (e) {
                   console.log(e);
                }


            });
            // return [
            //     <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            //     new google.maps.LatLng(<?php echo e($booking->pickup_latitude); ?>,<?php echo e($booking->pickup_longitude); ?>),
            //     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            // ];
        }
    </script>
    
    
    
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?=get_merchant_google_key(NULL, 'admin_backend');?>&libraries=visualization&callback=initMap">
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/map/heat.blade.php ENDPATH**/ ?>