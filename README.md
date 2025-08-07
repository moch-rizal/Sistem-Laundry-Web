# Sistem Informasi Laundry Berbasis Web dengan CodeIgniter 4

![Versi PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)
![Versi CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.5.1-orange.svg)
![Database](https://img.shields.io/badge/Database-MySQL-lightgrey.svg)
![Template](https://img.shields.io/badge/UI-Bootstrap%205-purple.svg)

Ini adalah aplikasi web Sistem Informasi Laundry lengkap yang dibangun menggunakan framework PHP CodeIgniter 4. Aplikasi ini dirancang untuk memfasilitasi manajemen operasional bisnis laundry, mulai dari pemesanan oleh pelanggan hingga pengelolaan transaksi oleh admin.

Proyek ini dibuat sebagai bagian dari Ujian Akhir Semester.

## ✨ Fitur Utama

Sistem ini memiliki dua peran utama dengan fitur yang komprehensif:

### 👤 Pelanggan
- **Registrasi & Login:** Sistem otentikasi yang aman untuk pelanggan.
- **Dashboard Interaktif:** Melihat ringkasan pesanan aktif dengan status visual (berubah warna sesuai progres) dan riwayat semua transaksi.
- **Pemesanan Online:** Pelanggan dapat membuat pesanan dari rumah, memilih layanan kiloan atau satuan.
- **Layanan Ekspres:** Opsi untuk memilih layanan reguler atau ekspres.
- **Pembayaran Online:** Terintegrasi dengan Midtrans (Sandbox) untuk berbagai metode pembayaran.
- **Notifikasi Email:** Menerima notifikasi email otomatis setiap kali status pesanan diperbarui oleh admin.

### 💼 Admin
- **Panel Admin Profesional:** Menggunakan template SB Admin 2 yang modern dan responsif.
- **Dashboard Analitik:** Menampilkan statistik kunci seperti jumlah pelanggan, pesanan baru, dan total pendapatan.
- **Manajemen Pesanan (CRUD):**
  - Melihat semua pesanan masuk dari pelanggan.
  - Membuat pesanan untuk pelanggan yang datang langsung (walk-in).
  - Mengupdate rincian pesanan (berat, total harga).
  - Mengubah status progres laundry (Diproses, Selesai Dicuci, Siap Diambil, dll).
  - Mengkonfirmasi pembayaran tunai (COD).
  - Menghapus pesanan.
- **Manajemen Layanan (CRUD):** Mengelola daftar layanan yang ditawarkan (kiloan, satuan, ekspres) beserta harganya.
- **Manajemen Pelanggan (CRUD):** Mengelola data semua pelanggan yang terdaftar.
- **Laporan Transaksi:** Melihat dan memfilter laporan pendapatan berdasarkan rentang tanggal.

## 🛠️ Teknologi yang Digunakan
- **Backend:** PHP 8.1, CodeIgniter 4
- **Frontend:**
  - HTML5, CSS3, JavaScript (jQuery)
  - **Admin:** Template SB Admin 2 (Bootstrap 4)
  - **Pelanggan:** Bootstrap 5
- **Database:** MySQL / MariaDB
- **Dependency Manager:** Composer
- **Payment Gateway:** Midtrans (Sandbox)
- **Email:** SMTP via Gmail

## 🚀 Panduan Instalasi dan Menjalankan Proyek

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal Anda.

### 1. Prasyarat
- PHP 8.1 atau lebih baru
- Composer
- Server Database (MySQL/MariaDB)
- Akun Gmail (untuk pengiriman email notifikasi)
- Akun Sandbox Midtrans

### 2. Instalasi
1.  **Clone Repository**
    ```bash
    git clone https://github.com/moch-rizal/Sistem-Laundry-Web.git
    cd Sistem-Laundry-Web
    ```

2.  **Instal Dependensi PHP**
    Jalankan Composer untuk mengunduh semua library yang dibutuhkan (termasuk CodeIgniter).
    ```bash
    composer install
    ```

3.  **Konfigurasi Environment**
    Salin file `env` menjadi `.env` dan sesuaikan konfigurasinya.
    ```bash
    cp env .env
    ```
    Buka file `.env` dan atur variabel berikut:
    ```dotenv
    # Atur baseURL sesuai dengan server lokal Anda
    app.baseURL = 'http://localhost:8080/'

    # Konfigurasi Database
    database.default.hostname = localhost
    database.default.database = db_sistem_laundry
    database.default.username = root
    database.default.password =
    database.default.DBDriver = MySQLi

    # Konfigurasi Midtrans (Dapatkan dari Dashboard Sandbox Midtrans)
    MIDTRANS_SERVER_KEY = "GANTI_DENGAN_SERVER_KEY_ANDA"
    MIDTRANS_CLIENT_KEY = "GANTI_DENGAN_CLIENT_KEY_ANDA"
    MIDTRANS_IS_PRODUCTION = "false"

    # Konfigurasi Email (Gunakan App Password dari Akun Google)
    email.fromEmail = "email.anda@gmail.com"
    email.fromName  = "Laundry Kilat" // dari siapa
    email.SMTPHost  = "smtp.gmail.com"
    email.SMTPUser  = "email.anda@gmail.com"
    email.SMTPPass  = "GANTI_DENGAN_APP_PASSWORD_GMAIL"
    email.SMTPPort  = 465
    email.SMTPCrypto= "ssl"
    
```
Langkah-langkah menambahkan payment gateway dan notifikasi email

Payment Gateway:

1. buat akun di midtrans
https://dashboard.midtrans.com/register
2. ubah environment ke sandbox
3. ke bagian setting -> access key 
4. Salin kedua kunci penting ini:
    Client Key (diawali dengan SB-Mid-client-...)
    Server Key (diawali dengan SB-Mid-server-...)
3. ke bagian settings -> payment -> notification URL
4. isi dengan URL pada Payment notification URL yang didapat dari ngrok

5. Download ngrok dari ngrok.com.
6. Jalankan di terminal: ngrok http 8080.
7. Salin URL HTTPS di bagian Forwarding yang diberikan (misal: https://xxxx.ngrok-free.app)


Notifikasi Email
Buat "App Password" di Akun Google Anda:

    Buka Pengaturan Akun Google Anda: myaccount.google.com.

    Pergi ke menu "Keamanan" (Security).

    Pastikan "Verifikasi 2 Langkah" (2-Step Verification) SUDAH AKTIF. Ini wajib.

    Di halaman Keamanan, cari dan klik bagian "Sandi aplikasi" (App passwords).

    Di halaman Sandi Aplikasi:

        Klik "Pilih aplikasi" -> "Lainnya (Nama kustom)...".

        Beri nama (misal: Aplikasi Laundry CI4), lalu klik "Buat".

    Google akan menampilkan password 16 karakter di dalam kotak kuning. Salin password ini. Ini BUKAN password Gmail Anda, ini adalah password khusus untuk aplikasi.

B. Isi Konfigurasi di File .env:

    Buka file .env di root proyek CodeIgniter.

    Tambahkan atau perbarui blok konfigurasi email ini:
    Generated dotenv

      
# --- KONFIGURASI EMAIL ---
email.fromEmail = "email.anda@gmail.com"
email.fromName  = "Laundry Kilat"
email.SMTPHost  = "smtp.gmail.com"
email.SMTPUser  = "email.anda@gmail.com"
email.SMTPPass  = "PASTE_APP_PASSWORD_16_KARAKTER_DI_SINI"
email.SMTPPort  = 465
email.SMTPCrypto= "ssl"
email.protocol  = "smtp"
email.mailType  = "html"


    Ganti email.anda@gmail.com dengan alamat email Anda.

    Paste "App Password" yang baru saja Anda buat ke dalam email.SMTPPass. Pastikan diapit tanda kutip (") jika mengandung karakter khusus.
```


4.  **Setup Database**
    - Buat sebuah database baru di MySQL/MariaDB dengan nama `db_sistem_laundry`.
    - Impor file SQL yang berisi struktur tabel dan data awal .
      ```
      db_sistem_laundry.sql

### 3. Menjalankan Aplikasi
1.  **Jalankan Server Development CodeIgniter**
    Buka terminal di root proyek dan jalankan:
    ```bash
    php spark serve
    ```

2.  **Akses Aplikasi**
    Buka browser Anda dan kunjungi alamat `http://localhost:8080`.

### 4. Akun Demo
- **Admin:**
  - **Email:** `admin@laundry.com`
  - **Password:** `admin123`
- **Pelanggan:**
  - Silakan buat akun baru melalui halaman registrasi.

---

Terima kasih telah mencoba proyek ini!

Dibuat oleh **Moch. Arif Samsul Rizal**.






