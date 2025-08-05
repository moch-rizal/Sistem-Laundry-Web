<?= $this->extend('layout/template_admin') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/sb-admin-2/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('title') ?>
Manajemen Pesanan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pesanan</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Pesanan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Tgl Pesan</th>
                            <th>Total Harga</th>
                            <th>Pengiriman</th>
                            <th>Status Pesanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($pesanan)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data pesanan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($pesanan as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($item['kode_invoice']) ?></td>
                                <td><?= esc($item['nama_lengkap']) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($item['tanggal_pesan'])) ?></td>
                                <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $item['metode_pengiriman'] == 'antar_jemput' ? 'primary text-white' : 'secondary text-white' ?>">
                                        <?= str_replace('_', ' ', $item['metode_pengiriman']) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Logika warna badge berdasarkan status -->
                                    <?php
                                        $status_class = '';
                                        switch ($item['status_pesanan']) {
                                            case 'Menunggu Penjemputan': $status_class = 'bg-warning text-dark'; break;
                                            case 'Diproses': $status_class = 'bg-info text-white'; break;
                                            case 'Selesai Dicuci': $status_class = 'bg-primary text-white'; break;
                                            case 'Siap Diambil':
                                            case 'Sedang Diantar': $status_class = 'bg-success text-white'; break;
                                            case 'Selesai': $status_class = 'bg-secondary text-white'; break;
                                            case 'Dibatalkan': $status_class = 'bg-danger text-white'; break;
                                        }
                                    ?>
                                    <span class="badge <?= $status_class ?>"><?= esc($item['status_pesanan']) ?></span>
                                </td>
                                <td>
                                    <a href="/admin/pesanan/<?= $item['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                                    <!-- Tombol Update Status & Hapus akan kita tambahkan nanti -->
                                    <form action="/admin/pesanan/<?= $item['id'] ?>" method="post" class="d-inline form-hapus">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Page level plugins -->
<script src="<?= base_url('assets/sb-admin-2/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/sb-admin-2/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<!-- Page level custom scripts -->
<script>
    $(document).ready(function() {
      $('#dataTable').DataTable({
          "order": [] // Menonaktifkan pengurutan default
      });
    });

    $('.form-hapus').on('submit', function(e) {
        e.preventDefault(); // Mencegah form dikirim langsung
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        })
    });
</script>
<?= $this->endSection() ?>