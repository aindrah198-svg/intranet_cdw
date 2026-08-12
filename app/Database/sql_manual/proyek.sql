CREATE TABLE IF NOT EXISTS `proyek_timeline` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proyek_id` INT(11) NOT NULL,
  `nama_tugas` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `karyawan_id` INT UNSIGNED DEFAULT NULL COMMENT 'Assigned to',
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `status` ENUM('pending','on_progress','done') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`proyek_id`) REFERENCES `project`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `laporan_harian` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` INT UNSIGNED NOT NULL,
  `tanggal` DATE NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `lampiran` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('menunggu_review','direview','revisi','disetujui') NOT NULL DEFAULT 'menunggu_review',
  `komentar_direktur` TEXT DEFAULT NULL,
  `direview_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pencarian_barang` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `batas_waktu` DATE NOT NULL,
  `karyawan_id` INT UNSIGNED NOT NULL COMMENT 'Assigned to',
  `status` ENUM('baru','proses','selesai','batal') NOT NULL DEFAULT 'baru',
  `hasil_pencarian` TEXT DEFAULT NULL,
  `lampiran_hasil` VARCHAR(255) DEFAULT NULL,
  `dibuat_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
