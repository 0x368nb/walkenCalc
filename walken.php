<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        *{
            font-family: Consolas;
        }
        html.load{
            position: relative;
            pointer-events: none;
        }
        body{
            position: relative;
            min-height: 100vh;
            margin: 0;
        }
        ul{
            padding-left: 0;
        }
        li{
            list-style: none;
        }
        html.load body:before{
            content: "";
            background: rgba(0,0,0,0.2);
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 3;
        }
        html.load body:after{
            content: "";
            position: fixed;
            left: 50%;
            top: 50%;
            width: 30px;
            height: 30px;
            background: white;
            z-index: 5;
            animation-name: rotate;
            animation-duration: 1s;
            animation-iteration-count: infinite;
        }
        @keyframes rotate {
            0%{
                transform: translate(-50%,-50%) rotate(0deg);
            }
            100%{
                transform: translate(-50%,-50%) rotate(360deg);
            }
        }

    </style>
</head>
<body style="padding: 30px">

<main>
    <label for=""
           style="font-size: 18px; font-weight: 700;margin-bottom: 5px;display: block"
    >Wallet address</label>
    <input name="wallet" type="text" style="    width: 400px;
    font-size: 16px!important;
    padding: 10px!important;
    border: 2px solid purple!important;"><br><br>
    <button class="button_1" style="    font-size: 16px;
    padding: 10px;
    border: 2px solid purple;
    background: purple;
    color: white; cursor: pointer; ">Transactions</button>
    <ul>

    </ul>
</main>

<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function (){
        $('.button_1').click(function (e){
            e.preventDefault();

            var wallet = $('[name="wallet"]').val();
            if(wallet){
                $('html').addClass('load');
                $.ajax({
                    url: "//public-api.solscan.io/account/splTransfers?account="+wallet+"&limit=100&offset=0",
                    type: 'get',
                    success: function (arr) {
                        console.log('===Success start===');

                        var globalArr = [];
                        var gaI = 0;

                        for(var i = 0; i < arr.data.length; i++){
                            var tx_symbol = arr.data[i]['symbol'];
                            var signatureStr = arr.data[i]['signature']['0'];
                            var tx_preBalance = arr.data[i]['preBalance']; tx_preBalance = tx_preBalance*1;
                            var tx_postBalance = arr.data[i]['postBalance']; tx_postBalance = tx_postBalance*1;
                            var change = tx_postBalance - tx_preBalance;
                            change = change.toString();
                            var change__int = change.slice(0, change.length - 9);
                            if (!change__int || change__int < 0){
                                change__int = '0';
                            }
                            var change__float = change.slice(change.length - 9, change.length - 7);
                            if(tx_postBalance > tx_preBalance && tx_symbol == 'WLKN'){
                                globalArr[gaI] = [];
                                globalArr[gaI]['sum'] = change__int+'.'+change__float;
                                globalArr[gaI]['signatureStr'] = signatureStr;
                                gaI++;
                            }
                        }

                        console.log(globalArr);
                        var num = globalArr.length - 1;
                        var totalSum = 0;
                        var k = -1;
                        let timerId = setTimeout(function tick() {
                            k++;
                            $.ajax({
                                url: "//public-api.solscan.io/transaction/"+globalArr[k]['signatureStr'],
                                type: 'get',
                                success: function (signature) {
                                    var signer = signature['signer']['0'];
                                    var consoleArr = [];
                                    consoleArr[0] = globalArr[k]['sum'];
                                    consoleArr[1] = signer;
                                    consoleArr[2] = globalArr[k]['signatureStr'];
                                    var date1 = signature['blockTime'];

                                    // console.log(signature);
                                    // console.log(consoleArr);
                                    if(signer == '6HyVjAUJu1T2EhojQa2bJ83TJ9dUdsXS3wveWh3XrxBN'){

                                        $('ul').append('<li> +'+globalArr[k]['sum']+' WLKN</li>');
                                        totalSum += globalArr[k]['sum']*1;
                                    }

                                    if( k < globalArr.length - 1){
                                        timerId = setTimeout(tick, 200);
                                    }
                                    else {
                                        setTimeout(function (){
                                            var date2 = Date.now();
                                            date2 = date2.toString().slice(0, 10);
                                            date2 = date2*1;
                                            console.log(date1);
                                            console.log(date2);

                                            var Difference_In_Time = date2 - date1;

                                            var Difference_In_Days = Difference_In_Time / (3600 * 24);
                                            totalSum = totalSum.toFixed(2);
                                            $('ul').append('<li><b>Total = '+totalSum+' WLKN</b></li>');
                                            var dscsdc = Difference_In_Days.toFixed(2);
                                            $('ul').append('<li><b>Days = '+dscsdc+'</b></li>');
                                            var lastSummm = totalSum/Difference_In_Days;
                                            lastSummm = lastSummm.toFixed(2);
                                            $('ul').append('<li><b>Per day ~ '+lastSummm+' WLKN</b></li>');
                                            var mnth = lastSummm*30;
                                            $('ul').append('<li><b>Per month ~ '+mnth+' WLKN</b></li>');
                                            $('html').removeClass('load');
                                        },500);
                                    }
                                },
                                error: function (err) {}
                            });

                        }, 200);
                        console.log('===Success end===');
                    },
                    error: function (err) {
                        console.log('===Error start===');
                        console.log(err);
                        console.log('===Error end===');
                    }
                });
            }


        })
    });
</script>

</body>
</html>