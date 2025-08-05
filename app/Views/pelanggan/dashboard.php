<?= $this->extend('layout/template_pelanggan') ?>

<?= $this->section('title') ?>
Dashboard Pelanggan
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Sedikit style tambahan untuk card dan badge */
    .status-badge {
        font-size: 0.9em;
        padding: 0.5em 0.75em;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
    }
    .card {
        transition: all 0.2s ease-in-out;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>


<!-- BANNER HEADER -->
<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Dashboard</h1>
            <p class="lead fw-normal text-white-50 mb-0">Kelola semua pesanan laundry Anda di sini.</p>
        </div>
    </div>
</header>

<!-- KONTEN UTAMA DASHBOARD -->
<div class="container py-5">
    <!-- Header Sambutan dan Tombol Aksi Utama -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Halo, <?= strtok(session()->get('nama_lengkap'), " ") ?>!</h3>
            <p class="text-muted mb-0">Selamat datang kembali di Laundry Kilat.</p>
        </div>
        <a href="/pelanggan/pesanan/new" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-2"></i>Buat Pesanan Baru
        </a>
    </div>

    <?php if(session()->get('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->get('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

<!-- Ringkasan Pesanan Aktif -->
<h4 class="mb-3">Pesanan Aktif Anda</h4>
<div class="row">
    <?php
        $pesanan_aktif = array_filter($pesanan, function($p) {
            return !in_array($p['status_pesanan'], ['Selesai', 'Dibatalkan']);
        });
    ?>

    <?php if (empty($pesanan_aktif)): ?>
        <div class="col-12">
            <div class="card card-body text-center">
                <p class="mb-0 text-muted">Anda tidak memiliki pesanan yang sedang aktif saat ini.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach(array_slice($pesanan_aktif, 0, 3) as $p_aktif): ?>
            <?php
                // KAMUS WARNA DAN IKON UNTUK SETIAP STATUS
                $status_map = [
                    'Menunggu Penjemputan' => ['class' => 'alert-warning', 'icon' => 'fas fa-box-open'],
                    'Diproses'              => ['class' => 'alert-info',    'icon' => 'fas fa-sync-alt fa-spin'],
                    'Selesai Dicuci'        => ['class' => 'alert-primary', 'icon' => 'fas fa-tshirt'],
                    'Siap Diambil'          => ['class' => 'alert-success', 'icon' => 'fas fa-check-circle'],
                    'Sedang Diantar'        => ['class' => 'alert-success', 'icon' => 'fas fa-truck'],
                ];

                // Tentukan status saat ini, default ke 'info' jika tidak ada di map
                $current_status = $status_map[$p_aktif['status_pesanan']] ?? ['class' => 'alert-secondary', 'icon' => 'fas fa-question-circle'];
            ?>
            <div class="col-lg-4 mb-4">
                <div class="card h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title">Invoice: <b><?= $p_aktif['kode_invoice'] ?></b></h6>
                            <span class="text-muted small"><?= date('d M Y', strtotime($p_aktif['tanggal_pesan'])) ?></span>
                        </div>
                        <p class="card-text">Status Saat Ini:</p>

                        <!-- KOTAK STATUS YANG SUDAH DINAMIS -->
                        <div class="alert <?= $current_status['class'] ?> text-center">
                            <i class="<?= $current_status['icon'] ?> me-2"></i>
                            <b><?= $p_aktif['status_pesanan'] ?></b>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Total Tagihan:</span>
                            <b>
                                <?php 
                                    $detail_aktif = $detail_pesanan[$p_aktif['id']] ?? [];
                                    if ($p_aktif['total_harga'] == 0 && in_array('kiloan', $detail_aktif)) {
                                        echo '<span class="text-muted" style="font-size: 0.9em;">Menunggu Penimbangan</span>';
                                    } else {
                                        echo 'Rp ' . number_format($p_aktif['total_harga']);
                                    }
                                ?>
                            </b>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Status Bayar:</span>
                            <?php
                                $status_bayar_aktif = $p_aktif['status_pembayaran'] ?? 'pending';
                                $badge_aktif = ['pending' => 'bg-warning text-dark', 'sukses' => 'bg-success', 'gagal' => 'bg-danger'];
                            ?>
                            <span class="badge <?= $badge_aktif[$status_bayar_aktif] ?>"><?= ucfirst($status_bayar_aktif) ?></span>
                        </div>
                    </div>
                    <?php if($status_bayar_aktif == 'pending' && $p_aktif['total_harga'] > 0): ?>
                        <div class="card-footer bg-white border-0">
                            <a href="/pelanggan/pesanan/bayar/<?= $p_aktif['id'] ?>" class="btn btn-success w-100">
                                <i class="fas fa-credit-card me-2"></i>Lanjutkan Pembayaran
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<!-- Riwayat Semua Pesanan -->
<div class="card mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Riwayat Semua Pesanan</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal Pesan</th>
                        <th>Status Akhir</th>
                        <th>Pembayaran</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($pesanan)): ?>
                        <tr><td colspan="5" class="text-center py-4">Anda belum pernah membuat pesanan.</td></tr>
                    <?php else: ?>
                        <?php foreach($pesanan as $p): ?>
                            <tr>
                                <td><b><?= $p['kode_invoice'] ?></b></td>
                                <td><?= date('d M Y', strtotime($p['tanggal_pesan'])) ?></td>
                                <td>
                                    <?php
                                        $status_pesanan = $p['status_pesanan'];
                                        $badge_pesanan = 'bg-secondary';
                                        if ($status_pesanan == 'Selesai') {
                                            $badge_pesanan = 'bg-success';
                                        } elseif ($status_pesanan != 'Dibatalkan') {
                                            $badge_pesanan = 'bg-info';
                                        }
                                    ?>
                                    <span class="badge <?= $badge_pesanan ?> status-badge"><?= $status_pesanan ?></span>
                                </td>
                                <td>
                                    <?php
                                        $status_bayar = $p['status_pembayaran'] ?? 'pending';
                                        $badge_bayar = ['pending' => 'bg-warning text-dark', 'sukses' => 'bg-success', 'gagal' => 'bg-danger'];
                                    ?>
                                    <span class="badge <?= $badge_bayar[$status_bayar] ?> status-badge"><?= ucfirst($status_bayar) ?></span>
                                </td>
                                <!-- <td><b>Rp <?= number_format($p['total_harga']) ?></b></td> -->
                                 <td>
                                    <b>
                                        <?php 
                                            $detail = $detail_pesanan[$p['id']] ?? [];
                                            if ($p['total_harga'] == 0 && in_array('kiloan', $detail)) {
                                                echo '<span class="text-muted" style="font-size: 0.9em;">Menunggu Penimbangan</span>';
                                            } else {
                                                echo 'Rp ' . number_format($p['total_harga']);
                                            }
                                        ?>
                                    </b>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>