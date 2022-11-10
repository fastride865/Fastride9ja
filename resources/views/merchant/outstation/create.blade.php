@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right">
                            <a href="{{ route('outstationpackage.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>

                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_package_details")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('outstationpackage.store') }}">
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">@lang("$string_file.city_name") <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="city" name="city"
                                               placeholder="" required>
                                        @if ($errors->has('city'))
                                            <label class="text-danger">{{ $errors->first('city')}}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">@lang("$string_file.service_type")<span
                                                    class="text-danger">*</span></label>
                                        {!! Form::select('service_type_id',add_blank_option($arr_services,trans("$string_file.select")),old('service_type_id'),['id'=>'outstation_service_type','class'=>'form-control','required'=>true]) !!}
                                        @if ($errors->has('service_type_id'))
                                            <label class="text-danger">{{ $errors->first('service_type_id')}}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> @lang("$string_file.description")<span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description" rows="3"
                                                  placeholder=""></textarea>
                                        @if ($errors->has('description'))
                                            <label class="text-danger">{{ $errors->first('description')}}</label>
                                        @endif
                                    </div>
                                </div>
                                <div id="newOpenstreet" style="width: 300px;">
                                    <input type="text" class="form-control" id="google_area"
                                           name="google_area"
                                           placeholder="@lang("$string_file.search_area")"
                                           style="padding:4px;margin-top: 5px;border: 4px solid;">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <label for="emailAddress5">
                                            @lang("$string_file.draw_area")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <div id="polygons"
                                                     style="height: 400px;width: 100%"></div>
                                            </div>
                                            <input type="hidden" class="form-control " id="lat"
                                                   name="lat">
                                        </div>
                                        @if ($errors->has('lat'))
                                            <label class="text-danger">{{ $errors->first('lat') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
@section('js')
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC7lIIgBajzx409vxmmY_CJPcRvDb114w4&libraries=places,drawing"></script>
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
            var input_city_name = document.getElementById('city');
            new google.maps.places.Autocomplete(input_city_name, options);
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

        // var ul = document.getElementById('list-gpfrm');
        // ul.onclick = function (event) {
        //     var target = getEventTarget(event);
        //     $('#google_area').val(target.innerHTML);
        //     $('.list-gpfrm').empty();
        //     var place = target.value;
        //     document.getElementById('lat').value = "";
        //     for (var i = 0; i < polygonArray.length; i++) {
        //         polygonArray[i].setMap(null);
        //     }
        //     AreaLatlong = [];
        //     var url = "https://nominatim.openstreetmap.org/details.php?polygon_geojson=1&format=json&place_id=" + place;
        //     var bounds = new google.maps.LatLngBounds();
        //     $.getJSON(url, function (result) {
        //         var data;
        //         if (result.geometry.type === "Polygon") {
        //             data = result.geometry.coordinates[0];
        //         } else if (result.geometry.type === "MultiPolygon") {
        //             data = result.geometry.coordinates[0][0];
        //         } else {
        //             alert("Plz enter City Or Area Name Only");
        //             return false;
        //         }
        //         triangleCoords = [];
        //         for (var i = 0; i < data.length; i++) {
        //             item = {}
        //             item["latitude"] = data[i][1].toString();
        //             item["longitude"] = data[i][0].toString();
        //             AreaLatlong.push(item);
        //             triangleCoords.push(new google.maps.LatLng(data[i][1], data[i][0]));
        //         }
        //         for (i = 0; i < triangleCoords.length; i++) {
        //             bounds.extend(triangleCoords[i]);
        //         }
        //         var latlng = bounds.getCenter();
        //         polygon = new google.maps.Polygon({
        //             paths: triangleCoords,
        //             strokeColor: '#FF0000',
        //             draggable: true,
        //             editable: true,
        //             strokeOpacity: 0.8,
        //             strokeWeight: 2,
        //             fillColor: '#FF0000',
        //             fillOpacity: 0.35
        //         });
        //         polygonArray.push(polygon);
        //         polygon.setMap(map);
        //         map.fitBounds(bounds);
        //         //map.setCenter(latlng)
        //         drawingManager.setDrawingMode(null);
        //         drawingManager.setOptions({
        //             // drawingControl: false
        //         });
        //         let NewJson = JSON.stringify(AreaLatlong);
        //         document.getElementById('lat').value = NewJson;
        //         AreaLatlong = [];
        //         // google.maps.event.addListener(polygon.getPath(), "insert_at", getPolygonCoords);
        //         // google.maps.event.addListener(polygon.getPath(), "set_at", getPolygonCoords);
        //     });
        // };

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
@endsection
