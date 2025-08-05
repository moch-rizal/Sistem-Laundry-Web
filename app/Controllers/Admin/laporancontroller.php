<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LaporanController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pesanan');

        // Ambil tanggal dari input GET, jika tidak ada, default ke bulan ini
        $tanggal_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-t');

        // Query untuk mengambil data laporan
        $builder->select('pesanan.*, users.nama_lengkap, pembayaran.waktu_pembayaran');
        $builder->join('users', 'users.id = pesanan.id_user');
        $builder->join('pembayaran', 'pembayaran.id_pesanan = pesanan.id');
        $builder->where('pembayaran.status_pembayaran', 'sukses');
        $builder->where('DATE(pembayaran.waktu_pembayaran) >=', $tanggal_mulai);
        $builder->where('DATE(pembayaran.waktu_pembayaran) <=', $tanggal_akhir);
        $builder->orderBy('pembayaran.waktu_pembayaran', 'ASC');
        $laporan = $builder->get()->getResultArray();

        // Hitung total pendapatan dari hasil query
        $totalPendapatan = 0;
        foreach ($laporan as $item) {
            $totalPendapatan += $item['total_harga'];
        }

        $data = [
            'title' => 'Laporan Transaksi',
            'laporan' => $laporan,
            'totalPendapatan' => $totalPendapatan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
        ];

        return view('admin/laporan/index', $data);
    }
}