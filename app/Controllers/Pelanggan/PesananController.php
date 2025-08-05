<?php namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\LayananModel;
use App\Models\PesananModel;
use App\Models\DetailPesananModel; // Kita akan buat model ini

class PesananController extends BaseController
{
    // Menampilkan form pemesanan
    public function new()
    {
        $layananModel = new LayananModel();
        $data = [
            'title' => 'Buat Pesanan Baru',
            'layanan_kiloan' => $layananModel->where('tipe_layanan', 'kiloan')->findAll(),
            'layanan_satuan' => $layananModel->where('tipe_layanan', 'satuan')->findAll(),
        ];
        return view('pelanggan/pesanan/create', $data);
    }

    // Memproses form pemesanan
    public function create()
    {
        helper('text'); // Untuk membuat string random


        $db = \Config\Database::connect();
        $pesananModel = new PesananModel();
        $detailModel = new DetailPesananModel();

        $db->transStart(); // Mulai transaksi

        // 1. Buat data untuk tabel 'pesanan'
        $kode_invoice = 'LNDRY-' . date('Ymd') . '-' . strtoupper(random_string('alnum', 4));
        $total_harga = 0;

        $dataPesanan = [
            'id_user' => session()->get('id_user'),
            'kode_invoice' => $kode_invoice,
            'metode_pengiriman' => $this->request->getPost('metode_pengiriman'),
            'alamat_pengiriman' => $this->request->getPost('metode_pengiriman') == 'antar_jemput' ? $this->request->getPost('alamat') : null,
            'status_pesanan' => $this->request->getPost('metode_pengiriman') == 'antar_jemput' ? 'Menunggu Penjemputan' : 'Diproses',
            'catatan_pelanggan' => $this->request->getPost('catatan'),
            'total_harga' => 0 // Akan diupdate oleh admin nanti
        ];
        $pesananModel->insert($dataPesanan);
        $id_pesanan = $pesananModel->getInsertID();

        
        (new \App\Models\PembayaranModel())->insert([
            'id_pesanan' => $id_pesanan,
            'metode_pembayaran' => 'cod', // Default, akan diupdate jika bayar online
            'status_pembayaran' => 'pending'
        ]);

        // 2. Buat data untuk tabel 'detail_pesanan'
        // Untuk layanan kiloan
        if ($this->request->getPost('layanan_kiloan')) {
            $detailModel->insert([
                'id_pesanan' => $id_pesanan,
                'id_layanan' => $this->request->getPost('layanan_kiloan'),
                'jumlah_item' => null, // Kiloan tidak ada jumlah item
                'subtotal' => 0 // Harga dihitung admin nanti
            ]);
        }
        // Untuk layanan satuan
        $layanan_satuan = $this->request->getPost('layanan_satuan');
        if (!empty($layanan_satuan)) {
            foreach ($layanan_satuan as $id_layanan => $jumlah) {
                if ($jumlah > 0) {
                    $layanan = (new LayananModel())->find($id_layanan);
                    $subtotal = $layanan['harga'] * $jumlah;
                    $total_harga += $subtotal;

                    $detailModel->insert([
                        'id_pesanan' => $id_pesanan,
                        'id_layanan' => $id_layanan,
                        'jumlah_item' => $jumlah,
                        'subtotal' => $subtotal
                    ]);
                }
            }
        }

        // 3. Update total harga di tabel pesanan
        $pesananModel->update($id_pesanan, ['total_harga' => $total_harga]);

        $db->transComplete(); // Selesaikan transaksi

        if ($db->transStatus() === FALSE) {
            // Jika transaksi gagal, rollback
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, pesanan gagal dibuat.');
        } else {
            // Jika transaksi berhasil
            return redirect()->to('/pelanggan/dashboard')->with('success', 'Pesanan Anda dengan invoice ' . $kode_invoice . ' berhasil dibuat!');
        }
    }

    public function bayar($id_pesanan)
    {
        helper('session');

        $pesananModel = new \App\Models\PesananModel();
        $userModel = new \App\Models\UserModel();

        $pesanan = $pesananModel->find($id_pesanan);
        if (!$pesanan) {
            return redirect()->to('/pelanggan/dashboard')->with('error', 'Pesanan tidak ditemukan.');
        }

        $user = $userModel->find($pesanan['id_user']);
        if (!$user || empty($user['no_telepon'])) {
            return redirect()->to('/pelanggan/dashboard')->with('error', 'Data pelanggan tidak lengkap (No. Telepon wajib diisi).');
        }

        if ($pesanan['total_harga'] <= 0) {
            return redirect()->to('/pelanggan/dashboard')->with('error', 'Pesanan ini belum memiliki total harga untuk dibayar.');
        }

        try {
            // Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
            \Midtrans\Config::$isProduction = ($_ENV['MIDTRANS_IS_PRODUCTION'] === 'true');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $pesanan['kode_invoice'],
                    'gross_amount' => (int) $pesanan['total_harga'],
                ],
                'customer_details' => [
                    'first_name' => $user['nama_lengkap'],
                    'email' => $user['email'],
                    'phone' => $user['no_telepon'],
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            $data = [
                'title' => 'Pembayaran Pesanan',
                'snapToken' => $snapToken,
                'pesanan' => $pesanan,
                'clientKey' => $_ENV['MIDTRANS_CLIENT_KEY'], // Kirim Client Key ke view
            ];

            return view('pelanggan/pesanan/bayar', $data);

        } catch (\Exception $e) {
            log_message('error', 'Midtrans Snap Token Error: ' . $e->getMessage());
            return redirect()->to('/pelanggan/dashboard')->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    // Menampilkan halaman detail satu pesanan untuk pelanggan
    public function show($id)
    {
        $id_user = session()->get('id_user');

        // Ambil data pesanan, pastikan pesanan ini milik user yang sedang login
        $pesanan = (new \App\Models\PesananModel())
                    ->where('id', $id)
                    ->where('id_user', $id_user) // Pengecekan keamanan
                    ->first();

        // Jika pesanan tidak ada atau bukan milik user ini, tampilkan error
        if (!$pesanan) {
            return redirect()->to('/pelanggan/dashboard')->with('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Ambil rincian item pesanan
        $db = \Config\Database::connect();
        $detail_pesanan = $db->table('detail_pesanan')
                            ->select('detail_pesanan.*, layanan.nama_layanan, layanan.tipe_layanan, layanan.harga as harga_satuan')
                            ->join('layanan', 'layanan.id = detail_pesanan.id_layanan')
                            ->where('detail_pesanan.id_pesanan', $id)
                            ->get()->getResultArray();
        
        // Ambil data pembayaran
        $pembayaran = (new \App\Models\PembayaranModel())->where('id_pesanan', $id)->first();

        $data = [
            'title' => 'Detail Pesanan ' . $pesanan['kode_invoice'],
            'pesanan' => $pesanan,
            'detail_pesanan' => $detail_pesanan,
            'pembayaran' => $pembayaran,
        ];

        return view('pelanggan/pesanan/show', $data);
    }
}