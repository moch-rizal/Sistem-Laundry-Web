<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LayananModel;
use App\Models\PesananModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $layananModel = new LayananModel();
        $pesananModel = new PesananModel();
        $db = \Config\Database::connect();

        $jumlahPelanggan = $userModel->where('role', 'pelanggan')->countAllResults();
        $jumlahLayanan = $layananModel->countAllResults();
        $pesananBaru = $pesananModel->whereIn('status_pesanan', ['Menunggu Penjemputan', 'Diproses'])->countAllResults();
        
        $totalPendapatan = $db->table('pesanan')
                               ->join('pembayaran', 'pembayaran.id_pesanan = pesanan.id')
                               ->where('pembayaran.status_pembayaran', 'sukses')
                               ->selectSum('pesanan.total_harga', 'total')
                               ->get()->getRow()->total ?? 0;

        
        // Siapkan rentang tanggal untuk 7 hari terakhir
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-6 days'));

        // Query untuk mengambil total pendapatan per hari dalam rentang waktu tersebut
        $chartQuery = $db->table('pembayaran')
                         ->select("DATE(waktu_pembayaran) as tanggal, SUM(pesanan.total_harga) as total_harian")
                         ->join('pesanan', 'pesanan.id = pembayaran.id_pesanan')
                         ->where('status_pembayaran', 'sukses')
                         ->where('DATE(waktu_pembayaran) >=', $startDate)
                         ->where('DATE(waktu_pembayaran) <=', $endDate)
                         ->groupBy('DATE(waktu_pembayaran)')
                         ->orderBy('tanggal', 'ASC')
                         ->get()->getResultArray();
        
        // Proses data untuk format yang dibutuhkan oleh Chart.js
        $chartLabels = [];
        $chartValues = [];
        
        $period = new \DatePeriod(
             new \DateTime($startDate),
             new \DateInterval('P1D'),
             (new \DateTime($endDate))->modify('+1 day') 
        );

        $dailyRevenue = [];
        foreach ($period as $date) {
            $dailyRevenue[$date->format('Y-m-d')] = 0;
        }

        // Isi pendapatan dari hasil query
        foreach ($chartQuery as $row) {
            $dailyRevenue[$row['tanggal']] = (int) $row['total_harian'];
        }

        // Pisahkan menjadi label (tanggal) dan value (pendapatan)
        foreach ($dailyRevenue as $date => $total) {
            $chartLabels[] = date('d M', strtotime($date)); 
            $chartValues[] = $total;
        }

        $chartData = [
            'labels' => $chartLabels,
            'data' => $chartValues
        ];

        $data = [
            'title' => 'Admin Dashboard',
            'jumlahPelanggan' => $jumlahPelanggan,
            'jumlahLayanan' => $jumlahLayanan,
            'pesananBaru' => $pesananBaru,
            'totalPendapatan' => $totalPendapatan,
            'chartData' => json_encode($chartData), // Kirim data dinamis ke view
        ];

        return view('admin/dashboard', $data);
    }
}