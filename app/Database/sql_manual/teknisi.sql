-- app/Database/sql_manual/teknisi.sql
-- Database DDL for Teknisi & Field Operations Module

CREATE TABLE IF NOT EXISTS `peralatan_dipinjam` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_peminjaman` VARCHAR(50) NOT NULL,
  `teknisi_id` INT NOT NULL,
  `nama_teknisi` VARCHAR(150) DEFAULT NULL,
  `nama_alat` VARCHAR(150) NOT NULL,
  `kode_alat` VARCHAR(50) DEFAULT NULL,
  `qty` INT DEFAULT 1,
  `tgl_pinjam` DATE NOT NULL,
  `tgl_kembali_rencana` DATE NOT NULL,
  `tgl_kembali_realisasi` DATE DEFAULT NULL,
  `kondisi_pinjam` ENUM('Baik','Rusak Ringan') DEFAULT 'Baik',
  `kondisi_kembali` ENUM('Baik','Rusak Ringan','Rusak Berat','Hilang') DEFAULT NULL,
  `status` ENUM('Dipinjam','Dikembalikan','Terlambat') DEFAULT 'Dipinjam',
  `proyek_id` INT DEFAULT NULL,
  `nama_proyek` VARCHAR(150) DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `perawatan_alat` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_perawatan` VARCHAR(50) NOT NULL,
  `nama_alat` VARCHAR(150) NOT NULL,
  `kode_alat` VARCHAR(50) DEFAULT NULL,
  `jenis_perawatan` ENUM('Rutin','Perbaikan','Kalibrasi') DEFAULT 'Rutin',
  `tgl_perawatan` DATE NOT NULL,
  `biaya` DECIMAL(15,2) DEFAULT 0.00,
  `penanggung_jawab` VARCHAR(150) DEFAULT NULL,
  `status` ENUM('Dijadwalkan','Proses','Selesai') DEFAULT 'Selesai',
  `catatan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `biaya_lapangan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_pengajuan` VARCHAR(50) NOT NULL,
  `teknisi_id` INT NOT NULL,
  `proyek_id` INT DEFAULT NULL,
  `nama_proyek` VARCHAR(150) DEFAULT NULL,
  `tgl_pengajuan` DATE NOT NULL,
  `kategori_biaya` ENUM('Transport','BBM','Makan','Parkir/Tol','Akomodasi','Material Darurat','Lainnya') DEFAULT 'Transport',
  `nominal` DECIMAL(15,2) NOT NULL,
  `bukti_foto` VARCHAR(255) DEFAULT NULL,
  `keterangan` TEXT NOT NULL,
  `status` ENUM('Pending','Disetujui','Ditolak','Dicairkan') DEFAULT 'Pending',
  `disetujui_oleh` INT DEFAULT NULL,
  `catatan_approval` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `keluhan_karyawan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_keluhan` VARCHAR(50) NOT NULL,
  `karyawan_id` INT NOT NULL,
  `nama_karyawan` VARCHAR(150) DEFAULT NULL,
  `kategori` ENUM('Fasilitas/Alat Kerja','Kendala Lapangan','K3/Keselamatan','Administrasi','Lainnya') DEFAULT 'Kendala Lapangan',
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `tgl_keluhan` DATE NOT NULL,
  `lampiran_foto` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Baru','Diproses','Selesai') DEFAULT 'Baru',
  `tanggapan_manajemen` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
