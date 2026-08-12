-- app/Database/sql_manual/manajemen_pajak.sql

CREATE TABLE IF NOT EXISTS `tarif_pajak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `jenis_pajak` VARCHAR(50) NOT NULL,
  `nama_tarif` VARCHAR(100) NOT NULL,
  `tarif_persen` DECIMAL(5,2) DEFAULT 0.00,
  `dasar_hukum` VARCHAR(255) DEFAULT NULL,
  `berlaku_mulai` DATE DEFAULT NULL,
  `berlaku_sampai` DATE DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `keterangan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `faktur_pajak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_faktur` VARCHAR(50) NOT NULL,
  `tanggal_faktur` DATE DEFAULT NULL,
  `jenis_faktur` ENUM('Masukan','Keluaran') DEFAULT 'Masukan',
  `invoice_id` INT DEFAULT NULL,
  `pembelian_id` INT DEFAULT NULL,
  `npwp_pengusaha` VARCHAR(30) DEFAULT NULL,
  `nama_pengusaha` VARCHAR(150) DEFAULT NULL,
  `alamat_pengusaha` TEXT DEFAULT NULL,
  `nilai_transaksi` DECIMAL(15,2) DEFAULT 0.00,
  `nilai_ppn` DECIMAL(15,2) DEFAULT 0.00,
  `tarif_ppn` DECIMAL(5,2) DEFAULT 11.00,
  `status_approval` ENUM('Draft','Pending','Approved','Rejected','Disetujui','Ditolak','Dibatalkan') DEFAULT 'Draft',
  `status_lapor` ENUM('Belum','Sudah','Belum Dilaporkan','Sudah Dilaporkan') DEFAULT 'Belum',
  `masa_pajak` INT DEFAULT NULL,
  `tahun_pajak` INT DEFAULT NULL,
  `file_faktur` VARCHAR(255) DEFAULT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ppn_masukan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `faktur_id` INT DEFAULT NULL,
  `tanggal_pembelian` DATE DEFAULT NULL,
  `supplier` VARCHAR(150) DEFAULT NULL,
  `npwp_supplier` VARCHAR(30) DEFAULT NULL,
  `nomor_invoice_supplier` VARCHAR(50) DEFAULT NULL,
  `nilai_dpp` DECIMAL(15,2) DEFAULT 0.00,
  `nilai_ppn` DECIMAL(15,2) DEFAULT 0.00,
  `masa_pajak` INT DEFAULT NULL,
  `tahun_pajak` INT DEFAULT NULL,
  `status_kredit` ENUM('Belum','Sudah','Tidak Dapat') DEFAULT 'Belum',
  `bulan_dikreditkan` INT DEFAULT NULL,
  `tahun_dikreditkan` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ppn_keluaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `faktur_id` INT DEFAULT NULL,
  `tanggal_penjualan` DATE DEFAULT NULL,
  `customer` VARCHAR(150) DEFAULT NULL,
  `npwp_customer` VARCHAR(30) DEFAULT NULL,
  `nomor_invoice` VARCHAR(50) DEFAULT NULL,
  `nilai_dpp` DECIMAL(15,2) DEFAULT 0.00,
  `nilai_ppn` DECIMAL(15,2) DEFAULT 0.00,
  `masa_pajak` INT DEFAULT NULL,
  `tahun_pajak` INT DEFAULT NULL,
  `status_setor` ENUM('Belum','Sudah') DEFAULT 'Belum',
  `tanggal_setor` DATE DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pph_badan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tahun_pajak` INT NOT NULL,
  `penghasilan_bruto` DECIMAL(15,2) DEFAULT 0.00,
  `biaya_fiskal` DECIMAL(15,2) DEFAULT 0.00,
  `penghasilan_neto_fiskal` DECIMAL(15,2) DEFAULT 0.00,
  `kompensasi_kerugian` DECIMAL(15,2) DEFAULT 0.00,
  `pkp` DECIMAL(15,2) DEFAULT 0.00,
  `tarif_persen` DECIMAL(5,2) DEFAULT 22.00,
  `pph_terutang` DECIMAL(15,2) DEFAULT 0.00,
  `kredit_pajak` DECIMAL(15,2) DEFAULT 0.00,
  `pph_kurang_bayar` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('Perhitungan','Draft','Posted','Selesai') DEFAULT 'Draft',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setoran_pajak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `jenis_pajak` VARCHAR(50) NOT NULL,
  `kode_akun_pajak` VARCHAR(50) DEFAULT NULL,
  `kode_jenis_setoran` VARCHAR(50) DEFAULT NULL,
  `masa_pajak` INT DEFAULT NULL,
  `tahun_pajak` INT DEFAULT NULL,
  `tanggal_bayar` DATE DEFAULT NULL,
  `ntpn` VARCHAR(50) DEFAULT NULL,
  `jumlah_bayar` DECIMAL(15,2) DEFAULT 0.00,
  `bukti_bayar` VARCHAR(255) DEFAULT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `arsip_pajak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_dokumen` VARCHAR(150) NOT NULL,
  `jenis_dokumen` VARCHAR(50) NOT NULL,
  `tahun_pajak` INT DEFAULT NULL,
  `masa_pajak` INT DEFAULT NULL,
  `file_path` VARCHAR(255) DEFAULT NULL,
  `kategori` VARCHAR(50) DEFAULT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
