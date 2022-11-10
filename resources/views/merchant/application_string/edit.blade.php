@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="mr--10 ml--10">
                        <div class="row" style="margin-right: 0rem;margin-left: 0rem">
                            <div class="col-md-4 col-sm-4 col-4">
                            <h3 class="panel-title">
                                <i class="fa fa-language"></i>
                                @lang("$string_file.customize_string")
                            </h3>
                            </div>
                            {!! Form::open(['url'=>route('admin-app-string'),'id'=>'filter','class'=>'','method'=>'GET']) !!}
                            <div class="col-md-12 col-sm-12 col-12">
                                <div class="row">
                                    <input type="hidden" value="{{app()->getLocale()}}" name="loc">
                               <div class="col-md-4 col-sm-4 ">
                                   {!! Form::select('platform',[''=>'--'.trans("$string_file.app").'--','android'=>trans("$string_file.android"),'ios'=>trans("$string_file.ios")],isset($searched_param['platform']) ? $searched_param['platform'] : NULL,["class"=>"form-control mt-10","required"=>true]) !!}
{{--                                <select class="form-control mt-10" name="platform" id="platform" >--}}
{{--                                    <option value =""> -- @lang("$string_file.app") -- </option>--}}
{{--                                    <option value ="android"> @lang("$string_file.android") </option>--}}
{{--                                    <option value ="ios"> @lang("$string_file.ios") </option>--}}
{{--                                </select>--}}
                                @if ($errors->has('platform'))
                                    <label class="text-danger">{{ $errors->first('platform') }}</label>
                                @endif
                                </div>
                                    <div class="col-md-4 col-sm-4">
                                        {!! Form::select('app',[''=> '--'.trans("$string_file.application"),'USER'=>trans("$string_file.user"),'DRIVER'=>trans("$string_file.driver")],isset($searched_param['app']) ? $searched_param['app'] : NULL,["class"=>"form-control mt-10","required"=>true]) !!}
{{--                                <select class="form-control mt-10" name="app" id="app">--}}
{{--                                    <option value =""> -- @lang("$string_file.application") -- </option>--}}
{{--                                    <option value ="USER"> @lang("$string_file.user") </option>--}}
{{--                                    <option value ="DRIVER"> @lang("$string_file.driver") </option>--}}
{{--                                </select>--}}
                                @if ($errors->has('app'))
                                    <label class="text-danger">{{ $errors->first('app') }}</label>
                                @endif
                                    </div>
                                    <div class="col-md-2">
                                    <button type="submit" id="search_data" class="btn btn-primary mt-10"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-md-2">
                                <a href="{{ route('applicationstring.index') }}">
                                    <button type="button" class="btn btn-success mt-10"><i class="fa fa-reply"></i>
                                    </button>
                                </a>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{route('customSave')}}">
                        @csrf
                        <div id="show_val">
                            {!! $final_text !!}
{{--                            <h3 class="text-center">@lang("$string_file.no_data")</h3>--}}
                        </div><br>
                        <div class="row">
                            <div class="col-md-12">
                                @if($result)
                                <input type="hidden" value="{{app()->getLocale()}}" name="loc">
                                <button type="submit" id="save_data" class="btn btn-primary"><i class="fa fa-check-circle"></i> @lang("$string_file.save")</button>
                            @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function sweetalert(msg) {
            swal({
                title: "Error",
                text: msg,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            });
        }

        function getKeyVal(obj) {
            var application = document.getElementById('app').value;
            var platform = document.getElementById('platform').value;
            var loc = "{{app()->getLocale()}}";
            if (platform == "") {
                sweetalert("Please Select Any Platform");
                return false;
            }
            if (application == "") {
                sweetalert("Please Select Any Application");
                return false;
            }


            $.ajax({
                method: 'GET',
                url: "getStringVal",
                data: {application: application,platform:platform,loc:loc},
                success: function (data) {
                    if(data){
                        $('#show_val').html(data);
                        $('#save_data').prop('hidden',false);
                    }else{
                        $('#show_val').text("");
                        alert('No Data Found');
                        return false;
                    }
                }, error: function (e) {
                    console.log(e);
                }
            });
        }

    </script>
@endsection

