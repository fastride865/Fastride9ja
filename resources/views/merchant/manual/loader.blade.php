<style>
    .circle {
        height: 100px;
        width: 100px;
        border-radius: 50%;
        background-color: #0066a1;
        position: absolute;
        top: 48%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        z-index: 99;
        text-align: center;
    }

    .circle img {
        margin-top: 15px;
    }

    .circle:before,
    .circle:after {
        content: '';
        display: block;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        border-radius: 50%;
        border: 1px solid rgba(0, 102, 161, 0.7);
    }

    .circle:before {
        -webkit-animation: ripple 2s linear infinite;
        animation: ripple 2s linear infinite;
    }

    .circle:after {
        -webkit-animation: ripple 2s linear 1s infinite;
        animation: ripple 2s linear 1s infinite;
    }

    @-webkit-keyframes ripple {
        0% {
            -webkit-transform: scale(1);
        }
        75% {
            -webkit-transform: scale(1.75);
            opacity: 1;
        }
        100% {
            -webkit-transform: scale(2);
            opacity: 0;
        }
    }

    @keyframes ripple {
        0% {
            transform: scale(1);
        }
        75% {
            transform: scale(1.75);
            opacity: 1;
        }
        100% {
            transform: scale(2);
            opacity: 0;
        }
    }
</style>


<style>
    .clear {
        clear: both !important;
    }

    #content {
        width: 76%;
        height: 5px;
        margin-left: 10%;
        margin-right: 10%;
        margin-top: 85%;
        margin-bottom: 10%;

    }

    .fullwidth .expand {
        width: 75%;
        height: 5px;
        margin: 2px 0;
        background: #2187e7;
        position: absolute;
        box-shadow: 0px 0px 10px 1px rgba(0, 198, 255, 0.7);
        -moz-animation: fullexpand 8s ease-out;
        -webkit-animation: fullexpand 8s ease-out;
    }

    @-moz-keyframes fullexpand {
        0% {
            width: 0px;
        }
        75% {
            width: 75%;
        }
    }

    @-webkit-keyframes fullexpand {
        0% {
            width: 0px;
        }
        75% {
            width: 75%;
        }
    }


</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js" type="text/javascript"></script>
<script>
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>
<script>
    $(document).ready(function () {
        $('#content').removeClass('fullwidth').delay(0).queue(function (next) {
            $(this).addClass('fullwidth');
            next();
        });
    });
</script>

<script type="text/javascript">
    $(function () {
        $("#testdiv").delay(5000).fadeOut(0);
    });
</script>
<script type="text/javascript">
    (function () {
        setTimeout(function () {
            var bookingRoute = "{{ route('BookingStatusWaiting')}}?booking_id={{$id}}";
            window.location.href = bookingRoute;
        },{{$time}});
        /* 1000 = 1 second*/
    })();
</script>
<body class="main_bg" style="background-color: aliceblue;">
    <div class="main_dv">
         <div class="circle">
             <img src="{{asset('/loader/Spin-1s-200px.gif')}}"/>
         </div>
    </div>
</body>
</html>
