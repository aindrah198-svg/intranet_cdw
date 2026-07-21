<?php
// app/Controllers/Accounting/PenggajianProsesPembayaran.php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PenggajianProsesPembayaranModel;
use App\Models\Accounting\PenggajianDetailPembayaranModel;
use App\Models\Accounting\PenggajianPerhitunganModel;
use App\Models\KaryawanModel;
use App\Models\CoaModel;
use App\Models\Accounting\MutasiBankModel;
use App\Models\JurnalModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PenggajianProsesPembayaran extends BaseController
{
    protected $prosesModel;
    protected $detailModel;
    protected $perhitunganModel;
    protected $karyawanModel;
    protected $coaModel;
    protected $mutasiBankModel;
    protected $jurnalModel;
    protected $db;

    public function __construct()
    {
        $this->prosesModel = new PenggajianProsesPembayaranModel();
        $this->detailModel = new PenggajianDetailPembayaranModel();
        $this->perhitunganModel = new PenggajianPerhitunganModel();
        $this->karyawanModel = new KaryawanModel();
        $this->coaModel = new CoaModel();
        $this->mutasiBankModel = new MutasiBankModel();
        $this->jurnalModel = new JurnalModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Daftar proses pembayaran
     */
    public function index()
    {
        $data['title'] = 'Proses Pembayaran Gaji';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $status = $this->request->getGet('status');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['status'] = $status;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['statusOptions'] = ['Draft', 'Diproses', 'Selesai', 'Dibatalkan'];
        
        // Ambil data proses pembayaran
        $builder = $this->prosesModel->select('penggajian_proses_pembayaran.*, coa.kode_akun as kode_akun_bank, coa.nama_akun as nama_akun_bank')
            ->join('coa', 'coa.id = penggajian_proses_pembayaran.coa_bank_id', 'left')
            ->where('penggajian_proses_pembayaran.periode_bulan', $bulan)
            ->where('penggajian_proses_pembayaran.periode_tahun', $tahun)
            ->where('penggajian_proses_pembayaran.deleted_at IS NULL');
        
        if ($status) {
            $builder->where('penggajian_proses_pembayaran.status', $status);
        }
        
        $data['proses'] = $builder->orderBy('penggajian_proses_pembayaran.tanggal_proses', 'DESC')->findAll();
        
        // Ringkasan
        $data['ringkasan'] = $this->prosesModel->getRingkasanPerPeriode($tahun);
        
        // Perhitungan yang sudah disetujui untuk periode ini
        $data['perhitunganTersedia'] = $this->perhitunganModel->getForPayment($bulan, $tahun);
        
        // Total gaji yang siap dibayar
        $data['totalSiapBayar'] = array_sum(array_column($data['perhitunganTersedia'], 'gaji_bersih'));
        
        // COA Bank options
        $data['coaBankOptions'] = $this->prosesModel->getCoaBankOptions();
        
        return view('accounting/penggajian/proses-pembayaran/index', $data);
    }

    /**
     * Form buat proses pembayaran baru
     */
    public function create()
    {
        $data['title'] = 'Buat Proses Pembayaran Gaji';
        
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Cek apakah sudah ada proses untuk periode ini
        if ($this->prosesModel->existsForPeriod($bulan, $tahun)) {
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('error', 'Proses pembayaran untuk periode ' . $this->getNamaBulan($bulan) . ' ' . $tahun . ' sudah ada');
        }
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil perhitungan gaji yang sudah disetujui
        $data['perhitungan'] = $this->perhitunganModel->getForPayment($bulan, $tahun);
        
        // Hitung total
        $data['totalKaryawan'] = count($data['perhitungan']);
        $data['totalNominal'] = array_sum(array_column($data['perhitungan'], 'gaji_bersih'));
        
        // COA Bank options
        $data['coaBankOptions'] = $this->prosesModel->getCoaBankOptions();
        
        // Metode pembayaran options
        $data['metodeOptions'] = ['Transfer Bank', 'Tunai', 'Cek', 'Giro'];
        
        return view('accounting/penggajian/proses-pembayaran/create', $data);
    }

    /**
     * Simpan proses pembayaran baru
     */
    public function store()
    {
        $rules = [
            'nama_proses' => 'required',
            'periode_bulan' => 'required|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
            'periode_tahun' => 'required|numeric|min_length[4]|max_length[4]',
            'tanggal_proses' => 'required|valid_date',
            'tanggal_pembayaran' => 'required|valid_date',
            'metode_pembayaran' => 'required|in_list[Transfer Bank,Tunai,Cek,Giro]',
            'coa_bank_id' => 'permit_empty|is_natural_no_zero'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $bulan = $this->request->getPost('periode_bulan');
        $tahun = $this->request->getPost('periode_tahun');
        
        // Cek duplikasi
        if ($this->prosesModel->existsForPeriod($bulan, $tahun)) {
            return redirect()->back()->withInput()
                ->with('error', 'Proses pembayaran untuk periode ' . $this->getNamaBulan($bulan) . ' ' . $tahun . ' sudah ada');
        }
        
        // Ambil perhitungan gaji yang sudah disetujui
        $perhitungan = $this->perhitunganModel->getForPayment($bulan, $tahun);
        
        if (empty($perhitungan)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tidak ada perhitungan gaji yang siap dibayar untuk periode ini');
        }
        
        // Hitung total
        $totalKaryawan = count($perhitungan);
        $totalNominal = array_sum(array_column($perhitungan, 'gaji_bersih'));
        
        $data = [
            'nama_proses' => $this->request->getPost('nama_proses'),
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'tanggal_proses' => $this->request->getPost('tanggal_proses'),
            'tanggal_pembayaran' => $this->request->getPost('tanggal_pembayaran'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'bank_pengirim' => $this->request->getPost('bank_pengirim'),
            'coa_bank_id' => $this->request->getPost('coa_bank_id') ?: null,
            'total_karyawan' => $totalKaryawan,
            'total_nominal' => $totalNominal,
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'Draft'
        ];
        
        try {
            $this->db->transBegin();
            
            // Insert proses
            $prosesId = $this->prosesModel->insert($data);
            
            if (!$prosesId) {
                throw new \Exception('Gagal menyimpan proses pembayaran');
            }
            
            // Buat detail pembayaran
            foreach ($perhitungan as $item) {
                // Ambil data karyawan
                $karyawan = $this->karyawanModel->find($item['karyawan_id']);
                
                $detailData = [
                    'proses_id' => $prosesId,
                    'perhitungan_id' => $item['id'],
                    'karyawan_id' => $item['karyawan_id'],
                    'nomor_karyawan' => $karyawan['nik'] ?? null,
                    'nama_karyawan' => $karyawan['nama_lengkap'] ?? $item['nama_karyawan'],
                    'bank' => $karyawan['bank'] ?? null,
                    'no_rekening' => $karyawan['no_rekening'] ?? null,
                    'nama_rekening' => $karyawan['nama_rekening'] ?? null,
                    'gaji_pokok' => $item['gaji_pokok'],
                    'total_tunjangan' => ($item['tunjangan_jabatan'] ?? 0) + 
                                        ($item['tunjangan_makan'] ?? 0) + 
                                        ($item['tunjangan_transport'] ?? 0) + 
                                        ($item['tunjangan_lainnya'] ?? 0),
                    'upah_lembur' => $item['upah_lembur'] ?? 0,
                    'total_potongan' => $item['total_potongan'],
                    'gaji_bersih' => $item['gaji_bersih'],
                    'status_pembayaran' => 'Pending'
                ];
                
                $saved = $this->detailModel->insert($detailData);
                
                if (!$saved) {
                    throw new \Exception('Gagal menyimpan detail pembayaran untuk karyawan ' . $karyawan['nama_lengkap']);
                }
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('success', 'Proses pembayaran berhasil dibuat');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan proses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Detail proses pembayaran
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Proses Pembayaran Gaji';
        
        $proses = $this->prosesModel->getWithDetails($id);
        
        if (!$proses) {
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('error', 'Proses pembayaran tidak ditemukan');
        }
        
        $data['proses'] = $proses;
        
        // Ambil ringkasan per bank
        $data['ringkasanPerBank'] = $this->detailModel->getRekapPerBank(
            $proses['periode_bulan'],
            $proses['periode_tahun']
        );
        
        return view('accounting/penggajian/proses-pembayaran/detail', $data);
    }

    /**
     * Edit proses pembayaran
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Proses Pembayaran Gaji';
        
        $proses = $this->prosesModel->find($id);
        
        if (!$proses) {
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('error', 'Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['status'] !== 'Draft') {
            return redirect()->back()
                ->with('error', 'Hanya proses dengan status Draft yang dapat diedit');
        }
        
        $data['proses'] = $proses;
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['metodeOptions'] = ['Transfer Bank', 'Tunai', 'Cek', 'Giro'];
        $data['coaBankOptions'] = $this->prosesModel->getCoaBankOptions();
        
        // Ambil detail pembayaran
        $data['details'] = $this->detailModel->getByProses($id);
        
        return view('accounting/penggajian/proses-pembayaran/edit', $data);
    }

    /**
     * Update proses pembayaran
     */
    public function update($id)
    {
        $proses = $this->prosesModel->find($id);
        
        if (!$proses) {
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('error', 'Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['status'] !== 'Draft') {
            return redirect()->back()
                ->with('error', 'Hanya proses dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'nama_proses' => 'required',
            'tanggal_proses' => 'required|valid_date',
            'tanggal_pembayaran' => 'required|valid_date',
            'metode_pembayaran' => 'required|in_list[Transfer Bank,Tunai,Cek,Giro]',
            'coa_bank_id' => 'permit_empty|is_natural_no_zero'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'nama_proses' => $this->request->getPost('nama_proses'),
            'tanggal_proses' => $this->request->getPost('tanggal_proses'),
            'tanggal_pembayaran' => $this->request->getPost('tanggal_pembayaran'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'bank_pengirim' => $this->request->getPost('bank_pengirim'),
            'coa_bank_id' => $this->request->getPost('coa_bank_id') ?: null,
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        try {
            $this->db->transBegin();
            
            $updated = $this->prosesModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data');
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('success', 'Proses pembayaran berhasil diupdate');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate proses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Hapus proses pembayaran
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $proses = $this->prosesModel->find($id);
        
        if (!$proses) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Proses pembayaran tidak ditemukan'
                ]);
            }
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('error', 'Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['status'] === 'Selesai') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Proses pembayaran yang sudah selesai tidak dapat dihapus'
                ]);
            }
            return redirect()->back()
                ->with('error', 'Proses pembayaran yang sudah selesai tidak dapat dihapus');
        }
        
        try {
            $this->db->transBegin();
            
            // Hapus detail pembayaran
            $this->detailModel->where('proses_id', $id)->delete();
            
            // Hapus proses
            $deleted = $this->prosesModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Proses pembayaran berhasil dihapus',
                    'redirect' => site_url('accounting/penggajian/proses-pembayaran')
                ]);
            }
            
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('success', 'Proses pembayaran berhasil dihapus');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus proses pembayaran: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus proses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Proses pembayaran (ubah status menjadi Diproses)
     */
    public function process($id)
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->prosesModel->process($id);
            
            if (!$result) {
                throw new \Exception('Gagal memproses pembayaran');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran berhasil diproses'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Pembayaran berhasil diproses');
                
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Selesaikan pembayaran dan buat jurnal
     */
    public function complete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $proses = $this->prosesModel->find($id);
        
        if (!$proses) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Proses pembayaran tidak ditemukan'
                ]);
            }
            return redirect()->back()->with('error', 'Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['status'] !== 'Diproses') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya proses dengan status Diproses yang dapat diselesaikan'
                ]);
            }
            return redirect()->back()->with('error', 'Hanya proses dengan status Diproses yang dapat diselesaikan');
        }
        
        try {
            $this->db->transBegin();
            
            // Generate file export untuk bank transfer
            $fileExport = null;
            if ($proses['metode_pembayaran'] === 'Transfer Bank') {
                $filePath = $this->detailModel->generateBankTransferFile($id);
                if ($filePath) {
                    $fileExport = $filePath;
                }
            }
            
            // Buat mutasi bank
            $mutasiBankId = null;
            if ($proses['metode_pembayaran'] === 'Transfer Bank' && $proses['coa_bank_id']) {
                $mutasiData = [
                    'tanggal' => $proses['tanggal_pembayaran'],
                    'tipe' => 'Debit',
                    'jumlah' => $proses['total_nominal'],
                    'keterangan' => $proses['nama_proses'],
                    'kategori' => 'Penggajian',
                    'coa_id_debit' => $proses['coa_bank_id'],
                    'coa_id_kredit' => null,
                    'bank_asal' => $proses['bank_pengirim'],
                    'bank_tujuan' => null,
                    'no_referensi' => $proses['nomor_proses'],
                    'status' => 'Posted',
                    'created_by' => session()->get('user_id')
                ];
                
                $mutasiBankId = $this->mutasiBankModel->insert($mutasiData);
            }
            
            // Buat jurnal
            $jurnalData = [
                'tanggal' => $proses['tanggal_pembayaran'],
                'keterangan' => 'Pembayaran gaji: ' . $proses['nama_proses'],
                'referensi' => $proses['nomor_proses'],
                'tipe_referensi' => 'penggajian',
                'status' => 'posted',
                'posted_by' => session()->get('user_id'),
                'posted_at' => date('Y-m-d H:i:s'),
                'created_by' => session()->get('user_id')
            ];
            
            $jurnalId = $this->jurnalModel->insert($jurnalData);
            
            if ($jurnalId) {
                // Detail jurnal
                // Debit: Beban Gaji
                $bebanGajiCoaId = $this->getCoaIdByKode('5-1201'); // Gaji Pokok
                if ($bebanGajiCoaId) {
                    $this->jurnalModel->db->table('jurnal_detail')->insert([
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $bebanGajiCoaId,
                        'kode_akun' => '5-1201',
                        'nama_akun' => 'Beban Gaji dan Upah',
                        'debit' => $proses['total_nominal'],
                        'kredit' => 0,
                        'keterangan' => 'Pembayaran gaji periode ' . $this->getNamaBulan($proses['periode_bulan']) . ' ' . $proses['periode_tahun']
                    ]);
                }
                
                // Kredit: Kas/Bank
                if ($proses['coa_bank_id']) {
                    $coa = $this->coaModel->find($proses['coa_bank_id']);
                    $this->jurnalModel->db->table('jurnal_detail')->insert([
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $proses['coa_bank_id'],
                        'kode_akun' => $coa['kode_akun'] ?? '',
                        'nama_akun' => $coa['nama_akun'] ?? '',
                        'debit' => 0,
                        'kredit' => $proses['total_nominal'],
                        'keterangan' => 'Pembayaran gaji dari rekening ' . ($coa['nama_akun'] ?? '')
                    ]);
                }
            }
            
            // Selesaikan proses
            $completeData = [
                'file_export' => $fileExport,
                'mutasi_bank_id' => $mutasiBankId,
                'jurnal_id' => $jurnalId
            ];
            
            $result = $this->prosesModel->complete($id, $completeData);
            
            if (!$result) {
                throw new \Exception('Gagal menyelesaikan pembayaran');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran berhasil diselesaikan dan jurnal telah dibuat'
                ]);
            }
            
            return redirect()->to('accounting/penggajian/proses-pembayaran')
                ->with('success', 'Pembayaran berhasil diselesaikan dan jurnal telah dibuat');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Batalkan proses pembayaran
     */
    public function cancel($id)
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->prosesModel->cancel($id);
            
            if (!$result) {
                throw new \Exception('Gagal membatalkan pembayaran');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dibatalkan'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Pembayaran berhasil dibatalkan');
                
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Export detail pembayaran ke Excel
     */
    public function exportExcel($id)
    {
        $proses = $this->prosesModel->find($id);
        
        if (!$proses) {
            return redirect()->back()->with('error', 'Proses pembayaran tidak ditemukan');
        }
        
        $data = $this->detailModel->getExportData(['proses_id' => $id]);
        
        try {
            $spreadsheet = new Spreadsheet();
            
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Detail Pembayaran Gaji - " . $proses['nama_proses'])
                ->setSubject("Detail Pembayaran Gaji");
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Detail Pembayaran');
            
            // Header
            $sheet->mergeCells('A1:N1');
            $sheet->setCellValue('A1', 'DETAIL PEMBAYARAN GAJI');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:N2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:N3');
            $sheet->setCellValue('A3', $proses['nama_proses']);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A4:N4');
            $sheet->setCellValue('A4', 'Periode: ' . $this->getNamaBulan($proses['periode_bulan']) . ' ' . $proses['periode_tahun']);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A5:N5');
            $sheet->setCellValue('A5', 'Tanggal Pembayaran: ' . date('d/m/Y', strtotime($proses['tanggal_pembayaran'])));
            $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'NIK',
                'C' => 'Nama Karyawan',
                'D' => 'Bank',
                'E' => 'No Rekening',
                'F' => 'Gaji Pokok',
                'G' => 'Tunjangan',
                'H' => 'Upah Lembur',
                'I' => 'Total Pendapatan',
                'J' => 'Potongan',
                'K' => 'Gaji Bersih',
                'L' => 'Status',
                'M' => 'Tanggal Bayar',
                'N' => 'Keterangan'
            ];
            
            $startRow = 7;
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':N' . $startRow;
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
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item['NIK']);
                $sheet->setCellValue('C' . $row, $item['Nama Karyawan']);
                $sheet->setCellValue('D' . $row, $item['Bank']);
                $sheet->setCellValue('E' . $row, $item['No Rekening']);
                $sheet->setCellValue('F' . $row, $item['Gaji Pokok']);
                $sheet->setCellValue('G' . $row, $item['Tunjangan']);
                $sheet->setCellValue('H' . $row, $item['Upah Lembur']);
                $sheet->setCellValue('I' . $row, $item['Gaji Pokok'] + $item['Tunjangan'] + $item['Upah Lembur']);
                $sheet->setCellValue('J' . $row, $item['Potongan']);
                $sheet->setCellValue('K' . $row, $item['Gaji Bersih']);
                $sheet->setCellValue('L' . $row, $item['Status']);
                $sheet->setCellValue('M' . $row, $item['Tanggal Bayar']);
                $sheet->setCellValue('N' . $row, $item['Keterangan']);
                
                // Format currency
                foreach (range('F', 'K') as $col) {
                    $sheet->getStyle($col . $row)->getNumberFormat()
                        ->setFormatCode('#,##0');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':N' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Footer
            $footerRow = $lastRow + 2;
            $sheet->mergeCells('A' . $footerRow . ':N' . $footerRow);
            $sheet->setCellValue('A' . $footerRow, 'Total Karyawan: ' . $proses['total_karyawan'] . ' | Total Nominal: Rp ' . number_format($proses['total_nominal'], 0, ',', '.'));
            $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Output file
            $filename = 'Pembayaran_Gaji_' . str_replace(' ', '_', $proses['nama_proses']) . '.xlsx';
            
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
     * Export untuk bank transfer (format CSV)
     */
    public function exportBankTransfer($id)
    {
        $proses = $this->prosesModel->find($id);
        
        if (!$proses) {
            return redirect()->back()->with('error', 'Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['metode_pembayaran'] !== 'Transfer Bank') {
            return redirect()->back()->with('error', 'Export bank transfer hanya untuk metode Transfer Bank');
        }
        
        $filePath = $this->detailModel->generateBankTransferFile($id);
        
        if (!$filePath) {
            return redirect()->back()->with('error', 'Tidak ada data yang siap untuk ditransfer');
        }
        
        return $this->response->download($filePath, null)->setFileName('transfer_gaji_' . date('Ymd_His') . '.csv');
    }

    /**
     * AJAX: Get perhitungan tersedia
     */
    public function ajaxGetPerhitunganTersedia()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        $perhitungan = $this->perhitunganModel->getForPayment($bulan, $tahun);
        
        $totalKaryawan = count($perhitungan);
        $totalNominal = array_sum(array_column($perhitungan, 'gaji_bersih'));
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $perhitungan,
            'total_karyawan' => $totalKaryawan,
            'total_nominal' => $totalNominal
        ]);
    }

    /**
     * AJAX: Get ringkasan pembayaran
     */
    public function ajaxGetRingkasanPembayaran()
    {
        $id = $this->request->getGet('id');
        
        if ($id) {
            $ringkasan = $this->detailModel->getSummaryByProses($id);
        } else {
            $bulan = $this->request->getGet('bulan');
            $tahun = $this->request->getGet('tahun');
            $ringkasan = $this->prosesModel->getRingkasanPerPeriode($tahun);
        }
        
        return $this->response->setJSON($ringkasan);
    }

   /**
 * AJAX: Validate budget sebelum pembayaran
 */
