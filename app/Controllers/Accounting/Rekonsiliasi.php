<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\RekonsiliasiModel;
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use App\Models\BukuBesarModel;
use App\Models\Accounting\MutasiBankModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Dompdf\Dompdf;
use Dompdf\Options;

class Rekonsiliasi extends BaseController
{
    protected $rekonsiliasiModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $bukuBesarModel;
    protected $mutasiBankModel;
    protected $db;

    public function __construct()
    {
        $this->rekonsiliasiModel = new RekonsiliasiModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->bukuBesarModel = new BukuBesarModel();
        $this->mutasiBankModel = new MutasiBankModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Rekonsiliasi
     */
    public function index()
    {
        $data['title'] = 'Daftar Rekonsiliasi Bank';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'periode_mulai' => $this->request->getGet('periode_mulai'),
            'periode_selesai' => $this->request->getGet('periode_selesai'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'coa_bank_id' => $this->request->getGet('coa_bank_id'),
            'status' => $this->request->getGet('status')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->rekonsiliasiModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['rekonsiliasi'] = $result['data'];
        $data['pager'] = $this->rekonsiliasiModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['bankOptions'] = $this->rekonsiliasiModel->getBankAccounts();
        $data['statusOptions'] = ['Draft', 'Selesai', 'Dibatalkan'];
        
        // Data untuk filter tahun
        $tahunOptions = [];
        for ($t = date('Y'); $t >= date('Y') - 5; $t--) {
            $tahunOptions[] = $t;
        }
        $data['tahunOptions'] = $tahunOptions;
        
        // Data untuk filter bulan
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $data['bulanOptions'] = $bulanOptions;
        
        $data['stats'] = $this->rekonsiliasiModel->getStats(
            $filters['tahun'] ?? null,
            $filters['bulan'] ?? null
        );
        
        $data['ringkasanBank'] = $this->rekonsiliasiModel->getRingkasanPerBank(
            $filters['tahun'] ?? date('Y')
        );
        
        return view('accounting/kas-bank/rekonsiliasi/index', $data);
    }

    /**
     * Form tambah rekonsiliasi
     */
    public function create()
    {
        $data['title'] = 'Tambah Rekonsiliasi Bank';
        $data['validation'] = \Config\Services::validation();
        
        $data['bankOptions'] = $this->rekonsiliasiModel->getBankAccounts();
        
        // Default periode (bulan ini)
        $data['rekonsiliasi'] = [
            'periode' => date('Y-m-01'),
            'tanggal_rekonsiliasi' => date('Y-m-d'),
            'status' => 'Draft'
        ];
        
        return view('accounting/kas-bank/rekonsiliasi/create', $data);
    }

    /**
     * Simpan rekonsiliasi baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'periode' => 'required|valid_date[Y-m-d]',
            'coa_bank_id' => 'required|is_natural_no_zero',
            'tanggal_rekonsiliasi' => 'required|valid_date',
            'saldo_akhir_bank' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'saldo_akhir_buku' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'keterangan' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Cek apakah sudah ada rekonsiliasi untuk periode dan bank ini
        $periode = $this->request->getPost('periode');
        $coaBankId = $this->request->getPost('coa_bank_id');
        
        if ($this->rekonsiliasiModel->existsForPeriode($periode, $coaBankId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Rekonsiliasi untuk periode dan bank ini sudah ada');
        }
        
        // Ambil saldo awal dari buku besar
        $saldoAwalBuku = $this->rekonsiliasiModel->getSaldoAwalBuku($coaBankId, $periode);
        
        // Ambil saldo akhir dari buku besar (jika tidak diisi manual)
        $saldoAkhirBuku = $this->request->getPost('saldo_akhir_buku');
        if (empty($saldoAkhirBuku)) {
            $saldoAkhirBuku = $this->rekonsiliasiModel->getSaldoAkhirBuku($coaBankId, $periode);
        }
        
        $data = [
            'periode' => $periode,
            'coa_bank_id' => $coaBankId,
            'saldo_awal_bank' => $this->cleanCurrency($this->request->getPost('saldo_awal_bank') ?? 0),
            'saldo_akhir_bank' => $this->cleanCurrency($this->request->getPost('saldo_akhir_bank') ?? 0),
            'saldo_awal_buku' => $saldoAwalBuku,
            'saldo_akhir_buku' => $this->cleanCurrency($saldoAkhirBuku),
            'tanggal_rekonsiliasi' => $this->request->getPost('tanggal_rekonsiliasi'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'Draft'
        ];
        
        $lampiran = $this->request->getFile('lampiran_rekening_koran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/rekonsiliasi', $newName);
            $data['lampiran_rekening_koran'] = 'uploads/rekonsiliasi/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->rekonsiliasiModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->rekonsiliasiModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('success', 'Rekonsiliasi bank berhasil disimpan sebagai Draft.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan rekonsiliasi: ' . $e->getMessage());
        }
    }

    /**
     * Detail rekonsiliasi
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Rekonsiliasi Bank';
        
        $rekonsiliasi = $this->rekonsiliasiModel->getWithDetails($id);
        
        if (!$rekonsiliasi) {
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('error', 'Rekonsiliasi tidak ditemukan');
        }
        
        // Format angka
        $rekonsiliasi['saldo_awal_bank_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_awal_bank']);
        $rekonsiliasi['saldo_akhir_bank_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_akhir_bank']);
        $rekonsiliasi['saldo_awal_buku_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_awal_buku']);
        $rekonsiliasi['saldo_akhir_buku_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_akhir_buku']);
        $rekonsiliasi['selisih_formatted'] = $this->formatRupiah($rekonsiliasi['selisih']);
        $rekonsiliasi['total_setoran_dalam_perjalanan_formatted'] = $this->formatRupiah($rekonsiliasi['total_setoran_dalam_perjalanan'] ?? 0);
        $rekonsiliasi['total_cek_dalam_edar_formatted'] = $this->formatRupiah($rekonsiliasi['total_cek_dalam_edar'] ?? 0);
        $rekonsiliasi['total_penyesuaian_bank_formatted'] = $this->formatRupiah($rekonsiliasi['total_penyesuaian_bank'] ?? 0);
        $rekonsiliasi['total_penyesuaian_buku_formatted'] = $this->formatRupiah($rekonsiliasi['total_penyesuaian_buku'] ?? 0);
        
        $data['rekonsiliasi'] = $rekonsiliasi;
        
        // Ambil mutasi bank yang belum direkonsiliasi (jika status Draft)
        if ($rekonsiliasi['status'] === 'Draft') {
            $data['mutasi_belum_rekonsiliasi'] = $this->rekonsiliasiModel->getMutasiBelumRekonsiliasi(
                $rekonsiliasi['coa_bank_id'],
                $rekonsiliasi['periode']
            );
        }
        
        return view('accounting/kas-bank/rekonsiliasi/detail', $data);
    }

    /**
     * Form edit rekonsiliasi
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Rekonsiliasi Bank';
        
        $rekonsiliasi = $this->rekonsiliasiModel->find($id);
        
        if (!$rekonsiliasi) {
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('error', 'Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('error', 'Hanya rekonsiliasi dengan status Draft yang dapat diedit');
        }
        
        // Format angka
        $rekonsiliasi['saldo_awal_bank_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_awal_bank']);
        $rekonsiliasi['saldo_akhir_bank_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_akhir_bank']);
        $rekonsiliasi['saldo_awal_buku_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_awal_buku']);
        $rekonsiliasi['saldo_akhir_buku_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_akhir_buku']);
        
        // Decode JSON data
        $rekonsiliasi['data_setoran_dalam_perjalanan'] = !empty($rekonsiliasi['data_setoran_dalam_perjalanan']) 
            ? json_decode($rekonsiliasi['data_setoran_dalam_perjalanan'], true) 
            : [];
        $rekonsiliasi['data_cek_dalam_edar'] = !empty($rekonsiliasi['data_cek_dalam_edar']) 
            ? json_decode($rekonsiliasi['data_cek_dalam_edar'], true) 
            : [];
        $rekonsiliasi['data_penyesuaian_bank'] = !empty($rekonsiliasi['data_penyesuaian_bank']) 
            ? json_decode($rekonsiliasi['data_penyesuaian_bank'], true) 
            : [];
        $rekonsiliasi['data_penyesuaian_buku'] = !empty($rekonsiliasi['data_penyesuaian_buku']) 
            ? json_decode($rekonsiliasi['data_penyesuaian_buku'], true) 
            : [];
        
        $data['validation'] = \Config\Services::validation();
        $data['rekonsiliasi'] = $rekonsiliasi;
        $data['bankOptions'] = $this->rekonsiliasiModel->getBankAccounts();
        
        return view('accounting/kas-bank/rekonsiliasi/edit', $data);
    }

    /**
     * Update rekonsiliasi
     */
    public function update($id)
    {
        $rekonsiliasi = $this->rekonsiliasiModel->find($id);
        
        if (!$rekonsiliasi) {
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('error', 'Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('error', 'Hanya rekonsiliasi dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'periode' => 'required|valid_date[Y-m-d]',
            'coa_bank_id' => 'required|is_natural_no_zero',
            'tanggal_rekonsiliasi' => 'required|valid_date',
            'saldo_akhir_bank' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'saldo_akhir_buku' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'keterangan' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Cek unique periode kecuali untuk dirinya sendiri
        $periode = $this->request->getPost('periode');
        $coaBankId = $this->request->getPost('coa_bank_id');
        
        $existing = $this->rekonsiliasiModel->where('periode', $periode)
            ->where('coa_bank_id', $coaBankId)
            ->where('id !=', $id)
            ->where('deleted_at IS NULL')
            ->first();
        
        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Rekonsiliasi untuk periode dan bank ini sudah ada');
        }
        
        $data = [
            'id' => $id,
            'periode' => $periode,
            'coa_bank_id' => $coaBankId,
            'saldo_awal_bank' => $this->cleanCurrency($this->request->getPost('saldo_awal_bank') ?? 0),
            'saldo_akhir_bank' => $this->cleanCurrency($this->request->getPost('saldo_akhir_bank') ?? 0),
            'saldo_akhir_buku' => $this->cleanCurrency($this->request->getPost('saldo_akhir_buku') ?? 0),
            'tanggal_rekonsiliasi' => $this->request->getPost('tanggal_rekonsiliasi'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        // Update data JSON jika ada perubahan dari form
        $setoranDalamPerjalanan = $this->request->getPost('setoran_dalam_perjalanan');
        if ($setoranDalamPerjalanan !== null) {
            $data['data_setoran_dalam_perjalanan'] = json_encode($setoranDalamPerjalanan);
            $data['total_setoran_dalam_perjalanan'] = array_sum(array_column($setoranDalamPerjalanan, 'jumlah'));
        }
        
        $cekDalamEdar = $this->request->getPost('cek_dalam_edar');
        if ($cekDalamEdar !== null) {
            $data['data_cek_dalam_edar'] = json_encode($cekDalamEdar);
            $data['total_cek_dalam_edar'] = array_sum(array_column($cekDalamEdar, 'jumlah'));
        }
        
        $penyesuaianBank = $this->request->getPost('penyesuaian_bank');
        if ($penyesuaianBank !== null) {
            $data['data_penyesuaian_bank'] = json_encode($penyesuaianBank);
            $totalPenyesuaianBank = 0;
            foreach ($penyesuaianBank as $p) {
                if ($p['tipe'] === 'Kredit') {
                    $totalPenyesuaianBank += $p['jumlah'];
                } else {
                    $totalPenyesuaianBank -= $p['jumlah'];
                }
            }
            $data['total_penyesuaian_bank'] = $totalPenyesuaianBank;
        }
        
        $penyesuaianBuku = $this->request->getPost('penyesuaian_buku');
        if ($penyesuaianBuku !== null) {
            $data['data_penyesuaian_buku'] = json_encode($penyesuaianBuku);
            $totalPenyesuaianBuku = 0;
            foreach ($penyesuaianBuku as $p) {
                if ($p['tipe'] === 'Kredit') {
                    $totalPenyesuaianBuku += $p['jumlah'];
                } else {
                    $totalPenyesuaianBuku -= $p['jumlah'];
                }
            }
            $data['total_penyesuaian_buku'] = $totalPenyesuaianBuku;
        }
        
        $lampiran = $this->request->getFile('lampiran_rekening_koran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            if (!empty($rekonsiliasi['lampiran_rekening_koran']) && file_exists(FCPATH . $rekonsiliasi['lampiran_rekening_koran'])) {
                unlink(FCPATH . $rekonsiliasi['lampiran_rekening_koran']);
            }
            
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/rekonsiliasi', $newName);
            $data['lampiran_rekening_koran'] = 'uploads/rekonsiliasi/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->rekonsiliasiModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->rekonsiliasiModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('success', 'Rekonsiliasi berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate rekonsiliasi: ' . $e->getMessage());
        }
    }

    /**
     * Hapus rekonsiliasi
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $rekonsiliasi = $this->rekonsiliasiModel->find($id);
        
        if (!$rekonsiliasi) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Rekonsiliasi tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi')
                    ->with('error', 'Rekonsiliasi tidak ditemukan');
            }
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya rekonsiliasi dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi')
                    ->with('error', 'Hanya rekonsiliasi dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if (!empty($rekonsiliasi['lampiran_rekening_koran']) && file_exists(FCPATH . $rekonsiliasi['lampiran_rekening_koran'])) {
                unlink(FCPATH . $rekonsiliasi['lampiran_rekening_koran']);
            }
            
            $deleted = $this->rekonsiliasiModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rekonsiliasi berhasil dihapus',
                    'redirect' => site_url('accounting/kas-bank/rekonsiliasi')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi')
                    ->with('success', 'Rekonsiliasi berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus rekonsiliasi: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus rekonsiliasi: ' . $e->getMessage());
            }
        }
    }

    /**
     * Selesaikan rekonsiliasi
     */
    public function selesaikan($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $rekonsiliasi = $this->rekonsiliasiModel->find($id);
        
        if (!$rekonsiliasi) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Rekonsiliasi tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi')
                    ->with('error', 'Rekonsiliasi tidak ditemukan');
            }
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya rekonsiliasi dengan status Draft yang dapat diselesaikan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi')
                    ->with('error', 'Hanya rekonsiliasi dengan status Draft yang dapat diselesaikan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $this->rekonsiliasiModel->selesaikanRekonsiliasi($id);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rekonsiliasi berhasil diselesaikan',
                    'redirect' => site_url('accounting/kas-bank/rekonsiliasi/detail/' . $id)
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi/detail/' . $id)
                    ->with('success', 'Rekonsiliasi berhasil diselesaikan');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyelesaikan rekonsiliasi: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menyelesaikan rekonsiliasi: ' . $e->getMessage());
            }
        }
    }

    /**
     * Batalkan rekonsiliasi
     */
    public function batalkan($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $rekonsiliasi = $this->rekonsiliasiModel->find($id);
        
        if (!$rekonsiliasi) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Rekonsiliasi tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi')
                    ->with('error', 'Rekonsiliasi tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $this->rekonsiliasiModel->batalkanRekonsiliasi($id);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rekonsiliasi berhasil dibatalkan',
                    'redirect' => site_url('accounting/kas-bank/rekonsiliasi')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/rekonsiliasi')
                    ->with('success', 'Rekonsiliasi berhasil dibatalkan');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan rekonsiliasi: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membatalkan rekonsiliasi: ' . $e->getMessage());
            }
        }
    }

    /**
     * AJAX: Get bank accounts untuk dropdown
     */
    public function ajaxGetBankAccounts()
    {
        $bankAccounts = $this->rekonsiliasiModel->getBankAccounts();
        
        $options = [];
        foreach ($bankAccounts as $bank) {
            $options[] = [
                'id' => $bank['id'],
                'text' => $bank['kode_akun'] . ' - ' . $bank['nama_akun']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get saldo bank berdasarkan COA bank
     */
    public function ajaxGetSaldoBank($coaBankId)
    {
        $tanggal = $this->request->getGet('tanggal');
        
        $saldo = $this->mutasiBankModel->getSaldoBank($coaBankId, $tanggal);
        $saldoAwalPeriode = $this->rekonsiliasiModel->getSaldoAwalBuku($coaBankId, $tanggal ?? date('Y-m-01'));
        
        return $this->response->setJSON([
            'success' => true,
            'saldo' => $this->formatRupiah($saldo),
            'saldo_raw' => $saldo,
            'saldo_awal' => $this->formatRupiah($saldoAwalPeriode),
            'saldo_awal_raw' => $saldoAwalPeriode
        ]);
    }

    /**
     * AJAX: Get mutasi yang belum direkonsiliasi
     */
    public function ajaxGetMutasiBelumRekonsiliasi($coaBankId)
    {
        $periode = $this->request->getGet('periode');
        
        if (!$periode) {
            $periode = date('Y-m-01');
        }
        
        $mutasi = $this->rekonsiliasiModel->getMutasiBelumRekonsiliasi($coaBankId, $periode);
        
        // Format data
        foreach ($mutasi as &$item) {
            $item['jumlah_formatted'] = $this->formatRupiah($item['jumlah']);
            $item['tanggal_formatted'] = date('d/m/Y', strtotime($item['tanggal']));
            
            // Tentukan tipe (masuk/keluar) relatif terhadap bank
            if ($item['coa_id_kredit'] == $coaBankId) {
                $item['arah'] = 'Masuk';
                $item['arah_class'] = 'text-success';
            } else {
                $item['arah'] = 'Keluar';
                $item['arah_class'] = 'text-danger';
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $mutasi
        ]);
    }

    /**
     * AJAX: Match transaksi (tandai sudah direkonsiliasi)
     */
    public function ajaxMatchTransaksi()
    {
        $transaksiId = $this->request->getPost('transaksi_id');
        $rekonsiliasiId = $this->request->getPost('rekonsiliasi_id');
        $tipe = $this->request->getPost('tipe'); // 'setoran', 'cek', 'penyesuaian_bank', 'penyesuaian_buku'
        
        if (!$transaksiId || !$rekonsiliasiId || !$tipe) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            // Ambil data mutasi
            $mutasi = $this->mutasiBankModel->find($transaksiId);
            
            if (!$mutasi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }
            
            // Buat item berdasarkan tipe
            $item = [
                'id' => $mutasi['id'],
                'kode_transaksi' => $mutasi['kode_transaksi'],
                'tanggal' => $mutasi['tanggal'],
                'keterangan' => $mutasi['keterangan'],
                'jumlah' => $mutasi['jumlah'],
                'no_referensi' => $mutasi['no_referensi']
            ];
            
            // Tambah ke data rekonsiliasi sesuai tipe
            switch ($tipe) {
                case 'setoran':
                    $item['tipe'] = 'Setoran';
                    $this->rekonsiliasiModel->addSetoranDalamPerjalanan($rekonsiliasiId, $item);
                    break;
                    
                case 'cek':
                    $item['tipe'] = 'Cek';
                    $this->rekonsiliasiModel->addCekDalamEdar($rekonsiliasiId, $item);
                    break;
                    
                case 'penyesuaian_bank':
                    $item['tipe'] = $mutasi['tipe']; // Kredit/Debit
                    $this->rekonsiliasiModel->addPenyesuaianBank($rekonsiliasiId, $item);
                    break;
                    
                case 'penyesuaian_buku':
                    $item['tipe'] = $mutasi['tipe']; // Kredit/Debit
                    $this->rekonsiliasiModel->addPenyesuaianBuku($rekonsiliasiId, $item);
                    break;
            }
            
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan ke rekonsiliasi'
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan transaksi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Unmatch transaksi (hapus dari daftar rekonsiliasi)
     */
    public function ajaxUnmatchTransaksi()
    {
        $rekonsiliasiId = $this->request->getPost('rekonsiliasi_id');
        $index = $this->request->getPost('index');
        $tipe = $this->request->getPost('tipe'); // 'setoran', 'cek', 'penyesuaian_bank', 'penyesuaian_buku'
        
        if ($rekonsiliasiId === null || $index === null || !$tipe) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            switch ($tipe) {
                case 'setoran':
                    $this->rekonsiliasiModel->removeSetoranDalamPerjalanan($rekonsiliasiId, $index);
                    break;
                    
                case 'cek':
                    $this->rekonsiliasiModel->removeCekDalamEdar($rekonsiliasiId, $index);
                    break;
                    
                default:
                    throw new \Exception('Tipe tidak didukung untuk operasi ini');
            }
            
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus dari rekonsiliasi'
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Get ringkasan rekonsiliasi
     */
    public function ajaxGetRingkasan()
    {
        $rekonsiliasiId = $this->request->getGet('rekonsiliasi_id');
        
        $rekonsiliasi = $this->rekonsiliasiModel->getWithDetails($rekonsiliasiId);
        
        if (!$rekonsiliasi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Rekonsiliasi tidak ditemukan'
            ]);
        }
        
        // Hitung saldo yang telah direkonsiliasi
        $saldoBankSetelahSetoran = $rekonsiliasi['saldo_akhir_bank'] - ($rekonsiliasi['total_setoran_dalam_perjalanan'] ?? 0);
        $saldoBankSetelahCek = $rekonsiliasi['saldo_akhir_bank'] + ($rekonsiliasi['total_cek_dalam_edar'] ?? 0);
        $saldoBankSetelahPenyesuaian = $rekonsiliasi['saldo_akhir_bank'] - ($rekonsiliasi['total_penyesuaian_bank'] ?? 0);
        $saldoBukuSetelahPenyesuaian = $rekonsiliasi['saldo_akhir_buku'] + ($rekonsiliasi['total_penyesuaian_buku'] ?? 0);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'saldo_akhir_bank' => $this->formatRupiah($rekonsiliasi['saldo_akhir_bank']),
                'saldo_akhir_buku' => $this->formatRupiah($rekonsiliasi['saldo_akhir_buku']),
                'selisih' => $this->formatRupiah($rekonsiliasi['selisih']),
                'total_setoran' => $this->formatRupiah($rekonsiliasi['total_setoran_dalam_perjalanan'] ?? 0),
                'total_cek' => $this->formatRupiah($rekonsiliasi['total_cek_dalam_edar'] ?? 0),
                'total_penyesuaian_bank' => $this->formatRupiah($rekonsiliasi['total_penyesuaian_bank'] ?? 0),
                'total_penyesuaian_buku' => $this->formatRupiah($rekonsiliasi['total_penyesuaian_buku'] ?? 0),
                'saldo_bank_setelah_setoran' => $this->formatRupiah($saldoBankSetelahSetoran),
                'saldo_bank_setelah_cek' => $this->formatRupiah($saldoBankSetelahCek),
                'saldo_bank_setelah_penyesuaian' => $this->formatRupiah($saldoBankSetelahPenyesuaian),
                'saldo_buku_setelah_penyesuaian' => $this->formatRupiah($saldoBukuSetelahPenyesuaian)
            ]
        ]);
    }

    /**
     * AJAX: Simpan penyesuaian manual
     */
    public function ajaxSimpanPenyesuaian()
    {
        $rekonsiliasiId = $this->request->getPost('rekonsiliasi_id');
        $tipe = $this->request->getPost('tipe'); // 'bank' atau 'buku'
        $data = $this->request->getPost('data');
        
        if (!$rekonsiliasiId || !$tipe || !$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            $item = [
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'],
                'jumlah' => $this->cleanCurrency($data['jumlah']),
                'tipe' => $data['tipe_penyesuaian'], // Kredit/Debit
                'no_referensi' => $data['no_referensi'] ?? null
            ];
            
            if ($tipe === 'bank') {
                $this->rekonsiliasiModel->addPenyesuaianBank($rekonsiliasiId, $item);
            } else {
                $this->rekonsiliasiModel->addPenyesuaianBuku($rekonsiliasiId, $item);
            }
            
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Penyesuaian berhasil disimpan'
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan penyesuaian: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk match - Match multiple transaksi sekaligus
     */
    public function bulkMatch()
    {
        $rekonsiliasiId = $this->request->getPost('rekonsiliasi_id');
        $transaksiIds = $this->request->getPost('transaksi_ids');
        $tipe = $this->request->getPost('tipe');
        
        if (empty($rekonsiliasiId) || empty($transaksiIds) || empty($tipe)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($transaksiIds as $transaksiId) {
            try {
                $this->db->transBegin();
                
                $mutasi = $this->mutasiBankModel->find($transaksiId);
                
                if (!$mutasi) {
                    throw new \Exception('Transaksi tidak ditemukan');
                }
                
                $item = [
                    'id' => $mutasi['id'],
                    'kode_transaksi' => $mutasi['kode_transaksi'],
                    'tanggal' => $mutasi['tanggal'],
                    'keterangan' => $mutasi['keterangan'],
                    'jumlah' => $mutasi['jumlah'],
                    'no_referensi' => $mutasi['no_referensi']
                ];
                
                switch ($tipe) {
                    case 'setoran':
                        $item['tipe'] = 'Setoran';
                        $this->rekonsiliasiModel->addSetoranDalamPerjalanan($rekonsiliasiId, $item);
                        break;
                        
                    case 'cek':
                        $item['tipe'] = 'Cek';
                        $this->rekonsiliasiModel->addCekDalamEdar($rekonsiliasiId, $item);
                        break;
                }
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Transaksi ID {$transaksiId}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil menambahkan {$success} transaksi";
        if ($failed > 0) {
            $message .= ", {$failed} gagal";
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    /**
     * Bulk unmatch - Hapus multiple transaksi dari rekonsiliasi
     */
    public function bulkUnmatch()
    {
        $rekonsiliasiId = $this->request->getPost('rekonsiliasi_id');
        $indices = $this->request->getPost('indices');
        $tipe = $this->request->getPost('tipe');
        
        if (empty($rekonsiliasiId) || empty($indices) || empty($tipe)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        // Hapus dari index terbesar ke terkecil agar index tidak berubah
        rsort($indices);
        
        foreach ($indices as $index) {
            try {
                $this->db->transBegin();
                
                switch ($tipe) {
                    case 'setoran':
                        $this->rekonsiliasiModel->removeSetoranDalamPerjalanan($rekonsiliasiId, $index);
                        break;
                        
                    case 'cek':
                        $this->rekonsiliasiModel->removeCekDalamEdar($rekonsiliasiId, $index);
                        break;
                }
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Index {$index}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil menghapus {$success} transaksi";
        if ($failed > 0) {
            $message .= ", {$failed} gagal";
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    /**
     * Laporan rekonsiliasi
     */
    public function laporanRekonsiliasi()
    {
        $data['title'] = 'Laporan Rekonsiliasi Bank';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        $coaBankId = $this->request->getGet('coa_bank_id');
        
        $data['ringkasanBank'] = $this->rekonsiliasiModel->getRingkasanPerBank($tahun);
        
        if ($coaBankId) {
            $periode = $bulan ? $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01' : $tahun . '-01-01';
            $data['detailRekonsiliasi'] = $this->rekonsiliasiModel->getByPeriode($periode, $coaBankId);
            
            if ($data['detailRekonsiliasi']) {
                $data['detailRekonsiliasi'] = $this->rekonsiliasiModel->getWithDetails($data['detailRekonsiliasi']['id']);
            }
        }
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['coaBankId'] = $coaBankId;
        $data['bankOptions'] = $this->rekonsiliasiModel->getBankAccounts();
        
        // Data untuk filter bulan
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $data['bulanOptions'] = $bulanOptions;
        
        return view('accounting/kas-bank/rekonsiliasi/laporan', $data);
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'periode_mulai' => $this->request->getGet('periode_mulai'),
            'periode_selesai' => $this->request->getGet('periode_selesai'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'coa_bank_id' => $this->request->getGet('coa_bank_id'),
            'status' => $this->request->getGet('status')
        ];
        
        session()->set('filter_rekonsiliasi', $filters);
        
        return redirect()->to('accounting/kas-bank/rekonsiliasi');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_rekonsiliasi') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_rekonsiliasi', $filters);
        
        return redirect()->to('accounting/kas-bank/rekonsiliasi');
    }

    /**
     * List status Draft
     */
    public function draft()
    {
        $filters = ['status' => 'Draft'];
        session()->set('filter_rekonsiliasi', $filters);
        
        return redirect()->to('accounting/kas-bank/rekonsiliasi');
    }

    /**
     * List status Selesai
     */
    public function selesai()
    {
        $filters = ['status' => 'Selesai'];
        session()->set('filter_rekonsiliasi', $filters);
        
        return redirect()->to('accounting/kas-bank/rekonsiliasi');
    }

    /**
     * List status Dibatalkan
     */
    public function dibatalkan()
    {
        $filters = ['status' => 'Dibatalkan'];
        session()->set('filter_rekonsiliasi', $filters);
        
        return redirect()->to('accounting/kas-bank/rekonsiliasi');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'search' => $this->request->getGet('search'),
            'periode_mulai' => $this->request->getGet('periode_mulai'),
            'periode_selesai' => $this->request->getGet('periode_selesai'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'coa_bank_id' => $this->request->getGet('coa_bank_id'),
            'status' => $this->request->getGet('status')
        ];
        
        $data = $this->rekonsiliasiModel->getExportData($filters);
        $stats = $this->rekonsiliasiModel->getStats(
            $filters['tahun'] ?? null,
            $filters['bulan'] ?? null
        );
        $ringkasanBank = $this->rekonsiliasiModel->getRingkasanPerBank(
            $filters['tahun'] ?? date('Y')
        );
        
        if ($type === 'excel') {
            return $this->exportExcel($data, $filters, $stats, $ringkasanBank);
        } else {
            return $this->exportPdf($data, $filters, $stats, $ringkasanBank);
        }
    }

    /**
     * Export ke Excel
     */
    private function exportExcel($data, $filters, $stats, $ringkasanBank)
    {
        try {
            $spreadsheet = new Spreadsheet();
            
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Laporan Rekonsiliasi Bank")
                ->setSubject("Laporan Rekonsiliasi Bank")
                ->setDescription("Laporan Rekonsiliasi Bank " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Rekonsiliasi');
            
            // Header laporan
            $sheet->mergeCells('A1:N1');
            $sheet->setCellValue('A1', 'LAPORAN REKONSILIASI BANK');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:N2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Periode
            $periodeText = 'Periode: ';
            if (!empty($filters['tahun'])) {
                $periodeText .= $filters['tahun'];
                if (!empty($filters['bulan'])) {
                    $bulanOptions = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    $periodeText .= ' - ' . ($bulanOptions[$filters['bulan']] ?? '');
                }
            } else {
                $periodeText .= 'Semua Periode';
            }
            
            $sheet->mergeCells('A3:N3');
            $sheet->setCellValue('A3', $periodeText);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Periode',
                'C' => 'Bank',
                'D' => 'Saldo Awal Bank',
                'E' => 'Saldo Akhir Bank',
                'F' => 'Saldo Awal Buku',
                'G' => 'Saldo Akhir Buku',
                'H' => 'Selisih',
                'I' => 'Setoran Perjalanan',
                'J' => 'Cek Dalam Edar',
                'K' => 'Penyesuaian Bank',
                'L' => 'Penyesuaian Buku',
                'M' => 'Tanggal Rekonsiliasi',
                'N' => 'Status'
            ];
            
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
                $selisih = ($item['Saldo Akhir Bank'] ?? 0) - ($item['Saldo Akhir Buku'] ?? 0);
                
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item['Periode']);
                $sheet->setCellValue('C' . $row, $item['Bank']);
                $sheet->setCellValue('D' . $row, $item['Saldo Awal Bank']);
                $sheet->setCellValue('E' . $row, $item['Saldo Akhir Bank']);
                $sheet->setCellValue('F' . $row, $item['Saldo Awal Buku']);
                $sheet->setCellValue('G' . $row, $item['Saldo Akhir Buku']);
                $sheet->setCellValue('H' . $row, $selisih);
                $sheet->setCellValue('I' . $row, $item['Setoran Dalam Perjalanan']);
                $sheet->setCellValue('J' . $row, $item['Cek Dalam Edar']);
                $sheet->setCellValue('K' . $row, $item['Penyesuaian Bank']);
                $sheet->setCellValue('L' . $row, $item['Penyesuaian Buku']);
                $sheet->setCellValue('M' . $row, date('d/m/Y', strtotime($item['Tanggal Rekonsiliasi'])));
                $sheet->setCellValue('N' . $row, $item['Status']);
                
                // Format currency
                foreach (range('D', 'L') as $col) {
                    $sheet->getStyle($col . $row)->getNumberFormat()
                        ->setFormatCode('"Rp" #,##0.00');
                }
                
                // Warna status
                $statusColor = match($item['Status']) {
                    'Selesai' => 'FF008000',
                    'Draft' => 'FFFFA500',
                    'Dibatalkan' => 'FFFF0000',
                    default => 'FF000000'
                };
                $sheet->getStyle('N' . $row)->getFont()->getColor()->setARGB($statusColor);
                
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
            
            // ============= SHEET 2: RINGKASAN =============
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2 = $spreadsheet->getActiveSheet();
            $sheet2->setTitle('Ringkasan');
            
            // Header
            $sheet2->mergeCells('A1:D1');
            $sheet2->setCellValue('A1', 'RINGKASAN REKONSILIASI BANK');
            $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            $sheet2->mergeCells('A2:D2');
            $sheet2->setCellValue('A2', $periodeText);
            
            // Statistik
            $sheet2->setCellValue('A4', 'STATISTIK');
            $sheet2->getStyle('A4')->getFont()->setBold(true);
            
            $statsData = [
                ['Total Rekonsiliasi', $stats['total_rekonsiliasi'] ?? 0],
                ['Selesai', $stats['selesai'] ?? 0],
                ['Draft', $stats['draft'] ?? 0],
                ['Dibatalkan', $stats['dibatalkan'] ?? 0],
                ['Jumlah Bank', $stats['jumlah_bank_direkonsiliasi'] ?? 0]
            ];
            
            $rowStat = 5;
            foreach ($statsData as $stat) {
                $sheet2->setCellValue('A' . $rowStat, $stat[0]);
                $sheet2->setCellValue('B' . $rowStat, $stat[1]);
                $rowStat++;
            }
            
            // Ringkasan per Bank
            $sheet2->setCellValue('A' . ($rowStat + 2), 'RINGKASAN PER BANK');
            $sheet2->getStyle('A' . ($rowStat + 2))->getFont()->setBold(true);
            
            $bankHeaders = ['Bank / Akun', 'Jumlah Rekonsiliasi', 'Selesai', 'Draft'];
            $colBank = 'A';
            foreach ($bankHeaders as $header) {
                $sheet2->setCellValue($colBank . ($rowStat + 3), $header);
                $sheet2->getStyle($colBank . ($rowStat + 3))->getFont()->setBold(true);
                $colBank++;
            }
            
            $rowBank = $rowStat + 4;
            foreach ($ringkasanBank as $bank) {
                $sheet2->setCellValue('A' . $rowBank, ($bank['kode_akun'] ?? '') . ' - ' . ($bank['nama_akun'] ?? ''));
                $sheet2->setCellValue('B' . $rowBank, $bank['jumlah_rekonsiliasi'] ?? 0);
                $sheet2->setCellValue('C' . $rowBank, $bank['selesai'] ?? 0);
                $sheet2->setCellValue('D' . $rowBank, $bank['draft'] ?? 0);
                $rowBank++;
            }
            
            // Auto-size columns sheet 2
            foreach (range('A', 'D') as $col) {
                $sheet2->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set active sheet ke sheet pertama
            $spreadsheet->setActiveSheetIndex(0);
            
            // Output file
            $filename = 'Rekonsiliasi_Bank_' . date('Ymd_His') . '.xlsx';
            
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
     * Export ke PDF
     */
    private function exportPdf($data, $filters, $stats, $ringkasanBank)
    {
        try {
            $html = $this->generatePdfHtml($data, $filters, $stats, $ringkasanBank);
            
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Rekonsiliasi_Bank_' . date('Ymd_His') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML untuk PDF
     */
    private function generatePdfHtml($data, $filters, $stats, $ringkasanBank)
    {
        $periodeText = '';
        if (!empty($filters['tahun'])) {
            $periodeText .= $filters['tahun'];
            if (!empty($filters['bulan'])) {
                $bulanOptions = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $periodeText .= ' - ' . ($bulanOptions[$filters['bulan']] ?? '');
            }
        } else {
            $periodeText = 'Semua Periode';
        }
        
        $filterInfo = [];
        if (!empty($filters['status'])) $filterInfo[] = 'Status: ' . $filters['status'];
        $filterText = implode(' | ', $filterInfo);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Rekonsiliasi Bank</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    line-height: 1.2;
                    margin: 15px;
                }
                h1 {
                    text-align: center;
                    font-size: 16px;
                    margin-bottom: 5px;
                }
                h2 {
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                    margin-top: 0;
                    margin-bottom: 10px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 15px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                    font-size: 8px;
                }
                th {
                    background-color: #4F81BD;
                    color: white;
                    padding: 5px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td {
                    padding: 4px;
                    border: 1px solid #000;
                    vertical-align: top;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .badge {
                    padding: 2px 4px;
                    border-radius: 2px;
                    font-weight: bold;
                    font-size: 7px;
                }
                .badge-success {
                    background-color: #28a745;
                    color: white;
                }
                .badge-warning {
                    background-color: #ffc107;
                    color: black;
                }
                .badge-danger {
                    background-color: #dc3545;
                    color: white;
                }
                .footer {
                    margin-top: 15px;
                    padding-top: 8px;
                    border-top: 1px solid #000;
                    font-size: 8px;
                }
                .summary-box {
                    float: left;
                    width: 18%;
                    margin: 0 1%;
                    padding: 5px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 3px;
                    text-align: center;
                }
                .summary-value {
                    font-size: 10px;
                    font-weight: bold;
                    margin-top: 3px;
                }
                .summary-label {
                    font-size: 7px;
                    color: #666;
                }
                .clearfix:after {
                    content: "";
                    display: table;
                    clear: both;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN REKONSILIASI BANK</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div style="font-size: 9px;">Periode: ' . $periodeText . '</div>
                ' . (!empty($filterText) ? '<div style="font-size: 9px;">' . $filterText . '</div>' : '') . '
            </div>
            
            <!-- Summary Cards -->
            <div class="clearfix" style="margin-bottom: 15px;">
                <div class="summary-box">
                    <div class="summary-label">Total Rekonsiliasi</div>
                    <div class="summary-value">' . number_format($stats['total_rekonsiliasi'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #d4edda;">
                    <div class="summary-label">Selesai</div>
                    <div class="summary-value">' . ($stats['selesai'] ?? 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #fff3cd;">
                    <div class="summary-label">Draft</div>
                    <div class="summary-value">' . ($stats['draft'] ?? 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #f8d7da;">
                    <div class="summary-label">Dibatalkan</div>
                    <div class="summary-value">' . ($stats['dibatalkan'] ?? 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #cce5ff;">
                    <div class="summary-label">Jumlah Bank</div>
                    <div class="summary-value">' . ($stats['jumlah_bank_direkonsiliasi'] ?? 0) . '</div>
                </div>
            </div>
            
            <!-- Ringkasan per Bank -->
            <h3 style="margin-bottom: 5px; font-size: 10px;">Ringkasan per Bank</h3>
            <table style="width: 60%; margin: 0 auto 15px auto;">
                <thead>
                    <tr>
                        <th>Bank / Akun</th>
                        <th>Jumlah</th>
                        <th>Selesai</th>
                        <th>Draft</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($ringkasanBank as $bank) {
            $html .= '
                    <tr>
                        <td>' . ($bank['kode_akun'] ?? '') . ' - ' . ($bank['nama_akun'] ?? '') . '</td>
                        <td class="text-center">' . ($bank['jumlah_rekonsiliasi'] ?? 0) . '</td>
                        <td class="text-center">' . ($bank['selesai'] ?? 0) . '</td>
                        <td class="text-center">' . ($bank['draft'] ?? 0) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <!-- Tabel Data Rekonsiliasi -->
            <h3 style="margin-bottom: 5px; font-size: 10px;">Detail Rekonsiliasi</h3>
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="6%">Periode</th>
                        <th width="10%">Bank</th>
                        <th width="8%">Saldo Akhir Bank</th>
                        <th width="8%">Saldo Akhir Buku</th>
                        <th width="6%">Selisih</th>
                        <th width="6%">Setoran</th>
                        <th width="6%">Cek</th>
                        <th width="6%">Penyes. Bank</th>
                        <th width="6%">Penyes. Buku</th>
                        <th width="8%">Tgl Rekonsiliasi</th>
                        <th width="5%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data rekonsiliasi</td>
                    </tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $selisih = ($item['Saldo Akhir Bank'] ?? 0) - ($item['Saldo Akhir Buku'] ?? 0);
                $statusClass = match($item['Status']) {
                    'Selesai' => 'badge-success',
                    'Draft' => 'badge-warning',
                    'Dibatalkan' => 'badge-danger',
                    default => ''
                };
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-center">' . $item['Periode'] . '</td>
                        <td><small>' . $item['Bank'] . '</small></td>
                        <td class="text-end">Rp ' . number_format($item['Saldo Akhir Bank'] ?? 0, 2) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Saldo Akhir Buku'] ?? 0, 2) . '</td>
                        <td class="text-end">Rp ' . number_format($selisih, 2) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Setoran Dalam Perjalanan'] ?? 0, 2) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Cek Dalam Edar'] ?? 0, 2) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Penyesuaian Bank'] ?? 0, 2) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Penyesuaian Buku'] ?? 0, 2) . '</td>
                        <td class="text-center">' . date('d/m/Y', strtotime($item['Tanggal Rekonsiliasi'])) . '</td>
                        <td class="text-center"><span class="badge ' . $statusClass . '">' . $item['Status'] . '</span></td>
                    </tr>';
                $no++;
            }
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <table style="width: 100%; border: none; margin-top: 5px;">
                    <tr>
                        <td style="border: none; text-align: left; width: 50%;">
                            <strong>Total Data:</strong> ' . count($data) . ' rekonsiliasi
                        </td>
                        <td style="border: none; text-align: right; width: 50%;">
                            Dicetak pada: ' . date('d/m/Y H:i:s') . '<br>
                            Oleh: ' . session()->get('name') . '
                        </td>
                    </tr>
                </table>
            </div>
            
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Print rekonsiliasi
     */
    public function print($id)
    {
        $rekonsiliasi = $this->rekonsiliasiModel->getWithDetails($id);
        
        if (!$rekonsiliasi) {
            return redirect()->to('accounting/kas-bank/rekonsiliasi')
                ->with('error', 'Rekonsiliasi tidak ditemukan');
        }
        
        // Format angka
        $rekonsiliasi['saldo_awal_bank_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_awal_bank']);
        $rekonsiliasi['saldo_akhir_bank_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_akhir_bank']);
        $rekonsiliasi['saldo_awal_buku_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_awal_buku']);
        $rekonsiliasi['saldo_akhir_buku_formatted'] = $this->formatRupiah($rekonsiliasi['saldo_akhir_buku']);
        $rekonsiliasi['selisih_formatted'] = $this->formatRupiah($rekonsiliasi['selisih']);
        
        $data['rekonsiliasi'] = $rekonsiliasi;
        $data['title'] = 'Print Rekonsiliasi Bank';
        
        return view('accounting/kas-bank/rekonsiliasi/print', $data);
    }

    /**
     * Fungsi untuk membersihkan format currency
     */
    private function cleanCurrency($value)
    {
        if (empty($value)) return 0;
        
        $value = str_replace('Rp', '', $value);
        $value = str_replace('rp', '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        $value = trim($value);
        
        return (float) $value;
    }

    /**
     * Fungsi untuk format currency ke Rupiah
     */
    private function formatRupiah($angka)
    {
        if (!$angka || $angka == 0) return '0';
        return number_format($angka, 0, ',', '.');
    }
}