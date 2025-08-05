<?= $this->extend('layout/template_admin') ?>

<?= $this->section('title') ?>
Tambah Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Form Tambah Pelanggan Baru</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/admin/pelanggan" method="post">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= old('email') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="no_telepon">No. Telepon</label>
                            <input type="text" class="form-control" name="no_telepon" value="<?= old('no_telepon') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" required>
                            <small class="form-text text-muted">Minimal 8 karakter.</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3"><?= old('alamat') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
                <a href="/admin/pelanggan" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>