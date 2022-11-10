@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
          @include("business-segment.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title">
                        <i class=" icon wb-paperclip" aria-hidden="true"></i>@lang("$string_file.style_segment")</h3>
                </header>
                <div class="panel-body">
                    <form method="POST" action="{{route('business-segment.style-segment.add')}}">
                        @csrf
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.id")</th>
                            <th>@lang("$string_file.name")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($data as $style)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    @php $selected = in_array($style->id,$selected_style) ? 'checked' : ''; @endphp
                                    <label>
                                        <input type="checkbox" class="checkbox" name="arr_style[]" value=" {{ ($style->id) }}" {!! $selected !!} >  {{ $style->Name($style->merchant_id) }}
                                    </label>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>
                                @if(!$is_demo)
                                <button type="submit" class="btn btn-primary float-right" onsubmit="">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                </button>
                                @else
                                    <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                                @endif
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

