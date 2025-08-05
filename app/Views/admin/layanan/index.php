<?= $this->section('styles') ?>
<link href="<?= base_url('assets/sb-admin-2/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->extend('layout/template_admin') ?> 
<!-- Kita akan buat template_admin nanti -->

<?= $this->section('title') ?>
Manajemen Layanan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Layanan</h1>
        <a href="/admin/layanan/new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Layanan Baru
        </a>
    </div>

    <?php if(session()->get('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->get('success') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Layanan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Layanan</th>
                            <th>Tipe</th>
                            <th>Harga</th>
                            <th>Estimasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($layanan as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['nama_layanan']) ?></td>
                            <td>
                                <span class="badge bg-<?= $item['tipe_layanan'] == 'kiloan' ? 'info' : 'success' ?>">
                                    <?= esc($item['tipe_layanan']) ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td><?= esc($item['estimasi_waktu']) ?></td>
                            <td>
                                <a href="/admin/layanan/<?= $item['id'] ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/admin/layanan/<?= $item['id'] ?>" method="post" class="d-inline form-hapus">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
    // Call the dataTables jQuery plugin
    $(document).ready(function() {
      $('#dataTable').DataTable();
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
            // Jika user mengklik "Ya, hapus!", kirim formnya
            this.submit();
            }
        })
    });
</script>
<?= $this->endSection() ?>