public function ajaxValidateBudget()
{
    $coaBankId = $this->request->getPost('coa_bank_id');
    $totalNominal = $this->request->getPost('total_nominal');
    
    if (!$coaBankId || !$totalNominal) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Data tidak lengkap'
        ]);
    }
    
    // Ambil saldo bank dari buku besar
    $bukuBesarModel = new \App\Models\BukuBesarModel();
    $saldoData = $bukuBesarModel->getSaldoAkhir($coaBankId);
    
    // Ekstrak nilai saldo dari array jika perlu
    $saldo = 0;
    if (is_array($saldoData)) {
        $saldo = $saldoData['saldo_akhir'] ?? 0;
    } else {
        $saldo = (float) $saldoData;
    }
    
    $totalNominalFloat = (float) $totalNominal;
    $tersedia = $saldo >= $totalNominalFloat;
    
    return $this->response->setJSON([
        'success' => true,
        'saldo_available' => $saldo,
        'total_payment' => $totalNominalFloat,
        'available' => $tersedia,
        'message' => $tersedia ? 'Saldo mencukupi' : 'Saldo tidak mencukupi (Rp ' . number_format($saldo, 0, ',', '.') . ')'
    ]);
}

    /**
     * Get COA ID by kode akun
     */
    private function getCoaIdByKode($kodeAkun)
    {
        $coa = $this->coaModel->where('kode_akun', $kodeAkun)->first();
        return $coa['id'] ?? null;
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