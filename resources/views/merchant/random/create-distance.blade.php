@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">@lang('admin.distancecalculationsettings')</h3>
                </div>
                <div class="content-header-right col-md-4 col-12">
                    <div class="btn-group float-md-right">
                        <a href="{{ route('merchant.distnace') }}">
                            <button type="button" class="btn btn-icon btn-success mr-1"><i class="fa fa-reply"></i>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" action="{{ route('merchant.distnace.store') }}" method="POST">
                                            @csrf
                                            <div class="row justify-content-md-center">
                                                <div class="col-md-6">
                                                    <div class="form-body">
                                                        <div class="form-group">
                                                            <label for="eventInput1"><h1>First Logic</h1></label>
                                                            <select class="form-control mySelect" name="method_id[]"
                                                                    id="first_logic" onchange="firstdiv(this.value)"
                                                                    required>
                                                                <option value="">--Select Method--</option>
                                                                @foreach($methods as $method)
                                                                    <option id="{{ $method->id }}"
                                                                            value="{{ $method->id }}">{{ $method->method }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="first-div" id="first-div">
                                                            <h4 class="form-section">Pre-Checks</h4>

                                                            <div class="row" style="margin-bottom: 10px">
                                                                <div class="col-md-8"><input type="checkbox"
                                                                                             id="checkbox1" value="1"
                                                                                             name="last_timestamp_difference_checkbox_one"
                                                                                             onclick="changeinput(this.value)">@lang('admin.lastlat')
                                                                </div>
                                                                <div class="col-md-4" style="margin-left: -50px"><input
                                                                            type="number" class="form-control"
                                                                            id="input1"
                                                                            name="last_timestamp_difference_one"
                                                                            placeholder="@lang('admin.seconds')"
                                                                            disabled></div>
                                                            </div>

                                                            <div class="row" style="margin-bottom: 10px">
                                                                <div class="col-md-8"><input type="checkbox"
                                                                                             id="checkbox2"
                                                                                             name="maximum_timestamp_difference_checkbox_one"
                                                                                             value="2"
                                                                                             onclick="changeinput(this.value)">@lang('admin.maxlat')
                                                                </div>
                                                                <div class="col-md-4" style="margin-left: -50px"><input
                                                                            type="number" class="form-control"
                                                                            id="input2"
                                                                            name="maximum_timestamp_difference_one"
                                                                            placeholder="@lang('admin.seconds')"
                                                                            disabled></div>
                                                            </div>

                                                            <div class="row" style="margin-bottom: 10px">
                                                                <div class="col-md-8"><input type="checkbox"
                                                                                             id="checkbox3"
                                                                                             name="minimum_lat_long_checkbox_one"
                                                                                             value="3"
                                                                                             onclick="changeinput(this.value)">@lang('admin.minilat')
                                                                </div>
                                                                <div class="col-md-4" style="margin-left: -50px"><input
                                                                            type="number" class="form-control"
                                                                            id="input3" name="minimum_lat_long_one"
                                                                            placeholder="@lang('admin.number')"
                                                                            disabled></div>
                                                            </div>

                                                            <div class="row" style="margin-bottom: 10px">
                                                                <div class="col-md-8"><input type="checkbox"
                                                                                             name="unknown_road_one"
                                                                                             id="checkbox"
                                                                                             value="1">@lang('admin.unnamed')
                                                                </div>
                                                            </div>

                                                            <h4 class="form-section">Post-Checks</h4>

                                                            <div class="form-group">
                                                                <div class="row" style="margin-bottom: 10px">
                                                                    <div class="col-md-2"><input type="checkbox"
                                                                                                 id="checkbox4"
                                                                                                 name="speed_checkbox_one"
                                                                                                 value="4"
                                                                                                 onclick="speedchangeinput(this.value)">@lang('admin.speed')
                                                                    </div>
                                                                    <div class="col-md-4" style="margin-left: -35px">
                                                                        <input type="number" class="form-control"
                                                                               id="input4" name="min_speed_one"
                                                                               placeholder="@lang("$string_file.min")"
                                                                               disabled></div>
                                                                    <div class="col-md-4" style="margin-left: -11px">
                                                                        <input type="number" class="form-control"
                                                                               id="input_min4" name="max_speed_one"
                                                                               placeholder="@lang('admin.max')"
                                                                               disabled></div>
                                                                </div>
                                                            </div>


                                                            <div class="form-group">
                                                                <label for="eventInput1"><h1>Second Logic</h1></label>
                                                                <select class="form-control mySelect" name="method_id[]"
                                                                        id="second_logic"
                                                                        onchange="seconddiv(this.value)" disabled>
                                                                    <option value="">--Select Method--</option>
                                                                    @foreach($methods as $method)
                                                                        <option id="{{ $method->id }}"
                                                                                value="{{ $method->id }}">{{ $method->method }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div id="second-div">
                                                                <h4 class="form-section">Pre-Checks</h4>

                                                                <div class="row" style="margin-bottom: 10px">
                                                                    <div class="col-md-8"><input type="checkbox"
                                                                                                 id="checkbox11"
                                                                                                 value="11"
                                                                                                 name="last_timestamp_difference_checkbox_second"
                                                                                                 onclick="changeinput(this.value)">@lang('admin.lastlat')
                                                                    </div>
                                                                    <div class="col-md-4" style="margin-left: -50px">
                                                                        <input type="number" class="form-control"
                                                                               id="input11"
                                                                               name="last_timestamp_difference_second"
                                                                               placeholder="@lang('admin.seconds')"
                                                                               disabled></div>
                                                                </div>

                                                                <div class="row" style="margin-bottom: 10px">
                                                                    <div class="col-md-8"><input type="checkbox"
                                                                                                 id="checkbox12"
                                                                                                 name="maximum_timestamp_difference_checkbox_second"
                                                                                                 value="12"
                                                                                                 onclick="changeinput(this.value)">@lang('admin.maxlat')
                                                                    </div>
                                                                    <div class="col-md-4" style="margin-left: -50px">
                                                                        <input type="number" class="form-control"
                                                                               id="input12"
                                                                               name="maximum_timestamp_difference_second"
                                                                               placeholder="@lang('admin.seconds')"
                                                                               disabled></div>
                                                                </div>

                                                                <div class="row" style="margin-bottom: 10px">
                                                                    <div class="col-md-8"><input type="checkbox"
                                                                                                 id="checkbox13"
                                                                                                 name="minimum_lat_long_checkbox_second"
                                                                                                 value="13"
                                                                                                 onclick="changeinput(this.value)">@lang('admin.minilat')
                                                                    </div>
                                                                    <div class="col-md-4" style="margin-left: -50px">
                                                                        <input type="number" class="form-control"
                                                                               id="input13"
                                                                               name="minimum_lat_long_second"
                                                                               placeholder="@lang('admin.number')"
                                                                               disabled></div>
                                                                </div>

                                                                <div class="row" style="margin-bottom: 10px">
                                                                    <div class="col-md-8"><input type="checkbox"
                                                                                                 name="unknown_road_second"
                                                                                                 id="checkbox"
                                                                                                 value="1">@lang('admin.unnamed')
                                                                    </div>
                                                                </div>

                                                                <h4 class="form-section">Post-Checks</h4>

                                                                <div class="form-group">
                                                                    <div class="row" style="margin-bottom: 10px">
                                                                        <div class="col-md-2"><input type="checkbox"
                                                                                                     id="checkbox14"
                                                                                                     name="speed_checkbox_second"
                                                                                                     value="14"
                                                                                                     onclick="speedchangeinput(this.value)">@lang('admin.speed')
                                                                        </div>
                                                                        <div class="col-md-4"
                                                                             style="margin-left: -35px"><input
                                                                                    type="number" class="form-control"
                                                                                    id="input14" name="min_speed_second"
                                                                                    placeholder="@lang("$string_file.min")"
                                                                                    disabled></div>
                                                                        <div class="col-md-4"
                                                                             style="margin-left: -11px"><input
                                                                                    type="number" class="form-control"
                                                                                    id="input_min14"
                                                                                    name="max_speed_second"
                                                                                    placeholder="@lang('admin.max')"
                                                                                    disabled></div>
                                                                    </div>
                                                                </div>


                                                                <div class="form-group">
                                                                    <label for="eventInput1"><h1>Third Logic</h1>
                                                                    </label>
                                                                    <select class="form-control mySelect"
                                                                            name="method_id[]" id="third_logic"
                                                                            onchange="thirddiv(this.value)" disabled>
                                                                        <option value="">--Select Method--</option>
                                                                        @foreach($methods as $method)
                                                                            <option id="{{ $method->id }}"
                                                                                    value="{{ $method->id }}">{{ $method->method }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div id="third">
                                                                    <h4 class="form-section">Pre-Checks</h4>

                                                                    <div class="row" style="margin-bottom: 10px">
                                                                        <div class="col-md-8"><input type="checkbox"
                                                                                                     id="checkbox21"
                                                                                                     value="21"
                                                                                                     name="last_timestamp_difference_checkbox_third"
                                                                                                     onclick="changeinput(this.value)">@lang('admin.lastlat')
                                                                        </div>
                                                                        <div class="col-md-4"
                                                                             style="margin-left: -50px"><input
                                                                                    type="number" class="form-control"
                                                                                    id="input21"
                                                                                    name="last_timestamp_difference_third"
                                                                                    placeholder="@lang('admin.seconds')"
                                                                                    disabled></div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom: 10px">
                                                                        <div class="col-md-8"><input type="checkbox"
                                                                                                     id="checkbox22"
                                                                                                     name="maximum_timestamp_difference_checkbox_third"
                                                                                                     value="22"
                                                                                                     onclick="changeinput(this.value)">@lang('admin.maxlat')
                                                                        </div>
                                                                        <div class="col-md-4"
                                                                             style="margin-left: -50px"><input
                                                                                    type="number" class="form-control"
                                                                                    id="input22"
                                                                                    name="maximum_timestamp_difference_third"
                                                                                    placeholder="@lang('admin.seconds')"
                                                                                    disabled></div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom: 10px">
                                                                        <div class="col-md-8"><input type="checkbox"
                                                                                                     id="checkbox23"
                                                                                                     name="minimum_lat_long_checkbox_third"
                                                                                                     value="23"
                                                                                                     onclick="changeinput(this.value)">@lang('admin.minilat')
                                                                        </div>
                                                                        <div class="col-md-4"
                                                                             style="margin-left: -50px"><input
                                                                                    type="number" class="form-control"
                                                                                    id="input13"
                                                                                    name="minimum_lat_long_third"
                                                                                    placeholder="@lang('admin.number')"
                                                                                    disabled></div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom: 10px">
                                                                        <div class="col-md-8"><input type="checkbox"
                                                                                                     name="unknown_road_third"
                                                                                                     id="checkbox"
                                                                                                     value="1">@lang('admin.unnamed')
                                                                        </div>
                                                                    </div>

                                                                    <h4 class="form-section">Post-Checks</h4>

                                                                    <div class="form-group">
                                                                        <div class="row" style="margin-bottom: 10px">
                                                                            <div class="col-md-2"><input type="checkbox"
                                                                                                         id="checkbox24"
                                                                                                         name="speed_checkbox_second"
                                                                                                         value="24"
                                                                                                         onclick="speedchangeinput(this.value)">@lang('admin.speed')
                                                                            </div>
                                                                            <div class="col-md-4"
                                                                                 style="margin-left: -35px"><input
                                                                                        type="number"
                                                                                        class="form-control"
                                                                                        id="input24"
                                                                                        name="min_speed_third"
                                                                                        placeholder="@lang("$string_file.min")"
                                                                                        disabled></div>
                                                                            <div class="col-md-4"
                                                                                 style="margin-left: -11px"><input
                                                                                        type="number"
                                                                                        class="form-control"
                                                                                        id="input_min24"
                                                                                        name="max_speed_third"
                                                                                        placeholder="@lang('admin.max')"
                                                                                        disabled></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="eventInput1"><h1>Fourth Logic</h1>
                                                                        </label>
                                                                        <select class="form-control mySelect"
                                                                                name="method_id[]" id="fourth_logic"
                                                                                disabled>
                                                                            <option value="">--Select Method--</option>
                                                                            @foreach($methods as $method)
                                                                                <option id="{{ $method->id }}"
                                                                                        value="{{ $method->id }}">{{ $method->method }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-actions center">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-check-square-o"></i> Save
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>


        </div>
    </div>

    <script>
        let methodArray = ["8", "9", "4", "5", "6", "7"];

        function speedchangeinput(val) {
            if (document.getElementById('checkbox' + val).checked) {
                document.getElementById('input' + val).disabled = false;
                document.getElementById('input_min' + val).disabled = false;
            } else {
                document.getElementById('input' + val).disabled = true;
                document.getElementById('input_min' + val).disabled = true;
            }
        }

        function firstdiv(val) {
            if (methodArray.indexOf(val) != -1) {
                document.getElementById('first-div').style.display = 'none';
            } else {
                document.getElementById('first-div').style.display = 'block';
            }
            document.getElementById('second_logic').disabled = false;
        }

        function seconddiv(val) {
            if (methodArray.indexOf(val) != -1) {
                document.getElementById('second-div').style.display = 'none';
            } else {
                document.getElementById('second-div').style.display = 'block';
            }
            document.getElementById('third_logic').disabled = false;
        }


        function thirddiv(val) {
            if (methodArray.indexOf(val) != -1) {
                document.getElementById('second-div').style.display = 'none';
            } else {
                document.getElementById('second-div').style.display = 'block';
            }
            document.getElementById('fourth_logic').disabled = false;
        }

        function changeinput(val) {
            if (document.getElementById('checkbox' + val).checked) {
                document.getElementById('input' + val).disabled = false;
            } else {
                document.getElementById('input' + val).disabled = true;
            }
        }
    </script>
@endsection