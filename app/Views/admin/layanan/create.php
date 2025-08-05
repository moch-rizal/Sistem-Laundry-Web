<?= $this->extend('layout/template_admin') ?>

<?= $this->section('title') ?>
Tambah Layanan Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Tambah Layanan Baru</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/admin/layanan" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="nama_layanan" class="form-label">Nama Layanan</label>
                    <input type="text" class="form-control" id="nama_layanan" name="nama_layanan" required>
                </div>
                <div class="mb-3">
                    <label for="tipe_layanan" class="form-label">Tipe Layanan</label>
                    <select class="form-select" name="tipe_layanan" id="tipe_layanan" required>
                        <option value="kiloan">Kiloan</option>
                        <option value="satuan">Satuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga (Rp)</label>
                    <input type="number" class="form-control" id="harga" name="harga" required>
                </div>
                <div class="mb-3">
                    <label for="estimasi_waktu" class="form-label">Estimasi Waktu (Contoh: 2 Hari)</label>
                    <input type="text" class="form-control" id="estimasi_waktu" name="estimasi_waktu">
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/admin/layanan" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>