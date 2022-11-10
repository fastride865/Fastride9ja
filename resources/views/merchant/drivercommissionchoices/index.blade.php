@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if($commission_options)
                @include('merchant.shared.errors-and-messages')
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <div class="panel-actions">
                            @if(!empty($info_setting) && $info_setting->view_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-8">
                                <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                                    @lang("$string_file.driver_commission_choice")</h3>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body container-fluid">
                        <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th> {{--S.no--}}
                                <th>@lang("$string_file.name")</th>
                                <th>@lang("$string_file.for")</th>
                                <th>@lang("$string_file.action")</th>
                            </tr>
                            </thead>
                            @php $sr = $commission_options->firstItem() @endphp
                            <tbody>
                            @forelse($commission_options as $commission_option)
                                <tr>
                                    <td>{{ $sr  }}</td>
                                    <td>@if(!empty($commission_option->NameAccMerchantWeb))
                                            @if(empty($commission_option->LangCommissionChoiceAccMerchantSingle))
                                                <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                                <span class="text-primary">( In {{ $commission_option->LangCommissionChoiceAccMerchantAny->LanguageName->name }}
                                                                            : {{ $commission_option->LangCommissionChoiceAccMerchantAny['name'] }}
                                                                            )</span>
                                            @else
                                                {{ $commission_option->LangCommissionChoiceAccMerchantSingle['name'] }}
                                            @endif
                                        @else
                                            -----------
                                        @endif
                                    </td>
                                    <td>
                                        {{ $commission_option->slug }}
                                    </td>

                                    <td>
                                        <div class="button-margin">
                                            @if(Auth::user('merchant')->can('update_driver_commission_choices'))
                                                <a href="{{ route('driver-commission-choices.edit',$commission_option->id) }}"
                                                   data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                            class="fa fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                        @csrf
                                    </td>
                                </tr>
                                @php $sr++  @endphp
                            @empty
                                @if(($commission_options->total() > 0) ||  (isset($_REQUEST['keyword'])))
                                    <p class="alert alert-warning">{{trans("$string_file.data_not_found")}}</p>
                                @else
                                @endif
                            @endforelse
                            </tbody>
                        </table>
                        @include('merchant.shared.table-footer', ['table_data' => $commission_options, 'data' => []])
                        {{--                        <div class="pagination1 float-right">{{$commission_options->links()}}</div>--}}
                    </div>
                </div>
            @endif
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@endsection

