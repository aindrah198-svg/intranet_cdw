-- app/Database/sql_manual/penggajian.sql

CREATE TABLE IF NOT EXISTS `penggajian_perhitungan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_perhitungan` VARCHAR(50) DEFAULT NULL,
  `karyawan_id` INT NOT NULL,
  `periode_bulan` INT NOT NULL,
  `periode_tahun` INT NOT NULL,
  `tanggal_perhitungan` DATE DEFAULT NULL,
  `gaji_pokok` DECIMAL(15,2) DEFAULT 0.00,
  `tunjangan_jabatan` DECIMAL(15,2) DEFAULT 0.00,
  `tunjangan_bpjs` DECIMAL(15,2) DEFAULT 0.00,
  `tunjangan_makan` DECIMAL(15,2) DEFAULT 0.00,
  `tunjangan_transport` DECIMAL(15,2) DEFAULT 0.00,
  `tunjangan_lainnya` DECIMAL(15,2) DEFAULT 0.00,
  `total_pendapatan` DECIMAL(15,2) DEFAULT 0.00,
  `potongan_bpjs_kes` DECIMAL(15,2) DEFAULT 0.00,
  `potongan_bpjs_tk` DECIMAL(15,2) DEFAULT 0.00,
  `potongan_pph21` DECIMAL(15,2) DEFAULT 0.00,
  `potongan_absensi` DECIMAL(15,2) DEFAULT 0.00,
  `potongan_kasbon` DECIMAL(15,2) DEFAULT 0.00,
  `potongan_lainnya` DECIMAL(15,2) DEFAULT 0.00,
  `total_potongan` DECIMAL(15,2) DEFAULT 0.00,
  `total_hari_kerja` INT DEFAULT 0,
  `total_hadir` INT DEFAULT 0,
  `total_izin` INT DEFAULT 0,
  `total_sakit` INT DEFAULT 0,
  `total_cuti` INT DEFAULT 0,
  `total_alpha` INT DEFAULT 0,
  `total_terlambat` INT DEFAULT 0,
  `jam_lembur` DECIMAL(8,2) DEFAULT 0.00,
  `upah_lembur` DECIMAL(15,2) DEFAULT 0.00,
  `gaji_bersih` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('Draft','Dihitung','Disetujui','Ditolak') DEFAULT 'Draft',
  `catatan` TEXT DEFAULT NULL,
  `disetujui_oleh` INT DEFAULT NULL,
  `disetujui_at` DATETIME DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `updated_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `penggajian_proses_pembayaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_pembayaran` VARCHAR(50) DEFAULT NULL,
  `periode_bulan` INT NOT NULL,
  `periode_tahun` INT NOT NULL,
  `tanggal_pembayaran` DATE DEFAULT NULL,
  `coa_bank_id` INT DEFAULT NULL,
  `total_karyawan` INT DEFAULT 0,
  `total_nominal` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('Draft','Diproses','Selesai','Dibatalkan') DEFAULT 'Draft',
  `catatan` TEXT DEFAULT NULL,
  `jurnal_id` INT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `updated_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `penggajian_komponen` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_komponen` VARCHAR(50) NOT NULL,
  `nama_komponen` VARCHAR(100) NOT NULL,
  `tipe` ENUM('Pendapatan','Potongan') NOT NULL,
  `nominal_default` DECIMAL(15,2) DEFAULT 0.00,
  `is_aktif` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `penggajian_detail_pembayaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pembayaran_id` INT NOT NULL,
  `perhitungan_id` INT NOT NULL,
  `karyawan_id` INT NOT NULL,
  `gaji_bersih` DECIMAL(15,2) DEFAULT 0.00,
  `status` VARCHAR(50) DEFAULT 'Paid',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
