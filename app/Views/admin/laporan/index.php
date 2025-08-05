<?= $this->extend('layout/template_admin') ?> 

<?= $this->section('title') ?>
Laporan Transaksi
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Transaksi</h1>
    </div>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="/admin/laporan" method="get">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" value="<?= esc($tanggal_mulai) ?>">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir" value="<?= esc($tanggal_akhir) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                         <label> </label>
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hasil Laporan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Hasil Laporan Periode <?= date('d M Y', strtotime($tanggal_mulai)) ?> s/d <?= date('d M Y', strtotime($tanggal_akhir)) ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Tgl Bayar</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($laporan)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data untuk periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($laporan as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($item['kode_invoice']) ?></td>
                                <td><?= esc($item['nama_lengkap']) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($item['waktu_pembayaran'])) ?></td>
                                <td class="text-right">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Total Pendapatan</td>
                            <td class="text-right">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
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
      $('#dataTable').DataTable({
          "order": [] // Menonaktifkan pengurutan default
      });
    });
</script>
<?= $this->endSection() ?>