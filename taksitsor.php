<?php
include "./Model/Paybull.php";
$paybull = new Paybull();


$paybull->setBaseUrl('https://test.paybull.com/ccpayment');
$paybull->setAppId('c3d81ae3cc3011ef10dcefa31a458d65');
$paybull->setAppSecret('217071ea9f3f2e9b695d8f0039024e64');
$paybull->setMerchantKey('$2y$10$w/ODdbTmfubcbUCUq/ia3OoJFMUmkM1UVNBiIQIuLfUlPmaLUT1he');
$paybull->setMerchantId('98950');


$taksit = $paybull
->addParameter('credit_card', '521848')
->addParameter('amount', '100')
->addParameter('merchant_key', $paybull->getMerchantKey())
->addParameter('is_2d', '0')
->addParameter('currency_code', 'TRY')
->getPos();

// JSON verisini PHP dizisine çevir
$dataArray = $taksit->response->data;

// Her bir elemanı işleyerek ekrana yazdıralım
foreach ($taksit->response->data as $item) {
    foreach ($item as $key => $value) {
        echo "$key: $value<br>";
    }
    echo "<br>";
}

?>
