<?php namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\PesananModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $pesanan_query = $db->table('pesanan')
                            ->select('pesanan.*, pembayaran.status_pembayaran')
                            ->join('pembayaran', 'pembayaran.id_pesanan = pesanan.id', 'left')
                            ->where('pesanan.id_user', session()->get('id_user'))
                            ->orderBy('pesanan.tanggal_pesan', 'DESC')
                            ->get()->getResultArray();
        
        // Ambil detail pesanan untuk semua pesanan
        $id_pesanan_list = array_column($pesanan_query, 'id');
        $detail_pesanan = [];
        if (!empty($id_pesanan_list)) {
            $detail_query = $db->table('detail_pesanan')
                                ->select('detail_pesanan.id_pesanan, layanan.tipe_layanan')
                                ->join('layanan', 'layanan.id = detail_pesanan.id_layanan')
                                ->whereIn('detail_pesanan.id_pesanan', $id_pesanan_list)
                                ->get()->getResultArray();

            foreach($detail_query as $detail) {
                $detail_pesanan[$detail['id_pesanan']][] = $detail['tipe_layanan'];
            }
        }

        $data = [
            'title' => 'Dashboard Pelanggan',
            'pesanan' => $pesanan_query,
            'detail_pesanan' => $detail_pesanan
        ];

        return view('pelanggan/dashboard', $data);
    }
}