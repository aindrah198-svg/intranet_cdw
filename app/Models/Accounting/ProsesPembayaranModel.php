<?php
namespace App\Models\Accounting\Penggajian;

use CodeIgniter\Model;

class SlipGajiLaporanModel extends Model
{
    protected $table = 'penggajian_detail_pembayaran'; // Menggunakan tabel detail sebagai basis
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false; // Read-only model untuk laporan

    /**
     * Get data slip gaji untuk seorang karyawan
     */
    public function getSlipGaji($karyawanId, $periodeBulan, $periodeTahun)
    {
        $db = \Config\Database::connect();
        
        // Cari di detail pembayaran yang sudah selesai
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                d.*,
                p.nomor_proses,
                p.nama_proses,
                p.tanggal_pembayaran,
                p.metode_pembayaran,
                p.bank_pengirim,
                pg.nomor_perhitungan,
                pg.tanggal_perhitungan,
                pg.gaji_pokok,
                pg.tunjangan_jabatan,
                pg.tunjangan_bpjs,
                pg.tunjangan_makan,
                pg.tunjangan_transport,
                pg.tunjangan_lainnya,
                pg.potongan_bpjs_kes,
                pg.potongan_bpjs_tk,
                pg.potongan_pph21,
                pg.potongan_absensi,
                pg.potongan_kasbon,
                pg.potongan_lainnya,
                pg.total_hari_kerja,
                pg.total_hadir,
                pg.total_izin,
                pg.total_sakit,
                pg.total_cuti,
                pg.total_alpha,
                pg.total_terlambat,
                pg.jam_lembur,
                pg.upah_lembur,
                k.nik,
                k.nama_lengkap,
                k.nama_panggilan,
                k.jenis_kelamin,
                k.tempat_lahir,
                k.tanggal_lahir,
                k.alamat,
                k.jabatan,
                k.departemen,
                k.divisi,
                k.tanggal_masuk,
                k.no_npwp,
                k.no_bpjs_kes,
                k.no_bpjs_tk,
                k.bank,
                k.no_rekening,
                k.nama_rekening
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->join('karyawan k', 'k.id = d.karyawan_id')
            ->where('d.karyawan_id', $karyawanId)
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->where('d.status_pembayaran', 'Berhasil');
        
        $result = $builder->get()->getRowArray();
        
        if ($result) {
            return $this->formatSlipGaji($result);
        }
        
        // Jika tidak ditemukan di detail, coba cari di perhitungan (belum diproses)
        $builder2 = $db->table('penggajian_perhitungan pg')
            ->select('
                pg.*,
                k.nik,
                k.nama_lengkap,
                k.nama_panggilan,
                k.jenis_kelamin,
                k.tempat_lahir,
                k.tanggal_lahir,
                k.alamat,
                k.jabatan,
                k.departemen,
                k.divisi,
                k.tanggal_masuk,
                k.no_npwp,
                k.no_bpjs_kes,
                k.no_bpjs_tk,
                k.bank,
                k.no_rekening,
                k.nama_rekening
            ')
            ->join('karyawan k', 'k.id = pg.karyawan_id')
            ->where('pg.karyawan_id', $karyawanId)
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('pg.status', 'Disetujui');
        
        $result2 = $builder2->get()->getRowArray();
        
        if ($result2) {
            $result2['status_proses'] = 'Belum Diproses';
            return $this->formatSlipGaji($result2);
        }
        
        return null;
    }

    /**
     * Get slip gaji berdasarkan ID detail pembayaran
     */
    public function getSlipGajiById($detailId)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                d.*,
                p.nomor_proses,
                p.nama_proses,
                p.tanggal_pembayaran,
                p.metode_pembayaran,
                p.bank_pengirim,
                pg.nomor_perhitungan,
                pg.tanggal_perhitungan,
                pg.gaji_pokok,
                pg.tunjangan_jabatan,
                pg.tunjangan_bpjs,
                pg.tunjangan_makan,
                pg.tunjangan_transport,
                pg.tunjangan_lainnya,
                pg.potongan_bpjs_kes,
                pg.potongan_bpjs_tk,
                pg.potongan_pph21,
                pg.potongan_absensi,
                pg.potongan_kasbon,
                pg.potongan_lainnya,
                pg.total_hari_kerja,
                pg.total_hadir,
                pg.total_izin,
                pg.total_sakit,
                pg.total_cuti,
                pg.total_alpha,
                pg.total_terlambat,
                pg.jam_lembur,
                pg.upah_lembur,
                k.nik,
                k.nama_lengkap,
                k.nama_panggilan,
                k.jenis_kelamin,
                k.tempat_lahir,
                k.tanggal_lahir,
                k.alamat,
                k.jabatan,
                k.departemen,
                k.divisi,
                k.tanggal_masuk,
                k.no_npwp,
                k.no_bpjs_kes,
                k.no_bpjs_tk,
                k.bank,
                k.no_rekening,
                k.nama_rekening,
                u.username,
                u.email as user_email
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id', 'left')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->join('karyawan k', 'k.id = d.karyawan_id')
            ->join('users u', 'u.karyawan_id = k.id', 'left')
            ->where('d.id', $detailId);
        
        $result = $builder->get()->getRowArray();
        
        return $result ? $this->formatSlipGaji($result) : null;
    }

