@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
       @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <button type="button" class="btn btn-icon btn-success" data-toggle="modal"
                                data-target="#inlineForm" style="margin: 10px;">
                            <i class="wb-plus" title="@lang("$string_file.add_designation")"></i>
                        </button>
                    </div>
                    <h3 class="panel-title"><i class="fab fa-get-pocket" aria-hidden="true"></i>
                        @lang("$string_file.designation_management")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.id")</th>
                            <th>@lang("$string_file.designation_name")</th>
                            <th>@lang("$string_file.expense_limit")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $designations->firstItem() @endphp
                        @foreach($designations as $designation)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>#{{ $designation->designation_id }}</td>
                                <td>{{ $designation->designation_name }}</td>
                                <td>{{ $designation->designation_expense_limit }}</td>
                                <td>
                                    <button type="submit"
                                            class="btn btn-sm btn-warning menu-icon btn_edit action_btn"
                                            onclick="EditDoc(this)"
                                            data-ID="{{ $designation->id }}"
                                            data-Name="{{ $designation->designation_name }}"
                                            data-limit="{{ $designation->designation_expense_limit }}">
                                        <i class="fa fa-edit" title="Edit"></i>
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-danger menu-icon btn_delete action_btn"
                                            data-Id = "{{$designation->id}}"
                                            onclick="DeleteDesignation(this)"><i class="fa fa-trash" title="Delete"></i>
                                    </button>
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
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.add_designation") </b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('employeeDesignation.store') }}">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.designation_name") <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" name="designation_name"
                                   placeholder="" maxlength="80" required>
                            @if ($errors->has('designation_name'))
                                <label class="text-danger">{{ $errors->first('designation_name') }}</label>
                            @endif
                        </div>
                        <label>@lang("$string_file.expense_limit") <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="expense_limit" name="expense_limit"
                                   placeholder="" maxlength="80" required>
                            @if ($errors->has('expense_limit'))
                                <label class="text-danger">{{ $errors->first('expense_limit') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-primary" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditDOc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.edit_designation") </b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{route('employee.Designation.update')}}">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.designation_name")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="designation_name" name="designation_name"
                                   placeholder="" maxlength="80" required>
                            <input type="hidden" id="docId" name="designationId">
                        </div>
                        <label>@lang("$string_file.expense_limit")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="expense_limit" name="expense_limit"
                                   placeholder="" maxlength="80" required>
                            @if ($errors->has('expense_limit'))
                                <label class="text-danger">{{ $errors->first('expense_limit') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-primary" value="@lang("$string_file.update")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="DeleteDesignation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.are_you_sure")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('employee.Designation.delete') }}">
                    @csrf
                    <div class="modal-body text-center">
                        <label><b class="text-danger">@lang("$string_file.delete_warning")</b></label>
                        <input type="hidden" id="docId" name="designationId">
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
            let Name = obj.getAttribute('data-Name');
            let limit = obj.getAttribute('data-limit');
            $(".modal-body #expense_limit").val(limit);
            $(".modal-body #designation_name").val(Name);
            $(".modal-body #docId").val(ID);
            $('#EditDOc').modal('show');
        }

        function DeleteDesignation(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #docId").val(ID);
            $('#DeleteDesignation').modal('show');
        }
    </script>
@endsection