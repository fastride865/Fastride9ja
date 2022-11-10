@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="card shadow ">
                    <div class="card-header py-3 ">

                        <h3 class="content-header-title mb-0 d-inline-block"><i
                                    class="fas fa-bed"></i> @lang('admin.message559')</h3>
                        <div class="btn-group float-md-right">
                            <div class="heading-elements">
                                <a title="@lang("$string_file.add") @lang('admin.message559')"
                                   href="{{route('franchisee.create')}}">
                                    <button class="btn btn-icon btn-success mr-1"><i class="fa fa-plus"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-header py-3">
                        <form action="#" method="post">
                            @csrf
                            <div class="table_search row">
                                <div class="col-md-2 col-xs-4 form-group ">
                                    <div class="input-group">
                                        <select class="form-control" name="parameter"
                                                id="parameter"
                                                required>
                                            <option value="1">@lang("$string_file.user_name")</option>
                                            <option value="2">@lang("$string_file.email")</option>
                                            <option value="3">@lang("$string_file.phone")</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-6 form-group ">
                                    <div class="input-group">
                                        <input type="text" name="keyword"
                                               placeholder="@lang("$string_file.enter_text")"
                                               class="form-control col-md-12 col-xs-12" required>
                                    </div>
                                </div>
                                <div class="col-sm-2  col-xs-12 form-group ">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"
                                                                                     aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display nowrap table-striped table-bordered" id="dataTable" width="100%"
                                   cellspacing="0">
                                <thead>
                                <th>#</th>
                                <th>@lang("$string_file.service_area")</th>
                                <th>@lang('admin.message561')</th>
                                <th>@lang('admin.message562')</th>
                                <th>@lang('admin.message563')</th>
                                <th>@lang('admin.message564')</th>
                                <th>@lang("$string_file.registered_date")</th>
                                <th>@lang("$string_file.status")</th>
                                <th>@lang("$string_file.action")</th>
                                <th>@lang("$string_file.url")</th>
                                </thead>
                                <tbody>
                                @php $sr = $franchisees->firstItem() @endphp
                                @foreach($franchisees as $franchisee)
                                    <tr>
                                        <td>
                                            {{ $sr }}
                                        </td>
                                        <td>
                                            {{ $franchisee->CountryArea->CountryAreaName }}
                                        </td>
                                        <td>{{ $franchisee->name }}</td>
                                        <td>{{ $franchisee->contact_person_name }}</td>
                                        <td>
                                            {{ $franchisee->email }}
                                        </td>
                                        <td>
                                            {{ $franchisee->phone }}
                                        </td>
                                        <td>{{ $franchisee->created_at->toformatteddatestring() }}</td>
                                        <td>
                                            @if($franchisee->status == 1)
                                                <label class="label_success">@lang("$string_file.active")</label>
                                            @else
                                                <label class="label_danger">@lang("$string_file.inactive")</label>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('franchisee.edit',$franchisee->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn menu-icon btn-warning action_btn"> <i
                                                        class="fa fa-edit"></i> </a>

                                            @if($franchisee->status == 1)
                                                <a href="{{ route('merchant.franchisee.active-deactive',['id'=>$franchisee->id,'status'=>2]) }}"
                                                   data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn menu-icon btn_eye_dis btn-success action_btn"> <i
                                                            class="fa fa-eye-slash"></i> </a>
                                            @else
                                                <a href="{{ route('merchant.franchisee.active-deactive',['id'=>$franchisee->id,'status'=>1]) }}"
                                                   data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn menu-icon btn_eye btn-danger action_btn"> <i
                                                            class="fa fa-eye"></i> </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ config('app.url') }}franchise/admin/{{Auth::guard('merchant')->user()->alias_name}}/{{$franchisee->alias}}/login"
                                               target="_blank">franchise/admin/{{Auth::guard('merchant')->user()->alias_name}}
                                                /{{$franchisee->alias}}/login</a></td>
                                    </tr>
                                    @php $sr++  @endphp
                                @endforeach
                                </tbody>
                            </table>
                            @include('merchant.shared.table-footer', ['table_data' => $franchisees, 'data' => []])
                        </div>
{{--                        <div class="col-sm-12">--}}
{{--                            <div class="pagination1">{{ $franchisees->links() }}</div>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection