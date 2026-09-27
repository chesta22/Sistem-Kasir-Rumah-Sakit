<?php
include 'koneksi.php';

// PROSES SIMPAN TRANSAKSI
if (isset($_POST['checkout'])) {
    $kasir_input = trim($_POST['kasir']);
    $kasir       = !empty($kasir_input) ? mysqli_real_escape_string($conn,$kasir_input) : 'Petugas RS';

    $nama_input   = trim($_POST['nama']);
    $no_rm_input  = trim($_POST['no_rm']);
    $alamat_input = trim($_POST['alamat']);

    $nama   = !empty($nama_input) ? mysqli_real_escape_string($conn,$nama_input) : 'Pasien Umum';
    $no_rm  = !empty($no_rm_input) ? mysqli_real_escape_string($conn,$no_rm_input) : '-';
    $alamat = !empty($alamat_input) ? mysqli_real_escape_string($conn,$alamat_input) : '-';
    
    $cart = json_decode($_POST['cart_data'], true);
    $uang_bayar = intval($_POST['uang_bayar']);

    if (!empty($cart)) {         // 1. Simpan / Ambil Data Pasien$id_pasien = 0;
        if ($no_rm !== '-') {
            $cek_pasien = mysqli_query($conn, "SELECT id_pasien FROM pasien WHERE no_rm = '$no_rm'");
            if (mysqli_num_rows($cek_pasien) > 0) {
                $row_pasien = mysqli_fetch_assoc($cek_pasien);
                $id_pasien  =$row_pasien['id_pasien'];
                
                // Update alamat & nama terbaru jika No RM sudah terdaftar
                mysqli_query($conn, "UPDATE pasien SET nama = '$nama', alamat = '$alamat' WHERE id_pasien = '$id_pasien'");
            }
        }

        if ($id_pasien == 0) {$query_pasien = "INSERT INTO pasien (nama, no_rm, alamat) VALUES ('$nama', '$no_rm', '$alamat')";
            mysqli_query($conn,$query_pasien);
            $id_pasien = mysqli_insert_id($conn);
        }

        // 2. Hitung Total & Simpan Header Tagihan
        $grand_total = 0;
        foreach ($cart as $item) {$grand_total += ($item['price'] *$item['qty']);
        }
        $kembalian = $uang_bayar -$grand_total;
        $no_nota   = "RS-" . date("YmdHis");
        $tgl_sekarang = date("Y-m-d H:i:s");

        $query_tagihan = "INSERT INTO tagihan (no_nota, id_pasien, kasir, total_bayar, bayar, kembali, tanggal) 
                          VALUES ('$no_nota', '$id_pasien', '$kasir', '$grand_total', '$uang_bayar', '$kembalian', '$tgl_sekarang')";
        mysqli_query($conn,$query_tagihan);
        $id_tagihan = mysqli_insert_id($conn);

        // 3. Simpan Detail Layanan/Obat & Update Stok
        foreach ($cart as $item) {$id_item = $item['id'];$jumlah  = $item['qty'];$total   = $item['price'] *$jumlah;

            $query_detail = "INSERT INTO detail_tagihan (id_tagihan, id_pasien, id_item, jumlah, total_harga) 
                             VALUES ('$id_tagihan', '$id_pasien', '$id_item', '$jumlah', '$total')";
            mysqli_query($conn,$query_detail);

            $query_stok = "UPDATE item_medis SET stok = stok - $jumlah WHERE id = '$id_item'";
            mysqli_query($conn,$query_stok);
        }

        header("Location: transaksi.php?sukses=1&id_tagihan=" . $id_tagihan);
        exit();
    } else {
        echo "<script>alert('Keranjang tagihan masih kosong!');</script>";
    }
}

// Cek status transaksi dari URL
$transaksi_sukses = isset($_GET['sukses']) && $_GET['sukses'] == '1';$id_tagihan_terakhir = isset($_GET['id_tagihan']) ? intval($_GET['id_tagihan']) : 0;

