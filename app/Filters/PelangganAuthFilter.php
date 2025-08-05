<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PelangganAuthFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'pelanggan') {
            return redirect()->to('/login')->with('error', 'Anda harus login sebagai pelanggan.');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}