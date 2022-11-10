@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{route('business-segment.category.add')}}">
                            <button type="button" title="@lang("$string_file.add_category")"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-plus"></i>
                            </button>
                        </a>
                        <button type="button" title="@lang("$string_file.delete_category")" onclick="DeleteEvent('group')"
                                class="btn btn-icon btn-danger float-right" style="margin:10px"><i
                                    class="wb-trash"></i>
                        </button>
                            <a href="{{route('merchant.category.export',$arr_search)}}">
                            <button type="button" title="@lang("$string_file.export_category")"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-download"></i>
                            </button>
                            </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-building" aria-hidden="true"></i>@lang("$string_file.category")</h3>
                </header>
                <div class="panel-body">
                    @php
                        $category = isset($arr_search['category']) ? $arr_search['category'] : "";
                    @endphp
                    {!! Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']) !!}
                    <div class="table_search row">
                        <div class="col-md-3 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="category" value="{{$category}}"
                                       placeholder="@lang("$string_file.category")"
                                       class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            <a href="{{$search_route}}" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <hr>
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th class="w-50">
                            <span class="checkbox-custom checkbox-primary">
                              <input class="example-select-all" type="checkbox" name="select_all" value="1" id="example-select-all">
                              <label></label>
                            </span>
                            </th>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.parent_category")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.sequence")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sr = 1; $arr_data = (!empty($data['parent_category']) && isset($data['parent_category'])) ? $data['parent_category'] : NULL;
                            unset($data['parent_category']);
                        @endphp
                        @foreach($data as $category)
                            <tr>
                                <td>
                                    <span class="checkbox-custom checkbox-primary">
                                      <input class="selectable-item" type="checkbox" id="ids[]"
                                             name="ids[]" value="{{$category->id}}">
                                      <label for="row-{{$category->id}}"></label>
                                    </span>
                                </td>
                                <td>{{ $sr }}</td>
                                <td>
{{--                                    {{ implode(',',array_pluck($category->Segment->toArray(),'slag')) }}--}}
                                    @foreach($category->Segment as $segment)
                                        {{$segment->Name($category->merchant_id)}},
                                    @endforeach
                                </td>
                                <td>
                                    {{ $category->Name($category->merchant_id) }}

                                </td>
                                <td>
                                    @if(!empty($category->category_parent_id))
                                        @php $parent_category = $arr_data->where('id',$category->category_parent_id);
                                        $parent_category = collect($parent_category->values());
                                         @endphp
                                        @if(isset($parent_category[0]) && !empty($parent_category[0]->id))
                                        {{ $parent_category[0]->Name($category->merchant_id) }}
                                        @endif
                                    @else
                                        @lang("$string_file.none")
                                    @endif
                                </td>
                                <td>@if($category->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>{{ $category->sequence }}</td>
                                <td>
                                    @if(!empty($data) && !empty($category['category_image']))
                                        @php $image = get_image($category->category_image,'category',$category->merchant_id); @endphp
                                        <a href="{{ $image }}" target="_blank">
                                            <img src="{{ $image }}" height="30" width="30">
                                        </a>
                                    @endif
                                </td>
                                @php $created_at = convertTimeToUSERzone($category->created_at, null, null, $category->Merchant,2); @endphp
                                <td>{!! $created_at !!}</td>
{{--                                <th>{{date_format($category->created_at,'H:i a')}}--}}
{{--                                    <br>--}}
{{--                                    {{date_format($category->created_at,'M d, Y')}}</th>--}}
                                <td>
{{--                                    @if(Auth::user('merchant')->can('add_category'))--}}
                                    <a href="{!! route('business-segment.category.add',$category->id) !!}"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="wb-edit"></i>
                                    </a>

                                    @csrf
                                    <button onclick="DeleteEvent({{ $category->id }})"
                                            type="button"
                                            data-original-title="@lang("$string_file.delete")"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $data, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id) {
            var is_ok = true;
            var token = $('[name="_token"]').val();
            if(id == 'group'){
                var searchIDs = $("#customDataTable input:checkbox:checked").map(function(){
                    return $(this).val();
                }).get();
                id = searchIDs
                if(id.length == 0){
                    is_ok = false;
                    swal("@lang("$string_file.select_at_least_one_record")");
                }
            }
            if(is_ok){
                swal({
                    title: "@lang("$string_file.are_you_sure")",
                    text: "@lang("$string_file.delete_category")",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirm) => {
                    if (isConfirm) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            type: "POST",
                            data: {
                                id: id,
                            },
                            url: "{{ route('business-segment.category.destroy') }}",
                        })
                            .done(function (data) {
                                swal({
                                    title: "DELETED!",
                                    text: data,
                                    type: "success",
                                });
                                window.location.href = "{{ route('merchant.category') }}";
                            });
                    } else {
                        swal("@lang("$string_file.data_is_safe")");
                    }
                });
            }
        }
        $(document).ready(function(){
            // Handle click on "Select all" control
            $('#example-select-all').on('click', function(){
                // Get all rows with search applied
                var table = $('#customDataTable').DataTable();
                var rows = table.rows({ 'search': 'applied' }).nodes();
                // Check/uncheck checkboxes for all rows in the table
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            // Handle click on checkbox to set state of "Select all" control
            $('.selectable-item').on('click', function(){
                // If checkbox is not checked
                if(!this.checked){
                    var el = $('#example-select-all').get(0);
                    // If "Select all" control is checked and has 'indeterminate' property
                    // if(el && el.checked && ('indeterminate' in el)){
                    if(el && el.checked){
                        // Set visual state of "Select all" control
                        // as 'indeterminate'
                        el.indeterminate = true;
                        $('#example-select-all').prop('checked', false);
                    }
                }
            });
        });
    </script>
@endsection