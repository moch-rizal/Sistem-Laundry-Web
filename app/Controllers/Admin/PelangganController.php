<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class PelangganController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Kita gunakan UserModel yang sudah ada
        $this->userModel = new UserModel();
        helper(['form']); // Load form helper
    }

    // Menampilkan semua data pelanggan
    public function index()
    {
        $data = [
            'title' => 'Manajemen Pelanggan',
            'pelanggan' => $this->userModel->where('role', 'pelanggan')->findAll()
        ];
        return view('admin/pelanggan/index', $data);
    }

    // Menampilkan form tambah pelanggan baru
    public function new()
    {
        $data = [
            'title' => 'Tambah Pelanggan Baru'
        ];
        return view('admin/pelanggan/create', $data);
    }

    // Menyimpan data pelanggan baru
    public function create()
    {
        // Aturan validasi
        $rules = [
            'nama_lengkap' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'no_telepon' => 'required|min_length[10]',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan data ke database
        $this->userModel->save([
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email' => $this->request->getPost('email'),
            'no_telepon' => $this->request->getPost('no_telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => 'pelanggan'
        ]);

        return redirect()->to('/admin/pelanggan')->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    // Menampilkan form edit pelanggan
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Pelanggan',
            'pelanggan' => $this->userModel->find($id)
        ];
        return view('admin/pelanggan/edit', $data);
    }

    // Mengupdate data pelanggan
    public function update($id)
    {
        // Aturan validasi untuk update
        $user = $this->userModel->find($id);
        $emailRule = 'required|valid_email';
        // Jika email tidak diganti, aturan is_unique tidak diperlukan
        if ($user['email'] !== $this->request->getPost('email')) {
            $emailRule .= '|is_unique[users.email]';
        }

        $rules = [
            'nama_lengkap' => 'required',
            'email' => $emailRule,
            'no_telepon' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', 'Terdapat kesalahan validasi.');
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email' => $this->request->getPost('email'),
            'no_telepon' => $this->request->getPost('no_telepon'),
            'alamat' => $this->request->getPost('alamat'),
        ];
        
        // Cek apakah admin mengisi password baru
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/admin/pelanggan')->with('success', 'Data pelanggan berhasil diubah.');
    }

    // Menghapus data pelanggan
    public function delete($id)
    {
        // TODO: Tambahkan logika untuk menangani pesanan yang terkait dengan pelanggan ini
        // Untuk sekarang, kita hapus langsung
        $this->userModel->delete($id);
        return redirect()->to('/admin/pelanggan')->with('success', 'Data pelanggan berhasil dihapus.');
    }
}