    /**
     * Get semua slip gaji untuk suatu proses
     */
    public function getSlipGajiByProses($prosesId)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                d.*,
                p.nomor_proses,
                p.nama_proses,
                p.tanggal_pembayaran,
                p.metode_pembayaran,
                p.bank_pengirim,
                pg.nomor_perhitungan,
                pg.tanggal_perhitungan,
                pg.gaji_pokok,
                pg.tunjangan_jabatan,
                pg.tunjangan_bpjs,
                pg.tunjangan_makan,
                pg.tunjangan_transport,
                pg.tunjangan_lainnya,
                pg.potongan_bpjs_kes,
                pg.potongan_bpjs_tk,
                pg.potongan_pph21,
                pg.potongan_absensi,
                pg.potongan_kasbon,
                pg.potongan_lainnya,
                pg.total_hari_kerja,
                pg.total_hadir,
                pg.total_izin,
                pg.total_sakit,
                pg.total_cuti,
                pg.total_alpha,
                pg.total_terlambat,
                pg.jam_lembur,
                pg.upah_lembur,
                k.nik,
                k.nama_lengkap,
                k.nama_panggilan,
                k.jabatan,
                k.departemen,
                k.divisi,
                k.tanggal_masuk,
                k.no_npwp,
                k.bank,
                k.no_rekening,
                k.nama_rekening
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->join('karyawan k', 'k.id = d.karyawan_id')
            ->where('d.proses_id', $prosesId)
            ->where('d.status_pembayaran', 'Berhasil')
            ->orderBy('k.nama_lengkap', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        foreach ($results as &$result) {
            $result = $this->formatSlipGaji($result);
        }
        
        return $results;
    }

    /**
     * Format data slip gaji
     */
    private function formatSlipGaji($data)
    {
        // Hitung ulang total jika perlu
        $data['total_pendapatan'] = ($data['gaji_pokok'] ?? 0) + 
                                    ($data['tunjangan_jabatan'] ?? 0) + 
                                    ($data['tunjangan_bpjs'] ?? 0) + 
                                    ($data['tunjangan_makan'] ?? 0) + 
                                    ($data['tunjangan_transport'] ?? 0) + 
                                    ($data['tunjangan_lainnya'] ?? 0) + 
                                    ($data['upah_lembur'] ?? 0);
        
        $data['total_potongan'] = ($data['potongan_bpjs_kes'] ?? 0) + 
                                  ($data['potongan_bpjs_tk'] ?? 0) + 
                                  ($data['potongan_pph21'] ?? 0) + 
                                  ($data['potongan_absensi'] ?? 0) + 
                                  ($data['potongan_kasbon'] ?? 0) + 
                                  ($data['potongan_lainnya'] ?? 0);
        
        $data['gaji_bersih'] = $data['total_pendapatan'] - $data['total_potongan'];
        
        // Format terbilang
        $data['terbilang'] = $this->terbilang($data['gaji_bersih']);
        
        // Format tanggal
        if (!empty($data['tanggal_pembayaran'])) {
            $data['tanggal_pembayaran_formatted'] = $this->formatTanggalIndonesia($data['tanggal_pembayaran']);
        }
        
        if (!empty($data['tanggal_perhitungan'])) {
            $data['tanggal_perhitungan_formatted'] = $this->formatTanggalIndonesia($data['tanggal_perhitungan']);
        }
        
        // Periode
        $data['periode_text'] = $this->getNamaBulan($data['periode_bulan'] ?? date('m')) . ' ' . ($data['periode_tahun'] ?? date('Y'));
        
        return $data;
    }

    /**
     * ============================================
     * LAPORAN REKAP GAJI
     * ============================================
     */

    /**
     * Get laporan rekap gaji per periode
     */
    public function getLaporanRekapGaji($periodeBulan, $periodeTahun, $filters = [])
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                k.nik,
                k.nama_lengkap,
                k.jabatan,
                k.departemen,
                k.divisi,
                k.bank,
                k.no_rekening,
                k.nama_rekening,
                d.gaji_pokok,
                d.total_tunjangan,
                d.upah_lembur,
                d.total_potongan,
                d.gaji_bersih,
                d.status_pembayaran,
                d.no_referensi_eksternal,
                d.tanggal_pembayaran,
                p.nomor_proses,
                p.tanggal_proses,
                p.metode_pembayaran
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('karyawan k', 'k.id = d.karyawan_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai');
        
