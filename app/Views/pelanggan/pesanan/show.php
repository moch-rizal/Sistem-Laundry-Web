<?= $this->extend('layout/template_pelanggan') ?>

<?= $this->section('title') ?>
Detail Pesanan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Detail Pesanan</h3>
        <a href="/pelanggan/dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <strong>Invoice:</strong> <?= esc($pesanan['kode_invoice']) ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <strong>Tanggal Pesan:</strong> <?= date('d M Y, H:i', strtotime($pesanan['tanggal_pesan'])) ?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title">Status Saat Ini: <span class="badge bg-primary"><?= esc($pesanan['status_pesanan']) ?></span></h5>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h6>Rincian Layanan:</h6>
                    <ul class="list-group">
                        <?php foreach($detail_pesanan as $detail): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <?= esc($detail['nama_layanan']) ?>
                                <small class="d-block text-muted">
                                    <?php if($detail['tipe_layanan'] == 'kiloan'): ?>
                                        <?= $pesanan['total_berat'] > 0 ? esc($pesanan['total_berat']) . ' kg' : 'Berat akan diupdate admin' ?>
                                    <?php else: ?>
                                        <?= esc($detail['jumlah_item']) ?> pcs
                                    <?php endif; ?>
                                </small>
                            </div>
                            <span>Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Rincian Pembayaran:</h6>
                    <table class="table table-sm">
                        <tr>
                            <td>Total Harga</td>
                            <td class="text-end"><b>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></b></td>
                        </tr>
                        <tr>
                            <td>Metode Bayar</td>
                            <td class="text-end text-uppercase"><?= esc($pembayaran['metode_pembayaran']) ?></td>
                        </tr>
                        <tr>
                            <td>Status Bayar</td>
                            <td class="text-end">
                                <?php
                                    $status_bayar = $pembayaran['status_pembayaran'] ?? 'pending';
                                    $badge_class = 'bg-warning text-dark';
                                    if ($status_bayar == 'sukses') { $badge_class = 'bg-success'; } 
                                    elseif ($status_bayar == 'gagal') { $badge_class = 'bg-danger'; }
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= ucfirst($status_bayar) ?></span>
                            </td>
                        </tr>
                    </table>
                    
                    <?php 
                        // Tampilkan tombol bayar jika diperlukan
                        if ($pesanan['total_harga'] > 0 && $pembayaran['status_pembayaran'] == 'pending'): 
                    ?>
                        <div class="d-grid">
                            <a href="/pelanggan/pesanan/bayar/<?= $pesanan['id'] ?>" class="btn btn-success mt-3">
                                <i class="fas fa-credit-card"></i> Lanjutkan Pembayaran
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>