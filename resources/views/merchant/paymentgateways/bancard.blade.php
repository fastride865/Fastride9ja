<! DOCTYPE html>
<html lang="en">
<Head>
    <Meta charset="UTF-8">
    <title> Bancard payment </title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link crossorigin='anonymous' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'
          integrity='sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb' rel='stylesheet'>
    <script src="https://vpos.infonet.com.py:8888/checkout/javascript/dist/bancard-checkout-2.0.0.js"></script>
</head>
<script type="application/javascript">
    {
        styles = {
            "Form-background-color": "#001b60",
            "Button-background-color": "#4faed1",
            "Button-text-color": "#fcfcfc",
            "Button-border-color": "#dddddd",
            "Input-background-color": "#fcfcfc",
            "Input-text-color": "# 111111",
            "Input-placeholder-color": "# 111111",
            "name": "button-background-color",
            "text": "Colorfondodelbotón",
            "type": "color",
            default: '#5CB85C',
            "name": "button-border-color",
            "text": "Colobordedelbotón",
            "type": "color",
            default: '#4CAE4C',
            "name": "button-text-color",
            "text": "Colortextodelbotón",
            "type": "color",
            default: '#FFFFFF',
            "name": "form-background-color",
            "text": "Colorfondodeformulario",
            "type": "color",
            default: '#FFFFFF',
            "name": "form - border - color",
            "text": "Colordelbordedelformulario",
            "type": "color",
            default: '#DDDDDD',
            "name": "header - background - color",
            "text": "Colorfondodeencabezado",
            "type": "color",
            default: '#F5F5F5',
            "name": "header - text - color",
            "text": "Colortextodeencabezado",
            "type": "color",
            default: '#333333',
            "name": "hr - border - color",
            "text": "Colordelineaseparadora",
            "type": "color",
            default: '#EEEEEE',
            "name": "input - background - color",
            "text": "Colorfondodecampos",
            "type": "color",
            default: '#FFFFFF',
            "name": "input - border - color",
            "text": "Colorbordedecampos",
            "type": "color",
            default: '#CCCCCC',
            "name": "input - placeholder - color",
            "text": "Colordelplaceholder",
            "type": "color",
            default: '#999999',
            "name": "input - text - color",
            "text": "Colortextodecampos",
            "type": "color",
            default: '#555555',
            "name": "label - kyc - text - color",
            "text": "Colortextodetu - eres - tu",
            "type": "color",
            default: '#000000',
        }
        ;
        window.onload = function () {
            Bancard.Cards.createForm('iframe-container', '{{ $process_id}}', styles);
        };
    }
</script>
<body>
<h1 style="text-align: center"> Bancard Payment </h1>
<div style=" margin:car;" id="iframe-container" class="container col-md-6">

</div>

</body>
</html>