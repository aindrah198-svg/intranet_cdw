CREATE TABLE IF NOT EXISTS `dokumen_penting` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul_dokumen` VARCHAR(255) NOT NULL,
  `nomor_dokumen` VARCHAR(100) DEFAULT NULL,
  `kategori` VARCHAR(100) DEFAULT 'Legalitas',
  `tanggal_terbit` DATE DEFAULT NULL,
  `tanggal_kadaluarsa` DATE DEFAULT NULL,
  `file_path` VARCHAR(255) DEFAULT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dokumen_sertifikat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_sertifikat` VARCHAR(255) NOT NULL,
  `penerbit` VARCHAR(255) NOT NULL,
  `nomor_sertifikat` VARCHAR(100) DEFAULT NULL,
  `karyawan_id` INT UNSIGNED DEFAULT NULL,
  `tanggal_perolehan` DATE DEFAULT NULL,
  `masa_berlaku` DATE DEFAULT NULL,
  `file_path` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('aktif','kadaluarsa','proses_perpanjangan') NOT NULL DEFAULT 'aktif',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `kontak_project` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) DEFAULT NULL,
  `nama_kontak` VARCHAR(255) NOT NULL,
  `perusahaan_klien` VARCHAR(255) DEFAULT NULL,
  `jabatan` VARCHAR(100) DEFAULT NULL,
  `telepon` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
