<?php

include "./Model/Paybull.php";
$paybull = new Paybull();
$invoncId = $_GET['invonce-id'];

$paybull->setBaseUrl('https://test.paybull.com/ccpayment');
$paybull->setAppId('c3d81ae3cc3011ef10dcefa31a458d65');
$paybull->setAppSecret('217071ea9f3f2e9b695d8f0039024e64');
$paybull->setMerchantKey('$2y$10$w/ODdbTmfubcbUCUq/ia3OoJFMUmkM1UVNBiIQIuLfUlPmaLUT1he');
$paybull->setMerchantId('98950');


$requestCheckStatus = $paybull
->addParameter('invoice_id', $invoncId)
->addParameter('include_pending_status', true)
->getCheckStatus();
// print_r(json_encode($requestCheckStatus));
// foreach ($requestCheckStatus as $key => $value) {
//     if (is_object($value)) {
//         // Eğer özellik bir nesne ise, içindeki özelliklere ayrıca erişelim
//         foreach ($value as $subKey => $subValue) {
//             echo $subKey . ": " . $subValue . "<br>";
//         }
//     } else {
//         echo $key . ": " . $value . "<br>";
//     }
// }
// foreach ($requestCheckStatus->response as $key => $value) {
//    echo "$key : $value <br>";
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Check Status Response</title>
</head>
<body>
    <div class="container mt-5">
    <a href="islemler.php">İşlemler tablosu</a><br>
<a href="pay.php">Yeni ödeme</a><br>
        <h2>Check Status Response</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>İşlem</th>
                    <th>Detay</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($requestCheckStatus->response as $key => $value) {
                    echo '<tr>';
                    echo '<td>' . $key . '</td>';
                    echo '<td>' . $value . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
