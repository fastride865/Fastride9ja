@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{ route('outstationpackage.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.package")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.edit_package")  {{ strtoupper(Config::get('app.locale')) }}
                        )</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data"
                          action="{{route('outstationpackage.update', $package->id)}}"> {{method_field('PUT')}}
                        @csrf
                        {!! Form::hidden('service_type_id',$package->service_type_id) !!}
                        <div class="row">
                            {{--                            <div class="col-md-12">--}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="firstName3">@lang("$string_file.city_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="city" name="city"
                                           value="@if($package->LanguageSingle) {{ $package->LanguageSingle->city }} @endif"
                                           placeholder="" required/>
                                    <!--<input type="hidden" class="form-control " id="lat" name="lat">-->
                                </div>
                                @if ($errors->has('city'))
                                    <label class="danger">{{ $errors->first('city') }}</label>
                                @endif
                            </div>
                            <div id="newOpenstreet">
                                <input type="text" class="form-control" id="google_area" name="google_area"
                                       placeholder="@lang("$string_file.search_area")"/>
                                <input type="hidden" class="form-control" id="lat" name="lat">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3">@lang("$string_file.description")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description" rows="3" name="description"
                                              placeholder="">@if($package->LanguageSingle) {{ $package->LanguageSingle->description }} @endif</textarea>
                                    @if ($errors->has('description'))
                                        <label class="danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--                        </div>--}}
                        <div id="polygons" style="height: 400px" class="height-400"></div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-check-circle"></i> @lang("$string_file.update")</button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection
@section('js')
    <script type="text/javascript "
            src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_merchant_google_key(NULL, 'admin_backend'); ?>&libraries=places,drawing"></script>
    <script>
        var map;
        let polygon;
        var NewJson;
        var polygonArray = [];
        let data = @if(!empty($package->area_coordinates)){!! $package->area_coordinates !!}@else [{
            "latitude": "28.679632",
            "longitude": "77.5442934"
        }] @endif;
        let triangleCoords = [];
        var bounds = new google.maps.LatLngBounds();
        var drawingManager;
        var AreaLatlong = [];

        function initMap() {

            var input_city_name = document.getElementById('city');
            var options = {
                types: ['(cities)'],
            };
            new google.maps.places.Autocomplete(input_city_name, options);
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
                // document.getElementById('lat').value += polygon.getPath().getAt(i).toUrlValue(6) + "|";
                var xy = polygon.getPath().getAt(i);
                item = {}
                item["latitude"] = xy.lat().toString();
                item["longitude"] = xy.lng().toString();
                AreaLatlong.push(item);
            }
            NewJson = JSON.stringify(AreaLatlong);
            document.getElementById('lat').value = NewJson;
            AreaLatlong = [];
            console.log(AreaLatlong);
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
                if (count <= 1) {
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
@endsection