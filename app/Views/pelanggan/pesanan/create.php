<?= $this->extend('layout/template_pelanggan') ?>

<?= $this->section('title') ?>
Buat Pesanan Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- BANNER HEADER -->
<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Dashboard Anda</h1>
            <p class="lead fw-normal text-white-50 mb-0">Kelola semua pesanan laundry Anda di sini.</p>
        </div>
    </div>
</header>
<div class="container py-5">
    <h3>Formulir Pesanan Baru</h3>
    <p>Silakan lengkapi formulir di bawah ini untuk membuat pesanan.</p>

    <form action="/pelanggan/pesanan" method="post">
        <?= csrf_field() ?>

        <!-- Metode Pengiriman -->
        <div class="card mb-3">
            <div class="card-header">1. Metode Pengiriman</div>
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="metode_pengiriman" id="antar_jemput" value="antar_jemput" checked>
                    <label class="form-check-label" for="antar_jemput">
                        Antar-Jemput oleh Kurir Laundry (Gratis)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="metode_pengiriman" id="datang_langsung" value="datang_langsung">
                    <label class="form-check-label" for="datang_langsung">
                        Saya Akan Datang Langsung ke Toko
                    </label>
                </div>
                <div class="mb-3 mt-3" id="alamat-wrapper">
                    <label for="alamat" class="form-label">Alamat Penjemputan & Pengantaran</label>
                    <textarea class="form-control" name="alamat" id="alamat" rows="3" placeholder="Masukkan alamat lengkap Anda..."><?= session()->get('alamat') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Pilihan Layanan -->
        <div class="card mb-3">
            <div class="card-header">2. Pilih Layanan Anda</div>
            <div class="card-body">
                <h5>Layanan Kiloan</h5>
                <p class="text-muted small">Pilih salah satu. Berat akan dihitung oleh admin saat penjemputan.</p>
                <?php foreach($layanan_kiloan as $lk): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="layanan_kiloan" id="kiloan_<?= $lk['id'] ?>" value="<?= $lk['id'] ?>">
                    <label class="form-check-label" for="kiloan_<?= $lk['id'] ?>">
                        <?= esc($lk['nama_layanan']) ?> (Rp <?= number_format($lk['harga'], 0, ',', '.') ?> / kg)
                    </label>
                </div>
                <?php endforeach; ?>
                
                <hr>

                <h5>Layanan Satuan</h5>
                <p class="text-muted small">Pilih item dan masukkan jumlahnya jika ada.</p>
                <table class="table">
                    <?php foreach($layanan_satuan as $ls): ?>
                    <tr>
                        <td><?= esc($ls['nama_layanan']) ?> (Rp <?= number_format($ls['harga'], 0, ',', '.') ?>)</td>
                        <td style="width: 120px;">
                            <input type="number" name="layanan_satuan[<?= $ls['id'] ?>]" class="form-control" value="0" min="0">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

         <!-- Catatan -->
        <div class="card mb-3">
            <div class="card-header">3. Catatan Tambahan (Opsional)</div>
            <div class="card-body">
                <textarea class="form-control" name="catatan" id="catatan" rows="3" placeholder="Contoh: Tolong jangan gunakan pelembut pakaian..."></textarea>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Kirim Pesanan</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function(){
        $('input[name="metode_pengiriman"]').on('change', function(){
            if (this.value == 'antar_jemput') {
                $('#alamat-wrapper').show();
            } else {
                $('#alamat-wrapper').hide();
            }
        });
    });
</script>
<?= $this->endSection() ?>