@extends('taxicompany.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                @csrf
                <div class="content-body">
                    <section id="gmaps-utils">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-content collapse show">
                                        <div class="card-header py-3">
                                            <h4 class="content-header-title mb-0 d-inline-block"><i class="fas fa-users"></i> @lang("$string_file.driver_map")</h4>
                                            {{--<div class="content-header-right col-md-8 col-12">--}}
                                                <div class="btn-group float-right">
                                                    <div class="heading-elements">
                                                        <div class="form-group">
                                                            <select onchange="getDriverLocationo(this.value)"
                                                                    class="c-select form-control"
                                                                    id="driver_marker"
                                                                    name="driver_marker">
                                                                <option value="1">@lang("$string_file.all")</option>
                                                                <option value="2">@lang("$string_file.available")</option>
                                                                <option value="3">@lang("$string_file.enroute_pickup")</option>
                                                                <option value="4">@lang("$string_file.reached_pickup")</option>
                                                                <option value="5">@lang("$string_file.journey_started")</option>
                                                                <option value="6">@lang("$string_file.offline")</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            {{--</div>--}}
                                        </div>
                                        <div class="card-body">
                                            <div id="context-menu" style="width: 100%;height: 550px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <br>
    </div>
{{--    <script async defer--}}
{{--            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDkKetQwosod2SZ7ZGCpxuJdxY3kxo5Po&libraries=visualization">--}}
{{--    </script>--}}
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?=get_merchant_google_key(NULL,'admin_backend');?>&libraries=visualization">
    </script>
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
            // directionsDisplay.setMap(map);
            getDriverLocationo(1);
        }

        function getDriverLocationo(type) {
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "{{ route('getDriverOnMaps') }}",
                data: {
                    type: type,
                },
                success: function (data) {
                    markerslocations = JSON.parse(data);
                    infowindow = new google.maps.InfoWindow();
                    for (var f = 0; f < markers.length; f++) {
                        markers[f].setMap(null);
                    }
                    for (var i = 0; i < markerslocations.length; i++) {
                        newName = markerslocations[i]['marker_name'];
                        marker_number = markerslocations[i]['marker_number'];
                        icon = markerslocations[i]['marker_icon'];
                        marker_image = markerslocations[i]['marker_image'];
                        email = markerslocations[i]['marker_email'];
                        newLatitude = markerslocations[i]['marker_latitude'];
                        newLongitude = markerslocations[i]['marker_longitude'];
                        markerlatlng = new google.maps.LatLng(newLatitude, newLongitude);
                        content = '<table><tr><td rowspan="4"><img src="' + marker_image + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + email + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + marker_number + '</b></td></tr></table>';
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
                                map.setZoom(21);
                            };
                        })(marker, content, infowindow));
                        markers.push(marker);
                    }
                }, error: function (e) {
                    console.log(e);
                }

            });
        }
        google.maps.event.addDomListener(window, 'load', initialize);

    </script>
@endsection