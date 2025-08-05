<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Status Pesanan</title>
    <style>
        body { font-family: sans-serif; }
        .container { padding: 20px; border: 1px solid #e0e0e0; max-width: 600px; margin: auto; }
        .header { background-color: #0d6efd; color: white; padding: 10px; text-align: center; }
        .content { padding: 20px 0; }
        .footer { font-size: 0.9em; text-align: center; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Update Pesanan Laundry Anda</h2>
        </div>
        <div class="content">
            <p>Halo, <b><?= esc($nama_pelanggan) ?></b>,</p>
            <p>
                Kami ingin memberitahu Anda bahwa status pesanan Anda dengan nomor invoice 
                <b><?= esc($kode_invoice) ?></b> telah diperbarui.
            </p>
            <p>
                Status Sebelumnya: <b><?= esc($status_lama) ?></b><br>
                Status Sekarang: <b><?= esc($status_baru) ?></b>
            </p>
            <p>
                Anda dapat melihat detail pesanan Anda dengan login ke akun Anda.
            </p>
            <p>Terima kasih telah menggunakan layanan kami.</p>
        </div>
        <div class="footer">
            <p>© <?= date('Y') ?> Laundry Kilat</p>
        </div>
    </div>
</body>
</html>