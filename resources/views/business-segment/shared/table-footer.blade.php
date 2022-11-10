<div class="mt-10">
    <div class="float-left">@lang("$string_file.showing") {{ ($table_data->firstItem() > 0) ? $table_data->firstItem() : 0 }} @lang("$string_file.to") {{ ($table_data->lastItem() > 0 ) ? $table_data->lastItem() : 0 }} @lang("$string_file.of") {{ $table_data->total() }}</div>
    @if(isset($data))
        <div class="pagination1 float-right">{{$table_data->appends($data)->links()}}</div>
    @else
        <div class="pagination1 float-right">{{$table_data->appends()->links()}}</div>
    @endif
</div>