@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('questions.create')}}">
                            <button type="button" title="@lang('admin.Addquestions')"
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon fa-question-circle-o" aria-hidden="true"></i>
                        @lang('admin.questions')</h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%" >
                        <thead>
                        <th>@lang("$string_file.sn")</th>
                        <th>@lang('admin.questions')</th>
                        <th>@lang("$string_file.registered_date")</th>
                        <th>@lang("$string_file.update")</th>
                        <th>@lang("$string_file.action")</th>
                        </thead>
                        <tbody>
                        @php $sr = 1; @endphp
                        @foreach($questions as $question)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $question->question }}</td>
                                <td>{{ $question->created_at }}</td>
                                <td>{{ $question->updated_at }}</td>
                                <td>
                                    <a href="{{ route('questions.edit',$question->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning"> <i
                                                class="wb-edit"></i> </a>
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

