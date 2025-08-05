<?= $this->extend('layout/template_admin') ?> 

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/sb-admin-2/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('title') ?>
Manajemen Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pelanggan</h1>
        <a href="/admin/pelanggan/new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pelanggan Baru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Tgl Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($pelanggan as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['nama_lengkap']) ?></td>
                            <td><?= esc($item['email']) ?></td>
                            <td><?= esc($item['no_telepon']) ?></td>
                            <td><?= date('d M Y', strtotime($item['created_at'])) ?></td>
                            <td>
                                <a href="/admin/pelanggan/<?= $item['id'] ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/admin/pelanggan/<?= $item['id'] ?>" method="post" class="d-inline form-hapus">
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
<script src="<?= base_url('assets/sb-admin-2/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/sb-admin-2/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script>
    $(document).ready(function() {
      $('#dataTable').DataTable();
      
      $('.form-hapus').on('submit', function(e) {
          e.preventDefault();
          Swal.fire({
              title: 'Apakah Anda yakin?',
              text: "Menghapus pelanggan juga akan menghapus semua riwayat pesanannya!",
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