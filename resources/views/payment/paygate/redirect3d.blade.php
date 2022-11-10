<!DOCTYPE html>
<html lang="en">
    <body>
<form action="{{$RedirectUrl}}" method="post" name="redirectForm" >
@foreach ($UrlParams as $item)
<input type="hidden" name="{{$item->key}}" value="{{$item->value}}">    
@endforeach
</form>
</body>
<script>
    document.redirectForm.submit();
</script>