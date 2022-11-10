@extends('taxicompany.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <section id="gmaps-utils">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-content collapse show">
                                        <div class="card-header py-3">
                                            <h4 class="content-header-title mb-0 d-inline-block"><i class="fas fa-map"></i> @lang("$string_file.heat_map")</h4>
                                            <div class="btn-group float-md-right">
                                                <button type="button" class="btn btn-outline-primary btn-min-width box-shadow-1 mr-1 mb-1"
                                                        onclick="changeGradient()">@lang("$string_file.change_gradient")
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-min-width box-shadow-1 mr-1 mb-1"
                                                        onclick="changeRadius()">@lang("$string_file.change_radius")
                                                </button>
                                            </div>
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
@endsection
@section('js')
    <script>

        var map, heatmap;

        function initMap() {
            map = new google.maps.Map(document.getElementById('context-menu'), {
                zoom: 2,
                center: {lat:28.7041, lng: 77.1025},
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            heatmap = new google.maps.visualization.HeatmapLayer({
                data: getPoints(),
                map: map
            });
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
            return [
                @foreach($bookings as $booking)
                new google.maps.LatLng({{$booking->pickup_latitude}},{{$booking->pickup_longitude}}),
                @endforeach
            ];
        }
    </script>
{{--    <script async defer--}}
{{--            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDkKetQwosod2SZ7ZGCpxuJdxY3kxo5Po&libraries=visualization&callback=initMap">--}}
{{--    </script>--}}
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?=get_merchant_google_key(NULL,'admin_backend');?>&libraries=visualization&callback=initMap">
    </script>
@endsection