        // Apply filters
        if (!empty($filters['departemen'])) {
            $builder->where('k.departemen', $filters['departemen']);
        }
        
        if (!empty($filters['divisi'])) {
            $builder->where('k.divisi', $filters['divisi']);
        }
        
        if (!empty($filters['status_pembayaran'])) {
            $builder->where('d.status_pembayaran', $filters['status_pembayaran']);
        }
        
        if (!empty($filters['bank'])) {
            $builder->where('k.bank', $filters['bank']);
        }
        
        $builder->orderBy('k.departemen', 'ASC')
                ->orderBy('k.nama_lengkap', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Hitung total
        $total = [
            'jumlah_karyawan' => count($results),
            'total_gaji_pokok' => 0,
            'total_tunjangan' => 0,
            'total_lembur' => 0,
            'total_potongan' => 0,
            'total_gaji_bersih' => 0,
            'total_berhasil' => 0,
            'total_gagal' => 0,
            'total_pending' => 0
        ];
        
        foreach ($results as &$row) {
            $total['total_gaji_pokok'] += $row['gaji_pokok'];
            $total['total_tunjangan'] += $row['total_tunjangan'];
            $total['total_lembur'] += $row['upah_lembur'];
            $total['total_potongan'] += $row['total_potongan'];
            $total['total_gaji_bersih'] += $row['gaji_bersih'];
            
            if ($row['status_pembayaran'] == 'Berhasil') {
                $total['total_berhasil'] += $row['gaji_bersih'];
            } elseif ($row['status_pembayaran'] == 'Gagal') {
                $total['total_gagal'] += $row['gaji_bersih'];
            } else {
                $total['total_pending'] += $row['gaji_bersih'];
            }
            
            $row['gaji_pokok_formatted'] = 'Rp ' . number_format($row['gaji_pokok'], 0, ',', '.');
            $row['total_tunjangan_formatted'] = 'Rp ' . number_format($row['total_tunjangan'], 0, ',', '.');
            $row['upah_lembur_formatted'] = 'Rp ' . number_format($row['upah_lembur'], 0, ',', '.');
            $row['total_potongan_formatted'] = 'Rp ' . number_format($row['total_potongan'], 0, ',', '.');
            $row['gaji_bersih_formatted'] = 'Rp ' . number_format($row['gaji_bersih'], 0, ',', '.');
        }
        
        return [
            'data' => $results,
            'total' => $total
        ];
    }

