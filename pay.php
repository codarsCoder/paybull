<?php

include "./Model/Paybull.php";
$paybull = new Paybull();


$paybull->setBaseUrl('https://test.paybull.com/ccpayment');
$paybull->setAppId('c3d81ae3cc3011ef10dcefa31a458d65');
$paybull->setAppSecret('217071ea9f3f2e9b695d8f0039024e64');
$paybull->setMerchantKey('$2y$10$w/ODdbTmfubcbUCUq/ia3OoJFMUmkM1UVNBiIQIuLfUlPmaLUT1he');
$paybull->setMerchantId('98950');

//$paybull->setTestMerchantWithTestCard();
// $requestInstallments = $paybull->installments();
// print_r($requestInstallments);
if ($_POST) {

    $küçükHarfler = range('a', 'z');
    $total = 12000;
    $invoiceId =rand(1,999999999). time().$küçükHarfler[rand(0,25)].$küçükHarfler[rand(0,25)].$küçükHarfler[rand(0,25)].$küçükHarfler[rand(0,25)];

    $logFilePath ='log.txt';

    // Dosyanın mevcut içeriğini al
    $existingContent = file_get_contents($logFilePath);
    $newContent = $invoiceId. PHP_EOL;
file_put_contents($logFilePath, $newContent . $existingContent);

    $hash = $paybull->generateHashKey($total, 1, 'TRY', $paybull->getMerchantKey(), $invoiceId, $paybull->getAppSecret());

    $items = [["name"=>" SIPARIS URUN ","price"=>12000,"quantity"=>1,"description"=>" SIPARIS URUN "]];


   // echo $hash;die();
    $requestToken = $paybull->getToken();

    $requestPaySmart3D = $paybull
        ->addParameter('cc_holder_name', 'John Dao')
        ->addParameter('cc_no', '5406675406675403')
        ->addParameter('expiry_month', '12')
        ->addParameter('expiry_year', '19')
        ->addParameter('cvv', '000')
        ->addParameter('currency_code', 'TRY')
        ->addParameter('installments_number', 1)
        ->addParameter('total', $total)
        ->addParameter('invoice_id', $invoiceId)
        ->addParameter('invoice_description', 'INVOICE TEST DESCRIPTION')
        ->addParameter('name', 'John')
        ->addParameter('surname', 'Dao')
        ->addParameter('items', json_encode($items))
        ->addParameter('return_url', "http://localhost/paybull/return.php")
        ->addParameter('cancel_url', "http://localhost/paybull/cancel.php")
        ->paySmart3D();

    if ($requestPaySmart3D->http_code != 200) {
        echo json_encode($requestPaySmart3D);
        exit;
    }

    echo $requestPaySmart3D->htmlBody;
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
    body{

        margin: 10px;
        padding: 10px;
        align-items: center;

    }

    </style>
</head>
<body>
<a href="islemler.php">İşlemler tablosu</a><br>
            <form  method="post" action="#" id="three_d_form">
                <input type="hidden" name="__token" value="form"/>
                <input type="submit" onclick="submitPaymentForm()" value="Ödeme yap" class="btn btn-primary">
            </form>

</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script>
    function submitPaymentForm() {
        document.getElementById("three_d_form").submit();
    }
</script>

</html>

