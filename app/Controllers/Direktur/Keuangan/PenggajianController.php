<?php

namespace App\Controllers\Direktur\Keuangan;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

class PenggajianController extends BaseController
{
    protected $db;
    protected $karyawanModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->karyawanModel = new KaryawanModel();
        $this->checkSchema();
    }

    private function checkSchema()
    {
        if (!$this->db->tableExists('penggajian_detail_pembayaran')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `penggajian_detail_pembayaran` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `pembayaran_id` INT DEFAULT 0,
                  `perhitungan_id` INT DEFAULT 0,
                  `karyawan_id` INT NOT NULL,
                  `bulan` VARCHAR(10) DEFAULT NULL,
                  `tahun` VARCHAR(10) DEFAULT NULL,
                  `gaji_pokok` DECIMAL(15,2) DEFAULT 0.00,
                  `tunjangan` DECIMAL(15,2) DEFAULT 0.00,
                  `bonus` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_kasbon` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_bpjs_kes` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_bpjs_jht` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_bpjs_jp` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_lainnya` DECIMAL(15,2) DEFAULT 0.00,
                  `total_tunjangan` DECIMAL(15,2) DEFAULT 0.00,
                  `total_potongan` DECIMAL(15,2) DEFAULT 0.00,
                  `gaji_bersih` DECIMAL(15,2) DEFAULT 0.00,
                  `status_pembayaran` VARCHAR(50) DEFAULT 'Pending',
                  `status` VARCHAR(50) DEFAULT 'Paid',
                  `bukti_transfer` VARCHAR(255) DEFAULT NULL,
                  `catatan` TEXT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        } else {
            $fields = $this->db->getFieldNames('penggajian_detail_pembayaran');
            if (!in_array('bulan', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `bulan` VARCHAR(10) DEFAULT NULL");
            }
            if (!in_array('tahun', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `tahun` VARCHAR(10) DEFAULT NULL");
            }
            if (!in_array('gaji_pokok', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `gaji_pokok` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('tunjangan', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `tunjangan` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('bonus', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `bonus` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('potongan_kasbon', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `potongan_kasbon` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('potongan_bpjs_kes', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `potongan_bpjs_kes` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('potongan_bpjs_jht', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `potongan_bpjs_jht` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('potongan_bpjs_jp', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `potongan_bpjs_jp` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('potongan_lainnya', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `potongan_lainnya` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('total_tunjangan', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `total_tunjangan` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('total_potongan', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `total_potongan` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!in_array('status_pembayaran', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `status_pembayaran` VARCHAR(50) DEFAULT 'Pending'");
            }
            if (!in_array('bukti_transfer', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `bukti_transfer` VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('catatan', $fields)) {
                $this->db->query("ALTER TABLE `penggajian_detail_pembayaran` ADD COLUMN `catatan` TEXT DEFAULT NULL");
            }
        }

        if ($this->db->tableExists('karyawan') && !$this->db->fieldExists('gaji_pokok', 'karyawan')) {
            $this->db->query("ALTER TABLE `karyawan` ADD COLUMN `gaji_pokok` DECIMAL(15,2) DEFAULT 0.00");
        }

        if ($this->db->tableExists('form_kasbon')) {
            $kasbonFields = $this->db->getFieldNames('form_kasbon');
            if (!in_array('sisa_pinjaman', $kasbonFields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `sisa_pinjaman` DECIMAL(15,2) DEFAULT NULL");
                $this->db->query("UPDATE `form_kasbon` SET `sisa_pinjaman` = `jumlah_kasbon` WHERE `sisa_pinjaman` IS NULL");
            }
        }
    }

    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $builder = $this->db->table('penggajian_detail_pembayaran p')
            ->select('p.*, k.nik, k.nama_lengkap, k.jabatan, k.divisi, k.telepon, k.gaji_pokok as default_gaji_pokok')
            ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
            ->where('p.bulan', $bulan)
            ->where('p.tahun', $tahun);
            
        $penggajian = $builder->get()->getResultArray();

        $totalGaji = 0;
        $totalTransfer = 0;
        $totalPending = 0;
        
        foreach ($penggajian as $g) {
            $totalGaji += floatval($g['gaji_bersih'] ?? 0);
            if (($g['status_pembayaran'] ?? '') === 'Dibayar' || strtolower($g['status_pembayaran'] ?? '') === 'sukses') {
                $totalTransfer += floatval($g['gaji_bersih'] ?? 0);
            } else {
                $totalPending += floatval($g['gaji_bersih'] ?? 0);
            }
        }

        $karyawanList = $this->karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll();

        // Get Kasbon Aktif per Karyawan Map
        $kasbonMap = [];
        if ($this->db->tableExists('form_kasbon')) {
            $rows = $this->db->table('form_kasbon')
                ->select('karyawan_id, SUM(IFNULL(sisa_pinjaman, jumlah_kasbon)) as sisa_total')
                ->where('status_direktur', 'Disetujui')
                ->where('status_keseluruhan !=', 'Lunas')
                ->where('deleted_at', null)
                ->groupBy('karyawan_id')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $kasbonMap[$r['karyawan_id']] = floatval($r['sisa_total']);
            }
        }

        $data = [
            'title' => 'Penggajian Karyawan',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'penggajian' => $penggajian,
            'karyawanList' => $karyawanList,
            'kasbonMap' => $kasbonMap,
            'totalGaji' => $totalGaji,
            'totalTransfer' => $totalTransfer,
            'totalPending' => $totalPending
        ];

        return view('direktur/keuangan/penggajian', $data);
    }

    private function parseNominal($val)
    {
        if (empty($val)) return 0;
        $str = (string)$val;
        $str = preg_replace('/[,.]00$/', '', $str);
        $clean = preg_replace('/[^0-9]/', '', $str);
        return floatval($clean);
    }

    /**
     * Helper Kompresi Gambar Bukti Transfer Otomatis
     */
    private function compressAndUploadImage($file, $targetDir = 'uploads/penggajian/')
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        if (!is_dir(FCPATH . $targetDir)) {
            mkdir(FCPATH . $targetDir, 0777, true);
        }

        $ext = strtolower($file->getClientExtension());
        $newName = 'bukti_' . time() . '_' . rand(100, 999) . '.' . ($ext === 'pdf' ? 'pdf' : 'jpg');
        $destinationPath = FCPATH . $targetDir . $newName;

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $tmpPath = $file->getTempName();
            list($width, $height, $type) = getimagesize($tmpPath);

            $source = null;
            if ($type === IMAGETYPE_JPEG) {
                $source = @imagecreatefromjpeg($tmpPath);
            } elseif ($type === IMAGETYPE_PNG) {
                $source = @imagecreatefrompng($tmpPath);
            } elseif ($type === IMAGETYPE_WEBP) {
                $source = @imagecreatefromwebp($tmpPath);
            }

            if ($source) {
                $maxDimension = 1200;
                if ($width > $maxDimension || $height > $maxDimension) {
                    if ($width > $height) {
                        $newWidth = $maxDimension;
                        $newHeight = floor($height * ($maxDimension / $width));
                    } else {
                        $newHeight = $maxDimension;
                        $newWidth = floor($width * ($maxDimension / $height));
                    }
                } else {
                    $newWidth = $width;
                    $newHeight = $height;
                }

                $virtualImage = imagecreatetruecolor($newWidth, $newHeight);
                if ($type === IMAGETYPE_PNG) {
                    imagealphablending($virtualImage, false);
                    imagesavealpha($virtualImage, true);
                }

                imagecopyresampled($virtualImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($virtualImage, $destinationPath, 75); // Compress 75%
                imagedestroy($source);
                imagedestroy($virtualImage);
                return $targetDir . $newName;
            }
        }

        $file->move(FCPATH . $targetDir, $newName);
        return $targetDir . $newName;
    }

    /**
     * Potong Sisa Kasbon Otomatis di tabel form_kasbon
     */
    private function prosesPotonganKasbon($karyawanId, $nominalPotongan, $periode)
    {
        if ($nominalPotongan <= 0 || !$this->db->tableExists('form_kasbon')) {
            return;
        }

        $activeKasbons = $this->db->table('form_kasbon')
            ->where('karyawan_id', $karyawanId)
            ->where('status_direktur', 'Disetujui')
            ->where('status_keseluruhan !=', 'Lunas')
            ->where('deleted_at', null)
            ->orderBy('tanggal_pengajuan', 'ASC')
            ->get()->getResultArray();

        $sisaDipotong = $nominalPotongan;

        foreach ($activeKasbons as $k) {
            if ($sisaDipotong <= 0) break;

            $sisaPinjamanSaatIni = floatval(isset($k['sisa_pinjaman']) && $k['sisa_pinjaman'] !== null ? $k['sisa_pinjaman'] : $k['jumlah_kasbon']);
            if ($sisaPinjamanSaatIni <= 0) continue;

            if ($sisaDipotong >= $sisaPinjamanSaatIni) {
                $sisaDipotong -= $sisaPinjamanSaatIni;
                $this->db->table('form_kasbon')->where('id', $k['id'])->update([
                    'sisa_pinjaman' => 0,
                    'status_keseluruhan' => 'Lunas',
                    'lunas_pada' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $sisaBaru = $sisaPinjamanSaatIni - $sisaDipotong;
                $sisaDipotong = 0;
                $this->db->table('form_kasbon')->where('id', $k['id'])->update([
                    'sisa_pinjaman' => $sisaBaru,
                    'status_keseluruhan' => 'Belum Lunas',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Simpan / Edit Detail Penggajian Per Orang oleh Direktur
     */
    public function simpanDetail()
    {
        $karyawanId = $this->request->getPost('karyawan_id');
        $bulan = $this->request->getPost('bulan') ?? date('m');
        $tahun = $this->request->getPost('tahun') ?? date('Y');
        
        $gajiPokok = $this->parseNominal($this->request->getPost('gaji_pokok'));
        $tunjangan = $this->parseNominal($this->request->getPost('tunjangan'));
        $bonus = $this->parseNominal($this->request->getPost('bonus'));
        $potonganKasbon = $this->parseNominal($this->request->getPost('potongan_kasbon'));
        $potonganLainnya = $this->parseNominal($this->request->getPost('potongan_lainnya'));
        $statusPembayaran = $this->request->getPost('status_pembayaran') ?? 'Pending';
        $catatan = $this->request->getPost('catatan') ?? '';

        // Hitung BPJS Karyawan
        $calcBpjsKes = $this->request->getPost('enable_bpjs_kes') ? ($gajiPokok * 0.01) : $this->parseNominal($this->request->getPost('potongan_bpjs_kes'));
        $calcBpjsJht = $this->request->getPost('enable_bpjs_jht') ? ($gajiPokok * 0.02) : $this->parseNominal($this->request->getPost('potongan_bpjs_jht'));
        $calcBpjsJp  = $this->request->getPost('enable_bpjs_jp')  ? ($gajiPokok * 0.01) : $this->parseNominal($this->request->getPost('potongan_bpjs_jp'));

        $totalTunjangan = $tunjangan + $bonus;
        $totalPotongan = $potonganKasbon + $calcBpjsKes + $calcBpjsJht + $calcBpjsJp + $potonganLainnya;
        $gajiBersih = $gajiPokok + $totalTunjangan - $totalPotongan;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Karyawan harus dipilih.');
        }

        // Upload & Compress Bukti Transfer
        $fileBukti = $this->request->getFile('bukti_transfer');
        $buktiPath = $this->compressAndUploadImage($fileBukti);

        // Update default gaji_pokok di tabel karyawan jika diubah
        if ($gajiPokok > 0) {
            $this->karyawanModel->update($karyawanId, ['gaji_pokok' => $gajiPokok]);
        }

        $existing = $this->db->table('penggajian_detail_pembayaran')
            ->where('karyawan_id', $karyawanId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()->getRowArray();

        $payload = [
            'pembayaran_id'     => 0,
            'perhitungan_id'    => 0,
            'karyawan_id'       => $karyawanId,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => $gajiPokok,
            'tunjangan'         => $tunjangan,
            'bonus'             => $bonus,
            'potongan_kasbon'   => $potonganKasbon,
            'potongan_bpjs_kes' => $calcBpjsKes,
            'potongan_bpjs_jht' => $calcBpjsJht,
            'potongan_bpjs_jp'  => $calcBpjsJp,
            'potongan_lainnya'  => $potonganLainnya,
            'total_tunjangan'   => $totalTunjangan,
            'total_potongan'    => $totalPotongan,
            'gaji_bersih'       => $gajiBersih,
            'status_pembayaran' => $statusPembayaran,
            'status'            => 'Paid',
            'catatan'           => $catatan,
            'updated_at'        => date('Y-m-d H:i:s')
        ];

        if ($buktiPath) {
            $payload['bukti_transfer'] = $buktiPath;
        }

        if ($existing) {
            $this->db->table('penggajian_detail_pembayaran')
                ->where('id', $existing['id'])
                ->update($payload);
        } else {
            $payload['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('penggajian_detail_pembayaran')->insert($payload);
        }

        // Jika status_pembayaran adalah Dibayar & ada potongan_kasbon, trigger pengurangan sisa kasbon
        if ($statusPembayaran === 'Dibayar' && $potonganKasbon > 0) {
            $this->prosesPotonganKasbon($karyawanId, $potonganKasbon, $bulan.'/'.$tahun);
        }

        return redirect()->to('direktur/keuangan/penggajian?bulan='.$bulan.'&tahun='.$tahun)
            ->with('success', 'Data penggajian karyawan berhasil disimpan.');
    }

    /**
     * Hapus Data Penggajian
     */
    public function delete($id)
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $record = $this->db->table('penggajian_detail_pembayaran')->where('id', $id)->get()->getRowArray();
        if ($record) {
            $this->db->table('penggajian_detail_pembayaran')->where('id', $id)->delete();
            return redirect()->to('direktur/keuangan/penggajian?bulan='.$bulan.'&tahun='.$tahun)
                ->with('success', 'Data penggajian berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Data penggajian tidak ditemukan.');
    }

    /**
     * Tandai Penggajian Dibayar & Kurangi Sisa Kasbon Otomatis
     */
    public function bayar($id)
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $record = $this->db->table('penggajian_detail_pembayaran')->where('id', $id)->get()->getRowArray();
        if ($record) {
            $this->db->table('penggajian_detail_pembayaran')
                ->where('id', $id)
                ->update([
                    'status_pembayaran' => 'Dibayar',
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);

            // Triggers automatic kasbon deduction if present
            if (floatval($record['potongan_kasbon'] ?? 0) > 0) {
                $this->prosesPotonganKasbon($record['karyawan_id'], floatval($record['potongan_kasbon']), $bulan.'/'.$tahun);
            }

            return redirect()->to('direktur/keuangan/penggajian?bulan='.$bulan.'&tahun='.$tahun)
                ->with('success', 'Status penggajian berhasil diperbarui menjadi Dibayar dan kasbon terpotong otomatis.');
        }

        return redirect()->back()->with('error', 'Data penggajian tidak ditemukan.');
    }

    public function cetak_slip($id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $gaji = [];
        if ($this->db->tableExists('penggajian_detail_pembayaran')) {
            $gaji = $this->db->table('penggajian_detail_pembayaran')
                ->where('karyawan_id', $id)
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();
        }

        $sisaKasbon = 0;
        if ($this->db->tableExists('form_kasbon')) {
            $row = $this->db->table('form_kasbon')
                ->select('SUM(IFNULL(sisa_pinjaman, jumlah_kasbon)) as total_sisa')
                ->where('karyawan_id', $id)
                ->where('status_direktur', 'Disetujui')
                ->where('status_keseluruhan !=', 'Lunas')
                ->where('deleted_at', null)
                ->get()->getRowArray();
            $sisaKasbon = floatval($row['total_sisa'] ?? 0);
        }

        $data = [
            'karyawan' => $karyawan,
            'gaji' => $gaji,
            'sisaKasbon' => $sisaKasbon,
            'autoPrint' => $this->request->getGet('autoprint') ? true : false,
            'tanggalCetak' => date('d F Y')
        ];

        return view('direktur/keuangan/cetak_slip', $data);
    }

    public function cetak()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $penggajian = [];
        if ($this->db->tableExists('penggajian_detail_pembayaran')) {
            $penggajian = $this->db->table('penggajian_detail_pembayaran p')
                ->select('p.*, k.nik, k.nama_lengkap, k.jabatan, k.divisi')
                ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
                ->where('p.bulan', $bulan)
                ->where('p.tahun', $tahun)
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Rekapitulasi Penggajian Karyawan',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'penggajian' => $penggajian,
            'tanggalCetak' => date('d F Y')
        ];

        return view('direktur/keuangan/cetak_penggajian', $data);
    }

    public function export_excel()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $penggajian = $this->db->table('penggajian_detail_pembayaran p')
            ->select('p.*, k.nik, k.nama_lengkap, k.jabatan, k.divisi')
            ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
            ->where('p.bulan', $bulan)
            ->where('p.tahun', $tahun)
            ->get()->getResultArray();

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Rekap_Penggajian_{$bulan}_{$tahun}.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<thead><tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Gaji Pokok (Rp)</th>
                <th>Tunjangan (Rp)</th>
                <th>Bonus (Rp)</th>
                <th>Pot. Kasbon (Rp)</th>
                <th>BPJS Kes 1% (Rp)</th>
                <th>BPJS JHT 2% (Rp)</th>
                <th>BPJS JP 1% (Rp)</th>
                <th>Pot. Lainnya (Rp)</th>
                <th>Gaji Bersih (Rp)</th>
                <th>Status Pembayaran</th>
              </tr></thead>";
        echo "<tbody>";

        $no = 1;
        foreach ($penggajian as $p) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . esc($p['nik'] ?? '-') . "</td>";
            echo "<td>" . esc($p['nama_lengkap'] ?? $p['nama_karyawan'] ?? '-') . "</td>";
            echo "<td>" . esc($p['jabatan'] ?? '-') . "</td>";
            echo "<td>" . ($p['gaji_pokok'] ?? 0) . "</td>";
            echo "<td>" . ($p['tunjangan'] ?? 0) . "</td>";
            echo "<td>" . ($p['bonus'] ?? 0) . "</td>";
            echo "<td>" . ($p['potongan_kasbon'] ?? 0) . "</td>";
            echo "<td>" . ($p['potongan_bpjs_kes'] ?? 0) . "</td>";
            echo "<td>" . ($p['potongan_bpjs_jht'] ?? 0) . "</td>";
            echo "<td>" . ($p['potongan_bpjs_jp'] ?? 0) . "</td>";
            echo "<td>" . ($p['potongan_lainnya'] ?? 0) . "</td>";
            echo "<td>" . ($p['gaji_bersih'] ?? 0) . "</td>";
            echo "<td>" . esc($p['status_pembayaran'] ?? 'Pending') . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        exit;
    }
}
