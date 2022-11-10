@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @csrf
            <section id="gmaps-utils">
                <div class="panel panel-bordered">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            @if(!empty($info_setting) && $info_setting->view_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                            <div class="row">
                                @if(count($merchant_segments) > 1)
                                    <div class="col-md-6 mt-5">
                                        @else
                                            <div class="col-md-12 mt-5">
                                                @endif
                                                <div class="form-group">
                                                    {!! Form::select('driver_status',arr_driver_search_status($string_file),'',['class'=>'form-control','id'=>'driver_status', 'onchange'=>"getDriverLocationo()"]) !!}
                                                </div>
                                            </div>
                                            @if(count($merchant_segments) > 1)
                                                <div class="col-md-6 mt-5">
                                                    <div class="form-group">
                                                        {!! Form::select('segment_id',$merchant_segments,[],['class'=>'form-control', 'id'=>'segment_id','data-placeholder'=>trans("$string_file.segment"), 'onchange'=>"getDriverLocationo()"]) !!}
                                                    </div>
                                                </div>
                                            @else
                                                {!! Form::hidden('segment_id',array_key_first($merchant_segments)) !!}
                                            @endif
                                    </div>
                            </div>
                            <h3 class="panel-title"><i class=" wb-flag" aria-hidden="true"></i>
                                @lang("$string_file.driver_map")
                            </h3>
                    </header>
                    <div class="panel-body container-fluid">
                        <div id="context-menu" style="width: 100%;height: 550px;"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=get_merchant_google_key(NULL, 'admin_backend');?>&libraries=visualization"></script>
@endsection
@section('js')
    <script>
        let map;
        let markers = [];
        let marker;
        let markerslocations;
        let infowindow;

        function initialize() {
            map = new google.maps.Map(document.getElementById('context-menu'), {
                zoom: 2,
                center: {lat: 8.7832, lng: 34.5085},
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            getDriverLocationo();
            // directionsDisplay.setMap(map);
        }

        function getDriverLocationo() {
            var token = $('[name="_token"]').val();
            var driver_status = '';
            var segment_id = null;
            if ($('#driver_status').val() != '') {
                driver_status = $('#driver_status').val();
            }
            if ($('#segment_id').val() != '') {
                segment_id = [$('#segment_id').val()];
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "{{ route('getDriverOnMap') }}",
                data: {
                    driver_status: driver_status,
                    segment_id: segment_id,
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    changeCountryCanter(data.country);
                    markerslocations = data.map_markers;
                    infowindow = new google.maps.InfoWindow();
                    for (var f = 0; f < markers.length; f++) {
                        markers[f].setMap(null);
                    }
                    for (var i = 0; i < markerslocations.length; i++) {
                        newName = markerslocations[i]['marker_name'];
                        marker_number = markerslocations[i]['marker_number'];
                        icon_url = markerslocations[i]['marker_icon'];
                        var icon = {
                            url: icon_url, // url
                            scaledSize: new google.maps.Size(50, 50), // scaled size
                            origin: new google.maps.Point(0,0), // origin
                            anchor: new google.maps.Point(0, 0) // anchor
                        };

                        marker_image = markerslocations[i]['marker_image'];
                        email = markerslocations[i]['marker_email'];
                        newLatitude = markerslocations[i]['marker_latitude'];
                        newLongitude = markerslocations[i]['marker_longitude'];
                        markerlatlng = new google.maps.LatLng(newLatitude, newLongitude);
                        content = '<table><tr><td rowspan="4"><img src="' + marker_image + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + email + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>' + marker_number + '</b></td></tr></table>';
                        var marker = new google.maps.Marker({
                            map: map,
                            title: newName,
                            position: markerlatlng,
                            icon: icon
                        });
                        google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                            return function () {
                                infowindow.setContent(content);
                                infowindow.open(map, marker);
                                map.panTo(this.getPosition());
                                //map.setZoom(21);
                            };
                        })(marker, content, infowindow));
                        markers.push(marker);
                    }
                }, error: function (e) {
                    console.log(e);
                }

            });
        }

        function changeCountryCanter(countryText) {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': countryText}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setZoom(5);
                    map.setCenter(results[0].geometry.location);
                }
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
        // initialize();
        //google.maps.event.addDomListener(window, 'load', initialize);

    </script>
@endsection
