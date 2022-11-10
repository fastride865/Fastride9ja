<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<title>Payment</title>
	<style>
		body{margin:0; padding:0; box-sizing: border-box;font-family: 'Open Sans', sans-serif;}
		.payAmount {display: flex; justify-content: space-between; align-items: center; padding:16px;}
		.payAmount h2, .payAmount strong {font-size: 20px; margin:0}
		.savedCard {}
		.cardDetails {margin: 0; padding: 0; list-style: none;}
		.cardDetails li {display: flex;min-height: 44px;padding: 16px;align-items: center; position: relative;}
		hr.line {border: 0;border-bottom: 2px solid #ebeff0;background: transparent;margin: 0;}
		.cvvDetails input {font-size: 16px;height: 44px;padding: 0px 16px;border: 2px solid #ebeff0;border-radius: 100px;}
		.cvvDetails {margin-top: 8px;}
		li.activeCard .cardBox strong {font-weight: bold;}
		.cardBox strong {font-weight: 400;}
		svg#add {width: 32px; height: 32px; margin: 0 16px;}
	.addNew a {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    font-size: 17px;
    /* flex-flow: row wrap; */
    text-decoration: none;
    color: #666666;
    padding: 16px 20px;
    min-height: 44px;
    cursor: pointer;
    clear: both;
}
		.addNew hr {width: 100%;}
		svg#add path {fill: #feb334;}
		.btn.btn-submit {background: #feb334; border: 0; color: #fff; width: 100%; max-width: 150px; margin: 0 auto; display: flex; justify-content: center; align-items: center; height: 44px; border-radius: 100px; margin-top: 16px; }
		/* Radio Button */
		[type="radio"]:checked, [type="radio"]:not(:checked) {position: absolute;left: 15px;z-index: 99;opacity: 0;width: 32px;height: 32px;}
		[type="radio"]:checked + label, [type="radio"]:not(:checked) + label {position: relative;padding-left: 48px;cursor: pointer;color: #666;z-index: 9;display: flex;flex-direction: column;width: 100%;padding: 5px 16px 5px 48px;}
		[type="radio"]:checked + label:before, [type="radio"]:not(:checked) + label:before {content: '';position: absolute;left: 0;top: 0;width: 32px;height: 32px;border-radius: 100%;background: url("{{url('img/radioCheck.svg')}}");}
		[type="radio"]:checked + label:after, [type="radio"]:not(:checked) + label:after {content: '';width: 32px;height: 32px;background: #fff url("{{url('img/radioChecked.svg')}}");position: absolute;top: 0;left: 0;border-radius: 100%;-webkit-transition: all 0.2s ease;transition: all 0.2s ease;}
		[type="radio"]:not(:checked) + label:after {opacity: 0; -webkit-transform: scale(0); transform: scale(0);}
		[type="radio"]:checked + label:after {opacity: 1; -webkit-transform: scale(1); transform: scale(1);}
		/* Add Option Form */
		.addOption {display: flex; flex-direction: column; background: #f4f4f4; padding: 16px; border-radius: 8px; max-width: 300px; width: 100%; margin: 0 auto; margin-bottom: 16px;}
		.formGroup {display: flex; flex-direction: column; margin-bottom: 16px; position: relative;}
		.formGroup input {height: 32px; padding: 4px 16px; border-radius: 100px; border: 1px solid #d6d6d6;}
		.formGroup label {margin-bottom: 8px; font-size: 15px;}
		.half {display: flex; flex-direction: column;}
		.half .formGroup {flex-flow: row wrap; justify-content: space-between;}
		.half .formGroup label {width: 100%;}
		.half .formGroup input {width: 38%; }
		.label.cardBox {
			display: flex;
			justify-content: space-between;
			width: 100%;
		}
		span.error{
			color:brown;
		}
		.sppiner {
		position: absolute;
		z-index: 999;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background: #ffffffd9;
		width: 100%;
		height: 100%;
		display: flex;
		justify-content: center;
		align-items: center;
		}

		.sppiner img {
		width: 100px;
		}

		.cardBox form input {
			position: absolute;
			right: 10px;
			top: 20px;
		}
		.def {
			position: absolute;
			right: 14px;
			top: 34px;
			z-index: 999;
			background: url({{url('img/card_icon.jpg')}});
			width: 0;
			height: 30px;
			background-position: 36px 0px;
			background-size: 40px;
		}

		.def.visacard {
			background: url({{url('img/card_icon.jpg')}});
			width: 31px;
			height: 30px;
			background-position: 36px 0px;
			background-size: 40px;
		}


		.def.mastercard {
			background-position: 38px -52px;
			background-size: 40px;
			width: 38px;
		}

		.def.amex {
			background-position: 38px 145px;
			background-size: 40px;
			width: 38px;
		}

		.def.discover {
			background-position: 38px 28px;
			background-size: 42px;
			width: 38px;
		}

		.def.diners {
			background-position: 46px 136px;
			background-size: 48px;
			width: 46px;
			height: 32px;
		}

		.def.diners_carte {
			background-position: 46px 101px;
			background-size: 48px;
			width: 46px;
			height: 32px;
		}

		.def.jcb {
			background-position: 46px 334px;
			background-size: 48px;
			width: 46px;
			height: 32px;
		}

		.def.visa_electron {
			background-position: 46px 237px;
			background-size: 48px;
			width: 46px;
			height: 32px;
		}

		.paygate span img {
			width: 160px;
			margin: 10px 0;
		}
		input[type=number]::-webkit-inner-spin-button, 
		input[type=number]::-webkit-outer-spin-button { 
		-webkit-appearance: none; 
		}
		.paygate {
			text-align: center;
		}

		input[type=number] {
		-moz-appearance: textfield;
		}
		.paymentOptions {
			width: 100%;
			display: flex;
			flex-direction: column;
			max-width: 414px;
			margin: 0 auto;
		}

		ul.cardDetails li a.del_bt {
			position: absolute;
			right: 0;

		}
		label.cardBoxd.mycard a.del_bt {
			position: absolute;
			right: 0;
			padding: 3px 7px;
			background: #e64942;
			border: 0;
			color: #fff;
			border-radius: 3px;
			/* cursor: pointer; */
			text-decoration: none;
			font-size: 13px;
		}

	</style>
</head>
<body>
	<div class="paymentOptions">
	<div class="sppiner" style="display:none">
{{--		<img src="{{asset('img/lg.-text-entering-comment-loader.gif')}}" alt="" srcset="">--}}
	</div>
<div class="paygate">
	<span>
{{--		<img src="https://www.paygate.co.za/wp-content/uploads/PayGate-Direct-Pay-Online-Logo-3x-1.png?x74986" alt="">--}}
	</span>
</div>
	@isset($money)
	<div class="payAmount">
		<h2>Select options to Pay </h2>
		<strong>{{$currency}}{{$money}}</strong>
	</div>
	@endisset	
		<div class="savedCard">
{{--		@if(isset($booking_id))--}}
			<form id="payusingvault" method="post" action="{{route('paygate-step2',$user_id)}}">
{{--			<input type="hidden" name="booking_id" value="{{$booking_id}}">--}}
			<input type="hidden" name="money" value="{{$money}}">
{{--			<input type="hidden" name="driver" value="{{$driver}}">--}}
{{--		@endif--}}
		@csrf
				<ul class="cardDetails">
					@if(count($cards)>0)
						@foreach ($cards as $card)
							<li class="activeCard">
								@if(isset($booking_id))	
									<input  type="radio" id="card_{{$card->id}}" required name="paymentCard" value="{{$card->token}}">
								@endif	
								<label for="card_{{$card->id}}" class="cardBoxd mycard">
									<strong>{{$card->card_number}}</strong>
{{--									<a href="{{route('delete.method',encrypt($card->id))}}" onclick="return confirm('Are you sure you want to delete the card?');" class="del_bt">Delete</a>--}}
								</label>
							</li>
							<hr class="line">	
						@endforeach
					@endif
				</ul>
{{--		@isset($booking_id)--}}
			<input type="submit" style="display: none">
		</form>
{{--		@endisset--}}
{{--		@if(isset($booking_id))--}}
			<div class="addNew">
				<hr class="line">
				<a class="addNewCard btn btn-warning" href="javascript:void(0)">
					Pay Using New Card
				</a>
				<hr class="line">
			</div>
{{--				<input id="mainSubmit" type="button" class="btn btn-submit" value="Pay">--}}
{{--			@endif--}}
		</div>
	</div>
</body>
{{--<script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/sweetalert.min.js') }}"></script>--}}
<script src="{{ asset('global/vendor/jquery/jquery.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
	$(document).on('click','.addNewCard',function(e){
		e.preventDefault();
		$check = confirm('Are you sure to continue your card will be saved for future payment.');
		if($check){
			$('#payusingvault').prepend('<input type="hidden" name="vault" value=1>');
			$('#payusingvault').submit();
		}
	});
function GetCardType()
{
	var number = $('.card_number').val();
	console.log('number-',number);
    // visa
    var re = new RegExp("^4");
    if (number.match(re) != null)
        return "visacard";
    // Mastercard 
    // Updated for Mastercard 2017 BINs expansion
     if (/^(5[1-5][0-9]{14}|2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12}))$/.test(number)) 
        return "mastercard";

    // AMEX
    re = new RegExp("^3[47]");
    if (number.match(re) != null)
        return "amex";

    // Discover
    re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
    if (number.match(re) != null)
        return "discover";

    // Diners
    re = new RegExp("^36");
    if (number.match(re) != null)
        return "diners";

    // Diners - Carte Blanche
    re = new RegExp("^30[0-5]");
    if (number.match(re) != null)
        return "diners_carte";

    // JCB
    re = new RegExp("^35(2[89]|[3-8][0-9])");
    if (number.match(re) != null)
        return "jcb";

    // Visa Electron
    re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
    if (number.match(re) != null)
        return "visa_electron";

    return "";
}
	
	$(document).on('keyup','.card_number',function(){
		console.log(GetCardType());
		$('#cardimage').removeAttr('class');
		$('#cardimage').addClass('def');
		$('#cardimage').addClass(GetCardType());
	});
	$(document).on('keyup','.cvvNo,.expiry_month,.expiry_year,.card_number',function(e){
		val 		= $(this).val();
		maxlength 	= $(this).attr("maxlength");
		console.log(maxlength);
		if(val.length > maxlength){
			val=val.slice(0, maxlength);
			$(this).val(val);
		}
	});
	$(document).on('click','#mainSubmit',function(){
		if($("input[name=paymentCard]").val()){
			$('#payusingvault').submit();
			$(document).find('.sppiner').show();
		}
	});
	$(document).on('submit','form',function(){
		$(document).find('.sppiner').show();
	});
</script>
</html>