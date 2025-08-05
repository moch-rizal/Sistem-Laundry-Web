<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LayananModel;

class LayananController extends BaseController
{
    protected $layananModel;

    public function __construct()
    {
        $this->layananModel = new LayananModel();
    }

    // Menampilkan semua data layanan
    public function index()
    {
        $data = [
            'title' => 'Manajemen Layanan',
            'layanan' => $this->layananModel->findAll()
        ];
        return view('admin/layanan/index', $data);
    }

    // Menampilkan form tambah data
    public function new()
    {
        $data = [
            'title' => 'Tambah Layanan Baru'
        ];
        return view('admin/layanan/create', $data);
    }

    // Menyimpan data baru ke database
    public function create()
    {
        $this->layananModel->save([
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'tipe_layanan' => $this->request->getPost('tipe_layanan'),
            'harga' => $this->request->getPost('harga'),
            'estimasi_waktu' => $this->request->getPost('estimasi_waktu'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to('/admin/layanan')->with('success', 'Data layanan berhasil ditambahkan.');
    }

    // Menampilkan form edit data
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Layanan',
            'layanan' => $this->layananModel->find($id)
        ];
        return view('admin/layanan/edit', $data);
    }

    // Mengupdate data di database
    public function update($id)
    {
        $this->layananModel->update($id, [
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'tipe_layanan' => $this->request->getPost('tipe_layanan'),
            'harga' => $this->request->getPost('harga'),
            'estimasi_waktu' => $this->request->getPost('estimasi_waktu'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to('/admin/layanan')->with('success', 'Data layanan berhasil diubah.');
    }

    // Menghapus data
    public function delete($id)
    {
        $this->layananModel->delete($id);
        return redirect()->to('/admin/layanan')->with('success', 'Data layanan berhasil dihapus.');
    }
}