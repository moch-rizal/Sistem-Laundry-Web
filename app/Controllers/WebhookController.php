<?php namespace App\Controllers;

class WebhookController extends BaseController
{
    public function midtrans()
    {
        // Set header untuk memberitahu Midtrans bahwa kita merespons dalam format JSON
        $this->response->setHeader('Content-Type', 'application/json');

        // Ambil notifikasi dalam bentuk raw input stream
        $json = file_get_contents('php://input');
        $notif = json_decode($json);

        // Jika tidak ada notifikasi atau tidak ada order_id, mungkin ini hanya tes sederhana
        if ($notif === null || !isset($notif->order_id)) {
            // Berikan respons sukses untuk tes yang berhasil terhubung
            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Test notification successfully received.']);
        }

        // Konfigurasi Midtrans untuk validasi signature key
        \Midtrans\Config::$isProduction = ($_ENV['MIDTRANS_IS_PRODUCTION'] === 'true');
        \Midtrans\Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
        
        // Validasi signature key untuk keamanan
        $signature_key = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . \Midtrans\Config::$serverKey);
        
        // Hentikan jika signature tidak cocok (menandakan notifikasi palsu)
        if ($notif->signature_key != $signature_key) {
            return $this->response->setStatusCode(403, 'Invalid Signature Key');
        }

        // Jika signature cocok, lanjutkan proses update
        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id; // Ini adalah kode_invoice kita
        $fraud = $notif->fraud_status;

        // Cari pesanan berdasarkan kode_invoice
        $db = \Config\Database::connect();
        $pembayaranModel = new \App\Models\PembayaranModel();
        $pesanan = $db->table('pesanan')->where('kode_invoice', $order_id)->get()->getRow();
        
        // Hentikan jika pesanan tidak ditemukan di database kita
        if (!$pesanan) {
            // Kita tetap beri respons 200 OK agar Midtrans berhenti mengirim notifikasi
            return $this->response->setStatusCode(200)->setJSON(['status' => 'error', 'message' => 'Order not found in our system.']);
        }
        
        $id_pesanan = $pesanan->id;
        $status_pembayaran_baru = 'pending';

        if ($transaction == 'capture' || $transaction == 'settlement') {
            if ($fraud == 'challenge') {
                $status_pembayaran_baru = 'challenge';
            } else if ($fraud == 'accept') {
                $status_pembayaran_baru = 'sukses';
            }
        } else if ($transaction == 'cancel' || $transaction == 'deny' || $transaction == 'expire') {
            $status_pembayaran_baru = 'gagal';
        } else if ($transaction == 'pending') {
            $status_pembayaran_baru = 'pending';
        }

        // Lakukan update hanya jika statusnya berubah
        $pembayaran_lama = $pembayaranModel->where('id_pesanan', $id_pesanan)->get()->getRow();
        if ($pembayaran_lama && $pembayaran_lama->status_pembayaran !== $status_pembayaran_baru) {
             $pembayaranModel->where('id_pesanan', $id_pesanan)->set([
                'status_pembayaran' => $status_pembayaran_baru,
                'metode_pembayaran' => 'midtrans',
                'midtrans_order_id' => $notif->transaction_id ?? null,
                'waktu_pembayaran' => $notif->transaction_time ?? null
            ])->update();
        }
        
        // Beri tahu Midtrans bahwa notifikasi sudah berhasil diproses
        return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Notification successfully processed.']);
    }
}