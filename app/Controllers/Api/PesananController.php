<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\LayananModel;
use App\Models\PembayaranModel;

class PesananController extends BaseController
{
    /**
     * API untuk mendapatkan riwayat pesanan milik user yang sedang login
     */
    public function index()
    {
        $user = $this->request->user; // Ambil data user dari filter
        $pesananModel = new PesananModel();
        
        $pesanan = $pesananModel->where('id_user', $user->uid)->findAll();

        // Langsung return array-nya, CodeIgniter akan menanganinya
        return $this->response->setJSON($pesanan);
    }


    /**
     * API untuk membuat pesanan baru dari aplikasi mobile
     */
    public function create()
    {
        helper('text');
        $user = $this->request->user; // Ambil data user dari JWTAuthFilter

        // Validasi input JSON
        $rules = [
            'metode_pengiriman' => 'required|in_list[antar_jemput,datang_langsung]',
            'items'             => 'required|is_array',
        ];
        // Alamat wajib jika metode antar_jemput
        if ($this->request->getJSON()->metode_pengiriman === 'antar_jemput') {
            $rules['alamat'] = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 
                'errors' => $this->validator->getErrors()
            ]);
        }

        $json = $this->request->getJSON();
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();

            $pesananModel = new PesananModel();
            $detailModel = new DetailPesananModel();
            $layananModel = new LayananModel();

            // 1. Buat data 'pesanan'
            $kode_invoice = 'LNDRY-' . date('Ymd') . '-' . strtoupper(random_string('alnum', 4));
            $pesananModel->insert([
                'id_user' => $user->uid, // Ambil ID dari payload token
                'kode_invoice' => $kode_invoice,
                'metode_pengiriman' => $json->metode_pengiriman,
                'alamat_pengiriman' => $json->alamat ?? null,
                'status_pesanan' => $json->metode_pengiriman === 'antar_jemput' ? 'Menunggu Penjemputan' : 'Diproses',
                'catatan_pelanggan' => $json->catatan ?? null,
                'total_harga' => 0 // Akan dihitung setelahnya
            ]);
            $id_pesanan = $pesananModel->getInsertID();

            // 2. Buat data 'detail_pesanan' dan hitung total harga
            $total_harga = 0;
            foreach ($json->items as $item) {
                $layanan = $layananModel->find($item->id);
                if (!$layanan) continue; // Lewati jika ID layanan tidak valid

                $subtotal = 0;
                if ($layanan['tipe_layanan'] == 'satuan') {
                    $subtotal = $layanan['harga'] * $item->jumlah;
                    $total_harga += $subtotal;
                }

                $detailModel->insert([
                    'id_pesanan' => $id_pesanan,
                    'id_layanan' => $item->id,
                    'jumlah_item' => ($layanan['tipe_layanan'] == 'satuan') ? $item->jumlah : null,
                    'subtotal' => $subtotal
                ]);
            }

            // 3. Update total harga final di tabel pesanan
            $pesananModel->update($id_pesanan, ['total_harga' => $total_harga]);
            
            // 4. Buat record pembayaran awal
            (new PembayaranModel())->insert([
                'id_pesanan' => $id_pesanan, 
                'status_pembayaran' => 'pending'
            ]);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                // Jika transaksi gagal, lempar exception
                throw new \Exception('Gagal menyimpan data pesanan ke database.');
            }
            
            return $this->response->setStatusCode(201)->setJSON([
                'status' => 'success', 
                'message' => 'Pesanan berhasil dibuat.',
                'data' => [
                    'id_pesanan' => $id_pesanan,
                    'kode_invoice' => $kode_invoice
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API untuk membatalkan pesanan (contoh implementasi Update)
     */
    public function update($id = null)
    {
        $user = $this->request->user;
        $pesananModel = new PesananModel();

        // Cari pesanan milik user ini
        $pesanan = $pesananModel->where('id', $id)->where('id_user', $user->uid)->first();

        if (!$pesanan) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.']);
        }

        // Logika bisnis: hanya boleh dibatalkan jika statusnya masih "Menunggu Penjemputan"
        if ($pesanan['status_pesanan'] !== 'Menunggu Penjemputan') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Pesanan tidak dapat dibatalkan karena sudah diproses.']);
        }

        // Update status menjadi 'Dibatalkan'
        $pesananModel->update($id, ['status_pesanan' => 'Dibatalkan']);

        return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan.']);
    }

    /**
     * API untuk menghapus pesanan (opsional)
     */
    public function delete($id = null)
    {
        $user = $this->request->user;
        $pesananModel = new PesananModel();
        
        // Cari pesanan milik user ini
        $pesanan = $pesananModel->where('id', $id)->where('id_user', $user->uid)->first();
        if (!$pesanan) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.']);
        }

        // Hapus pesanan menggunakan transaksi untuk memastikan semua data terkait terhapus
        $db = \Config\Database::connect();
        $db->transStart();
        $db->table('detail_pesanan')->where('id_pesanan', $id)->delete();
        $db->table('pembayaran')->where('id_pesanan', $id)->delete();
        $pesananModel->delete($id);
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menghapus pesanan.']);
        }
        
        return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Pesanan berhasil dihapus.']);
    }

    /**
     * API untuk mendapatkan detail satu pesanan
     */
    public function show($id = null)
    {
        $user = $this->request->user;
        $pesananModel = new PesananModel();
        
        $pesanan = $pesananModel->where('id', $id)->where('id_user', $user->uid)->first();
        if (!$pesanan) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.']);
        }

        // Ambil juga detail itemnya
        $detailModel = new DetailPesananModel();
        $detail_items = $detailModel->select('layanan.nama_layanan, detail_pesanan.*')
                                    ->join('layanan', 'layanan.id = detail_pesanan.id_layanan')
                                    ->where('id_pesanan', $id)
                                    ->findAll();
        
        $pesanan['detail_items'] = $detail_items;

        return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'data' => $pesanan]);
    }

     /**
     * API untuk mengupload foto kondisi barang untuk sebuah pesanan
     */
    public function uploadFoto($id_pesanan = null)
    {
        $user = $this->request->user;
        $pesananModel = new PesananModel();

        // 1. Validasi Pesanan
        // Pastikan pesanan ada dan dimiliki oleh user yang sedang login
        $pesanan = $pesananModel->where('id', $id_pesanan)->where('id_user', $user->uid)->first();
        if (!$pesanan) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.']);
        }

        // 2. Validasi File Upload
        $validationRule = [
            'foto' => [
                'label' => 'Foto Kondisi Barang',
                'rules' => 'uploaded[foto]'
                    . '|is_image[foto]'
                    . '|mime_in[foto,image/jpg,image/jpeg,image/png]'
                    . '|max_size[foto,2048]', // Maksimal 2MB
            ],
        ];
        if (!$this->validate($validationRule)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
        }

        // 3. Proses File Upload
        $img = $this->request->getFile('foto');

        if (!$img->isValid() || $img->hasMoved()) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal memproses file.']);
        }
        
        // Hapus foto lama jika ada untuk menghemat ruang
        if (!empty($pesanan['foto_kondisi_barang'])) {
            $path_lama = FCPATH . 'uploads/kondisi_barang/' . $pesanan['foto_kondisi_barang'];
            if (file_exists($path_lama)) {
                unlink($path_lama);
            }
        }

        // Buat nama file baru yang unik untuk menghindari konflik
        $newName = $img->getRandomName();
        // Pindahkan file ke folder public/uploads/kondisi_barang
        $img->move(FCPATH . 'uploads/kondisi_barang', $newName);

        // 4. Update Database
        $pesananModel->update($id_pesanan, ['foto_kondisi_barang' => $newName]);

        // 5. Kirim Respons Sukses
        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'message' => 'Foto berhasil diupload.',
            'url' => base_url('uploads/kondisi_barang/' . $newName)
        ]);
    }


}