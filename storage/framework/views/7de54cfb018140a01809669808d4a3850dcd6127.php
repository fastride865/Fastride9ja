<?php $__env->startSection('content'); ?>
    <style>
        em {
            color: red;
        }
    </style>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin-left: 10px;"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('countryareas.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" >
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.service_area"); ?> (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</h3>
                </header>
                <?php $display = true; $selected_doc = []; $id = NULL ?>
                <?php if(isset($area->id) && !empty($area->id)): ?>
                    <?php $display = false;
                    $selected_doc = array_pluck($area->Documents,'id');
                    $id =  $area->id;
                    ?>
                <?php endif; ?>

                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"  enctype="multipart/form-data" action="<?php echo e(route('countryareas.save',$id)); ?>" id="country_area_form">
                        <?php echo csrf_field(); ?>
                        <?php echo Form::hidden("id",$id,['class'=>'','id'=>'id']); ?>

                        <h5>
                            <i class="m-1 fa fa-map"></i>
                            <?php echo app('translator')->get("$string_file.area_basic_configuration"); ?>
                        </h5>
                        <hr/>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.name"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::text('name',old('name',isset($area->LanguageSingle) ? $area->LanguageSingle->AreaName : ''),['class'=>'form-control','id'=>'name','placeholder'=>'']); ?>

                                    <?php if($errors->has('name')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('name')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div id="newOpenstreet" style="width: 300px;">
                                <input type="text" class="form-control" id="google_area" name="google_area"placeholder="<?php echo app('translator')->get("$string_file.enter_area"); ?>"style="padding:4px;margin-top: 5px;border: 4px solid;">
                            </div>

                            <?php if($display == true): ?>
                                <div class="col-md-3">
                                    <div class="form-group field">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.country"); ?><span class="text-danger">*</span></label>
                                        <?php echo Form::select('country',$countries,old('country'),["class"=>"form-control select","id"=>"country"]); ?>

                                        <?php if($errors->has('country')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('country')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.timezone"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2" name="timezone"
                                            id="timezone">
                                        <?php $__currentLoopData = $timezones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($time); ?>" <?php if($display == true && $time == old('timezone')): ?> selected <?php else: ?> <?php if(isset($area->timezone) && $time == $area->timezone): ?> selected <?php endif; ?> <?php endif; ?>> <?php echo e($time); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('timezone')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('timezone')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.status"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select('status',$arr_status,old('status',isset($area->status) ? $area->status : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                    <?php if($errors->has('status')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('status')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($config->driver_wallet_status == 1): ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.driver_wallet_min_amount"); ?> <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::number('minimum_wallet_amount',old('minimum_wallet_amount',isset($area->minimum_wallet_amount) ? $area->minimum_wallet_amount : 0),['class'=>'form-control','id'=>'minimum_wallet_amount','placeholder'=>"",'required'=>true]); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($config->no_driver_availabe_enable) && $config->no_driver_availabe_enable == 1): ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="emailAddress5"><?php echo app('translator')->get("$string_file.auto_upgradation"); ?><span class="text-danger">*</span> </label>
                                        <?php echo e(Form::select('auto_upgradetion', ['' => trans("$string_file.select"), '1' => trans("$string_file.enable"), '2' => trans("$string_file.disable")], old('auto_upgradetion',$area->auto_upgradetion ?? 2), ['class'=>'form-control','id' =>'auto_upgradetion','required'=>true])); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($config->manual_downgrade_enable) && $config->manual_downgrade_enable == 1): ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="emailAddress5"><?php echo app('translator')->get("$string_file.manual_downgradation"); ?><span class="text-danger">*</span> </label>
                                        <?php echo e(Form::select('manual_downgradation', ['' => trans("$string_file.select"), '1' => trans("$string_file.enable"), '2' => trans("$string_file.disable")], old('manual_downgradation',$area->manual_downgradation ?? 2), ['class'=>'form-control','id' =>'manual_downgradation','required'=>true])); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.personal_document"); ?><span class="text-danger">*</span></label>
                                        <?php echo Form::select('driver_document[]',$documents,old('driver_document',$selected_doc),["class"=>"form-control select2","id"=>"document","multiple"=>true,'required'=>true]); ?>

                                        <?php if($errors->has('driver_document')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('driver_document')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_method">
                                     <?php echo app('translator')->get("$string_file.payment_method"); ?><span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select('payment_method[]',$payment_method,old('payment_method',$selected_payment_method),["class"=>"form-control select2","id"=>"payment_method","multiple"=>true,'required'=>true]); ?>

                                    <?php if($errors->has('payment_method')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('payment_method')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($config->driver_cash_limit == 1): ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.driver_cash_limit_amount"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::number("driver_cash_limit_amount",old("driver_cash_limit_amount",isset($area->driver_cash_limit_amount) ? $area->driver_cash_limit_amount : NULL),["step"=>"0.01", "min"=>"0","class"=>"form-control", "id"=>"driver_cash_limit_amount","placeholder"=>"","required"=>true]); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($config->geofence_module) && $config->geofence_module == 1 && $display == true): ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="geofence_area">
                                            <?php echo app('translator')->get("$string_file.geofence_area"); ?><span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::select('is_geofence',get_status(true,$string_file),old('is_geofence',2),["class"=>"form-control","id"=>"is_geofence","required"=>true]); ?>

                                        <?php if($errors->has('is_geofence')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('is_geofence')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.draw_map"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div id="polygons" style="height: 400px;width: 100%"></div>
                                <?php if(!empty($id)): ?>
                                    <br>
                                    <span class="text-danger"><?php echo app('translator')->get("$string_file.note"); ?> :- <?php echo app('translator')->get("$string_file.service_area_document_warning"); ?></span>
                                <?php endif; ?>
                                <input type="hidden" class="form-control " id="lat" name="lat">
                                <?php if($errors->has('lat')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('lat')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i><?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                            <?php else: ?>
                                <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_merchant_google_key(NULL,'admin_backend'); ?>&libraries=places,drawing"></script>
    <?php if($display): ?>
        <script>
            var map;
            var polygonArray = [];
            let inputSerach;
            let polygon;
            var drawingManager;
            let triangleCoords = [];
            var AreaLatlong = [];
            var bounds = new google.maps.LatLngBounds();

            function initMap() {
                map = new google.maps.Map(
                    document.getElementById("polygons"), {
                        center: new google.maps.LatLng(37.4419, -122.1419),
                        zoom: 10,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });

                drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: ['polygon']
                    },
                    polygonOptions: {
                        fillColor: '#93BE52',
                        fillOpacity: 0.5,
                        strokeWeight: 2,
                        strokeColor: '#000000',
                        clickable: false,
                        editable: true,
                        draggable: true,
                        zIndex: 1
                    }
                });
                drawingManager.setMap(map);
                var options = {
                    types: ['(cities)'],
                };
                inputSerach = document.getElementById('newOpenstreet');
                autoPlace = document.getElementById('google_area');
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(inputSerach);
                var autocomplete = new google.maps.places.Autocomplete(autoPlace, options);
                autocomplete.bindTo('bounds', map);
                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }
                    map.setCenter(place.geometry.location);
                    map.setZoom(10);
                    var shortName = place.address_components[0] && place.address_components[0].short_name || '';
                    var long_name = place.address_components[0] && place.address_components[0].long_name || '';
                    var url = "https://nominatim.openstreetmap.org/search.php?polygon_geojson=1&format=json&q=" + shortName;
                    $.getJSON(url, function (result) {
                        console.log(result);
                        var arrayLength = result.length;
                        document.getElementById('lat').value = "";
                        for (var i = 0; i < polygonArray.length; i++) {
                            polygonArray[i].setMap(null);
                        }
                        for (var i = 0; i < arrayLength; i++) {
                            if (result[i].geojson.type === "Polygon" || result[i].geojson.type === "MultiPolygon") {
                                var PlaceId = result[i].place_id;
                                break;
                            }
                        }
                        if (PlaceId) {
                            var bounds = new google.maps.LatLngBounds();
                            var url = "https://nominatim.openstreetmap.org/details.php?polygon_geojson=1&format=json&place_id=" + PlaceId;
                            $.getJSON(url, function (result) {
                                var data;
                                if (result.geometry.type === "Polygon") {
                                    data = result.geometry.coordinates[0];
                                } else if (result.geometry.type === "MultiPolygon") {
                                    data = result.geometry.coordinates[0][0];
                                } else {
                                }
                                if (data) {
                                    var myObject = JSON.stringify(data);
                                    var count = Object.keys(myObject).length;
                                    console.log('object has a length of ' + count);

                                    triangleCoords = [];
                                    for (var i = 0; i < data.length; i++) {
                                        item = {}
                                        item["latitude"] = data[i][1].toString();
                                        item["longitude"] = data[i][0].toString();
                                        AreaLatlong.push(item);
                                        triangleCoords.push(new google.maps.LatLng(data[i][1], data[i][0]));
                                    }
                                    for (i = 0; i < triangleCoords.length; i++) {
                                        bounds.extend(triangleCoords[i]);
                                    }
                                    var latlng = bounds.getCenter();
                                    polygon = new google.maps.Polygon({
                                        paths: triangleCoords,
                                        strokeColor: '#FF0000',
                                        draggable: true,
                                        editable: true,
                                        strokeOpacity: 0.8,
                                        strokeWeight: 2,
                                        fillColor: '#FF0000',
                                        fillOpacity: 0.35
                                    });
                                    polygonArray.push(polygon);
                                    if (count > 50000) {
                                        alert("This area can't be draw. Please create manually.");
                                    }else{
                                        polygon.setMap(map);
                                    }
                                    map.fitBounds(bounds);
                                    map.setCenter(latlng)
                                    drawingManager.setDrawingMode(null);
                                    drawingManager.setOptions({
                                        // drawingControl: false
                                    });
                                    let NewJson = JSON.stringify(AreaLatlong);
                                    document.getElementById('lat').value = NewJson;
                                    AreaLatlong = [];
                                }
                            });
                        }
                    });
                });
                var centerControlDiv = document.createElement('div');
                var centerControl = new CenterControl(centerControlDiv, map);
                centerControlDiv.index = 1;
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);

                google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {
                    for (var i = 0; i < polygon.getPath().getLength(); i++) {
                        // document.getElementById('lat').value += polygon.getPath().getAt(i).toUrlValue(6) + "|";
                        var xy = polygon.getPath().getAt(i);
                        item = {}
                        item["latitude"] = xy.lat().toString();
                        item["longitude"] = xy.lng().toString();
                        AreaLatlong.push(item);
                    }
                    let NewJson = JSON.stringify(AreaLatlong);
                    document.getElementById('lat').value = NewJson;
                    AreaLatlong = [];
                    polygonArray.push(polygon);
                    drawingManager.setDrawingMode(null);
                    drawingManager.setOptions({
                        // drawingControl: false
                    });
                    google.maps.event.addListener(polygon.getPath(), "insert_at", function () {
                        for (var i = 0; i < polygon.getPath().getLength(); i++) {
                            // document.getElementById('lat').value += polygon.getPath().getAt(i).toUrlValue(6) + "|";
                            var xy = polygon.getPath().getAt(i);
                            item = {}
                            item["latitude"] = xy.lat().toString();
                            item["longitude"] = xy.lng().toString();
                            AreaLatlong.push(item);
                        }
                        let NewJson = JSON.stringify(AreaLatlong);
                        document.getElementById('lat').value = NewJson;
                        AreaLatlong = [];
                    });
                    google.maps.event.addListener(polygon.getPath(), "set_at", function () {
                        for (var i = 0; i < polygon.getPath().getLength(); i++) {
                            // document.getElementById('lat').value += polygon.getPath().getAt(i).toUrlValue(6) + "|";
                            var xy = polygon.getPath().getAt(i);
                            item = {}
                            item["latitude"] = xy.lat().toString();
                            item["longitude"] = xy.lng().toString();
                            AreaLatlong.push(item);
                        }
                        let NewJson = JSON.stringify(AreaLatlong);
                        document.getElementById('lat').value = NewJson;
                        AreaLatlong = [];
                    });
                });

                google.maps.event.addListener(drawingManager, "drawingmode_changed", function () {
                    if (drawingManager.getDrawingMode() != null) {
                        document.getElementById('lat').value = "";
                        for (var i = 0; i < polygonArray.length; i++) {
                            polygonArray[i].setMap(null);
                        }
                        polygonArray = [];
                        AreaLatlong = [];
                    }
                });
            }
            function getEventTarget(e) {
                e = e || window.event;
                return e.target || e.srcElement;
            }
            function openStreetMap() {
                var query = $('#google_area').val();
                var url = "https://nominatim.openstreetmap.org/search.php?polygon_geojson=1&format=json&q=" + query;
                $.getJSON(url, function (result) {
                    var arrayLength = result.length;
                    $('.list-gpfrm').empty();
                    for (var i = 0; i < arrayLength; i++) {
                        var myhtml = "<li value=" + result[i].place_id + ">" + result[i].display_name + "</li>";
                        $(".list-gpfrm").append(myhtml);
                    }
                });
            }

            function changeCanter(s) {
                var country = s[s.selectedIndex].id;
                if (country != "") {
                    var geocoder;
                    geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'address': country}, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            //alert(results[0].geometry.location);
                            map.setZoom(6);
                            map.setCenter(results[0].geometry.location)
                        }
                    });
                }
            }

            function CenterControl(controlDiv, map) {
                var controlUI = document.createElement('div');
                controlUI.style.backgroundColor = '#fff';
                controlUI.style.border = '2px solid #fff';
                controlUI.style.borderRadius = '3px';
                controlUI.style.marginRight = '1px';
                controlUI.style.marginTop = '5px';
                controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
                controlUI.style.cursor = 'pointer';
                controlUI.style.marginBottom = '22px';
                controlUI.style.textAlign = 'center';
                controlUI.title = 'Delete Polygon';
                controlDiv.appendChild(controlUI);
                var controlText = document.createElement('div');
                controlText.style.color = 'rgb(25,25,25)';
                controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
                controlText.style.fontSize = '16px';
                controlText.style.lineHeight = '20px';
                controlText.style.paddingLeft = '5px';
                controlText.style.paddingRight = '5px';
                controlText.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i>';
                controlUI.appendChild(controlText);

                // Setup the click event listeners: simply set the map to Chicago.
                controlUI.addEventListener('click', function () {
                    document.getElementById('lat').value = "";
                    for (var i = 0; i < polygonArray.length; i++) {
                        polygonArray[i].setMap(null);
                    }
                    polygonArray = [];
                    AreaLatlong = [];
                });

            }

            initMap();

    </script>
    <?php else: ?>
        <script>
            var map;
            let polygon;
            var NewJson;
            var polygonArray = [];
            let data = <?php echo $area->AreaCoordinates; ?>;
            let triangleCoords = [];
            var bounds = new google.maps.LatLngBounds();
            var drawingManager;
            var AreaLatlong = [];

            function initMap() {
                map = new google.maps.Map(
                    document.getElementById("polygons"), {
                        center: new google.maps.LatLng(data[0].latitude, data[0].longitude),
                        zoom: 8,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });

                drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: ['polygon']
                    },
                    polygonOptions: {
                        fillColor: '#93BE52',
                        fillOpacity: 0.5,
                        strokeWeight: 2,
                        strokeColor: '#000000',
                        clickable: false,
                        editable: true,
                        draggable: true,
                        zIndex: 1
                    }
                });

                var centerControlDiv = document.createElement('div');
                var centerControl = new CenterControl(centerControlDiv, map);
                centerControlDiv.index = 1;
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);
                for (var i = 0; i < data.length; i++) {
                    triangleCoords.push(new google.maps.LatLng(data[i].latitude, data[i].longitude));
                }
                for (i = 0; i < triangleCoords.length; i++) {
                    bounds.extend(triangleCoords[i]);
                }
                var latlng = bounds.getCenter();
                map.setCenter(latlng)
                polygon = new google.maps.Polygon({
                    paths: triangleCoords,
                    strokeColor: '#FF0000',
                    draggable: true,
                    editable: true,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35
                });
                polygon.setMap(map);
                polygonArray.push(polygon);
                map.fitBounds(bounds);
                google.maps.event.addListener(polygon.getPath(), "insert_at", getPolygonCoords);
                google.maps.event.addListener(polygon.getPath(), "set_at", getPolygonCoords);
                inputSerach = document.getElementById('newOpenstreet');
                autoPlace = document.getElementById('google_area');
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(inputSerach);
                var autocomplete = new google.maps.places.Autocomplete(autoPlace);
                autocomplete.bindTo('bounds', map);
                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }
                    map.setCenter(place.geometry.location);
                    map.setZoom(10);
                    var shortName = place.address_components[0] && place.address_components[0].short_name || '';
                    var long_name = place.address_components[0] && place.address_components[0].long_name || '';
                    var url = "https://nominatim.openstreetmap.org/search.php?polygon_geojson=1&format=json&q=" + shortName;
                    $.getJSON(url, function (result) {
                        var arrayLength = result.length;
                        document.getElementById('lat').value = "";
                        for (var i = 0; i < polygonArray.length; i++) {
                            polygonArray[i].setMap(null);
                        }
                        for (var i = 0; i < arrayLength; i++) {
                            if (result[i].geojson.type === "Polygon" || result[i].geojson.type === "MultiPolygon") {
                                var PlaceId = result[i].place_id;
                                break;
                            }
                        }
                        if (PlaceId) {
                            var bounds = new google.maps.LatLngBounds();
                            var url = "https://nominatim.openstreetmap.org/details.php?polygon_geojson=1&format=json&place_id=" + PlaceId;
                            $.getJSON(url, function (result) {
                                var data;
                                if (result.geometry.type === "Polygon") {
                                    data = result.geometry.coordinates[0];
                                } else if (result.geometry.type === "MultiPolygon") {
                                    data = result.geometry.coordinates[0][0];
                                } else {
                                }
                                if (data) {
                                    triangleCoords = [];
                                    for (var i = 0; i < data.length; i++) {
                                        item = {}
                                        item["latitude"] = data[i][1].toString();
                                        item["longitude"] = data[i][0].toString();
                                        AreaLatlong.push(item);
                                        triangleCoords.push(new google.maps.LatLng(data[i][1], data[i][0]));
                                    }
                                    for (i = 0; i < triangleCoords.length; i++) {
                                        bounds.extend(triangleCoords[i]);
                                    }
                                    var latlng = bounds.getCenter();
                                    polygon = new google.maps.Polygon({
                                        paths: triangleCoords,
                                        strokeColor: '#FF0000',
                                        draggable: true,
                                        editable: true,
                                        strokeOpacity: 0.8,
                                        strokeWeight: 2,
                                        fillColor: '#FF0000',
                                        fillOpacity: 0.35
                                    });
                                    polygonArray.push(polygon);
                                    polygon.setMap(map);
                                    map.fitBounds(bounds);
                                    map.setCenter(latlng)
                                    drawingManager.setDrawingMode(null);
                                    drawingManager.setOptions({
                                        // drawingControl: false
                                    });
                                    let NewJson = JSON.stringify(AreaLatlong);
                                    document.getElementById('lat').value = NewJson;
                                    AreaLatlong = [];
                                }
                            });
                        }
                    });


                });
            }

            function getPolygonCoords() {
                var len = polygon.getPath().getLength();
                var AreaLatlong = [];
                for (var i = 0; i < polygon.getPath().getLength(); i++) {
                    var xy = polygon.getPath().getAt(i);
                    item = {}
                    item["latitude"] = xy.lat().toString();
                    item["longitude"] = xy.lng().toString();
                    AreaLatlong.push(item);
                }
                NewJson = JSON.stringify(AreaLatlong);
                document.getElementById('lat').value = NewJson;
                AreaLatlong = [];
            }

            function CenterControl(controlDiv, map) {
                var controlUI = document.createElement('div');
                controlUI.style.backgroundColor = '#fff';
                controlUI.style.border = '2px solid #fff';
                controlUI.style.borderRadius = '3px';
                controlUI.style.marginRight = '1px';
                controlUI.style.marginTop = '5px';
                controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
                controlUI.style.cursor = 'pointer';
                controlUI.style.marginBottom = '22px';
                controlUI.style.textAlign = 'center';
                controlUI.title = 'Delete Polygon';
                controlUI.id = 'delete_polygon';
                controlDiv.appendChild(controlUI);
                var controlText = document.createElement('div');
                controlText.style.color = 'rgb(25,25,25)';
                controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
                controlText.style.fontSize = '16px';
                controlText.style.lineHeight = '20px';
                controlText.style.paddingLeft = '5px';
                controlText.style.paddingRight = '5px';
                controlText.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i>';
                controlUI.appendChild(controlText);
                var count = 0;
                // Setup the click event listeners: simply set the map to Chicago.
                controlUI.addEventListener('click', function () {
                    $('#delete_polygon').hide();
                    count += 1;
                    polygon.setMap(null);
                    if (count <= 1){
                        drawingManager = new google.maps.drawing.DrawingManager({
                            drawingMode: google.maps.drawing.OverlayType.POLYGON,
                            drawingControl: true,
                            drawingControlOptions: {
                                position: google.maps.ControlPosition.TOP_CENTER,
                                drawingModes: ['polygon']
                            },
                            polygonOptions: {
                                fillColor: '#93BE52',
                                fillOpacity: 0.5,
                                strokeWeight: 2,
                                strokeColor: '#000000',
                                clickable: false,
                                editable: true,
                                draggable: true,
                                zIndex: 1
                            }
                        });
                        drawingManager.setMap(map);

                        google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {
                            for (var i = 0; i < polygon.getPath().getLength(); i++) {
                                // document.getElementById('lat').value += polygon.getPath().getAt(i).toUrlValue(6) + "|";
                                var xy = polygon.getPath().getAt(i);
                                item = {}
                                item["latitude"] = xy.lat().toString();
                                item["longitude"] = xy.lng().toString();
                                AreaLatlong.push(item);
                            }
                            let NewJson = JSON.stringify(AreaLatlong);
                            document.getElementById('lat').value = NewJson;
                            AreaLatlong = [];
                            polygonArray.push(polygon);
                            drawingManager.setDrawingMode(null);
                            drawingManager.setOptions({
                                // drawingControl: false
                            });

                            google.maps.event.addListener(polygon.getPath(), "insert_at", function () {
                                for (var i = 0; i < polygon.getPath().getLength(); i++) {
                                    // document.getElementById('lat').value += polygon.getPath().getAt(i).toUrlValue(6) + "|";
                                    var xy = polygon.getPath().getAt(i);
                                    item = {}
                                    item["latitude"] = xy.lat().toString();
                                    item["longitude"] = xy.lng().toString();
                                    AreaLatlong.push(item);
                                }
                                let NewJson = JSON.stringify(AreaLatlong);
                                document.getElementById('lat').value = NewJson;
                                AreaLatlong = [];
                            });
                            google.maps.event.addListener(polygon.getPath(), "set_at", function () {
                                for (var i = 0; i < polygon.getPath().getLength(); i++) {
                                    // document.getElementById('lat').value += polygon.getPath().getAt(i).toUrlValue(6) + "|";
                                    var xy = polygon.getPath().getAt(i);
                                    item = {}
                                    item["latitude"] = xy.lat().toString();
                                    item["longitude"] = xy.lng().toString();
                                    AreaLatlong.push(item);
                                }
                                let NewJson = JSON.stringify(AreaLatlong);
                                document.getElementById('lat').value = NewJson;
                                AreaLatlong = [];
                            });
                        });
                        google.maps.event.addListener(drawingManager, "drawingmode_changed", function () {
                            if (drawingManager.getDrawingMode() != null) {
                                document.getElementById('lat').value = "";
                                for (var i = 0; i < polygonArray.length; i++) {
                                    polygonArray[i].setMap(null);
                                }
                                polygonArray = [];
                                AreaLatlong = [];
                            }
                        });
                    }
                });
            }

            initMap();
        </script>
    <?php endif; ?>

    <script>
        jQuery(document).ready(function () {

            jQuery.validator.addMethod("lettersonly", function(value, element) {
                return this.optional(element) || /^[A-Za-z0-9\s\-\_]+$/i.test(value);
            }, "Only alphabetical, Number, hyphen and underscore allow");

            // jQuery.validator.addMethod("validIndianNumber", function(value, element) {
            //     return this.optional(element) || /^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)?[56789]\d{9}$/i.test(value);
            // }, "Please enter valid phone no.");

            $("#country_area_form").validate({
                /* @validation  states + elements
                ------------------------------------------- */
                errorClass: "has-error",
                validClass: "has-success",
                errorElement: "em",
                /* @validation  rules
                ------------------------------------------ */
                rules: {
                    country: {
                        required: true,
                    },
                    name: {
                        required: true,
                        maxlength: 255,
                    },
                    timezone: {
                        required: true,
                    },
                    bill_period_id: {
                        required: true,
                    },
                    // "normal_service[]": {
                    //     required: true,
                    // },
                    "document[]": {
                        required: true,
                    },
                    "payment_method[]": {
                        required: true,
                    },
                    // "segment[]": {
                    //     required: true,
                    // },
                },
                /* @validation  highlighting + error placement
                ---------------------------------------------------- */
                highlight: function (element, errorClass, validClass) {
                    $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
                    $(element).closest('.form-group').addClass(errorClass).removeClass(validClass);
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
                    $(element).closest('.form-group').removeClass(errorClass).addClass(validClass);
                },
                errorPlacement: function (error, element) {
                    if (element.is(":radio") || element.is(":checkbox")) {
                        error.insertAfter(element.parent());
                        // element.closest('.form-group').after(error);
                    } else {
                        error.insertAfter(element.parent());
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
        $(document).on('keypress','#manual_toll_price',function (event) {
            if ( event.keyCode == 46 || event.keyCode == 8 ) {
            }
            else {
                if (event.keyCode < 48 || event.keyCode > 57 ) {
                    event.preventDefault();
                }
            }
        });

        function changeBill(type) {
            switch (type) {
                case "1":
                    document.getElementById('start_time').style.display = 'block';
                    document.getElementById('start_day').style.display = 'none';
                    document.getElementById('start_date').style.display = 'none';
                    break;
                case "2":
                    document.getElementById('start_time').style.display = 'none';
                    document.getElementById('start_day').style.display = 'block';
                    document.getElementById('start_date').style.display = 'none';
                    break;
                case "3":
                    document.getElementById('start_time').style.display = 'none';
                    document.getElementById('start_day').style.display = 'none';
                    document.getElementById('start_date').style.display = 'block';
                    break;
                default:
                    document.getElementById('start_time').style.display = 'none';
                    document.getElementById('start_day').style.display = 'none';
                    document.getElementById('start_date').style.display = 'none';
            }
        }


        // segmentSetting();
        // $(document).ready(function(){
        //  $(document).on("click",".area_segment",function(){
        //      segmentSetting();
        //  })
        // });
        // function segmentSetting()
        // {
        //     var  segment = [];
        //     $(".services").hide();
        //     $('.other_segment').prop('required', false);
        //     $.each($(".area_segment"), function(){
        //         var segment_id = $(this).val();
        //         if(this.checked)
        //         {
        //              segment.push(segment_id);
        //             $("#segment_" + segment_id).show();
        //             // $('.segment_service_'+ segment_id).prop('required', true);
        //             $('.segment_service'+ segment_id).prop('checked', true);
        //         }
        //         else
        //         {
        //             $('.segment_service'+ segment_id).prop('checked', false);
        //         }
        //     });
        //     // console.log(segment)
        //     if($.inArray("1", segment) > -1  || $.inArray("2", segment) > -1)
        //     {
        //     }
        //
        // }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/area/form-step1.blade.php ENDPATH**/ ?>