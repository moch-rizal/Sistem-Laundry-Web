<?= $this->extend('layout/template_admin') ?>

<?= $this->section('title') ?>
Detail Pesanan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pesanan: <?= esc($pesanan['kode_invoice']) ?></h1>
        <a href="/admin/pesanan" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Daftar Pesanan
        </a>

        <form action="/admin/pesanan/<?= $pesanan['id'] ?>" method="post" class="d-inline form-hapus">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash fa-sm"></i> Hapus Pesanan Ini
            </button>
        </form>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Info Pesanan & Pelanggan -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Info Pelanggan</h6>
                </div>
                <div class="card-body">
                    <strong>Nama:</strong><p class="mb-2"><?= esc($pesanan['nama_lengkap']) ?></p>
                    <strong>Email:</strong><p class="mb-2"><?= esc($pesanan['email']) ?></p>
                    <strong>No. Telepon:</strong><p class="mb-2"><?= esc($pesanan['no_telepon'] ?? '-') ?></p>
                    <strong>Alamat:</strong>
                    <p class="mb-0"><?= esc($pesanan['alamat_pengiriman'] ?? $pesanan['alamat'] ?? 'Datang langsung ke toko') ?></p>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Info Pesanan</h6>
                </div>
                <div class="card-body">
                    <strong>Tgl Pesan:</strong><p class="mb-2"><?= date('d M Y, H:i', strtotime($pesanan['tanggal_pesan'])) ?></p>
                    <strong>Pengiriman:</strong><p class="mb-2 text-capitalize"><?= str_replace('_', ' ', $pesanan['metode_pengiriman']) ?></p>

                    <?php
                    // ==========================================================
                    // BAGIAN YANG DITAMBAHKAN
                    // ==========================================================
                    ?>
                    <strong>Status Pembayaran:</strong>
                    <p class="mb-2">
                        <?php
                            $status_bayar = $pesanan['status_pembayaran'] ?? 'pending';
                            $badge_class = 'bg-warning text-dark';
                            if ($status_bayar == 'sukses') {
                                $badge_class = 'bg-success text-white';
                            } elseif ($status_bayar == 'gagal') {
                                $badge_class = 'bg-danger text-white';
                            }
                        ?>
                        <span class="badge <?= $badge_class ?>"><?= ucfirst($status_bayar) ?></span>
                    </p>
                    <?php // ========================================================== ?>

                    <?php
                    // ==========================================================
                    // BAGIAN YANG DITAMBAHKAN
                    // ==========================================================
                    $metode_bayar = $pesanan['metode_pembayaran'] ?? 'cod';
                    if ($status_bayar === 'pending' && $metode_bayar === 'cod'):
                    ?>
                        <form action="/admin/pesanan/konfirmasi_cod/<?= $pesanan['id'] ?>" method="post" class="mt-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-success w-100" onclick="return confirm('Apakah Anda yakin pesanan ini sudah dibayar lunas secara tunai?')">
                                <i class="fas fa-check"></i> Tandai Sudah Bayar (COD)
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php // ========================================================== ?>

                    <strong>Catatan Pelanggan:</strong><p class="mb-0 fst-italic">"<?= esc($pesanan['catatan_pelanggan'] ?? 'Tidak ada catatan.') ?>"</p>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian & Update -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rincian Layanan & Update Status</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Layanan</th>
                                    <th>Tipe</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($detail_pesanan as $detail): ?>
                                <tr>
                                    <td><?= esc($detail['nama_layanan']) ?></td>
                                    <td><?= esc($detail['tipe_layanan']) ?></td>
                                    <td><?= $detail['tipe_layanan'] == 'kiloan' ? ($pesanan['total_berat'] . ' kg') : ($detail['jumlah_item'] . ' pcs') ?></td>
                                    <td>Rp <?= number_format($detail['harga_satuan']) ?></td>
                                    <td>Rp <?= number_format($detail['subtotal']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Total Akhir</td>
                                    <td>Rp <?= number_format($pesanan['total_harga']) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <!-- FORM UPDATE -->
                    <form action="/admin/pesanan/<?= $pesanan['id'] ?>" method="post">
                        <?= csrf_field() ?>
                         <input type="hidden" name="_method" value="PUT">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_berat"><b>Update Berat (jika kiloan)</b></label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" class="form-control" name="total_berat" id="total_berat" value="<?= $pesanan['total_berat'] ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">kg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_pesanan"><b>Update Status Pesanan</b></label>
                                    <select name="status_pesanan" id="status_pesanan" class="form-control">
                                        <?php 
                                            $statuses = ['Menunggu Penjemputan', 'Diproses', 'Selesai Dicuci', 'Siap Diambil', 'Sedang Diantar', 'Selesai', 'Dibatalkan'];
                                            foreach($statuses as $status):
                                        ?>
                                            <option value="<?= $status ?>" <?= $pesanan['status_pesanan'] == $status ? 'selected' : '' ?>><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!-- TAMBAHKAN SECTION BARU INI -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.form-hapus').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pesanan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            })
        });
    });
</script>
<?= $this->endSection() ?>