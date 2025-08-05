<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> | Laundry Kilat</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS BARU UNTUK NAVBAR -->
    <style>
        body { background-color: #f8f9fa; }
        .navbar {
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .navbar-transparent {
            background-color: transparent !important;
        }
        .navbar-solid {
            background-color: #0d6efd !important; /* Warna biru primary Bootstrap */
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        /* Penyesuaian agar konten tidak tertutup navbar saat di atas */
        .banner-padding {
            padding-top: 80px; /* Sesuaikan dengan tinggi navbar Anda */
        }
        .avatar {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #495057;
            font-weight: bold;
            border-radius: 50%;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- NAVBAR BARU YANG DINAMIS -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-transparent" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">LAUNDRY KILAT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavPelanggan">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavPelanggan">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if(session()->get('isLoggedIn') && session()->get('role') == 'pelanggan'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/pelanggan/dashboard">Riwayat Pesanan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pelanggan/pesanan/new">Buat Pesanan</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="avatar me-2">
                                    <?= strtoupper(substr(session()->get('nama_lengkap'), 0, 1)) ?>
                                </div>
                                <?= strtok(session()->get('nama_lengkap'), " ") ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt fa-fw me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light btn-sm px-3" href="/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>
    
    <!-- FOOTER SEDERHANA -->
    <footer class="py-4 bg-light mt-auto">
        <div class="container">
            <p class="m-0 text-center text-muted">Copyright © Laundry Kilat <?= date('Y') ?></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JAVASCRIPT BARU UNTUK EFEK SCROLL NAVBAR -->
    <script>
        $(document).ready(function() {
            // Cek posisi scroll saat halaman dimuat
            if ($(window).scrollTop() > 50) {
                $('#mainNavbar').removeClass('navbar-transparent').addClass('navbar-solid');
            }
            
            // Cek posisi scroll saat pengguna menggulir halaman
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('#mainNavbar').removeClass('navbar-transparent').addClass('navbar-solid');
                } else {
                    $('#mainNavbar').removeClass('navbar-solid').addClass('navbar-transparent');
                }
            });
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>