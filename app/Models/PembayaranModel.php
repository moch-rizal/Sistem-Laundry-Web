<?php 
namespace App\Models;
use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'id_pesanan', 
        'metode_pembayaran', 
        'status_pembayaran', 
        'midtrans_order_id', 
        'waktu_pembayaran'
    ];
    
    protected $useTimestamps = false; 
}