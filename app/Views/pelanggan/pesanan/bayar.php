<?= $this->extend('layout/template_pelanggan') ?>

<?= $this->section('title') ?>
Pembayaran <?= $pesanan['kode_invoice'] ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <!-- Tambahkan script Midtrans Snap.js -->
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="<?= getenv('MIDTRANS_CLIENT_KEY') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container text-center">
    <h4>Selesaikan Pembayaran Anda</h4>
    <p>Invoice: <b><?= $pesanan['kode_invoice'] ?></b></p>
    <p>Total: <b>Rp <?= number_format($pesanan['total_harga']) ?></b></p>
    <br>
    <button id="pay-button" class="btn btn-success">Bayar Sekarang!</button>
</div>

<script type="text/javascript">
  // Ambil tombol bayar
  var payButton = document.getElementById('pay-button');
  
  // Tambahkan event listener untuk tombol
  payButton.addEventListener('click', function () {
    // Panggil snap.pay dengan Snap Token
    snap.pay('<?= $snapToken ?>', {
      onSuccess: function(result){
        /* Anda bisa menambahkan logika di sini, misal: redirect ke halaman sukses */
        alert("Pembayaran berhasil!"); 
        console.log(result);
        window.location.href = '/pelanggan/dashboard';
      },
      onPending: function(result){
        /* Pelanggan belum menyelesaikan pembayaran */
        alert("Menunggu pembayaran Anda!"); 
        console.log(result);
        window.location.href = '/pelanggan/dashboard';
      },
      onError: function(result){
        /* Terjadi kesalahan */
        alert("Pembayaran gagal!"); 
        console.log(result);
        window.location.href = '/pelanggan/dashboard';
      },
      onClose: function(){
        /* Pelanggan menutup pop-up tanpa menyelesaikan pembayaran */
        alert('Anda menutup pop-up tanpa menyelesaikan pembayaran');
      }
    });
  });
</script>
<?= $this->endSection() ?>