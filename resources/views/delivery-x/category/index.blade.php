@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('categories.create')}}">
                            <button type="button" title=""
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-list-alt" aria-hidden="true"></i></h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th>@lang('admin.message188')</th>
                            <th>@lang('admin.message590')</th>
                            <th>@lang('admin.delivery_type')</th>
                            <th>@lang('admin.message206')</th>
                            <th>@lang('admin.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1@endphp
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $category->CategoryName }}
                                </td>
                                <td>
                                    {{ $category->DeliveryType->name }}
                                </td>
                                <td>
                                    {{ $category->CategoryDescription }}
                                </td>
                                <td>
                                    {{--                                            <form method="POST" action="{{ route('categories.destroy',$category->id) }}"--}}
                                    {{--                                                  onsubmit="return confirm('@lang('admin.are_you_sure')')">--}}
                                    {{--                                                @csrf--}}
                                    {{--                                                {{method_field('DELETE')}}--}}
                                    <a href="{{ route('categories.edit',$category->id) }}"
                                       data-original-title="Edit" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                    {{--                                                <button--}}
                                    {{--                                                        data-original-title="@lang('admin.delete')"--}}
                                    {{--                                                        data-toggle="tooltip"--}}
                                    {{--                                                        data-placement="top"--}}
                                    {{--                                                        class="btn btn-sm btn-danger menu-icon btn_edit action_btn">--}}
                                    {{--                                                    <i--}}
                                    {{--                                                            class="fa fa-trash"></i></button>--}}
                                    {{--                                            </form>--}}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

