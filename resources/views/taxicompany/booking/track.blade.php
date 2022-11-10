@extends('merchant.layouts.main')
@section('content')
    <style>
        #map {
            height: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
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
                                            <h4 class="content-header-title mb-0 d-inline-block"><i
                                                        class="fas fa-fw fa-map"></i> Driver Tracking Map</h4>
                                        </div>
                                        <div class="card-body">
                                            <div id="map" style="width: 100%;height: 550px;"></div>
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
@endsection
@section('js')  
    <script>
        var map;
        var position = [{{$booking->pickup_latitude}},{{$booking->pickup_longitude}}];
    
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: {{$booking->pickup_latitude}}, lng: {{$booking->pickup_longitude}}},
                zoom: 12
            });
            var driverLocation = new google.maps.LatLng({{$booking->Driver->current_latitude}},{{$booking->Driver->current_longitude}});
            var icon = {
                url: "{{ view_config_image($booking->VehicleType->vehicleTypeMapImage) }}", // url
                scaledSize: new google.maps.Size(50, 50), // scaled size
            };
            marker = new google.maps.Marker({
                position: driverLocation,
                map: map,
                animation: google.maps.Animation.DROP,
                icon: icon
            });
            var refreshId = setInterval(function () {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{route('driverTrack')}}",
                    data: 'driver_id=' + {{ $booking->Driver->id}} ,
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{$booking->Merchant->BookingConfiguration->google_key}}&callback=initMap"
            async defer></script>
@endsection