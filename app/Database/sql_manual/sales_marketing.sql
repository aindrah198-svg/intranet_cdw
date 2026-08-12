-- app/Database/sql_manual/sales_marketing.sql
-- Database DDL for Sales & Marketing Module

CREATE TABLE IF NOT EXISTS `sales_leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_lead` VARCHAR(50) NOT NULL,
  `nama_lead` VARCHAR(150) NOT NULL,
  `perusahaan` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `telepon` VARCHAR(30) DEFAULT NULL,
  `sumber_lead` ENUM('Website','Referral','Social Media','Cold Call','Pameran','Lainnya') DEFAULT 'Website',
  `nilai_potensi` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('Baru','Follow Up','Negosiasi','Closing','Hilang') DEFAULT 'Baru',
  `tgl_follow_up` DATE DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales_quotation` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_quotation` VARCHAR(50) NOT NULL,
  `lead_id` INT DEFAULT NULL,
  `klien_id` INT DEFAULT NULL,
  `nama_klien` VARCHAR(150) NOT NULL,
  `perusahaan` VARCHAR(150) DEFAULT NULL,
  `tanggal_quotation` DATE DEFAULT NULL,
  `berlaku_hingga` DATE DEFAULT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT 0.00,
  `diskon` DECIMAL(15,2) DEFAULT 0.00,
  `ppn` DECIMAL(15,2) DEFAULT 0.00,
  `total` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('Draft','Sent','Approved','Rejected','Revised') DEFAULT 'Draft',
  `versi` INT DEFAULT 1,
  `catatan` TEXT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales_quotation_item` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `quotation_id` INT NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `qty` INT DEFAULT 1,
  `harga_satuan` DECIMAL(15,2) DEFAULT 0.00,
  `total_harga` DECIMAL(15,2) DEFAULT 0.00,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales_deal` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_deal` VARCHAR(50) NOT NULL,
  `lead_id` INT DEFAULT NULL,
  `quotation_id` INT DEFAULT NULL,
  `klien_id` INT DEFAULT NULL,
  `nama_deal` VARCHAR(150) NOT NULL,
  `perusahaan` VARCHAR(150) DEFAULT NULL,
  `nilai_deal` DECIMAL(15,2) DEFAULT 0.00,
  `tanggal_closing` DATE DEFAULT NULL,
  `status_invoice` ENUM('Belum','Draft','Issued') DEFAULT 'Belum',
  `status_project` ENUM('Belum','Draft','Created') DEFAULT 'Belum',
  `invoice_id` INT DEFAULT NULL,
  `project_id` INT DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales_target` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sales_id` INT DEFAULT NULL,
  `tahun` INT NOT NULL,
  `bulan` INT NOT NULL,
  `target_penjualan` DECIMAL(15,2) DEFAULT 0.00,
  `realisasi_penjualan` DECIMAL(15,2) DEFAULT 0.00,
  `target_leads` INT DEFAULT 0,
  `realisasi_leads` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales_klien` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_klien` VARCHAR(50) NOT NULL,
  `nama_klien` VARCHAR(150) NOT NULL,
  `perusahaan` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `telepon` VARCHAR(30) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `industri` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('Aktif','Prospek','Inaktif') DEFAULT 'Aktif',
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales_klien_interaksi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `klien_id` INT NOT NULL,
  `tanggal` DATETIME DEFAULT NULL,
  `jenis_interaksi` ENUM('Telepon','Email','Meeting','Presentasi','Lainnya') DEFAULT 'Telepon',
  `ringkasan` TEXT NOT NULL,
  `follow_up_note` TEXT DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
