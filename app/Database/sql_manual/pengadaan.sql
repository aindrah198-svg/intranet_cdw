CREATE TABLE IF NOT EXISTS `pengajuan_atk` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` INT UNSIGNED NOT NULL,
  `nama_barang` VARCHAR(255) NOT NULL,
  `jumlah` INT NOT NULL DEFAULT 1,
  `satuan` VARCHAR(50) DEFAULT 'Pcs',
  `alasan` TEXT DEFAULT NULL,
  `status` ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `komentar_direktur` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `stok_atk` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` VARCHAR(50) NOT NULL,
  `nama_barang` VARCHAR(255) NOT NULL,
  `kategori` VARCHAR(100) DEFAULT 'Umum',
  `stok` INT NOT NULL DEFAULT 0,
  `satuan` VARCHAR(50) DEFAULT 'Pcs',
  `lokasi` VARCHAR(100) DEFAULT 'Gudang Utama',
  `status_stok` ENUM('aman','menipis','habis') NOT NULL DEFAULT 'aman',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pengadaan_aset` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_pengadaan` VARCHAR(50) NOT NULL,
  `nama_aset` VARCHAR(255) NOT NULL,
  `kategori` VARCHAR(100) DEFAULT 'Elektronik',
  `estimasi_harga` DECIMAL(15,2) DEFAULT 0,
  `jumlah` INT NOT NULL DEFAULT 1,
  `alasan_pengadaan` TEXT DEFAULT NULL,
  `status` ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `komentar_direktur` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `laporan_kerusakan` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_laporan` VARCHAR(50) NOT NULL,
  `nama_alat` VARCHAR(255) NOT NULL,
  `lokasi_alat` VARCHAR(100) DEFAULT NULL,
  `pelapor_id` INT UNSIGNED NOT NULL,
  `deskripsi_kerusakan` TEXT NOT NULL,
  `tingkat_kerusakan` ENUM('ringan','sedang','berat') NOT NULL DEFAULT 'ringan',
  `status_tindakan` ENUM('dilaporkan','proses_perbaikan','selesai','diganti') NOT NULL DEFAULT 'dilaporkan',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `monitoring_gudang` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` VARCHAR(50) NOT NULL,
  `nama_barang` VARCHAR(255) NOT NULL,
  `kategori` VARCHAR(100) DEFAULT 'Material',
  `stok_tersedia` INT NOT NULL DEFAULT 0,
  `satuan` VARCHAR(50) DEFAULT 'Unit',
  `lokasi_rak` VARCHAR(100) DEFAULT 'Rak A-1',
  `status` ENUM('tersedia','kosong','indent') NOT NULL DEFAULT 'tersedia',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
