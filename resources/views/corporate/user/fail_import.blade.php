@extends('corporate.layouts.main')
@section('content')
    @csrf
    <div class="page">
        <div class="page-content">
         @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ URL::previous() }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.user_management")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-users" aria-hidden="true"></i>
                        @lang("$string_file.import_fail_user",['count'=> count($users)])</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.fail_reason")</th>
                            <th>@lang("$string_file.date")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $users->firstItem() @endphp
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $sr }}  </td>
                                @if(Auth::user()->Merchant->demo == 1)
                                    <td>
                                        <span class="long_text">   {!! nl2br("********".substr($user->name, -2)."\n"."********".substr($user->phone, -2)."\n"."********".substr($user->email, -2)) !!}</span>
                                    </td>
                                @else
                                    <td>
                                        <span class="long_text">   {!! nl2br($user->name."\n".$user->phone."\n".$user->email) !!}</span>
                                    </td>
                                @endif
                                @php $failReasons = json_decode($user->error_message) @endphp
                                @if(!empty($failReasons))
                                    <td>
                                        @foreach($failReasons as $failReason)
                                            {{$failReason}},<br>
                                        @endforeach
                                    </td>
                                @else
                                    <td> --- </td>
                                @endif

                                <td>{{ $user->created_at->toformatteddatestring() }}</td>
                                <td>
                                    <div>
                                        @if(Auth::user()->Merchant->demo != 1)
                                            <a href="#" class="btn btn-sm btn-danger menu-icon" data-original-title="Delete" data-toggle="tooltip"
                                               data-placement="top" data-Id = "{{$user->id}}" onclick="EditDoc(this)"> <i
                                                        class="fa fa-trash"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditDOc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.delete_user")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('corporate.user.import.fail.destroy') }}">
                    @csrf
                    <div class="modal-body text-center">
                        <label><b class="text-danger">@lang("$string_file.are_you_sure")</b></label>
                        <label><b class="text-danger">@lang("$string_file.delete_warning")</b></label>
                        <input type="hidden" id="userId" name="userId">
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-sm btn-danger" value="@lang("$string_file.delete")">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function EditDoc(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #userId").val(ID);
            $('#EditDOc').modal('show');
        }
    </script>
@endsection
