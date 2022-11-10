@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('document-message'))
                <div class="col-md-8 alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-info"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong> {{ session()->get('document-message') }} </strong>
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('users.index')}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                        @if($user->signup_status==1)
                            <a href="{{route('merchant.user.AlldocumentStatus' , ['id' => $user->id,'status'=>2])}}">
                                <button type="button" class="btn btn-icon btn-warning">Approve All Documents
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-file" aria-hidden="true">
                        </i> {{ $user->first_name." ".$user->last_name }}'s @lang("$string_file.documents") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        @if(empty($user->UserDocuments))
                            <div class="container text-center">
                                <h4> @lang('admin.noDocuments') </h4>
                            </div>
                        @else
                            @foreach ($user->UserDocuments as $doc)
                                <div class="col col-12 col-sm-6 col-md-4">
                                    <div class="card" style="margin-top:20px;">
                                        @if ($doc->pivot->document_verification_status == 1)
                                            <div class="dropdown">
                                                <button type="button" style="right:0;" class="btn btn-secondary position-absolute btn-sm rounded-0" data-toggle="dropdown">
                                                    @lang("$string_file.action") <span class="fa fa-chevron-down"></span>
                                                </button>
                                                <div class="dropdown-menu pb-0" style="min-width:265px;">
                                                    {{--<a class="dropdown-item text-success" href="{{route('merchant.user.documentStatus' , ['id' => $doc->pivot->id,'status'=>2])}}"> <strong>@lang('admin.approve')</strong> </a>--}}
                                                    <a class="dropdown-item text-danger" href="#" data-toggle="collapse" onclick="$('#reason-collapse-{{$doc->id}}').toggle();event.stopPropagation();"> <strong>Reject</strong> </a>

                                                    <div id="reason-collapse-{{$doc->id}}" class="collapse">
                                                        <form class="" action="{{route('merchant.user.documentStatus')}}" method="get">
                                                            <div class="form-group text-center">
                                                                <input type="hidden" name="id" value="{{$doc->pivot->id}}">
                                                                <input type="hidden" name="status" value="3">

                                                                <select class="select2 form-control" name="reject_reason_id"
                                                                        id="timezone" required>
                                                                    <option value="" selected disabled>@lang('admin.selectRejectReason')<option>
                                                                    @foreach($rejectReasons as $reason)
                                                                        <option value="{{ $reason->id }}"> {{ $reason->LanguageSingle->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('reject_reason_id'))
                                                                    <label class="danger d-block">{{ $errors->first('reject_reason_id') }}</label>
                                                                @endif

                                                                <button type="submit" class="btn btn-danger btn-sm text-white mt-2">@lang("$string_file.rejected") </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($doc->pivot->document_verification_status == 2)
                                            <a class="btn btn-success position-absolute btn-sm rounded-0 text-white" style="right:0;"> @lang("$string_file.approved")</a>
                                        @elseif($doc->pivot->document_verification_status == 3)
                                            <a class="btn btn-danger position-absolute btn-sm rounded-0 text-white" style="right:0;"> @lang("$string_file.rejected")</a>
                                        @endif
                                        <div class="card-body bg-light">
                                            <h4 class="card-title"> {{ $doc->LanguageSingle->documentname }} ({{$doc->pivot->document_number}})</h4>
                                            @if(!empty($doc->pivot->expire_date))
                                                <h6 class="text-muted card-subtitle mb-2">@lang('admin.expiryDate') : {{ $doc->pivot->expire_date }}</h6>
                                            @endif
                                            <img src="{{ get_image($doc->pivot->document_file,'user_document') }}" style="width:300px;height:250px;">
                                            <p class="card-text"></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

