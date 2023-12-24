<?php

$invoice = $_GET['invoice_id'];
$status_description = $_GET['status_description'];
$payment_status = $_GET['payment_status'];
$amount = $_GET['amount'];
$time = date('Y-m-d H:i:s');

// Dosyanın adı
$filename = 'log.json';

// Dosyayı oku
$fileContent = file_get_contents($filename);

// Dosya içeriğini JSON'dan diziye çevir
$data = json_decode($fileContent, true);

// Yeni veriyi oluştur
$newData = array(
    'invoice' => $invoice,
    'time' => $time,
    'status_description' => $status_description,
    'amount' => $amount,
    'payment_status' => $payment_status,

);

// Yeni veriyi dosyanın en başına ekle
array_unshift($data, $newData);

// Dosyayı JSON formatına çevir ve tekrar yaz
file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

echo 'İşlem başarılı. <br>';

?>
<a href="islemler.php">İşlemler tablosu</a><br>
<a href="pay.php">Yeni ödeme</a><br>
  
