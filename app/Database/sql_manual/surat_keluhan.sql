-- ================================================
-- SQL untuk membuat tabel Surat & Keluhan Karyawan
-- Jalankan di phpMyAdmin atau MySQL client
-- Database: cdwengin_intranet
-- ================================================

CREATE TABLE IF NOT EXISTS `surat_karyawan` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_surat` VARCHAR(50) DEFAULT NULL,
  `jenis_surat` ENUM('Kontrak Kerja','Surat Peringatan (SP1)','Surat Peringatan (SP2)','Surat Peringatan (SP3)','Surat Keterangan Kerja','Surat Tugas','Surat Pernyataan','Lainnya') NOT NULL,
  `karyawan_id` INT UNSIGNED NOT NULL,
  `tanggal_surat` DATE NOT NULL,
  `perihal` VARCHAR(255) NOT NULL,
  `isi_surat` TEXT DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `dibuat_oleh` INT UNSIGNED DEFAULT NULL,
  `status` ENUM('draft','diterbitkan','dibatalkan') NOT NULL DEFAULT 'draft',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `karyawan_id` (`karyawan_id`),
  KEY `jenis_surat` (`jenis_surat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `keluhan_karyawan` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` INT UNSIGNED NOT NULL,
  `tanggal` DATE NOT NULL,
  `kategori` ENUM('Lingkungan Kerja','Hubungan Rekan Kerja','Atasan/Manajemen','Gaji & Tunjangan','Fasilitas','Beban Kerja','Lainnya') NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `status` ENUM('baru','diproses','selesai','ditolak') NOT NULL DEFAULT 'baru',
  `tanggapan` TEXT DEFAULT NULL,
  `ditanggapi_oleh` INT UNSIGNED DEFAULT NULL,
  `tanggal_tanggapan` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `karyawan_id` (`karyawan_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
