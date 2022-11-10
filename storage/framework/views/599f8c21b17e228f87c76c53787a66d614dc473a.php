<!DOCTYPE html>
<html>
<head>
    <title>Ride Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
            border:1px soldi gray;
        }
        #floating-panel{
            background: #fff;
            padding: 10px 10px 10px 15px;
            font-size: 14px;
            font-family: Arial;
            border: 1px solid #ccc;
            box-shadow: 0 2px 2px rgba(33, 33, 33, 0.4);
            /*display: none;*/
            height: auto;
            width: auto;

        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #profile_pic {
            border-radius:50px;
        }
        #number_pic{
            height:50px;
            width:50px;
           
        }
        #number_pic:hover{
            height:55px;
            width:55px;
        }

    </style>
</head>
<body>

<div id="floating-panel">

    <table cellpadding="0" cellspacing="0">

        <tr>
            <td width="100" align="center">

                <img
                       id="profile_pic" src="<?php echo e(get_image($booking->Driver->profile_image,'driver',$booking->merchant_id)); ?>" width="70" height="70">

            </td>
            <td width="240">

                <table cellspacing="0" cellpadding="0">

                    <tr>

                        <td height="30" style="font-size:20px;"><strong><?php echo app('translator')->get("$string_file.driver_details"); ?></strong></td>

                    </tr>

                    <tr>

                        <td height="30"><strong><?php echo app('translator')->get("$string_file.driver_name"); ?> : </strong><?php echo e($booking->Driver->fullName); ?></td>

                    </tr>
                    <tr>

                        <td height="30"><strong><?php echo app('translator')->get("$string_file.driver_email"); ?> : </strong><?php echo e($booking->Driver->email); ?></td>

                    </tr>

                    <tr>

                        <td height="30"><strong><?php echo app('translator')->get("$string_file.vehicle_number"); ?>: </strong> <span><?php echo e($booking->DriverVehicle->vehicle_number); ?></span></td>

                    </tr>
                    <tr>

                        <td height="30"><strong><?php echo app('translator')->get("$string_file.driver_number"); ?>: </strong> <span><?php echo e($booking->Driver->phoneNumber); ?></span></td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>


</div>
<div id="map"></div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>
    var map;
    var position = [parseFloat("<?php echo e($booking->pickup_latitude); ?>"),parseFloat("<?php echo e($booking->pickup_longitude); ?>")];
    let marker;
    let infowindow;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: parseFloat("<?php echo e($booking->pickup_latitude); ?>"), lng: parseFloat("<?php echo e($booking->pickup_longitude); ?>")},
            zoom: 12
        });
        var driverLocation = new google.maps.LatLng(parseFloat("<?php echo e($booking->Driver->current_latitude); ?>"),parseFloat("<?php echo e($booking->Driver->current_longitude); ?>"));
        var icon = {
            url: "<?php echo e(view_config_image($booking->VehicleType->vehicleTypeMapImage)); ?>", // url
            scaledSize: new google.maps.Size(50, 50), // scaled size
            labelOrigin: new google.maps.Point(15, 0),
        };
        var marker = new google.maps.Marker({
            position: driverLocation,
            map: map,
            animation: google.maps.Animation.DROP,
            icon: icon,
            // label:{text:'View Details'}
        });
                
                
                
                
                
                
                
                
                
                
                
        var refreshId = setInterval(function () {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "<?php echo e(route('driverTrack')); ?>",
                    data: 'driver_id=' + "<?php echo e($booking->Driver->id); ?>" ,
                    success:
                        function (data) {
                            var lat = data.current_latitude;
                            var long = data.current_longitude;
                            var result = [lat, long];
                            transition(result);
                            var latLng = new google.maps.LatLng(lat, long);
                            map.panTo(latLng);
                        }
                });
            }, 10000);
        var numDeltas = 100;
        var delay = 100; //milliseconds
        var i = 0;
        var deltaLat;
        var deltaLng;
        function transition(result) {
            i = 0;
            deltaLat = (result[0] - position[0]) / numDeltas;
            deltaLng = (result[1] - position[1]) / numDeltas;

            moveMarker();

        }
        function moveMarker() {
            position[0] += deltaLat;
            position[1] += deltaLng;
            var latlng = new google.maps.LatLng(position[0], position[1]);
            marker.setPosition(latlng);
            if (i != numDeltas) {
                i++;
                setTimeout(moveMarker, delay);
            }
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(get_merchant_google_key($booking->merchant_id,'admin_backend')); ?>&callback=initMap"
        async defer></script>
</body>
</html><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/map.blade.php ENDPATH**/ ?>