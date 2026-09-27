<?php
include 'koneksi.php';

$id_tagihan = isset($_GET['id_tagihan']) ? intval($_GET['id_tagihan']) : 0;

$query_header = "SELECT t.*, p.nama, p.no_rm, p.alamat 
                 FROM tagihan t 
                 JOIN pasien p ON t.id_pasien = p.id_pasien 
                 WHERE t.id_tagihan = '$id_tagihan'";
$result_header = mysqli_query($conn, $query_header);
$header = mysqli_fetch_assoc($result_header);

$query_detail = "SELECT dt.*, im.nama_item, im.harga 
                 FROM detail_tagihan dt 
                 JOIN item_medis im ON dt.id_item = im.id 
                 WHERE dt.id_tagihan = '$id_tagihan'
                 ORDER BY dt.id_detail ASC";
$detail = mysqli_query($conn, $query_detail);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk RS - <?= $header['no_nota'] ?? 'INV'; ?></title>
</head>
<body onload="window.print()">


    <pre>
===========================================
        RUMAH SAKIT SEHAT BANGET
    Jl. Kesembuhan No. 99, Jawa Barat
           Telp: (021) 1234567
===========================================

No. Nota : <?= $header['no_nota'] ?? '-'; ?> 
Tanggal  : <?= isset($header['tanggal']) ? date('d/m/Y H:i', strtotime($header['tanggal'])) : date('d/m/Y H:i'); ?> 
Kasir    : <?= $header['kasir'] ?? 'Admin'; ?> 

--- DATA PASIEN ---
Nama RM  : <?= $header['nama'] ?? 'Pasien Umum'; ?> 
No. RM   : <?= $header['no_rm'] ?? '-'; ?> 
Alamat   : <?= $header['alamat'] ?? '-'; ?> 

===========================================
RINCIAN TAGIHAN:
<?php if ($detail && mysqli_num_rows($detail) > 0) : ?>
<?php while ($row = mysqli_fetch_assoc($detail)) : ?>
<?= $row['nama_item']; ?> 
   <?= $row['jumlah']; ?> x Rp <?= number_format($row['harga'], 0, ',', '.'); ?> = Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?> 
<?php endwhile; ?>
<?php else : ?>
(Tidak ada detail layanan)
<?php endif; ?>
-------------------------------------------
TOTAL TAGIHAN : Rp <?= number_format($header['total_bayar'] ?? 0, 0, ',', '.'); ?> 
UANG PASIEN   : Rp <?= number_format($header['bayar'] ?? 0, 0, ',', '.'); ?> 
KEMBALIAN     : Rp <?= number_format($header['kembali'] ?? 0, 0, ',', '.'); ?> 
===========================================

    Semoga lekas sembuh!
    Terima kasih atas kepercayaan Anda.
    </pre>

</body>
</html>