// Ambil list item medis dari database
$data_item = mysqli_query($conn, "SELECT * FROM item_medis");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistem Kasir Rumah Sakit</title>
    <!-- Tag penghubung CSS dengan ID khusus -->
    <link rel="stylesheet" href="transaksi.css" id="theme-css">
</head>
<body>

    <div class="header-area">
        <h1>Sistem Kasir Rumah Sakit</h1>
        <!-- Tombol untuk On/Off CSS -->
        <button class="btn-toggle" id="toggleCssBtn" onclick="toggleCSS()">Matikan Tampilan Menarik</button>
    </div>

    <?php if ($transaksi_sukses) : ?>
        <div class="success-banner">
            <div>
                <b>✔ TRANSAKSI BERHASIL DISIMPAN!</b><br>
                Data pembayaran rumah sakit telah masuk ke database.
            </div>
            <div>
                <a href="cetak_struk.php?id_tagihan=<?= $id_tagihan_terakhir; ?>" target="_blank">
                    <button class="btn-add">Cetak Struk</button>
                </a>
                <a href="transaksi.php"><button class="btn-toggle">Tutup</button></a>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" id="formCheckout" onsubmit="return validasiBayar()">
        <div class="layout-grid">
            <!-- data kasir dan pasien -->
            <div class="panel">
                <h3>1. Data Kasir & Pasien</h3>
                
                <div class="form-group">
                    <label>Nama Petugas Kasir</label>
                    <input type="text" name="kasir" value="Admin RS" required>
                </div>
                <div class="form-group">
                    <label>Nama Pasien</label>
                    <input type="text" name="nama" value="-" placeholder="Nama Pasien">
                </div>
                <div class="form-group">
                    <label>No. Rekam Medis / NIK</label>
                    <input type="text" name="no_rm" value="-" placeholder="RM-XXXXX">
                </div>
                <div class="form-group">
                    <label>Alamat Pasien</label>
                    <textarea name="alamat" rows="3">-</textarea>
                </div>
            </div>

            <!-- keranjang dan pembayaran-->
            <div class="panel">
                <h3>2. Rincian Tagihan Layanan & Obat</h3>
                
                <table border="1">
                    <thead>
                        <tr>
                            <th align="left">Item Layanan/Obat</th>
                            <th align="center">Qty</th>
                            <th align="right">Total (Rp)</th>
                            <th align="center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                        <tr><td colspan="4" align="center">Belum ada tindakan atau obat</td></tr>
                    </tbody>
                </table>

                <div class="summary-box">
                    <div class="summary-row total">
                        <span>TOTAL TAGIHAN</span>
                        <span>Rp <span id="grandTotalLabel">0</span></span>
                    </div>
                    <div class="summary-row">
                        <span>Uang Diterima dari Pasien</span>
                        <input type="number" id="uangBayar" name="uang_bayar" value="0" min="0" oninput="hitungKembalian()" required class="input-bayar">
                    </div>
                    <div class="summary-row kembali">
                        <span>KEMBALIAN</span>
                        <span>Rp <span id="kembalianLabel">0</span></span>
                    </div>
                </div>

                <input type="hidden" name="cart_data" id="cartDataInput">
                <button type="submit" name="checkout" class="btn-primary">Selesaikan Pembayaran</button>
            </div>
        </div>
    </form>

    <!-- katalog -->
    <div class="panel">
        <h3>Katalog Tindakan Medis & Farmasi</h3>
        <table border="1">
            <thead>
                <tr>
                    <th align="center">ID</th>
                    <th align="left">Nama Layanan / Obat</th>
                    <th align="right">Harga (Rp)</th>
                    <th align="center">Stok</th>
                    <th align="center">Jumlah</th>
                    <th align="center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($data_item)) : ?>
                    <tr>
                        <td align="center"><?= $b['id']; ?></td>
                        <td><?= $b['nama_item']; ?></td>
                        <td align="right"><?= number_format($b['harga'], 0, ',', '.'); ?></td>
                        <td align="center"><?= $b['stok'] > 1000 ? 'Unlimited' :$b['stok']; ?></td>
                        <td align="center">
                            <input type="number" id="qty_<?= $b['id']; ?>" value="1" min="1" class="input-qty">
                        </td>
                        <td align="center">
                            <button type="button" class="btn-add" onclick="tambahKeKeranjang('<?= $b['id']; ?>', '<?= $b['nama_item']; ?>', <?=$b['harga']; ?>)">+ Tambah</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<script>
