@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('deleted'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.updated')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('goods.create')}}">
                            <button type="button" title="@lang('admin.add_new')"
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                        @lang('admin.goods_list')</h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>#Sr.No</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang('admin.delivery_types')</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($goods as $good)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $good->GoodName }}
                                </td>
                                <?php $c = array() ?>
                                @foreach($good->deliveryTypes as $type)
                                    @php $c[] = $type->name @endphp
                                @endforeach
                                <td>
                                    {{ implode(' | ',$c) }}
                                </td>
                                <td>
                                    @if($good->status == 1)
                                        <label class="label_success">@lang("$string_file.active")</label>
                                    @else
                                        <label class="label_danger">@lang("$string_file.inactive")</label>
                                    @endif
                                </td>
                                <td>


                                    <form method="POST" action="{{ route('goods.destroy',$good->id) }}"
                                          onsubmit="return confirm('@lang('admin.are_you_sure')')">
                                        @csrf
                                        {{method_field('DELETE')}}
                                        <a href="{{ route('goods.edit',$good->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                        <button
                                                data-original-title="@lang("$string_file.delete")"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-danger menu-icon btn_edit action_btn">
                                            <i
                                                    class="fa fa-trash"></i></button>
                                    </form>

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


