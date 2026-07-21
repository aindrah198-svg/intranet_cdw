<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\TarifPajakModel;
use App\Models\Accounting\FakturPajakModel;
use App\Models\Accounting\PpnMasukanModel;
use App\Models\Accounting\PpnKeluaranModel;
use App\Models\Accounting\PphBadanModel;
use App\Models\Accounting\SetoranPajakModel;
use App\Models\Accounting\ArsipPajakModel;

class ManajemenPajak extends BaseController
{
    protected $tarifPajakModel;
    protected $fakturPajakModel;
    protected $ppnMasukanModel;
    protected $ppnKeluaranModel;
    protected $pphBadanModel;
    protected $setoranPajakModel;
    protected $arsipPajakModel;
    protected $db;

    public function __construct()
    {
        $this->tarifPajakModel = new TarifPajakModel();
        $this->fakturPajakModel = new FakturPajakModel();
        $this->ppnMasukanModel = new PpnMasukanModel();
        $this->ppnKeluaranModel = new PpnKeluaranModel();
        $this->pphBadanModel = new PphBadanModel();
        $this->setoranPajakModel = new SetoranPajakModel();
        $this->arsipPajakModel = new ArsipPajakModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Dashboard Manajemen Pajak
     */
    public function index()
    {
        $data['title'] = 'Dashboard Manajemen Pajak';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        // Statistik Tarif Pajak
        $data['statsTarif'] = $this->tarifPajakModel->getStats();
        $data['tarifPpn'] = $this->tarifPajakModel->getCurrentRate('PPN');
        $data['tarifPphBadan'] = $this->tarifPajakModel->getCurrentRate('PPh Badan');
        
        // Statistik Faktur Pajak
        $data['statsFaktur'] = $this->fakturPajakModel->getStats($tahun);
        
        // Statistik PPN
        $data['statsPpnMasukan'] = $this->ppnMasukanModel->getStats($tahun);
        $data['statsPpnKeluaran'] = $this->ppnKeluaranModel->getStats($tahun);
        
        // PPN untuk masa pajak tertentu
        $ppnTerutang = $this->ppnKeluaranModel->getPpnTerutang($bulan, $tahun);
        $data['ppn_masukan_periode'] = $ppnTerutang['total_ppn_masukan'];
        $data['ppn_keluaran_periode'] = $ppnTerutang['total_ppn_keluaran'];
        $data['ppn_kurang_bayar'] = $ppnTerutang['ppn_kurang_bayar'];
        
        // Statistik PPh Badan
        $data['statsPphBadan'] = $this->pphBadanModel->getStats($tahun);
        
        // Statistik Setoran Pajak
        $data['statsSetoran'] = $this->setoranPajakModel->getStats($tahun);
        $data['setoranPerJenis'] = $this->setoranPajakModel->getTotalPerJenis($tahun);
        
        // Statistik Arsip Pajak
        $data['statsArsip'] = $this->arsipPajakModel->getStats($tahun);
        $data['arsipTerbaru'] = $this->arsipPajakModel->getLatest(5);
        
        // Grafik data
        $data['chartPpnPerMasa'] = $this->getPpnChartData($tahun);
        $data['chartSetoranPerJenis'] = $this->getSetoranChartData($tahun);
        
        // Peringatan
        $data['expiringTarif'] = $this->tarifPajakModel->getExpiringSoon(30);
        $data['upcomingTarif'] = $this->tarifPajakModel->getUpcomingSoon(30);
        $data['expiringPpn'] = $this->ppnMasukanModel->getExpiringSoon(3);
        
        return view('accounting/manajemen-pajak/dashboard', $data);
    }

    /**
     * Filter dashboard by tahun/bulan
     */
    public function filter()
    {
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        
        return redirect()->to('accounting/manajemen-pajak?tahun=' . $tahun . '&bulan=' . $bulan);
    }

    /**
     * Menu PPN (halaman utama PPN)
     */
    public function ppn()
    {
        $data['title'] = 'Manajemen PPN';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        // Ringkasan PPN per masa
        $data['ringkasanPpn'] = $this->fakturPajakModel->getTotalPpnPerMasa($tahun);
        
        // PPN untuk masa yang dipilih
        $ppnTerutang = $this->ppnKeluaranModel->getPpnTerutang($bulan, $tahun);
        $data['ppn_masukan'] = $ppnTerutang['total_ppn_masukan'];
        $data['ppn_keluaran'] = $ppnTerutang['total_ppn_keluaran'];
        $data['ppn_kurang_bayar'] = $ppnTerutang['ppn_kurang_bayar'];
        
        // Statistik
        $data['statsFaktur'] = $this->fakturPajakModel->getStats($tahun);
        $data['statsPpnMasukan'] = $this->ppnMasukanModel->getStats($tahun);
        $data['statsPpnKeluaran'] = $this->ppnKeluaranModel->getStats($tahun);
        
        // Tarif PPN aktif
        $data['tarifPpn'] = $this->tarifPajakModel->getCurrentRate('PPN');
        
        return view('accounting/manajemen-pajak/ppn/index', $data);
    }

    /**
     * Menu PPh (halaman utama PPh)
     */
    public function pph()
    {
        $data['title'] = 'Manajemen PPh';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $data['tahun'] = $tahun;
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ringkasan PPh per tahun
        $data['ringkasanPph'] = $this->pphBadanModel->getRingkasanPerTahun();
        
        // PPh Badan tahun ini
        $data['pphTahunIni'] = $this->pphBadanModel->getTahunanByTahun($tahun);
        
        // PPh 21 rates
        $data['pph21Rates'] = $this->tarifPajakModel->getPph21Rates();
        
        // PPh 23 rates
        $data['pph23Rates'] = $this->tarifPajakModel->getPph23Rates();
        
        // Tarif PPh Badan aktif
        $data['tarifPphBadan'] = $this->tarifPajakModel->getCurrentRate('PPh Badan');
        
        return view('accounting/manajemen-pajak/pph/index', $data);
    }

    /**
     * Menu Laporan Pajak
     */
    public function laporan()
    {
        $data['title'] = 'Laporan Pajak';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        // SPT Masa PPN
        $data['sptMasaPpn'] = $this->fakturPajakModel->getTotalPpnForSpt($bulan, $tahun);
        
        // SPT Masa PPh 21 (simulasi)
        $data['sptMasaPph21'] = $this->getSimulasiPph21($tahun);
        
        // SPT Tahunan PPh Badan
        $data['sptTahunanPph'] = $this->pphBadanModel->getSptTahunanSummary($tahun);
        
        // Rekapitulasi setoran pajak
        $data['rekapSetoran'] = $this->setoranPajakModel->getTotalPerMasa($tahun);
        
        return view('accounting/manajemen-pajak/laporan/index', $data);
    }

    /**
     * Export laporan pajak
     */
    public function exportLaporan()
    {
        $jenis = $this->request->getGet('jenis');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        
        switch ($jenis) {
            case 'spt_ppn':
                return $this->exportSptPpn($bulan, $tahun);
            case 'spt_pph21':
                return $this->exportSptPph21($tahun);
            case 'spt_pph_badan':
                return $this->exportSptPphBadan($tahun);
            default:
                return redirect()->back()->with('error', 'Jenis laporan tidak valid');
        }
    }

    /**
     * Export SPT Masa PPN
     */
    private function exportSptPpn($bulan, $tahun)
    {
        $data = $this->fakturPajakModel->getTotalPpnForSpt($bulan, $tahun);
        $ppnMasukan = $this->ppnMasukanModel->getTotalForSpt($bulan, $tahun);
        $ppnKeluaran = $this->ppnKeluaranModel->getTotalForSpt($bulan, $tahun);
        
        $html = $this->generateSptPpnHtml($bulan, $tahun, $data, $ppnMasukan, $ppnKeluaran);
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'SPT_Masa_PPN_' . $bulan . '_' . $tahun . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit();
    }

    /**
     * Export SPT Masa PPh 21
     */
    private function exportSptPph21($tahun)
    {
        $html = $this->generateSptPph21Html($tahun);
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'SPT_Masa_PPh21_Tahun_' . $tahun . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit();
    }

    /**
     * Export SPT Tahunan PPh Badan
     */
    private function exportSptPphBadan($tahun)
    {
        $data = $this->pphBadanModel->getSptTahunanSummary($tahun);
        
        $html = $this->generateSptPphBadanHtml($tahun, $data);
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'SPT_Tahunan_PPh_Badan_' . $tahun . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit();
    }

    /**
     * Generate HTML SPT Masa PPN
     */
    private function generateSptPpnHtml($bulan, $tahun, $data, $ppnMasukan, $ppnKeluaran)
    {
        $namaBulan = $this->getNamaBulan($bulan);
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>SPT Masa PPN ' . $namaBulan . ' ' . $tahun . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11px;
                    margin: 20px;
                }
                h1, h2 {
                    text-align: center;
                    margin-bottom: 5px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    vertical-align: top;
                }
                th {
                    background-color: #f0f0f0;
                    text-align: center;
                    font-weight: bold;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 9px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>SURAT PEMBERITAHUAN (SPT) MASA PPN</h1>
                <h2>Masa Pajak: ' . $namaBulan . ' ' . $tahun . '</h2>
                <h3>PT. CIPTA DUTA WACANA</h3>
                <p>NPWP: 01.234.567.8-123.000</p>
            </div>
            
            <h3>A. PENGHITUNGAN PPN KURANG/LEBIH BAYAR</h3>
            <table>
                <tr>
                    <th width="70%">Uraian</th>
                    <th width="30%">Jumlah (Rp)</th>
                </tr>
                <tr>
                    <td>I. PPN KELUARAN</td>
                    <td class="text-end">' . number_format($ppnKeluaran['total_ppn'], 0) . '</td>
                </tr>
                <tr>
                    <td>II. PPN MASUKAN YANG DAPAT DIKREDITKAN</td>
                    <td class="text-end">' . number_format($ppnMasukan['total_ppn'], 0) . '</td>
                </tr>
                <tr>
                    <td><strong>III. PPN KURANG/LEBIH BAYAR (I - II)</strong></td>
                    <td class="text-end"><strong>' . number_format($ppnKeluaran['total_ppn'] - $ppnMasukan['total_ppn'], 0) . '</strong></td>
                </tr>
            </table>
            
            <h3>B. RINCIAN PPN KELUARAN</h3>
            <table>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nomor Faktur</th>
                    <th width="25%">Nama Pengusaha</th>
                    <th width="25%">NPWP</th>
                    <th width="25%">Nilai PPN (Rp)</th>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">*Data diambil dari modul Faktur Pajak*</td>
                    <td class="text-end">' . number_format($ppnKeluaran['total_ppn'], 0) . '</td>
                </tr>
            </table>
            
            <h3>C. RINCIAN PPN MASUKAN</h3>
            <table>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nomor Faktur</th>
                    <th width="25%">Nama Supplier</th>
                    <th width="25%">NPWP Supplier</th>
                    <th width="25%">Nilai PPN (Rp)</th>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">*Data diambil dari modul PPN Masukan*</td>
                    <td class="text-end">' . number_format($ppnMasukan['total_ppn'], 0) . '</td>
                </tr>
            </table>
            
            <div class="footer">
                <table style="border: none;">
                    <tr>
                        <td style="border: none; width: 60%;">
                            Demikian SPT Masa PPN ini dibuat dengan sebenarnya.<br><br><br>
                            _________________________<br>
                            (Nama dan Tanda Tangan)
                        </td>
                        <td style="border: none; text-align: right;">
                            ' . date('d/m/Y') . '<br>
                            Jakarta<br><br><br>
                            _________________________<br>
                            Stempel Perusahaan
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * Generate HTML SPT Masa PPh 21
     */
    private function generateSptPph21Html($tahun)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>SPT Masa PPh 21 ' . $tahun . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11px;
                    margin: 20px;
                }
                h1, h2 {
                    text-align: center;
                    margin-bottom: 5px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    vertical-align: top;
                }
                th {
                    background-color: #f0f0f0;
                    text-align: center;
                    font-weight: bold;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 9px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>SURAT PEMBERITAHUAN (SPT) MASA PPh PASAL 21</h1>
                <h2>Masa Pajak: ' . $this->getNamaBulan(date('m')) . ' ' . $tahun . '</h2>
                <h3>PT. CIPTA DUTA WACANA</h3>
                <p>NPWP: 01.234.567.8-123.000</p>
            </div>
            
            <h3>PENGHITUNGAN PPh PASAL 21</h3>
            <table>
                <tr>
                    <th width="70%">Uraian</th>
                    <th width="30%">Jumlah (Rp)</th>
                </tr>
                <tr>
                    <td>Jumlah Penghasilan Bruto</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td>Pengurangan (Biaya Jabatan, Iuran Pensiun)</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td>Penghasilan Neto Sebulan</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td>Penghasilan Neto Disetahunkan</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td>PTKP (K/0)</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td>PKP</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td>PPh Pasal 21 Terutang</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td>PPh Pasal 21 Dipotong</td>
                    <td class="text-end">0</td>
                </tr>
                <tr>
                    <td><strong>PPh Pasal 21 Kurang/(Lebih) Bayar</strong></td>
                    <td class="text-end"><strong>0</strong></td>
                </tr>
            </table>
            
            <div class="footer">
                <table style="border: none;">
                    <tr>
                        <td style="border: none; width: 60%;">
                            Demikian SPT Masa PPh 21 ini dibuat dengan sebenarnya.<br><br><br>
                            _________________________<br>
                            (Nama dan Tanda Tangan)
                        </td>
                        <td style="border: none; text-align: right;">
                            ' . date('d/m/Y') . '<br>
                            Jakarta<br><br><br>
                            _________________________<br>
                            Stempel Perusahaan
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * Generate HTML SPT Tahunan PPh Badan
     */
    private function generateSptPphBadanHtml($tahun, $data)
    {
        if (!$data) {
            $data = [
                'penghasilan_bruto' => 0,
                'biaya_fiskal' => 0,
                'penghasilan_neto_fiskal' => 0,
                'kompensasi_kerugian' => 0,
                'pkp' => 0,
                'tarif' => 22,
                'pph_terutang' => 0,
                'total_kredit_pajak_aktual' => 0,
                'pph_kurang_bayar_aktual' => 0
            ];
        }
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>SPT Tahunan PPh Badan ' . $tahun . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11px;
                    margin: 20px;
                }
                h1, h2 {
                    text-align: center;
                    margin-bottom: 5px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    vertical-align: top;
                }
                th {
                    background-color: #f0f0f0;
                    text-align: center;
                    font-weight: bold;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 9px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>SURAT PEMBERITAHUAN (SPT) TAHUNAN PPh BADAN</h1>
                <h2>Tahun Pajak: ' . $tahun . '</h2>
                <h3>PT. CIPTA DUTA WACANA</h3>
                <p>NPWP: 01.234.567.8-123.000</p>
            </div>
            
            <h3>PENGHITUNGAN PPh BADAN TAHUNAN</h3>
            <table>
                <tr>
                    <th width="70%">Uraian</th>
                    <th width="30%">Jumlah (Rp)</th>
                </tr>
                <tr>
                    <td>Penghasilan Bruto</td>
                    <td class="text-end">' . number_format($data['penghasilan_bruto'], 0) . '</td>
                </tr>
                <tr>
                    <td>Biaya Fiskal</td>
                    <td class="text-end">' . number_format($data['biaya_fiskal'], 0) . '</td>
                </tr>
                <tr>
                    <td><strong>Penghasilan Neto Fiskal</strong></td>
                    <td class="text-end"><strong>' . number_format($data['penghasilan_neto_fiskal'], 0) . '</strong></td>
                </tr>
                <tr>
                    <td>Kompensasi Kerugian</td>
                    <td class="text-end">' . number_format($data['kompensasi_kerugian'], 0) . '</td>
                </tr>
                <tr>
                    <td><strong>Penghasilan Kena Pajak (PKP)</strong></td>
                    <td class="text-end"><strong>' . number_format($data['pkp'], 0) . '</strong></td>
                </tr>
                <tr>
                    <td>Tarif PPh Badan</td>
                    <td class="text-end">' . $data['tarif'] . '%</td>
                </tr>
                <tr>
                    <td><strong>PPh Badan Terutang</strong></td>
                    <td class="text-end"><strong>' . number_format($data['pph_terutang'], 0) . '</strong></td>
                </tr>
                <tr>
                    <td>Kredit Pajak (PPh 22/23/25)</td>
                    <td class="text-end">' . number_format($data['total_kredit_pajak_aktual'], 0) . '</td>
                </tr>
                <tr>
                    <td><strong>PPh Badan Kurang/(Lebih) Bayar</strong></td>
                    <td class="text-end"><strong>' . number_format($data['pph_kurang_bayar_aktual'], 0) . '</strong></td>
                </tr>
            </table>
            
            <div class="footer">
                <table style="border: none;">
                    <tr>
                        <td style="border: none; width: 60%;">
                            Demikian SPT Tahunan PPh Badan ini dibuat dengan sebenarnya.<br><br><br>
                            _________________________<br>
                            (Nama dan Tanda Tangan)
                        </td>
                        <td style="border: none; text-align: right;">
                            ' . date('d/m/Y') . '<br>
                            Jakarta<br><br><br>
                            _________________________<br>
                            Stempel Perusahaan
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * Get data untuk chart PPN per masa
     */
    private function getPpnChartData($tahun)
    {
        $data = [];
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $ppnTerutang = $this->ppnKeluaranModel->getPpnTerutang($bulan, $tahun);
            $data[] = [
                'bulan' => $bulan,
                'nama_bulan' => $this->getNamaBulan($bulan),
                'ppn_masukan' => $ppnTerutang['total_ppn_masukan'],
                'ppn_keluaran' => $ppnTerutang['total_ppn_keluaran']
            ];
        }
        
        return $data;
    }

    /**
     * Get data untuk chart setoran per jenis
     */
    private function getSetoranChartData($tahun)
    {
        return $this->setoranPajakModel->getTotalPerJenis($tahun);
    }

    /**
     * Get simulasi PPh 21
     */
    private function getSimulasiPph21($tahun)
    {
        // Simulasi sederhana
        return [
            'total_pegawai' => 0,
            'total_penghasilan_bruto' => 0,
            'total_pph_terutang' => 0,
            'total_pph_dipotong' => 0
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

    /**
     * Get tahun options untuk dropdown
     */
    private function getTahunOptions()
    {
        $tahunSekarang = date('Y');
        $options = [];
        
        for ($i = $tahunSekarang - 5; $i <= $tahunSekarang + 1; $i++) {
            $options[] = $i;
        }
        
        return array_reverse($options);
    }

    /**
     * Get bulan options untuk dropdown
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
}