@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-6 col-sm-8">
                            <h3 class="panel-title">
                                <i class=" fa fa-language" aria-hidden="true"></i>
                                @lang('admin.customize_string')</h3>
                        </div>
                    </div>
                </div>
                <div class="panel-body container-fluid">
                    <form method="post" action="">
                        <div class="row after-add-more">
                            <div class="col-md-2">
                                <label for="location3">Select Platform<span class="text-danger">*</span></label>
                                <select class="form-control" name="platform" id="platform" >
                                    <option value =""> -- Select One -- </option>
                                    <option value ="android"> Android </option>
                                    <option value ="ios"> IOS </option>
                                </select>
                                @if ($errors->has('platform'))
                                    <label class="text-danger">{{ $errors->first('platform') }}</label>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <label for="location3">Select Application<span class="text-danger">*</span></label>
                                <select class="form-control" name="app" id="app" >
                                    <option value =""> -- Select One -- </option>
                                    <option value ="USER"> User </option>
                                    <option value ="DRIVER"> Driver </option>
                                </select>
                                @if ($errors->has('app'))
                                    <label class="text-danger">{{ $errors->first('app') }}</label>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <label for="location3">Select String Key<span class="text-danger">*</span></label>
                                <select class="form-control" name="string_key" id="string_key" onchange="getKeyVal(this)">
                                    <option value =""> -- Select One -- </option>
                                    @foreach($strings->ApplicationString as $general_string)
                                        <option value="{{$general_string->id}}">{{$general_string->string_key}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('string_key'))
                                    <label class="text-danger">{{ $errors->first('string_key') }}</label>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="location3">Enter String Value<span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="form-control" name="key_value" id="key_value" placeholder="Enter Key Value" >
                                        @if ($errors->has('key_value'))
                                            <label class="text-danger">{{ $errors->first('key_value') }}</label>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-warning float-right add-more" type="button">Add <i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" ><i class="fa fa-check-circle"></i> @lang("$string_file.save")</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function getKeyVal(obj) {
            var id = $(obj).attr('id');
            alert(id);
            var key_id = obj.value;
            $.ajax({
                method: 'GET',
                url: "getStringVal",
                data: {key_id: key_id,loc:'en'},
                success: function (data) {
                    if(data){
                        console.log(data);
                        //$('#showData').html(data);
                    }else{
                        alert('No Data Found');
                    }
                }, error: function (e) {
                    console.log(e);
                }
            });
        }

        $(document).ready(function () {
            var limit = 4;
            count = 0;
            $(".add-more").click(function () {
                //return false;
                if (count == limit) {
                    return false;
                }
                count++;
                //console.log(count);
                var html = $(".copy").html();
                //////////////////////////////////////////////////////////////////
                if (count > 1) {
                    $(".after-add-more").after(html);
                    var last = $('.after-add-more').next();
                } else {
                    $(".after-add-more").before(html);
                    var last = $('.after-add-more').prev();
                }
                /////////////////////////////////////////////////////////////////

                $('.after-add-more').removeClass('after-add-more');
                last.addClass('after-add-more');
                //$('#platform').attr('q', count);

                $('#platform').attr('name', 'platform' + '_' + count);
                $('#platform').attr('id', 'platform' + '_' + count);

                $('#app').attr('name', 'app' + '_' + count);
                $('#app').attr('id', 'app' + '_' + count);

                $('#string_key').attr('name', 'string_key' + '_' + count);
                $('#string_key').attr('id', 'string_key' + '_' + count);

                $('#key_value').attr('name', 'key_value' + '_' + count);
                $('#key_value').attr('id', 'key_value' + '_' + count);


                if (count > 1) {
                    console.log($('.remove').length);
                    $('.remove').slice(0, ($('.remove').length - 2)).attr('disabled', true);
                    //$('.after-add-more').prev().children().children().attr('disabled', true)
                }
            });

            $("body").on("click", ".remove", function () {
                if ($(this).parents(".input-group").children().attr('q') < count)  // IT WILL RUN IF SOME EARLIER add more ELEMENT DELETED
                {   // DELETE ALL OF THE add more ELEMENTS, AS EARLIER WAS DELETED, COUNT VALUE DISTURBED
                    console.log('Yes Small FROM .remove');
                    while (count >= 1) {
                        if ($('#drop_loc_' + count).parents().hasClass('after-add-more')) {
                            // console.log("Yes this have class (after-add-more) ");
                            let last = $('.after-add-more').next();
                            last.addClass('after-add-more');
                        }
                        $('#drop_loc_' + count).parents(".input-group").remove();
                        //$(this).parents(".input-group").remove();
                        count--;
                        console.log(count);
                    }
                    if (($('#drop_latitude').val()) && ($('#pickup_latitude').val())) {
                        $("#loader1").show();
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        getDistance();
                        $("#loader1").hide();
                    }
                } else {
                    console.log('NO Small FROM .remove');
                    if ($(this).parents().hasClass('after-add-more')) {
                        console.log("Yes this have class (after-add-more) ");
                        if (count > 1) {
                            var last = $('.after-add-more').prev();
                        } else {
                            var last = $('.after-add-more').next();
                        }
                        //let last = $('.after-add-more').next();
                        last.addClass('after-add-more');
                        if (count > 1) {
                            $('.remove').slice(($('.remove').length - 3),($('.remove').length - 2)).attr('disabled', false);
                        }
                    }
                    $(this).parents(".row").remove();
                    //console.log($('.remove').length);
                    count--;
                    //console.log(count);
                }
            });

        });
    </script>
@endsection