<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * API untuk Registrasi Pelanggan Baru
     */
    public function register()
    {
        // Aturan validasi sama seperti di web
        $rules = [
            'nama_lengkap' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'no_telepon' => 'required|min_length[10]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kirim respons error
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'messages' => $this->validator->getErrors()
            ]);
        }

        // Simpan data ke database
        $this->userModel->save([
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'email' => $this->request->getVar('email'),
            'no_telepon' => $this->request->getVar('no_telepon'),
            'alamat' => $this->request->getVar('alamat'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'role' => 'pelanggan'
        ]);

        // Kirim respons sukses
        return $this->response->setStatusCode(201)->setJSON([
            'status' => 'success',
            'message' => 'Registrasi berhasil!'
        ]);
    }
    
    /**
     * API untuk Login Pelanggan
     */
    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('email', $email)->first();

        // Cek jika user ada dan password benar
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Email atau password salah.'
            ]);
        }
        
        // Buat JWT Token
        $key = $_ENV['JWT_SECRET_KEY'];
        $iat = time(); // Waktu saat token dibuat
        $exp = $iat + (60 * 60 * 24 * 7); // Kedaluwarsa dalam 7 hari

        $payload = [
            "iat" => $iat,
            "exp" => $exp,
            "uid" => $user['id'],
            "email" => $user['email'],
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        // Kirim respons sukses beserta token
        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'nama_lengkap' => $user['nama_lengkap'],
                'email' => $user['email'],
                'no_telepon' => $user['no_telepon']
            ]
        ]);
    }
}