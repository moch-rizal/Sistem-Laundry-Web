<?= $this->extend('layout/template_admin') ?>

<?= $this->section('title') ?>
Edit Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Edit Data Pelanggan</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/admin/pelanggan/<?= $pelanggan['id'] ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" value="<?= old('nama_lengkap', $pelanggan['nama_lengkap']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= old('email', $pelanggan['email']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="no_telepon">No. Telepon</label>
                            <input type="text" class="form-control" name="no_telepon" value="<?= old('no_telepon', $pelanggan['no_telepon']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru (Opsional)</label>
                            <input type="password" class="form-control" name="password">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3"><?= old('alamat', $pelanggan['alamat']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Data</button>
                <a href="/admin/pelanggan" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>