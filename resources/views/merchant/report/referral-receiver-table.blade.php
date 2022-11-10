<table class="table display nowrap table-striped table-bordered">
    <thead>
    <tr>
        <th>@lang("$string_file.name")</th>
        <th>@lang("$string_file.receiver_type") </th>
        <th>@lang("$string_file.offer_type") </th>
        <th>@lang("$string_file.offer_value") </th>
        <th>@lang("$string_file.status") </th>
        <th>@lang("$string_file.date")</th>
    </tr>
    </thead>
    <tbody>
    @foreach($receiverBasic as $receiver)
        <tr>
            <td>
                {{ $receiver['name'] }}
                <br>
                {{ $receiver['phone'] }}
                <br>
                {{ $receiver['email'] }}
            </td>
            <td>{{$receiver['type']}}</td>
            <td>
                @switch($receiver['offer_type'])
                    @case(1)
                    @lang("$string_file.fixed_amount")
                    @break
                    @case(2)
                    @lang("$string_file.discount")
                    @break
                @endswitch
            </td>
            <td>
                @switch($receiver['offer_type'])
                    @case(1)
                    {{$receiver['currency']." ".$receiver['offer_value']}}
                    @break
                    @case(2)
                    {{$receiver['offer_value']." %"}}
                    @break
                @endswitch
            </td>
            <td>
                @if($receiver['referral_available'] == 1)
                    @lang("$string_file.pending")
                @else
                    @lang("$string_file.redeemed")
                @endif
            </td>
            <td>{{$receiver['date']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>