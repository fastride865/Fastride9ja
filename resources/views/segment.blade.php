<div class="row">
    <div class="col-md-2">
        <h3 class="panel-title" style="padding: 1px !important;">
            @lang("$string_file.segments")<span class="text-danger">*</span>
        </h3>
    </div>
    <div class="col-md-10">
    @foreach($arr_segment as $key => $segment)
                 {!! $segment !!}<input name="segment[]" value="{!! $key !!}" class="form-group mr-10 mt-5 ml-20 area_segment" type="checkbox" @if(in_array($key,$selected))checked @endif>
    @endforeach
    @if ($errors->has('segment'))
        <span class="help-block">
            <strong>{{ $errors->first('segment') }}</strong>
        </span>
    @endif
</div>
</div>
<hr>