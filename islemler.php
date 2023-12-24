<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Log Verileri</title>
</head>
<body>
    <div class="container mt-5">
<a href="pay.php">Yeni ödeme</a><br>
        <h2>Log Verileri</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Time</th>
                    <th>Status Description</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Dosyanın adı
                $filename = 'log.json';

                // Dosyayı oku
                $fileContent = file_get_contents($filename);

                // Dosya içeriğini JSON'dan diziye çevir
                $data = json_decode($fileContent, true);

                // Her bir veriyi tabloya ekle
                foreach ($data as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['invoice'] . '</td>';
                    echo '<td>' . $row['time'] . '</td>';
                    echo '<td>' . $row['status_description'] . '</td>';
                    echo '<td>' . $row['amount'] . '</td>';
                    echo '<td>' . ($row['payment_status'] ?? '') . '</td>';
                    echo '<td> <a href="islemdetay.php?invonce-id='.$row['invoice'].'">Detay</a></td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<!-- 
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
