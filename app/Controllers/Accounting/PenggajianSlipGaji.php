<?php
// app/Controllers/Accounting/PenggajianSlipGaji.php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PenggajianPerhitunganModel;
use App\Models\Accounting\PenggajianProsesPembayaranModel;
use App\Models\Accounting\PenggajianDetailPembayaranModel;
use App\Models\KaryawanModel;
use App\Models\PerusahaanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PenggajianSlipGaji extends BaseController
{
    protected $perhitunganModel;
    protected $prosesModel;
    protected $detailModel;
    protected $karyawanModel;
    protected $perusahaanModel;
    protected $db;

    public function __construct()
    {
        $this->perhitunganModel = new PenggajianPerhitunganModel();
        $this->prosesModel = new PenggajianProsesPembayaranModel();
        $this->detailModel = new PenggajianDetailPembayaranModel();
        $this->karyawanModel = new KaryawanModel();
        $this->perusahaanModel = new PerusahaanModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Halaman utama slip gaji & laporan
     */
    public function index()
    {
        $data['title'] = 'Slip Gaji & Laporan';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $karyawanId = $this->request->getGet('karyawan_id');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['karyawan_id'] = $karyawanId;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        // Data karyawan untuk filter
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan, departemen')
            ->where('deleted_at IS NULL')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        // Ambil slip gaji untuk periode
        $slipQuery = $this->perhitunganModel->select('
                penggajian_perhitungan.*,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                karyawan.departemen,
                karyawan.bank,
                karyawan.no_rekening,
                karyawan.nama_rekening,
                karyawan.tanggal_masuk
            ')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('penggajian_perhitungan.periode_bulan', $bulan)
            ->where('penggajian_perhitungan.periode_tahun', $tahun)
            ->where('penggajian_perhitungan.status', 'Disetujui')
            ->where('penggajian_perhitungan.deleted_at IS NULL');
        
        if ($karyawanId) {
            $slipQuery->where('penggajian_perhitungan.karyawan_id', $karyawanId);
        }
        
        $data['slipGaji'] = $slipQuery->orderBy('karyawan.nama_lengkap', 'ASC')->findAll();
        
        // Ringkasan periode
        $data['ringkasan'] = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        // Rekap per departemen
        $data['rekapDepartemen'] = $this->perhitunganModel->getRekapPerDepartemen($bulan, $tahun);
        
        // Data perusahaan
        $data['perusahaan'] = $this->perusahaanModel->find(1);
        
        return view('accounting/penggajian/slip-gaji-laporan/index', $data);
    }

    /**
     * Lihat slip gaji per karyawan
     */
    public function view($id)
    {
        $data['title'] = 'Slip Gaji Karyawan';
        
        $perhitungan = $this->perhitunganModel->getWithDetails($id);
        
        if (!$perhitungan) {
            return redirect()->to('accounting/penggajian/slip-gaji-laporan')
                ->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        // Ambil data proses pembayaran jika sudah dibayar
        $pembayaran = $this->detailModel->where('perhitungan_id', $id)->first();
        
        $data['perhitungan'] = $perhitungan;
        $data['pembayaran'] = $pembayaran;
        
        // Data perusahaan
        $data['perusahaan'] = $this->perusahaanModel->find(1);
        
        // Hitung komponen pendapatan detail
        $data['pendapatanDetail'] = [
            'gaji_pokok' => $perhitungan['gaji_pokok'],
            'tunjangan_jabatan' => $perhitungan['tunjangan_jabatan'],
            'tunjangan_makan' => $perhitungan['tunjangan_makan'],
            'tunjangan_transport' => $perhitungan['tunjangan_transport'],
            'tunjangan_lainnya' => $perhitungan['tunjangan_lainnya'],
            'upah_lembur' => $perhitungan['upah_lembur']
        ];
        
        // Hitung komponen potongan detail
        $data['potonganDetail'] = [
            'bpjs_kes' => $perhitungan['potongan_bpjs_kes'],
            'bpjs_tk' => $perhitungan['potongan_bpjs_tk'],
            'pph21' => $perhitungan['potongan_pph21'],
            'absensi' => $perhitungan['potongan_absensi'],
            'kasbon' => $perhitungan['potongan_kasbon'],
            'lainnya' => $perhitungan['potongan_lainnya']
        ];
        
        // Hitung kehadiran
        $data['kehadiran'] = [
            'hari_kerja' => $perhitungan['total_hari_kerja'],
            'hadir' => $perhitungan['total_hadir'],
            'izin' => $perhitungan['total_izin'],
            'sakit' => $perhitungan['total_sakit'],
            'cuti' => $perhitungan['total_cuti'],
            'alpha' => $perhitungan['total_alpha'],
            'terlambat' => $perhitungan['total_terlambat'],
            'lembur' => $perhitungan['jam_lembur']
        ];
        
        return view('accounting/penggajian/slip-gaji-laporan/slip-gaji/view', $data);
    }

    /**
     * Print slip gaji
     */
    public function print($id)
    {
        $perhitungan = $this->perhitunganModel->getWithDetails($id);
        
        if (!$perhitungan) {
            return redirect()->to('accounting/penggajian/slip-gaji-laporan')
                ->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        $data['perhitungan'] = $perhitungan;
        $data['perusahaan'] = $this->perusahaanModel->find(1);
        
        // Hitung komponen pendapatan detail
        $data['pendapatanDetail'] = [
            'gaji_pokok' => $perhitungan['gaji_pokok'],
            'tunjangan_jabatan' => $perhitungan['tunjangan_jabatan'],
            'tunjangan_makan' => $perhitungan['tunjangan_makan'],
            'tunjangan_transport' => $perhitungan['tunjangan_transport'],
            'tunjangan_lainnya' => $perhitungan['tunjangan_lainnya'],
            'upah_lembur' => $perhitungan['upah_lembur']
        ];
        
        // Hitung komponen potongan detail
        $data['potonganDetail'] = [
            'bpjs_kes' => $perhitungan['potongan_bpjs_kes'],
            'bpjs_tk' => $perhitungan['potongan_bpjs_tk'],
            'pph21' => $perhitungan['potongan_pph21'],
            'absensi' => $perhitungan['potongan_absensi'],
            'kasbon' => $perhitungan['potongan_kasbon'],
            'lainnya' => $perhitungan['potongan_lainnya']
        ];
        
        // Hitung kehadiran
        $data['kehadiran'] = [
            'hari_kerja' => $perhitungan['total_hari_kerja'],
            'hadir' => $perhitungan['total_hadir'],
            'izin' => $perhitungan['total_izin'],
            'sakit' => $perhitungan['total_sakit'],
            'cuti' => $perhitungan['total_cuti'],
            'alpha' => $perhitungan['total_alpha'],
            'terlambat' => $perhitungan['total_terlambat'],
            'lembur' => $perhitungan['jam_lembur']
        ];
        
        return view('accounting/penggajian/slip-gaji-laporan/slip-gaji/print', $data);
    }

    /**
     * Generate PDF slip gaji
     */
    public function pdf($id)
    {
        $perhitungan = $this->perhitunganModel->getWithDetails($id);
        
        if (!$perhitungan) {
            return redirect()->to('accounting/penggajian/slip-gaji-laporan')
                ->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        $data['perhitungan'] = $perhitungan;
        $data['perusahaan'] = $this->perusahaanModel->find(1);
        
        // Hitung komponen pendapatan detail
        $data['pendapatanDetail'] = [
            'gaji_pokok' => $perhitungan['gaji_pokok'],
            'tunjangan_jabatan' => $perhitungan['tunjangan_jabatan'],
            'tunjangan_makan' => $perhitungan['tunjangan_makan'],
            'tunjangan_transport' => $perhitungan['tunjangan_transport'],
            'tunjangan_lainnya' => $perhitungan['tunjangan_lainnya'],
            'upah_lembur' => $perhitungan['upah_lembur']
        ];
        
        // Hitung komponen potongan detail
        $data['potonganDetail'] = [
            'bpjs_kes' => $perhitungan['potongan_bpjs_kes'],
            'bpjs_tk' => $perhitungan['potongan_bpjs_tk'],
            'pph21' => $perhitungan['potongan_pph21'],
            'absensi' => $perhitungan['potongan_absensi'],
            'kasbon' => $perhitungan['potongan_kasbon'],
            'lainnya' => $perhitungan['potongan_lainnya']
        ];
        
        // Hitung kehadiran
        $data['kehadiran'] = [
            'hari_kerja' => $perhitungan['total_hari_kerja'],
            'hadir' => $perhitungan['total_hadir'],
            'izin' => $perhitungan['total_izin'],
            'sakit' => $perhitungan['total_sakit'],
            'cuti' => $perhitungan['total_cuti'],
            'alpha' => $perhitungan['total_alpha'],
            'terlambat' => $perhitungan['total_terlambat'],
            'lembur' => $perhitungan['jam_lembur']
        ];
        
        $html = $this->generateSlipGajiHtml($data);
        
        try {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $filename = 'Slip_Gaji_' . $perhitungan['nomor_perhitungan'] . '_' . $perhitungan['nama_karyawan'] . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Generate PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Batch print slip gaji
     */
    public function batchPrint()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $karyawanIds = $this->request->getGet('karyawan_ids');
        
        if ($karyawanIds) {
            $karyawanIds = explode(',', $karyawanIds);
        }
        
        $slipQuery = $this->perhitunganModel->select('
                penggajian_perhitungan.*,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                karyawan.departemen,
                karyawan.bank,
                karyawan.no_rekening,
                karyawan.nama_rekening
            ')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('penggajian_perhitungan.periode_bulan', $bulan)
            ->where('penggajian_perhitungan.periode_tahun', $tahun)
            ->where('penggajian_perhitungan.status', 'Disetujui')
            ->where('penggajian_perhitungan.deleted_at IS NULL');
        
        if ($karyawanIds) {
            $slipQuery->whereIn('penggajian_perhitungan.karyawan_id', $karyawanIds);
        }
        
        $slipGaji = $slipQuery->orderBy('karyawan.nama_lengkap', 'ASC')->findAll();
        
        if (empty($slipGaji)) {
            return redirect()->back()->with('error', 'Tidak ada slip gaji untuk periode ini');
        }
        
        $data['slipGaji'] = $slipGaji;
        $data['perusahaan'] = $this->perusahaanModel->find(1);
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        
        return view('accounting/penggajian/slip-gaji-laporan/slip-gaji/batch-print', $data);
    }

    /**
     * Laporan penggajian per periode
     */
    public function laporanPeriode()
    {
        $data['title'] = 'Laporan Penggajian Periode';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        if ($bulan) {
            // Data perhitungan per periode
            $data['perhitungan'] = $this->perhitunganModel->getByPeriode($bulan, $tahun, 'Disetujui');
            
            // Ringkasan periode
            $data['ringkasan'] = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
            
            // Rekap per departemen
            $data['rekapDepartemen'] = $this->perhitunganModel->getRekapPerDepartemen($bulan, $tahun);
            
            // Data perusahaan
            $data['perusahaan'] = $this->perusahaanModel->find(1);
        } else {
            $data['perhitungan'] = [];
            $data['ringkasan'] = [];
            $data['rekapDepartemen'] = [];
        }
        
        return view('accounting/penggajian/slip-gaji-laporan/laporan/periode', $data);
    }

    /**
     * Laporan per karyawan
     */
    public function laporanKaryawan($karyawanId)
    {
        $data['title'] = 'Laporan Penggajian Karyawan';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $karyawan = $this->karyawanModel->find($karyawanId);
        
        if (!$karyawan) {
            return redirect()->to('accounting/penggajian/slip-gaji-laporan')
                ->with('error', 'Karyawan tidak ditemukan');
        }
        
        $data['karyawan'] = $karyawan;
        $data['tahun'] = $tahun;
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil riwayat gaji 12 bulan terakhir
        $riwayat = $this->perhitunganModel->where('karyawan_id', $karyawanId)
            ->where('periode_tahun', $tahun)
            ->where('status', 'Disetujui')
            ->orderBy('periode_bulan', 'ASC')
            ->findAll();
        
        $data['riwayat'] = $riwayat;
        
        // Ringkasan tahunan
        $data['ringkasanTahunan'] = [
            'total_pendapatan' => array_sum(array_column($riwayat, 'total_pendapatan')),
            'total_potongan' => array_sum(array_column($riwayat, 'total_potongan')),
            'total_gaji_bersih' => array_sum(array_column($riwayat, 'gaji_bersih')),
            'rata_rata_gaji' => count($riwayat) > 0 ? array_sum(array_column($riwayat, 'gaji_bersih')) / count($riwayat) : 0
        ];
        
        // Data perusahaan
        $data['perusahaan'] = $this->perusahaanModel->find(1);
        
        return view('accounting/penggajian/slip-gaji-laporan/laporan/karyawan', $data);
    }

    /**
     * Rekap gaji per periode
     */
    public function rekapGaji()
    {
        $data['title'] = 'Rekap Gaji Per Periode';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $data['tahun'] = $tahun;
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil rekap per bulan
        $rekap = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $ringkasan = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
            $rekap[$bulan] = [
                'bulan' => $bulan,
                'nama_bulan' => $this->getNamaBulan($bulan),
                'jumlah_karyawan' => $ringkasan['jumlah_karyawan'] ?? 0,
                'total_gaji_pokok' => $ringkasan['total_gaji_pokok'] ?? 0,
                'total_tunjangan' => ($ringkasan['total_tunjangan_jabatan'] ?? 0) + 
                                     ($ringkasan['total_tunjangan_makan'] ?? 0) + 
                                     ($ringkasan['total_tunjangan_transport'] ?? 0),
                'total_upah_lembur' => $ringkasan['total_upah_lembur'] ?? 0,
                'total_pendapatan' => $ringkasan['total_pendapatan'] ?? 0,
                'total_potongan' => $ringkasan['total_potongan'] ?? 0,
                'total_gaji_bersih' => $ringkasan['total_gaji_bersih'] ?? 0
            ];
        }
        
        $data['rekap'] = $rekap;
        
        // Total tahunan
        $data['totalTahunan'] = [
            'total_karyawan' => array_sum(array_column($rekap, 'jumlah_karyawan')),
            'total_gaji_pokok' => array_sum(array_column($rekap, 'total_gaji_pokok')),
            'total_tunjangan' => array_sum(array_column($rekap, 'total_tunjangan')),
            'total_upah_lembur' => array_sum(array_column($rekap, 'total_upah_lembur')),
            'total_pendapatan' => array_sum(array_column($rekap, 'total_pendapatan')),
            'total_potongan' => array_sum(array_column($rekap, 'total_potongan')),
            'total_gaji_bersih' => array_sum(array_column($rekap, 'total_gaji_bersih'))
        ];
        
        // Data perusahaan
        $data['perusahaan'] = $this->perusahaanModel->find(1);
        
        return view('accounting/penggajian/slip-gaji-laporan/laporan/rekap', $data);
    }

    /**
     * Export slip gaji ke Excel
     */
    public function exportExcel()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $karyawanId = $this->request->getGet('karyawan_id');
        
        $slipQuery = $this->perhitunganModel->select('
                penggajian_perhitungan.*,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                karyawan.departemen,
                karyawan.bank,
                karyawan.no_rekening,
                karyawan.nama_rekening
            ')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('penggajian_perhitungan.periode_bulan', $bulan)
            ->where('penggajian_perhitungan.periode_tahun', $tahun)
            ->where('penggajian_perhitungan.status', 'Disetujui')
            ->where('penggajian_perhitungan.deleted_at IS NULL');
        
        if ($karyawanId) {
            $slipQuery->where('penggajian_perhitungan.karyawan_id', $karyawanId);
        }
        
        $slipGaji = $slipQuery->orderBy('karyawan.nama_lengkap', 'ASC')->findAll();
        
        if (empty($slipGaji)) {
            return redirect()->back()->with('error', 'Tidak ada slip gaji untuk periode ini');
        }
        
        try {
            $spreadsheet = new Spreadsheet();
            
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Slip Gaji " . $this->getNamaBulan($bulan) . " $tahun")
                ->setSubject("Slip Gaji");
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Slip Gaji');
            
            // Header
            $sheet->mergeCells('A1:P1');
            $sheet->setCellValue('A1', 'SLIP GAJI KARYAWAN');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:P2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:P3');
            $sheet->setCellValue('A3', 'Periode: ' . $this->getNamaBulan($bulan) . ' ' . $tahun);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A4:P4');
            $sheet->setCellValue('A4', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'NIK',
                'C' => 'Nama Karyawan',
                'D' => 'Jabatan',
                'E' => 'Departemen',
                'F' => 'Gaji Pokok',
                'G' => 'Tunjangan Jabatan',
                'H' => 'Tunjangan Makan',
                'I' => 'Tunjangan Transport',
                'J' => 'Upah Lembur',
                'K' => 'Total Pendapatan',
                'L' => 'Potongan BPJS',
                'M' => 'Potongan PPh21',
                'N' => 'Potongan Lain',
                'O' => 'Total Potongan',
                'P' => 'Gaji Bersih'
            ];
            
            $startRow = 6;
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':P' . $startRow;
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            
            // Isi data
            $row = $startRow + 1;
            $no = 1;
            foreach ($slipGaji as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item['nik']);
                $sheet->setCellValue('C' . $row, $item['nama_lengkap']);
                $sheet->setCellValue('D' . $row, $item['jabatan']);
                $sheet->setCellValue('E' . $row, $item['departemen']);
                $sheet->setCellValue('F' . $row, $item['gaji_pokok']);
                $sheet->setCellValue('G' . $row, $item['tunjangan_jabatan']);
                $sheet->setCellValue('H' . $row, $item['tunjangan_makan']);
                $sheet->setCellValue('I' . $row, $item['tunjangan_transport']);
                $sheet->setCellValue('J' . $row, $item['upah_lembur']);
                $sheet->setCellValue('K' . $row, $item['total_pendapatan']);
                $sheet->setCellValue('L' . $row, ($item['potongan_bpjs_kes'] + $item['potongan_bpjs_tk']));
                $sheet->setCellValue('M' . $row, $item['potongan_pph21']);
                $sheet->setCellValue('N' . $row, ($item['potongan_absensi'] + $item['potongan_kasbon'] + $item['potongan_lainnya']));
                $sheet->setCellValue('O' . $row, $item['total_potongan']);
                $sheet->setCellValue('P' . $row, $item['gaji_bersih']);
                
                // Format currency
                foreach (range('F', 'P') as $col) {
                    $sheet->getStyle($col . $row)->getNumberFormat()
                        ->setFormatCode('#,##0');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':P' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Footer
            $footerRow = $lastRow + 2;
            $sheet->mergeCells('A' . $footerRow . ':P' . $footerRow);
            $totalGaji = array_sum(array_column($slipGaji, 'gaji_bersih'));
            $sheet->setCellValue('A' . $footerRow, 'Total Karyawan: ' . count($slipGaji) . ' | Total Gaji Bersih: Rp ' . number_format($totalGaji, 0, ',', '.'));
            $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Output file
            $filename = 'Slip_Gaji_' . $this->getNamaBulan($bulan) . '_' . $tahun . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export Excel error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Generate slip gaji HTML untuk PDF
     */
    private function generateSlipGajiHtml($data)
    {
        $p = $data['perhitungan'];
        $perusahaan = $data['perusahaan'];
        
        $pendapatan = $data['pendapatanDetail'];
        $potongan = $data['potonganDetail'];
        $kehadiran = $data['kehadiran'];
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Slip Gaji - ' . $p['nama_karyawan'] . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 10px;
                    margin: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                }
                .company-name {
                    font-size: 18px;
                    font-weight: bold;
                }
                .company-address {
                    font-size: 8px;
                    color: #666;
                }
                .slip-title {
                    font-size: 14px;
                    font-weight: bold;
                    margin-top: 10px;
                }
                .periode {
                    font-size: 12px;
                    margin-bottom: 15px;
                }
                .info-karyawan {
                    width: 100%;
                    margin-bottom: 15px;
                    border: 1px solid #ddd;
                    border-collapse: collapse;
                }
                .info-karyawan td {
                    padding: 5px;
                    border: 1px solid #ddd;
                }
                .info-karyawan td:first-child {
                    width: 30%;
                    background-color: #f8f9fa;
                    font-weight: bold;
                }
                .section-title {
                    font-weight: bold;
                    background-color: #4F81BD;
                    color: white;
                    padding: 5px;
                    margin-top: 10px;
                    margin-bottom: 10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 5px;
                }
                th {
                    background-color: #f2f2f2;
                    text-align: center;
                    font-weight: bold;
                }
                .text-right {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .total-row {
                    font-weight: bold;
                    background-color: #f8f9fa;
                }
                .signature {
                    margin-top: 30px;
                    display: flex;
                    justify-content: space-between;
                }
                .signature-item {
                    text-align: center;
                    width: 45%;
                }
                .signature-line {
                    margin-top: 40px;
                    border-top: 1px solid #000;
                    width: 100%;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 8px;
                    text-align: center;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="company-name">' . ($perusahaan['nama_perusahaan'] ?? 'PT. CIPTA DUTA WACANA') . '</div>
                <div class="company-address">' . ($perusahaan['alamat'] ?? 'Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41, Jakarta Selatan') . '</div>
                <div class="slip-title">SLIP GAJI</div>
                <div class="periode">Periode: ' . $this->getNamaBulan($p['periode_bulan']) . ' ' . $p['periode_tahun'] . '</div>
            </div>
            
            <table class="info-karyawan">
                <tr>
                    <td>NIK / NIP</td>
                    <td>' . ($p['nomor_karyawan'] ?? $p['nik'] ?? '-') . '</td>
                    <td>Jabatan</td>
                    <td>' . ($p['jabatan'] ?? '-') . '</td>
                </tr>
                <tr>
                    <td>Nama Karyawan</td>
                    <td>' . $p['nama_karyawan'] . '</td>
                    <td>Departemen</td>
                    <td>' . ($p['departemen'] ?? '-') . '</td>
                </tr>
                <tr>
                    <td>Tanggal Masuk</td>
                    <td>' . ($p['tanggal_masuk'] ?? '-') . '</td>
                    <td>Status Karyawan</td>
                    <td>' . ($p['status_karyawan'] ?? '-') . '</td>
                </tr>
            </table>
            
            <div class="section-title">DETAIL PENDAPATAN</div>
            <table>
                <thead>
                    <tr><th>Komponen</th><th class="text-right">Jumlah (Rp)</th></tr>
                </thead>
                <tbody>
                    <tr><td>Gaji Pokok</td><td class="text-right">' . number_format($pendapatan['gaji_pokok'], 0, ',', '.') . '</td></tr>
                    <tr><td>Tunjangan Jabatan</td><td class="text-right">' . number_format($pendapatan['tunjangan_jabatan'], 0, ',', '.') . '</td></tr>
                    <tr><td>Tunjangan Makan</td><td class="text-right">' . number_format($pendapatan['tunjangan_makan'], 0, ',', '.') . '</td></tr>
                    <tr><td>Tunjangan Transport</td><td class="text-right">' . number_format($pendapatan['tunjangan_transport'], 0, ',', '.') . '</td></tr>
                    <tr><td>Tunjangan Lainnya</td><td class="text-right">' . number_format($pendapatan['tunjangan_lainnya'], 0, ',', '.') . '</td></tr>
                    <tr><td>Upah Lembur</td><td class="text-right">' . number_format($pendapatan['upah_lembur'], 0, ',', '.') . '</td></tr>
                    <tr class="total-row"><td>TOTAL PENDAPATAN</td><td class="text-right">' . number_format($p['total_pendapatan'], 0, ',', '.') . '</td></tr>
                </tbody>
            </table>
            
            <div class="section-title">DETAIL POTONGAN</div>
            <table>
                <thead>
                    <tr><th>Komponen</th><th class="text-right">Jumlah (Rp)</th></tr>
                </thead>
                <tbody>
                    <tr><td>BPJS Kesehatan</td><td class="text-right">' . number_format($potongan['bpjs_kes'], 0, ',', '.') . '</td></tr>
                    <tr><td>BPJS Ketenagakerjaan</td><td class="text-right">' . number_format($potongan['bpjs_tk'], 0, ',', '.') . '</td></tr>
                    <tr><td>PPh 21</td><td class="text-right">' . number_format($potongan['pph21'], 0, ',', '.') . '</td></tr>
                    <tr><td>Potongan Absensi</td><td class="text-right">' . number_format($potongan['absensi'], 0, ',', '.') . '</td></tr>
                    <tr><td>Potongan Kasbon</td><td class="text-right">' . number_format($potongan['kasbon'], 0, ',', '.') . '</td></tr>
                    <tr><td>Potongan Lainnya</td><td class="text-right">' . number_format($potongan['lainnya'], 0, ',', '.') . '</td></tr>
                    <tr class="total-row"><td>TOTAL POTONGAN</td><td class="text-right">' . number_format($p['total_potongan'], 0, ',', '.') . '</td></tr>
                </tbody>
            </table>
            
            <table>
                <tr class="total-row">
                    <td style="width: 70%;"><strong>GAJI BERSIH</strong></td>
                    <td class="text-right" style="background-color: #e8f0fe;"><strong>Rp ' . number_format($p['gaji_bersih'], 0, ',', '.') . '</strong></td>
                </tr>
            </table>';
            
        // Tampilkan data kehadiran jika ada
        if ($kehadiran['hari_kerja'] > 0 || $kehadiran['hadir'] > 0) {
            $html .= '
            <div class="section-title">RINGKASAN KEHADIRAN</div>
            <table>
                <thead>
                    <tr>
                        <th>Hari Kerja</th>
                        <th>Hadir</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Cuti</th>
                        <th>Alpha</th>
                        <th>Terlambat (hari)</th>
                        <th>Lembur (jam)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td>' . $kehadiran['hari_kerja'] . '</td>
                        <td>' . $kehadiran['hadir'] . '</td>
                        <td>' . $kehadiran['izin'] . '</td>
                        <td>' . $kehadiran['sakit'] . '</td>
                        <td>' . $kehadiran['cuti'] . '</td>
                        <td>' . $kehadiran['alpha'] . '</td>
                        <td>' . $kehadiran['terlambat'] . '</td>
                        <td>' . number_format($kehadiran['lembur'], 1) . '</td>
                    </tr>
                </tbody>
            </table>';
        }
        
        $html .= '
            <div class="signature">
                <div class="signature-item">
                    <div>Hormat Kami,</div>
                    <div class="signature-line"></div>
                    <div>HRD / Accounting</div>
                </div>
                <div class="signature-item">
                    <div>Diterima oleh,</div>
                    <div class="signature-line"></div>
                    <div>' . $p['nama_karyawan'] . '</div>
                </div>
            </div>
            
            <div class="footer">
                Slip gaji ini adalah bukti resmi pembayaran gaji periode ' . $this->getNamaBulan($p['periode_bulan']) . ' ' . $p['periode_tahun'] . '<br>
                Dicetak pada: ' . date('d/m/Y H:i:s') . '
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * AJAX: Get slip gaji by periode
     */
    public function ajaxGetSlipsByPeriode()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        $slips = $this->perhitunganModel->select('
                penggajian_perhitungan.id,
                penggajian_perhitungan.nomor_perhitungan,
                penggajian_perhitungan.gaji_bersih,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan
            ')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('status', 'Disetujui')
            ->findAll();
        
        return $this->response->setJSON($slips);
    }

    /**
     * AJAX: Get summary by periode
     */
    public function ajaxGetSummaryByPeriode()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        $summary = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        return $this->response->setJSON($summary);
    }

    /**
     * Get tahun options
     */
    private function getTahunOptions()
    {
        $tahunSekarang = date('Y');
        $options = [];
        
        for ($i = $tahunSekarang - 2; $i <= $tahunSekarang + 1; $i++) {
            $options[] = $i;
        }
        
        return $options;
    }

    /**
     * Get bulan options
     */
    private function getBulanOptions()
    {
        return [
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
    }

    /**
     * Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanNames[$bulan] ?? '';
    }
}