<?php 
namespace App\Models;
use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_user', 'kode_invoice', 'total_berat', 'total_harga', 
        'metode_pengiriman', 'status_pesanan', 'alamat_pengiriman', 
        'catatan_pelanggan', 'tanggal_pesan', 'tanggal_selesai',
        'foto_kondisi_barang'
    ];
    protected $useTimestamps = false; 
}