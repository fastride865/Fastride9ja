@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class="fas fa-chart-pie" aria-hidden="true"></i>
                        @lang("$string_file.drivers_charts")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card" style="box-shadow: 10px 10px 10px gainsboro;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <h4 class="card-title text-center" style="color: black">@lang("$string_file.driver_signup_month_wise")</h4><hr>
                                        <div id="perf_div"></div>
                                        {!!  $lava->render('ColumnChart', 'Finances', 'perf_div') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="card" style="box-shadow: 10px 10px 10px gainsboro;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <h4 class="card-title text-center" style="color: black">@lang("$string_file.driver_growth_chart")</h4><hr>
                                        <div id="pop_div"></div>
                                        {!!  $lava->render('AreaChart', 'Population', 'pop_div') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="card" style="box-shadow: 10px 10px 10px gainsboro;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <h4 class="card-title text-center" style="color: black">@lang("$string_file.driver_area_wise_pie")</h4><hr>
                                        <div id="chart-div"></div>
                                        {!!  $lava->render('PieChart', 'IMDB', 'chart-div') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="card" style="box-shadow: 10px 10px 10px gainsboro;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <h4 class="card-title text-center" style="color: black">@lang("$string_file.top_driver_rating")</h4><hr>
                                        <div id="poll_div"></div>
                                        <?= $lava->render('BarChart', 'Rating', 'poll_div') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="card" style="box-shadow: 10px 10px 10px gainsboro;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <h4 class="card-title text-center" style="color: black">@lang("$string_file.top_driver_revenue")</h4><hr>
                                        <div id="rev-div"></div>
                                        {!!  $lava->render('BarChart', 'Votes', 'rev-div') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="card" style="box-shadow: 10px 10px 10px gainsboro;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <h4 class="card-title text-center" style="color: black">@lang("$string_file.drivers_services")</h4><hr>
                                        <div id="temps_div"></div>
                                        <?= $lava->render('GaugeChart', 'Temps', 'temps_div') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="card" style="box-shadow: 10px 10px 10px gainsboro;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <h4 class="card-title text-center" style="color: black">@lang("$string_file.drivers_vehicle")</h4><hr>
                                        <div id="ve-div"></div>
                                        {!!  $lava->render('DonutChart', 'Vehicle', 've-div') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

