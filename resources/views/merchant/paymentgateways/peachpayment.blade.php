@foreach($responseData['redirect']['parameters'] as $value)
    @if($value['name'] == "TermUrl")
        @php  $termUrl = $value['value'];  @endphp
    @elseif($value['name'] == "MD")
        @php  $md = $value['value'];  @endphp
    @else
        @php  $PaReq = $value['value'];  @endphp
    @endif
@endforeach


<html>
<head>
    <meta charset="utf-8">
</head>
<body onload="document.form.submit();">
<form name="form" action="{{ $responseData['redirect']['url'] }}" target="_self" method="POST">
    <input type="hidden" name="TermUrl" value="{{ $termUrl }}"/>
    <input type="hidden" name="MD" value="{{ $md }}"/>
    <input type="hidden" name="PaReq" value="{{ $PaReq }}"/>
    <script>
        <input type = "submit" value = "Click here to continue" / >
    </script>
</form>
</body>
</html>