// --- on/off css---
function toggleCSS() {
    let cssLink = document.getElementById('theme-css');
    let btn = document.getElementById('toggleCssBtn');
    
    if (cssLink.disabled) {
        cssLink.disabled = false; // buat menyalakan css
        localStorage.setItem('css_status', 'on');
        btn.innerText = "Matikan Tampilan Menarik";
    } else {
        cssLink.disabled = true; // buat mematikan css
        localStorage.setItem('css_status', 'off');
        btn.innerText = "Nyalakan Tampilan Menarik";
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let status = localStorage.getItem('css_status');
    if (status === 'off') {
        document.getElementById('theme-css').disabled = true;
        document.getElementById('toggleCssBtn').innerText = "Nyalakan Desain (CSS On)";
    }
});

// Keranjang kasir disini
let cart = [];
let totalHargaCart = 0;

function tambahKeKeranjang(id, name, price) {
    let inputQty = document.getElementById('qty_' + id);
    let qty = parseInt(inputQty.value);

    let index = cart.findIndex(item => item.id === id);
    if (index !== -1) {
        cart[index].qty += qty;
    } else {
        cart.push({ id, name, price, qty });
    }

    renderCart();
    inputQty.value = 1; 
}

function hapusCart(id) {
    cart = cart.filter(item => item.id !== id);
    renderCart();
}

function renderCart() {
    let tbody = document.getElementById('cartItems');
    totalHargaCart = 0;
    
    if (cart.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" align="center">Belum ada tindakan atau obat</td></tr>';
    } else {
        tbody.innerHTML = '';
        cart.forEach(item => {
            let itemTotal = item.price * item.qty;
            totalHargaCart += itemTotal;
            
            tbody.innerHTML += `
                <tr>
                    <td>${item.name}</td>
                    <td align="center">${item.qty}</td>
                    <td align="right">${itemTotal.toLocaleString('id-ID')}</td>
                    <td align="center"><button type="button" class="btn-del" onclick="hapusCart('${item.id}')">X</button></td>
                </tr>
            `;
        });
    }

    document.getElementById('grandTotalLabel').innerText = totalHargaCart.toLocaleString('id-ID');
    document.getElementById('cartDataInput').value = JSON.stringify(cart);
    hitungKembalian();
}

function hitungKembalian() {
    let inputBayar = parseInt(document.getElementById('uangBayar').value) || 0;
    let kembalianLabel = document.getElementById('kembalianLabel');
    
    if (totalHargaCart > 0 && inputBayar >= totalHargaCart) {
        let kembalian = inputBayar - totalHargaCart;
        kembalianLabel.innerText = kembalian.toLocaleString('id-ID');
    } else if (inputBayar > 0 && inputBayar < totalHargaCart) {
        kembalianLabel.innerText = 'Uang kurang!';
    } else {
        kembalianLabel.innerText = '0';
    }
}

function validasiBayar() {
    if (cart.length === 0) {
        alert('Keranjang tagihan masih kosong!');
        return false;
    }
    
    let inputBayar = parseInt(document.getElementById('uangBayar').value) || 0;
    if (inputBayar < totalHargaCart) {
        alert('Uang pembayaran dari pasien kurang!');
        return false;
    }

    return true;
}
</script>

</body>
</html>