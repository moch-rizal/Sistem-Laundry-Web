/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 8.0.30 : Database - db_sistem_laundry
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_sistem_laundry` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `db_sistem_laundry`;

/*Table structure for table `detail_pesanan` */

DROP TABLE IF EXISTS `detail_pesanan`;

CREATE TABLE `detail_pesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `id_layanan` int NOT NULL,
  `jumlah_item` int DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_pesanan` (`id_pesanan`),
  KEY `id_layanan` (`id_layanan`),
  CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `detail_pesanan` */

insert  into `detail_pesanan`(`id`,`id_pesanan`,`id_layanan`,`jumlah_item`,`subtotal`) values 
(1,1,1,NULL,13500.00),
(2,2,2,1,20000.00),
(3,3,1,NULL,0.00),
(4,4,1,NULL,0.00),
(7,8,1,NULL,0.00),
(8,9,2,1,20000.00),
(9,10,1,NULL,0.00),
(12,12,1,NULL,0.00),
(13,12,2,1,20000.00),
(14,12,3,2,40000.00),
(15,13,1,NULL,0.00),
(16,13,2,1,20000.00),
(17,13,3,2,40000.00),
(18,14,1,NULL,0.00),
(19,14,2,1,20000.00),
(20,14,3,2,40000.00),
(24,17,1,NULL,0.00),
(25,17,2,2,40000.00),
(26,18,2,2,40000.00),
(27,18,6,2,80000.00),
(28,18,3,1,20000.00),
(29,18,5,1,40000.00),
(30,19,1,NULL,0.00),
(31,20,2,1,20000.00),
(32,20,3,1,20000.00),
(33,20,5,1,40000.00),
(34,20,6,1,40000.00),
(35,21,1,NULL,0.00),
(36,22,4,NULL,0.00),
(37,23,1,NULL,0.00),
(38,24,1,NULL,0.00),
(39,25,4,NULL,0.00);

/*Table structure for table `layanan` */

DROP TABLE IF EXISTS `layanan`;

CREATE TABLE `layanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_layanan` varchar(255) NOT NULL,
  `tipe_layanan` enum('kiloan','satuan') NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text,
  `estimasi_waktu` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `layanan` */

insert  into `layanan`(`id`,`nama_layanan`,`tipe_layanan`,`harga`,`deskripsi`,`estimasi_waktu`) values 
(1,'Cuci Kering Setrika Reguler','kiloan',9000.00,'','2 hari'),
(2,'Cuci Selimut Reguler','satuan',20000.00,'','3 hari'),
(3,'Cuci Boneka Besar Reguler','satuan',20000.00,'','3 hari'),
(4,'Cuci Kering Setrika Ekspress','kiloan',16000.00,'','1 hari'),
(5,'Cuci Selimut Ekspress','satuan',40000.00,'','2 hari'),
(6,'Cuci Boneka Besar Ekspress','satuan',40000.00,'','2 hari');

/*Table structure for table `pembayaran` */

DROP TABLE IF EXISTS `pembayaran`;

CREATE TABLE `pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `metode_pembayaran` enum('cod','midtrans') NOT NULL,
  `status_pembayaran` enum('pending','sukses','gagal') NOT NULL DEFAULT 'pending',
  `midtrans_order_id` varchar(255) DEFAULT NULL,
  `waktu_pembayaran` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_pesanan` (`id_pesanan`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `pembayaran` */

insert  into `pembayaran`(`id`,`id_pesanan`,`metode_pembayaran`,`status_pembayaran`,`midtrans_order_id`,`waktu_pembayaran`) values 
(3,8,'midtrans','sukses','4726f3f4-7813-4ed9-b543-209b1b49dd43','2025-07-29 19:40:26'),
(4,9,'cod','sukses',NULL,'2025-07-29 15:38:02'),
(5,10,'cod','pending',NULL,NULL),
(6,12,'cod','sukses',NULL,'2025-07-30 09:37:16'),
(7,13,'cod','pending',NULL,NULL),
(8,14,'cod','pending',NULL,NULL),
(10,16,'cod','pending',NULL,NULL),
(11,17,'cod','pending',NULL,NULL),
(12,18,'cod','pending',NULL,NULL),
(13,19,'cod','pending',NULL,NULL),
(14,20,'cod','pending',NULL,NULL),
(15,21,'cod','pending',NULL,NULL),
(16,22,'cod','pending',NULL,NULL),
(17,23,'cod','pending',NULL,NULL),
(18,24,'cod','pending',NULL,NULL),
(19,25,'cod','pending',NULL,NULL);

/*Table structure for table `pesanan` */

DROP TABLE IF EXISTS `pesanan`;

CREATE TABLE `pesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `kode_invoice` varchar(50) NOT NULL,
  `total_berat` decimal(5,2) DEFAULT '0.00',
  `total_harga` decimal(10,2) DEFAULT '0.00',
  `metode_pengiriman` enum('antar_jemput','datang_langsung') NOT NULL,
  `status_pesanan` enum('Menunggu Penjemputan','Diproses','Selesai Dicuci','Siap Diambil','Sedang Diantar','Selesai','Dibatalkan') NOT NULL,
  `alamat_pengiriman` text,
  `catatan_pelanggan` text,
  `foto_kondisi_barang` varchar(255) DEFAULT NULL,
  `tanggal_pesan` datetime DEFAULT CURRENT_TIMESTAMP,
  `tanggal_selesai` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_invoice` (`kode_invoice`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `pesanan` */

insert  into `pesanan`(`id`,`id_user`,`kode_invoice`,`total_berat`,`total_harga`,`metode_pengiriman`,`status_pesanan`,`alamat_pengiriman`,`catatan_pelanggan`,`foto_kondisi_barang`,`tanggal_pesan`,`tanggal_selesai`) values 
(1,2,'LNDRY-20250728-Y50D',4.00,36000.00,'datang_langsung','Diproses',NULL,'',NULL,'2025-07-28 17:43:57',NULL),
(2,2,'LNDRY-20250728-KZOV',0.00,20000.00,'antar_jemput','Selesai','','',NULL,'2025-07-28 17:50:10','2025-07-28 14:44:37'),
(3,2,'LNDRY-20250728-CY3N',4.50,40500.00,'datang_langsung','Diproses',NULL,'',NULL,'2025-07-28 18:15:03',NULL),
(4,3,'LNDRY-20250728-F8UG',0.00,0.00,'antar_jemput','Menunggu Penjemputan','','',NULL,'2025-07-29 05:57:40',NULL),
(8,2,'LNDRY-20250729-JR0T',9.00,81000.00,'antar_jemput','Diproses','','',NULL,'2025-07-29 19:39:39',NULL),
(9,2,'LNDRY-20250729-QHXA',0.00,20000.00,'datang_langsung','Selesai',NULL,'',NULL,'2025-07-29 22:35:06','2025-07-30 13:33:30'),
(10,2,'LNDRY-20250729-D2BS',1.50,13500.00,'datang_langsung','Selesai Dicuci',NULL,'',NULL,'2025-07-29 22:38:57',NULL),
(12,5,'LNDRY-20250730-OUAK',0.00,60000.00,'antar_jemput','Menunggu Penjemputan','Jalan Melati No. 12, Bandung','Tolong jangan pakai pemutih.',NULL,'2025-07-30 14:53:02',NULL),
(13,5,'LNDRY-20250731-EW60',0.00,60000.00,'antar_jemput','Menunggu Penjemputan','Jalan Melati No. 12, Bandung','Tolong jangan pakai pemutih.',NULL,'2025-07-31 09:53:19',NULL),
(14,6,'LNDRY-20250731-INO4',0.00,60000.00,'antar_jemput','Menunggu Penjemputan','Jalan Melati No. 12, Bandung','Tolong jangan pakai pemutih.','1753949636_0dd615156417cacc561b.png','2025-07-31 09:54:55',NULL),
(16,2,'LNDRY-20250801-XGAI',0.00,0.00,'antar_jemput','Menunggu Penjemputan','Jl. Pengujian API No. 123, Postman City','Tes dari Postman',NULL,'2025-08-01 11:12:37',NULL),
(17,2,'LNDRY-20250801-HSLV',0.00,40000.00,'antar_jemput','Menunggu Penjemputan','Jl. Pengujian API No. 321, Postman City','Tes dari Postman',NULL,'2025-08-01 11:17:47',NULL),
(18,2,'LNDRY-20250801-6HEK',0.00,180000.00,'antar_jemput','Menunggu Penjemputan','jalan kartanegara no 12','',NULL,'2025-08-01 15:56:54',NULL),
(19,2,'LNDRY-20250801-ETV1',0.00,0.00,'datang_langsung','Menunggu Penjemputan','','',NULL,'2025-08-01 15:59:15',NULL),
(20,2,'LNDRY-20250801-O46S',0.00,120000.00,'datang_langsung','Dibatalkan','','',NULL,'2025-08-01 16:01:12',NULL),
(21,2,'LNDRY-20250801-X2KE',0.00,0.00,'datang_langsung','Menunggu Penjemputan','','',NULL,'2025-08-01 16:02:38',NULL),
(22,2,'LNDRY-20250801-MHY8',0.00,0.00,'datang_langsung','Menunggu Penjemputan','','',NULL,'2025-08-01 16:04:06',NULL),
(23,2,'LNDRY-20250801-VTRW',0.00,0.00,'datang_langsung','Diproses',NULL,'',NULL,'2025-08-01 16:04:29',NULL),
(24,2,'LNDRY-20250801-RDOQ',0.00,0.00,'antar_jemput','Menunggu Penjemputan','jalan hahahaha no 333','',NULL,'2025-08-01 16:06:59',NULL),
(25,2,'LNDRY-20250801-5258',0.00,0.00,'antar_jemput','Menunggu Penjemputan','jalan','',NULL,'2025-08-01 19:18:19',NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text,
  `role` enum('admin','pelanggan') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`nama_lengkap`,`email`,`password`,`no_telepon`,`alamat`,`role`,`created_at`) values 
(1,'Admin Utama','admin@laundry.com','$2a$12$z.QEvQVhSqlzv1b34s333eGfhVX01WfLawF7MnPBU3V8xjjx.M7ZG',NULL,NULL,'admin','2025-07-27 20:40:10'),
(2,'steve','steve@gmail.com','$2a$12$.bIOBZgd.z8wLeL7PXsIROfxLjVOTzpeJaHBe56c9io3mp73U.hGe','081234567890',NULL,'pelanggan','2025-07-27 14:49:19'),
(3,'robert','robert82@gmail.com','$2y$10$DUihr0Ka5WW5QeHu1q6rI.LbA5Wp/Uk1v/qkYStcRFYqnBrjALwEO',NULL,NULL,'pelanggan','2025-07-28 22:56:23'),
(4,'bondan','bondan@gmail.com','$2y$10$dqf9InAPVbAtqMkdbhEPWurDdAo09/8gVTx9sl4FAh1wZnRncU/Em','082131213212',NULL,'pelanggan','2025-07-30 01:32:50'),
(5,'sam','sam@gmail.com','$2y$10$L1Mvyx42P7f08UsdpZAFnui.caBIBeJWG35ZHo9eDCh80wDKTtIeW','08121321342',NULL,'pelanggan','2025-07-30 01:41:00'),
(6,'Budi','budi.testing@example.com','$2y$10$9PxieEOC1NkyZ0uvQPb/vuihr8KJI8TCuHhi/XA1KrCvd1aHcXkZa','08121321342',NULL,'pelanggan','2025-07-31 01:02:31'),
(7,'andi chou','andi@gmail.com','$2y$10$AK/5GJwGEnVnPp7BwvRGXOhSCBywJQKtPa7OoTWs3DLOLqYmRwkS2','018291728167',NULL,'pelanggan','2025-07-31 23:43:18');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