    /**
     * Get laporan rekap gaji per departemen
     */
    public function getLaporanRekapPerDepartemen($periodeBulan, $periodeTahun)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                k.departemen,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan,
                SUM(d.gaji_pokok) as total_gaji_pokok,
                SUM(d.total_tunjangan) as total_tunjangan,
                SUM(d.upah_lembur) as total_lembur,
                SUM(d.total_potongan) as total_potongan,
                SUM(d.gaji_bersih) as total_gaji_bersih,
                SUM(CASE WHEN d.status_pembayaran = "Berhasil" THEN d.gaji_bersih ELSE 0 END) as total_terbayar,
                SUM(CASE WHEN d.status_pembayaran = "Pending" THEN d.gaji_bersih ELSE 0 END) as total_pending
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('karyawan k', 'k.id = d.karyawan_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->groupBy('k.departemen')
            ->orderBy('total_gaji_bersih', 'DESC');
        
        $results = $builder->get()->getResultArray();
        
        $grandTotal = [
            'jumlah_karyawan' => 0,
            'total_gaji_pokok' => 0,
            'total_tunjangan' => 0,
            'total_lembur' => 0,
            'total_potongan' => 0,
            'total_gaji_bersih' => 0,
            'total_terbayar' => 0,
            'total_pending' => 0
        ];
        
        foreach ($results as &$row) {
            foreach ($grandTotal as $key => $value) {
                if ($key != 'departemen') {
                    $grandTotal[$key] += $row[$key] ?? 0;
                }
            }
            
            $row['total_gaji_pokok_formatted'] = 'Rp ' . number_format($row['total_gaji_pokok'], 0, ',', '.');
            $row['total_gaji_bersih_formatted'] = 'Rp ' . number_format($row['total_gaji_bersih'], 0, ',', '.');
        }
        
        return [
            'data' => $results,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * ============================================
     * LAPORAN DETAIL GAJI PER KARYAWAN
     * ============================================
     */

    /**
     * Get laporan detail gaji per karyawan (histori)
     */
    public function getLaporanDetailKaryawan($karyawanId, $tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                pg.periode_bulan,
                pg.periode_tahun,
                pg.gaji_pokok,
                pg.tunjangan_jabatan,
                pg.tunjangan_bpjs,
                pg.tunjangan_makan,
                pg.tunjangan_transport,
                pg.tunjangan_lainnya,
                pg.upah_lembur,
                pg.potongan_bpjs_kes,
                pg.potongan_bpjs_tk,
                pg.potongan_pph21,
                pg.potongan_absensi,
                pg.potongan_kasbon,
                pg.potongan_lainnya,
                pg.total_hadir,
                pg.total_izin,
                pg.total_sakit,
                pg.total_cuti,
                pg.total_alpha,
                pg.jam_lembur,
                d.gaji_bersih,
                d.status_pembayaran,
                d.tanggal_pembayaran,
                p.nomor_proses,
                p.tanggal_proses
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('d.karyawan_id', $karyawanId)
            ->where('pg.periode_tahun', $tahun)
            ->where('p.status', 'Selesai')
            ->orderBy('pg.periode_tahun', 'DESC')
            ->orderBy('pg.periode_bulan', 'DESC');
        
        $results = $builder->get()->getResultArray();
        
        // Hitung total setahun
        $total = [
            'total_gaji_pokok' => 0,
            'total_tunjangan' => 0,
            'total_lembur' => 0,
            'total_potongan' => 0,
            'total_gaji_bersih' => 0,
            'total_hadir' => 0,
            'total_alpha' => 0,
            'rata_rata' => 0
        ];
        
        foreach ($results as $row) {
            $total['total_gaji_pokok'] += $row['gaji_pokok'];
            $total['total_tunjangan'] += ($row['tunjangan_jabatan'] + $row['tunjangan_bpjs'] + 
                                          $row['tunjangan_makan'] + $row['tunjangan_transport'] + 
                                          $row['tunjangan_lainnya']);
            $total['total_lembur'] += $row['upah_lembur'];
            $total['total_potongan'] += ($row['potongan_bpjs_kes'] + $row['potongan_bpjs_tk'] + 
                                         $row['potongan_pph21'] + $row['potongan_absensi'] + 
                                         $row['potongan_kasbon'] + $row['potongan_lainnya']);
            $total['total_gaji_bersih'] += $row['gaji_bersih'];
            $total['total_hadir'] += $row['total_hadir'];
            $total['total_alpha'] += $row['total_alpha'];
        }
        
        if (count($results) > 0) {
            $total['rata_rata'] = $total['total_gaji_bersih'] / count($results);
        }
        
        return [
            'data' => $results,
            'total' => $total,
            'jumlah_bulan' => count($results)
        ];
    }

    /**
     * ============================================
     * LAPORAN KOMPONEN GAJI
     * ============================================
     */

    /**
     * Get laporan komponen gaji (rincian pendapatan dan potongan)
     */
    public function getLaporanKomponenGaji($periodeBulan, $periodeTahun)
    {
        $db = \Config\Database::connect();
        
        // Pendapatan
        $pendapatan = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Pendapatan' as tipe,
                'Gaji Pokok' as komponen,
                SUM(d.gaji_pokok) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $tunjanganJabatan = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Pendapatan' as tipe,
                'Tunjangan Jabatan' as komponen,
                SUM(pg.tunjangan_jabatan) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $tunjanganMakan = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Pendapatan' as tipe,
                'Tunjangan Makan' as komponen,
                SUM(pg.tunjangan_makan) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $lembur = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Pendapatan' as tipe,
                'Upah Lembur' as komponen,
                SUM(pg.upah_lembur) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        // Potongan
        $bpjsKes = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Potongan' as tipe,
                'BPJS Kesehatan' as komponen,
                SUM(pg.potongan_bpjs_kes) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $bpjsTK = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Potongan' as tipe,
                'BPJS Ketenagakerjaan' as komponen,
                SUM(pg.potongan_bpjs_tk) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $pph21 = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Potongan' as tipe,
                'PPh 21' as komponen,
                SUM(pg.potongan_pph21) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $absensi = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Potongan' as tipe,
                'Potongan Absensi' as komponen,
                SUM(pg.potongan_absensi) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $kasbon = $db->table('penggajian_detail_pembayaran d')
            ->select("
                'Potongan' as tipe,
                'Potongan Kasbon' as komponen,
                SUM(pg.potongan_kasbon) as total,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan
            ")
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        $komponen = [
            $pendapatan,
            $tunjanganJabatan,
            $tunjanganMakan,
            $lembur,
            $bpjsKes,
            $bpjsTK,
            $pph21,
            $absensi,
            $kasbon
        ];
        
        // Hitung total
        $totalPendapatan = 0;
        $totalPotongan = 0;
        
        foreach ($komponen as &$item) {
            if ($item['tipe'] == 'Pendapatan') {
                $totalPendapatan += $item['total'];
            } else {
                $totalPotongan += $item['total'];
            }
            $item['total_formatted'] = 'Rp ' . number_format($item['total'], 0, ',', '.');
        }
        
        return [
            'komponen' => $komponen,
            'total_pendapatan' => $totalPendapatan,
            'total_potongan' => $totalPotongan,
            'total_bersih' => $totalPendapatan - $totalPotongan
        ];
    }

    /**
     * ============================================
     * LAPORAN SUMMARY PERIODE
     * ============================================
     */

    /**
     * Get laporan summary per periode (tahunan)
     */
    public function getLaporanSummaryPeriode($tahun)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                pg.periode_bulan,
                COUNT(DISTINCT d.karyawan_id) as jumlah_karyawan,
                SUM(d.gaji_pokok) as total_gaji_pokok,
                SUM(d.total_tunjangan) as total_tunjangan,
                SUM(d.upah_lembur) as total_lembur,
                SUM(d.total_potongan) as total_potongan,
                SUM(d.gaji_bersih) as total_gaji_bersih
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_tahun', $tahun)
            ->where('p.status', 'Selesai')
            ->groupBy('pg.periode_bulan')
            ->orderBy('pg.periode_bulan', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Format untuk 12 bulan
        $data = [];
        $grandTotal = [
            'jumlah_karyawan' => 0,
            'total_gaji_pokok' => 0,
            'total_tunjangan' => 0,
            'total_lembur' => 0,
            'total_potongan' => 0,
            'total_gaji_bersih' => 0,
            'rata_rata_per_bulan' => 0
        ];
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $found = false;
            foreach ($results as $row) {
                if ($row['periode_bulan'] == $bulan) {
                    $row['bulan'] = $this->getNamaBulan($bulan);
                    $row['total_gaji_pokok_formatted'] = 'Rp ' . number_format($row['total_gaji_pokok'], 0, ',', '.');
                    $row['total_gaji_bersih_formatted'] = 'Rp ' . number_format($row['total_gaji_bersih'], 0, ',', '.');
                    
                    $data[] = $row;
                    
                    $grandTotal['jumlah_karyawan'] += $row['jumlah_karyawan'];
                    $grandTotal['total_gaji_pokok'] += $row['total_gaji_pokok'];
                    $grandTotal['total_tunjangan'] += $row['total_tunjangan'];
                    $grandTotal['total_lembur'] += $row['total_lembur'];
                    $grandTotal['total_potongan'] += $row['total_potongan'];
                    $grandTotal['total_gaji_bersih'] += $row['total_gaji_bersih'];
                    
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $data[] = [
                    'periode_bulan' => $bulan,
                    'bulan' => $this->getNamaBulan($bulan),
                    'jumlah_karyawan' => 0,
                    'total_gaji_pokok' => 0,
                    'total_tunjangan' => 0,
                    'total_lembur' => 0,
                    'total_potongan' => 0,
                    'total_gaji_bersih' => 0,
                    'total_gaji_pokok_formatted' => 'Rp 0',
                    'total_gaji_bersih_formatted' => 'Rp 0'
                ];
            }
        }
        
        if (count($data) > 0) {
            $grandTotal['rata_rata_per_bulan'] = $grandTotal['total_gaji_bersih'] / count($data);
        }
        
        return [
            'data' => $data,
            'grand_total' => $grandTotal,
            'tahun' => $tahun
        ];
    }

    /**
     * ============================================
     * LAPORAN POTONGAN
     * ============================================
     */

    /**
     * Get laporan potongan (BPJS, PPh, dll)
     */
    public function getLaporanPotongan($periodeBulan, $periodeTahun)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                k.nik,
                k.nama_lengkap,
                k.jabatan,
                k.departemen,
                k.no_npwp,
                k.no_bpjs_kes,
                k.no_bpjs_tk,
                pg.potongan_bpjs_kes,
                pg.potongan_bpjs_tk,
                pg.potongan_pph21,
                pg.potongan_absensi,
                pg.potongan_kasbon,
                pg.potongan_lainnya,
                pg.total_potongan
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->join('karyawan k', 'k.id = d.karyawan_id')
            ->where('pg.periode_bulan', $periodeBulan)
            ->where('pg.periode_tahun', $periodeTahun)
            ->where('p.status', 'Selesai')
            ->orderBy('k.departemen', 'ASC')
            ->orderBy('k.nama_lengkap', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Hitung total
        $total = [
            'bpjs_kes' => 0,
            'bpjs_tk' => 0,
            'pph21' => 0,
            'absensi' => 0,
            'kasbon' => 0,
            'lainnya' => 0,
            'total' => 0
        ];
        
        foreach ($results as &$row) {
            $total['bpjs_kes'] += $row['potongan_bpjs_kes'];
            $total['bpjs_tk'] += $row['potongan_bpjs_tk'];
            $total['pph21'] += $row['potongan_pph21'];
            $total['absensi'] += $row['potongan_absensi'];
            $total['kasbon'] += $row['potongan_kasbon'];
            $total['lainnya'] += $row['potongan_lainnya'];
            $total['total'] += $row['total_potongan'];
            
            $row['potongan_bpjs_kes_formatted'] = 'Rp ' . number_format($row['potongan_bpjs_kes'], 0, ',', '.');
            $row['potongan_bpjs_tk_formatted'] = 'Rp ' . number_format($row['potongan_bpjs_tk'], 0, ',', '.');
            $row['potongan_pph21_formatted'] = 'Rp ' . number_format($row['potongan_pph21'], 0, ',', '.');
            $row['total_potongan_formatted'] = 'Rp ' . number_format($row['total_potongan'], 0, ',', '.');
        }
        
        return [
            'data' => $results,
            'total' => $total
        ];
    }

    /**
     * ============================================
     * LAPORAN PERBANKAN
     * ============================================
     */

    /**
     * Get laporan untuk keperluan bank (transfer)
     */
    public function getLaporanBankTransfer($prosesId)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                k.bank,
                k.no_rekening,
                k.nama_rekening,
                k.nama_lengkap,
                k.nik,
                d.gaji_bersih,
                d.status_pembayaran
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('karyawan k', 'k.id = d.karyawan_id')
            ->where('d.proses_id', $prosesId)
            ->where('d.status_pembayaran !=', 'Gagal')
            ->where('k.bank IS NOT NULL')
            ->where('k.no_rekening IS NOT NULL')
            ->where('k.no_rekening !=', '')
            ->orderBy('k.bank', 'ASC')
            ->orderBy('k.nama_lengkap', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Group by bank
        $perBank = [];
        foreach ($results as $row) {
            $bank = $row['bank'] ?: 'Tidak Ada Bank';
            if (!isset($perBank[$bank])) {
                $perBank[$bank] = [
                    'bank' => $bank,
                    'jumlah_karyawan' => 0,
                    'total' => 0,
                    'data' => []
                ];
            }
            $perBank[$bank]['jumlah_karyawan']++;
            $perBank[$bank]['total'] += $row['gaji_bersih'];
            $perBank[$bank]['data'][] = $row;
        }
        
        return [
            'data' => $results,
            'per_bank' => $perBank,
            'total_karyawan' => count($results),
            'total_nominal' => array_sum(array_column($results, 'gaji_bersih'))
        ];
    }

    /**
     * ============================================
     * DASHBOARD STATISTICS
     * ============================================
     */

    /**
     * Get statistik untuk dashboard penggajian
     */
    public function getDashboardStats($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $db = \Config\Database::connect();
        
        // Total penggajian tahun ini
        $tahunan = $db->table('penggajian_detail_pembayaran d')
            ->select('SUM(d.gaji_bersih) as total')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_tahun', $tahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        // Total bulan ini
        $bulanIni = $db->table('penggajian_detail_pembayaran d')
            ->select('SUM(d.gaji_bersih) as total')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_tahun', date('Y'))
            ->where('pg.periode_bulan', date('m'))
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        // Rata-rata per karyawan
        $rataRata = $db->table('penggajian_detail_pembayaran d')
            ->select('AVG(d.gaji_bersih) as rata_rata')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_tahun', $tahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        // Jumlah karyawan yang menerima gaji
        $jumlahKaryawan = $db->table('penggajian_detail_pembayaran d')
            ->select('COUNT(DISTINCT d.karyawan_id) as jumlah')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_tahun', $tahun)
            ->where('p.status', 'Selesai')
            ->get()->getRowArray();
        
        return [
            'total_tahunan' => $tahunan['total'] ?? 0,
            'total_bulan_ini' => $bulanIni['total'] ?? 0,
            'rata_rata_per_karyawan' => $rataRata['rata_rata'] ?? 0,
            'jumlah_karyawan' => $jumlahKaryawan['jumlah'] ?? 0,
            'tahun' => $tahun
        ];
    }

    /**
     * Get data untuk chart penggajian
     */
    public function getChartData($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $db = \Config\Database::connect();
        
        $builder = $db->table('penggajian_detail_pembayaran d')
            ->select('
                pg.periode_bulan,
                SUM(d.gaji_bersih) as total
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('pg.periode_tahun', $tahun)
            ->where('p.status', 'Selesai')
            ->groupBy('pg.periode_bulan')
            ->orderBy('pg.periode_bulan', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Format untuk chart
        $labels = [];
        $values = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $this->getNamaBulan($i);
            $found = false;
            foreach ($results as $row) {
                if ($row['periode_bulan'] == $i) {
                    $values[] = (float)$row['total'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $values[] = 0;
            }
        }
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * ============================================
     * FUNGSI BANTU
     * ============================================
     */

    /**
     * Konversi angka ke terbilang
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        
        if ($angka < 12) {
            return $huruf[$angka];
        } elseif ($angka < 20) {
            return $huruf[$angka - 10] . " belas";
        } elseif ($angka < 100) {
            return $huruf[floor($angka / 10)] . " puluh " . $huruf[$angka % 10];
        } elseif ($angka < 200) {
            return "seratus " . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $huruf[floor($angka / 100)] . " ratus " . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return "seribu " . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(floor($angka / 1000)) . " ribu " . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(floor($angka / 1000000)) . " juta " . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return $this->terbilang(floor($angka / 1000000000)) . " milyar " . $this->terbilang($angka % 1000000000);
        }
        
        return "Angka terlalu besar";
    }

    /**
     * Format tanggal Indonesia
     */
    private function formatTanggalIndonesia($tanggal)
    {
        if (empty($tanggal) || $tanggal == '0000-00-00') {
            return '-';
        }
        
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        $tgl = date('j', strtotime($tanggal));
        $bln = date('n', strtotime($tanggal));
        $thn = date('Y', strtotime($tanggal));
        
        return $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
    }

    /**
     * Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $namaBulan[(int)$bulan] ?? $bulan;
    }

    /**
     * Export data laporan ke Excel
     */
    public function exportLaporan($jenis, $params = [])
    {
        switch ($jenis) {
            case 'rekap-gaji':
                $data = $this->getLaporanRekapGaji($params['bulan'], $params['tahun'], $params);
                return $this->formatExportRekapGaji($data);
                
            case 'detail-karyawan':
                $data = $this->getLaporanDetailKaryawan($params['karyawan_id'], $params['tahun']);
                return $this->formatExportDetailKaryawan($data);
                
            case 'komponen-gaji':
                $data = $this->getLaporanKomponenGaji($params['bulan'], $params['tahun']);
                return $this->formatExportKomponenGaji($data);
                
            case 'summary-periode':
                $data = $this->getLaporanSummaryPeriode($params['tahun']);
                return $this->formatExportSummaryPeriode($data);
                
            case 'potongan':
                $data = $this->getLaporanPotongan($params['bulan'], $params['tahun']);
                return $this->formatExportPotongan($data);
                
            default:
                return [];
        }
    }

    /**
     * Format export rekap gaji
     */
    private function formatExportRekapGaji($data)
    {
        $export = [];
        
        foreach ($data['data'] as $row) {
            $export[] = [
                'NIK' => $row['nik'],
                'Nama Karyawan' => $row['nama_lengkap'],
                'Jabatan' => $row['jabatan'],
                'Departemen' => $row['departemen'],
                'Bank' => $row['bank'],
                'No Rekening' => $row['no_rekening'],
                'Gaji Pokok' => $row['gaji_pokok'],
                'Tunjangan' => $row['total_tunjangan'],
                'Lembur' => $row['upah_lembur'],
                'Potongan' => $row['total_potongan'],
                'Gaji Bersih' => $row['gaji_bersih'],
                'Status' => $row['status_pembayaran'],
                'Tgl Bayar' => $row['tanggal_pembayaran']
            ];
        }
        
        // Baris total
        $export[] = [
            'NIK' => 'TOTAL',
            'Nama Karyawan' => '',
            'Jabatan' => '',
            'Departemen' => '',
            'Bank' => '',
            'No Rekening' => '',
            'Gaji Pokok' => $data['total']['total_gaji_pokok'],
            'Tunjangan' => $data['total']['total_tunjangan'],
            'Lembur' => $data['total']['total_lembur'],
            'Potongan' => $data['total']['total_potongan'],
            'Gaji Bersih' => $data['total']['total_gaji_bersih'],
            'Status' => '',
            'Tgl Bayar' => ''
        ];
        
        return $export;
    }

    /**
     * Format export detail karyawan
     */
    private function formatExportDetailKaryawan($data)
    {
        $export = [];
        
        foreach ($data['data'] as $row) {
            $export[] = [
                'Periode' => $row['periode_bulan'] . '-' . $row['periode_tahun'],
                'Gaji Pokok' => $row['gaji_pokok'],
                'Tunjangan Jabatan' => $row['tunjangan_jabatan'],
                'Tunjangan Makan' => $row['tunjangan_makan'],
                'Tunjangan Transport' => $row['tunjangan_transport'],
                'Upah Lembur' => $row['upah_lembur'],
                'Pot BPJS Kes' => $row['potongan_bpjs_kes'],
                'Pot BPJS TK' => $row['potongan_bpjs_tk'],
                'Pot PPh21' => $row['potongan_pph21'],
                'Pot Absensi' => $row['potongan_absensi'],
                'Pot Kasbon' => $row['potongan_kasbon'],
                'Gaji Bersih' => $row['gaji_bersih'],
                'Hadir' => $row['total_hadir'],
                'Izin' => $row['total_izin'],
                'Sakit' => $row['total_sakit'],
                'Cuti' => $row['total_cuti'],
                'Alpha' => $row['total_alpha'],
                'Jam Lembur' => $row['jam_lembur']
            ];
        }
        
        // Baris total
        $export[] = [
            'Periode' => 'TOTAL',
            'Gaji Pokok' => $data['total']['total_gaji_pokok'],
            'Tunjangan Jabatan' => '',
            'Tunjangan Makan' => '',
            'Tunjangan Transport' => '',
            'Upah Lembur' => $data['total']['total_lembur'],
            'Pot BPJS Kes' => '',
            'Pot BPJS TK' => '',
            'Pot PPh21' => '',
            'Pot Absensi' => '',
            'Pot Kasbon' => '',
            'Gaji Bersih' => $data['total']['total_gaji_bersih'],
            'Hadir' => $data['total']['total_hadir'],
            'Izin' => '',
            'Sakit' => '',
            'Cuti' => '',
            'Alpha' => $data['total']['total_alpha'],
            'Jam Lembur' => ''
        ];
        
        return $export;
    }

    /**
     * Format export komponen gaji
     */
    private function formatExportKomponenGaji($data)
    {
        $export = [];
        
        foreach ($data['komponen'] as $row) {
            $export[] = [
                'Tipe' => $row['tipe'],
                'Komponen' => $row['komponen'],
                'Jumlah Karyawan' => $row['jumlah_karyawan'],
                'Total' => $row['total']
            ];
        }
        
        $export[] = [
            'Tipe' => 'RINGKASAN',
            'Komponen' => 'Total Pendapatan',
            'Jumlah Karyawan' => '',
            'Total' => $data['total_pendapatan']
        ];
        
        $export[] = [
            'Tipe' => 'RINGKASAN',
            'Komponen' => 'Total Potongan',
            'Jumlah Karyawan' => '',
            'Total' => $data['total_potongan']
        ];
        
        $export[] = [
            'Tipe' => 'RINGKASAN',
            'Komponen' => 'Total Bersih',
            'Jumlah Karyawan' => '',
            'Total' => $data['total_bersih']
        ];
        
        return $export;
    }

    /**
     * Format export summary periode
     */
    private function formatExportSummaryPeriode($data)
    {
        $export = [];
        
        foreach ($data['data'] as $row) {
            $export[] = [
                'Bulan' => $row['bulan'],
                'Jumlah Karyawan' => $row['jumlah_karyawan'],
                'Total Gaji Pokok' => $row['total_gaji_pokok'],
                'Total Tunjangan' => $row['total_tunjangan'],
                'Total Lembur' => $row['total_lembur'],
                'Total Potongan' => $row['total_potongan'],
                'Total Gaji Bersih' => $row['total_gaji_bersih']
            ];
        }
        
        $export[] = [
            'Bulan' => 'TOTAL',
            'Jumlah Karyawan' => $data['grand_total']['jumlah_karyawan'],
            'Total Gaji Pokok' => $data['grand_total']['total_gaji_pokok'],
            'Total Tunjangan' => $data['grand_total']['total_tunjangan'],
            'Total Lembur' => $data['grand_total']['total_lembur'],
            'Total Potongan' => $data['grand_total']['total_potongan'],
            'Total Gaji Bersih' => $data['grand_total']['total_gaji_bersih']
        ];
        
        return $export;
    }

    /**
     * Format export potongan
     */
    private function formatExportPotongan($data)
    {
        $export = [];
        
        foreach ($data['data'] as $row) {
            $export[] = [
                'NIK' => $row['nik'],
                'Nama Karyawan' => $row['nama_lengkap'],
                'Jabatan' => $row['jabatan'],
                'Departemen' => $row['departemen'],
                'NPWP' => $row['no_npwp'],
                'No BPJS Kes' => $row['no_bpjs_kes'],
                'No BPJS TK' => $row['no_bpjs_tk'],
                'Pot BPJS Kes' => $row['potongan_bpjs_kes'],
                'Pot BPJS TK' => $row['potongan_bpjs_tk'],
                'Pot PPh21' => $row['potongan_pph21'],
                'Pot Absensi' => $row['potongan_absensi'],
                'Pot Kasbon' => $row['potongan_kasbon'],
                'Total Potongan' => $row['total_potongan']
            ];
        }
        
        // Baris total
        $export[] = [
            'NIK' => 'TOTAL',
            'Nama Karyawan' => '',
            'Jabatan' => '',
            'Departemen' => '',
            'NPWP' => '',
            'No BPJS Kes' => '',
            'No BPJS TK' => '',
            'Pot BPJS Kes' => $data['total']['bpjs_kes'],
            'Pot BPJS TK' => $data['total']['bpjs_tk'],
            'Pot PPh21' => $data['total']['pph21'],
            'Pot Absensi' => $data['total']['absensi'],
            'Pot Kasbon' => $data['total']['kasbon'],
            'Total Potongan' => $data['total']['total']
        ];
        
        return $export;
    }
}