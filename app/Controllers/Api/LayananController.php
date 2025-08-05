<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LayananModel;

class LayananController extends BaseController
{
    /**
     * API untuk mendapatkan semua data layanan
     */
    public function index()
    {
        $model = new LayananModel();
        $data = $model->findAll();
        
        // Gunakan $this->response->setJSON() untuk mengirim array mentah
        return $this->response->setJSON($data); 
    }
}