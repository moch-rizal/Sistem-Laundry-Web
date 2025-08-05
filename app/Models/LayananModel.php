<?php 
namespace App\Models;
use CodeIgniter\Model;

class LayananModel extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_layanan', 'tipe_layanan', 'harga', 'deskripsi', 'estimasi_waktu'];
    protected $useTimestamps = false; 
}