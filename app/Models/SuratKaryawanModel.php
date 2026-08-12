<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratKaryawanModel extends Model
{
    protected $table      = 'surat_karyawan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
        try {
            if ($this->db && !$this->db->fieldExists('template_layout', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN template_layout VARCHAR(50) DEFAULT 'standard'");
            }
            if ($this->db && !$this->db->fieldExists('logo_position', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN logo_position VARCHAR(20) DEFAULT 'top_right'");
            }
            if ($this->db && !$this->db->fieldExists('address_position', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN address_position VARCHAR(20) DEFAULT 'top_left'");
            }
            if ($this->db && !$this->db->fieldExists('accent_style', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN accent_style VARCHAR(50) DEFAULT 'line'");
            }
            if ($this->db && !$this->db->fieldExists('paper_size', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN paper_size VARCHAR(20) DEFAULT 'A4'");
            }
            if ($this->db && !$this->db->fieldExists('signature_layout', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN signature_layout VARCHAR(20) DEFAULT '1_pihak'");
            }
            if ($this->db && !$this->db->fieldExists('signature_data', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN signature_data TEXT DEFAULT NULL");
            }
            if ($this->db && !$this->db->fieldExists('html_full', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN html_full LONGTEXT DEFAULT NULL");
            }
            if ($this->db && $this->db->fieldExists('karyawan_id', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} MODIFY karyawan_id INT UNSIGNED NULL");
            }
        } catch (\Throwable $e) {
            // Ignore if column already exists or DB error handled
        }
    }

    protected $allowedFields = [
        'nomor_surat', 'jenis_surat', 'karyawan_id', 'tanggal_surat',
        'perihal', 'isi_surat', 'html_full', 'catatan', 'dibuat_oleh', 'status',
        'template_layout', 'logo_position', 'address_position', 'accent_style', 'paper_size', 'signature_layout', 'signature_data'
    ];

    public $jenisSurat = [
        'Surat Masuk',
        'Surat Keluar',
        'Surat Jalan (Pengantar Barang)',
        'Surat Penawaran Harga (Quotation)',
        'Surat Perintah Kerja (SPK)',
        'Berita Acara Serah Terima (BAST)',
        'Surat Perjanjian Kerja Sama (MOU)',
        'Surat Keterangan Domisili Perusahaan (SKDP)',
        'Surat Perintah Tugas (SPT)',
        'Surat Keputusan (SK Direksi)',
        'Surat Peringatan (SP1)',
        'Surat Peringatan (SP2)',
        'Surat Peringatan (SP3)',
        'Surat Keterangan Kerja (Paklaring)',
        'Surat Edaran / Memo Internal',
        'Kontrak Kerja Karyawan',
        'Surat Permintaan Dana / Kas Kecil (Requisition)',
        'Invois / Faktur Tagihan',
        'Lainnya',
    ];

    public $paperSizes = [
        'A4'     => 'A4 (210 x 297 mm)',
        'A3'     => 'A3 (297 x 420 mm)',
        'Letter' => 'Letter (216 x 279 mm)',
        'Legal'  => 'Legal (216 x 356 mm)',
        'Folio'  => 'F4 / Folio (215 x 330 mm)',
    ];

    public function getCompanyLogoBase64()
    {
        $possibleNames = ['logo.png', 'logo.jpg', 'logo.jpeg', 'logo.PNG', 'logo.JPG'];
        $possibleDirs  = [
            FCPATH . 'uploads/logo/',
            ROOTPATH . 'public/uploads/logo/',
            WRITEPATH . 'uploads/logo/',
        ];

        foreach ($possibleDirs as $dir) {
            foreach ($possibleNames as $file) {
                $fullPath = $dir . $file;
                if (file_exists($fullPath)) {
                    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
                    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
                }
            }
        }
        return '';
    }

    public function getAllWithKaryawan()
    {
        return $this->select('surat_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
                    ->join('karyawan', 'karyawan.id = surat_karyawan.karyawan_id', 'left')
                    ->orderBy('surat_karyawan.tanggal_surat', 'DESC')
                    ->findAll();
    }

    public function getDetailWithKaryawan($id)
    {
        return $this->select('surat_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan, karyawan.alamat, karyawan.tanggal_masuk, karyawan.status_karyawan')
                    ->join('karyawan', 'karyawan.id = surat_karyawan.karyawan_id', 'left')
                    ->where('surat_karyawan.id', $id)
                    ->first();
    }

    public function generateNomor($jenis)
    {
        $prefix = match(true) {
            str_contains($jenis, 'Kontrak')     => 'KTK',
            str_contains($jenis, 'SP1')         => 'SP1',
            str_contains($jenis, 'SP2')         => 'SP2',
            str_contains($jenis, 'SP3')         => 'SP3',
            str_contains($jenis, 'Keterangan')  => 'SKK',
            str_contains($jenis, 'Tugas')       => 'SKT',
            str_contains($jenis, 'Pernyataan')  => 'SPY',
            default                              => 'SRT',
        };
        $bulan = date('m');
        $tahun = date('Y');
        $urutan = $this->where('MONTH(tanggal_surat)', $bulan)
                       ->where('YEAR(tanggal_surat)', $tahun)
                       ->countAllResults() + 1;
        return sprintf('%s/%03d/CDW/%s/%s', $prefix, $urutan, $bulan, $tahun);
    }
}
