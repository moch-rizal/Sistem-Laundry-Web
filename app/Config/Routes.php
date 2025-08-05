<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');

// Rute Otentikasi
$routes->get('/login', 'AuthController::login');
$routes->post('/prosesLogin', 'AuthController::prosesLogin');
$routes->get('/register', 'AuthController::register');
$routes->post('/prosesRegister', 'AuthController::prosesRegister');
$routes->get('/logout', 'AuthController::logout');

// Rute Admin
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
     // --- DASHBOARD ---
    $routes->get('dashboard', 'Admin\DashboardController::index');
    
    // --- MANAJEMEN DATA MASTER (CRUD) ---
    $routes->resource('layanan', ['controller' => 'Admin\LayananController']);
    $routes->resource('pelanggan', ['controller' => 'Admin\PelangganController']);

    // --- MANAJEMEN TRANSAKSI ---
    // Gunakan resource untuk semua rute CRUD standar pesanan
    $routes->resource('pesanan', ['controller' => 'Admin\PesananController']);
    // Tambahkan satu rute 'custom' untuk konfirmasi COD yang tidak termasuk dalam standar resource
    $routes->post('pesanan/konfirmasi_cod/(:num)', 'Admin\PesananController::konfirmasiCod/$1');

    // --- LAPORAN ---
    $routes->get('laporan', 'Admin\LaporanController::index');
});

// Rute Pelanggan
$routes->group('pelanggan', ['filter' => 'pelangganauth'], static function ($routes) {
    $routes->get('dashboard', 'Pelanggan\DashboardController::index');
    $routes->get('pesanan/new', 'Pelanggan\PesananController::new');
    $routes->post('pesanan', 'Pelanggan\PesananController::create');

    $routes->get('pesanan/bayar/(:num)', 'Pelanggan\PesananController::bayar/$1');

    $routes->get('pesanan/(:num)', 'Pelanggan\PesananController::show/$1');
});

$routes->post('/webhook/midtrans', 'WebhookController::midtrans');





















$routes->group('api', ['filter' => 'cors'], static function ($routes) {
    // Rute untuk Otentikasi
    $routes->post('register', 'Api\AuthController::register');
    $routes->post('login', 'Api\AuthController::login');

    // Rute untuk Layanan
    $routes->get('layanan', 'Api\LayananController::index');

     // Rute yang Memerlukan Otentikasi (Dilindungi oleh JWTAuthFilter)
    $routes->group('', ['filter' => 'jwtauth'], static function ($routes) {
        // $routes->get('pesanan', 'Api\PesananController::index');
        // $routes->post('pesanan', 'Api\PesananController::create');
        // Nanti kita bisa tambah rute lain di sini (detail, batal, dll)
         // CRUD untuk Pesanan
        $routes->get('pesanan', 'Api\PesananController::index');        // Get all
        $routes->get('pesanan/(:num)', 'Api\PesananController::show/$1'); // Get single
        $routes->post('pesanan', 'Api\PesananController::create');      // Create
        $routes->put('pesanan/(:num)', 'Api\PesananController::update/$1'); // Update (untuk batal)
        $routes->delete('pesanan/(:num)', 'Api\PesananController::delete/$1'); // Delete

        // RUTE BARU UNTUK UPLOAD FOTO
        $routes->post('pesanan/(:num)/upload-foto', 'Api\PesananController::uploadFoto/$1');
        });
});