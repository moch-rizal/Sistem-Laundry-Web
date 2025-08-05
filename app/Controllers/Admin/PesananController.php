<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PesananModel;

class PesananController extends BaseController
{
    protected $pesananModel;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
    }

    // Menampilkan semua data pesanan
    public function index()
    {
        // Kita perlu menggabungkan (JOIN) tabel pesanan dengan tabel users untuk mendapatkan nama pelanggan
        $db = \Config\Database::connect();
        $builder = $db->table('pesanan');
        $builder->select('pesanan.*, users.nama_lengkap'); // Pilih semua kolom dari pesanan dan nama_lengkap dari users
        $builder->join('users', 'users.id = pesanan.id_user');
        $builder->orderBy('pesanan.tanggal_pesan', 'DESC'); // Urutkan dari yang terbaru
        $query = $builder->get();

        $data = [
            'title' => 'Manajemen Pesanan',
            'pesanan' => $query->getResultArray() // Ambil hasil sebagai array
        ];
        
        return view('admin/pesanan/index', $data);
    }
    
    // Menampilkan halaman detail satu pesanan
    public function show($id)
    {
    // 1. Ambil data pesanan utama, join dengan user untuk nama pelanggan
    $pesanan = $this->pesananModel
                    ->select('pesanan.*, users.nama_lengkap, users.email, users.no_telepon, users.alamat, pembayaran.status_pembayaran, pembayaran.metode_pembayaran') // TAMBAHKAN metode_pembayaran
                    ->join('users', 'users.id = pesanan.id_user')
                    ->join('pembayaran', 'pembayaran.id_pesanan = pesanan.id', 'left')
                    ->find($id);

    if (!$pesanan) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan');
    }

    // 2. Ambil rincian item pesanan, join dengan layanan untuk nama layanan dan harga
    $db = \Config\Database::connect();
    $detail_pesanan = $db->table('detail_pesanan')
                         ->select('detail_pesanan.*, layanan.nama_layanan, layanan.tipe_layanan, layanan.harga as harga_satuan')
                         ->join('layanan', 'layanan.id = detail_pesanan.id_layanan')
                         ->where('detail_pesanan.id_pesanan', $id)
                         ->get()->getResultArray();

    $data = [
        'title' => 'Detail Pesanan ' . $pesanan['kode_invoice'],
        'pesanan' => $pesanan,
        'detail_pesanan' => $detail_pesanan,
    ];
    
    return view('admin/pesanan/show', $data);
    }

    // Memproses update dari halaman detail
    public function update($id)
        {

        // ==========================================================
        // PASTIKAN BLOK INI ADA DAN BENAR
        // Ini adalah blok yang mendefinisikan variabel $pesanan_lama
        // ==========================================================
        $pesanan_lama = $this->pesananModel
                            ->select('pesanan.status_pesanan, users.email, users.nama_lengkap, pesanan.kode_invoice')
                            ->join('users', 'users.id = pesanan.id_user')
                            ->find($id);

        // Jika pesanan tidak ditemukan, hentikan proses untuk menghindari error
        if (!$pesanan_lama) {
            return redirect()->to('/admin/pesanan')->with('error', 'Pesanan tidak ditemukan.');
        }
        // ==========================================================
        // AKHIR DARI BLOK PENTING
        // ==========================================================

        // 1. Ambil data dari form
        $new_berat = $this->request->getPost('total_berat');
        $new_status = $this->request->getPost('status_pesanan');
        $new_total_harga = 0;

        // 2. Ambil semua item detail pesanan ini
        $db = \Config\Database::connect();
        $detail_pesanan = $db->table('detail_pesanan')
                            ->join('layanan', 'layanan.id = detail_pesanan.id_layanan')
                            ->where('id_pesanan', $id)->get()->getResultArray();

        // 3. Hitung ulang total harga berdasarkan data baru
        foreach ($detail_pesanan as $item) {
            if ($item['tipe_layanan'] == 'kiloan') {
                // Hitung subtotal baru untuk kiloan
                $subtotal_kiloan = $item['harga'] * $new_berat;
                $db->table('detail_pesanan')->where('id', $item['id'])->set(['subtotal' => $subtotal_kiloan])->update();
                $new_total_harga += $subtotal_kiloan;
            } else {
                // Untuk satuan, harganya tetap
                $new_total_harga += $item['subtotal'];
            }
        }

        // 4. Siapkan data untuk diupdate ke tabel pesanan
        $data_update = [
            'total_berat' => $new_berat,
            'total_harga' => $new_total_harga,
            'status_pesanan' => $new_status,
        ];
        
        // Jika status "Selesai", catat tanggal selesainya
        if ($new_status == 'Selesai') {
            $data_update['tanggal_selesai'] = date('Y-m-d H:i:s');
        }

        // 5. Lakukan update
        $this->pesananModel->update($id, $data_update);

        // ==========================================================
        //  PENGIRIMAN EMAIL
        // ==========================================================
        if ($pesanan_lama['status_pesanan'] != $new_status) {
            $email = \Config\Services::email();
            
            $emailData = [
                'nama_pelanggan' => $pesanan_lama['nama_lengkap'],
                'kode_invoice' => $pesanan_lama['kode_invoice'],
                'status_lama' => $pesanan_lama['status_pesanan'],
                'status_baru' => $new_status
            ];
            
            $email->setTo($pesanan_lama['email']);
            $email->setSubject('Update Status Pesanan Anda: ' . $pesanan_lama['kode_invoice']);
            $email->setMessage(view('emails/notifikasi_status', $emailData));

            if (!$email->send()) {
                log_message('error', 'Gagal mengirim email notifikasi status ke ' . $pesanan_lama['email'] . ': ' . $email->printDebugger(['headers']));
            }
        }

        return redirect()->to('/admin/pesanan/' . $id)->with('success', 'Data pesanan berhasil diupdate!');
    }

    // Fungsi untuk konfirmasi pembayaran COD
    public function konfirmasiCod($id_pesanan)
    {
        // Cari record pembayaran untuk pesanan ini
        $pembayaranModel = new \App\Models\PembayaranModel();
        $pembayaran = $pembayaranModel->where('id_pesanan', $id_pesanan)->first();

        if ($pembayaran) {
            // Update statusnya menjadi sukses
            $pembayaranModel->update($pembayaran['id'], [
                'status_pembayaran' => 'sukses',
                'waktu_pembayaran' => date('Y-m-d H:i:s')
            ]);
            return redirect()->to('/admin/pesanan/' . $id_pesanan)->with('success', 'Pembayaran COD berhasil dikonfirmasi.');
        }

        return redirect()->to('/admin/pesanan/' . $id_pesanan)->with('error', 'Data pembayaran tidak ditemukan.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        
        // Mulai transaksi
        $db->transStart();

        // Hapus dari tabel detail_pesanan
        $db->table('detail_pesanan')->where('id_pesanan', $id)->delete();
        
        // Hapus dari tabel pembayaran
        $db->table('pembayaran')->where('id_pesanan', $id)->delete();
        
        // Hapus dari tabel pesanan utama
        $this->pesananModel->delete($id);

        // Selesaikan transaksi
        $db->transComplete();

        if ($db->transStatus() === false) {
            // Jika transaksi gagal
            return redirect()->to('/admin/pesanan')->with('error', 'Gagal menghapus pesanan.');
        }

        // Jika transaksi berhasil
        return redirect()->to('/admin/pesanan')->with('success', 'Pesanan berhasil dihapus.');
    }
}
