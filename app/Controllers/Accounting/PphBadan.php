<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PphBadanModel;
use App\Models\Accounting\TarifPajakModel;
use App\Models\Accounting\SetoranPajakModel;
use App\Models\CoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PphBadan extends BaseController
{
    protected $pphBadanModel;
    protected $tarifPajakModel;
    protected $setoranPajakModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->pphBadanModel = new PphBadanModel();
        $this->tarifPajakModel = new TarifPajakModel();
        $this->setoranPajakModel = new SetoranPajakModel();
        $this->coaModel = new CoaModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Perhitungan PPh Badan
     */
    public function index()
    {
        $data['title'] = 'Daftar Perhitungan PPh Badan';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'periode' => $this->request->getGet('periode'),
            'tahun' => $this->request->getGet('tahun'),
            'status' => $this->request->getGet('status')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->pphBadanModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['pph'] = $result['data'];
        $data['pager'] = $this->pphBadanModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['periodeOptions'] = ['Tahunan', 'Triwulan'];
        $data['statusOptions'] = ['Draft', 'Selesai', 'Dilaporkan'];
        $data['tahunOptions'] = $this->getTahunOptions();
        
        $data['stats'] = $this->pphBadanModel->getStats();
        
        return view('accounting/manajemen-pajak/pph-badan/index', $data);
    }

    /**
     * Form tambah perhitungan PPh Badan
     */
    public function create()
    {
        $data['title'] = 'Tambah Perhitungan PPh Badan';
        $data['validation'] = \Config\Services::validation();
        
        $tahunSekarang = date('Y');
        
        $data['periodeOptions'] = ['Tahunan', 'Triwulan'];
        $data['triwulanOptions'] = [1 => 'Triwulan I (Jan-Mar)', 2 => 'Triwulan II (Apr-Jun)', 3 => 'Triwulan III (Jul-Sep)', 4 => 'Triwulan IV (Okt-Des)'];
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil tarif PPh Badan terbaru
        $tarifPph = $this->tarifPajakModel->getCurrentRate('PPh Badan');
        
        $data['pph'] = [
            'periode' => 'Tahunan',
            'tahun' => $tahunSekarang,
            'triwulan' => null,
            'penghasilan_bruto' => 0,
            'biaya_fiskal' => 0,
            'penghasilan_neto_fiskal' => 0,
            'kompensasi_kerugian' => 0,
            'pkp' => 0,
            'tarif' => $tarifPph,
            'pph_terutang' => 0,
            'kredit_pajak' => 0,
            'pph_kurang_bayar' => 0,
            'status' => 'Draft'
        ];
        
        return view('accounting/manajemen-pajak/pph-badan/create', $data);
    }

    /**
     * Simpan perhitungan PPh Badan baru
     */
    public function store()
    {
        $rules = [
            'periode' => 'required|in_list[Tahunan,Triwulan]',
            'tahun' => 'required|numeric|min_length[4]|max_length[4]',
            'triwulan' => 'permit_empty|numeric|in_list[1,2,3,4]',
            'penghasilan_bruto' => 'required|numeric|greater_than_equal_to[0]',
            'biaya_fiskal' => 'required|numeric|greater_than_equal_to[0]',
            'kompensasi_kerugian' => 'required|numeric|greater_than_equal_to[0]',
            'kredit_pajak' => 'required|numeric|greater_than_equal_to[0]',
            'tarif' => 'required|numeric|greater_than[0]|less_than_equal_to[100]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $periode = $this->request->getPost('periode');
        $tahun = $this->request->getPost('tahun');
        $triwulan = $this->request->getPost('triwulan');
        
        // Validasi untuk periode triwulan harus memiliki triwulan
        if ($periode === 'Triwulan' && empty($triwulan)) {
            return redirect()->back()->withInput()
                ->with('error', 'Untuk periode Triwulan, harus memilih triwulan');
        }
        
        // Cek apakah sudah ada untuk periode yang sama
        $existing = $this->pphBadanModel->where('periode', $periode)
            ->where('tahun', $tahun);
        if ($periode === 'Triwulan' && $triwulan) {
            $existing->where('triwulan', $triwulan);
        }
        $existing = $existing->first();
        
        if ($existing) {
            $periodeText = $periode === 'Tahunan' ? "Tahunan $tahun" : "Triwulan $triwulan $tahun";
            return redirect()->back()->withInput()
                ->with('error', "Perhitungan PPh Badan untuk $periodeText sudah ada");
        }
        
        $data = [
            'periode' => $periode,
            'tahun' => $tahun,
            'triwulan' => $triwulan ?: null,
            'penghasilan_bruto' => $this->cleanCurrency($this->request->getPost('penghasilan_bruto')),
            'biaya_fiskal' => $this->cleanCurrency($this->request->getPost('biaya_fiskal')),
            'kompensasi_kerugian' => $this->cleanCurrency($this->request->getPost('kompensasi_kerugian')),
            'kredit_pajak' => $this->cleanCurrency($this->request->getPost('kredit_pajak')),
            'tarif' => $this->request->getPost('tarif'),
            'status' => 'Draft'
        ];
        
        // Hitung penghasilan neto fiskal
        $data['penghasilan_neto_fiskal'] = $data['penghasilan_bruto'] - $data['biaya_fiskal'];
        
        try {
            $this->db->transBegin();
            
            $saved = $this->pphBadanModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->pphBadanModel->errors()));
            }
            
            $this->db->transCommit();
            
            $id = $this->pphBadanModel->insertID();
            
            return redirect()->to('accounting/manajemen-pajak/pph-badan/detail/' . $id)
                ->with('success', 'Perhitungan PPh Badan berhasil disimpan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan perhitungan PPh Badan: ' . $e->getMessage());
        }
    }

    /**
     * Detail perhitungan PPh Badan
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Perhitungan PPh Badan';
        
        $pph = $this->pphBadanModel->getWithDetails($id);
        
        if (!$pph) {
            return redirect()->to('accounting/manajemen-pajak/pph-badan')
                ->with('error', 'Data perhitungan PPh Badan tidak ditemukan');
        }
        
        $pph['penghasilan_bruto_formatted'] = $this->formatRupiah($pph['penghasilan_bruto']);
        $pph['biaya_fiskal_formatted'] = $this->formatRupiah($pph['biaya_fiskal']);
        $pph['penghasilan_neto_fiskal_formatted'] = $this->formatRupiah($pph['penghasilan_neto_fiskal']);
        $pph['kompensasi_kerugian_formatted'] = $this->formatRupiah($pph['kompensasi_kerugian']);
        $pph['pkp_formatted'] = $this->formatRupiah($pph['pkp']);
        $pph['pph_terutang_formatted'] = $this->formatRupiah($pph['pph_terutang']);
        $pph['kredit_pajak_formatted'] = $this->formatRupiah($pph['kredit_pajak']);
        $pph['pph_kurang_bayar_formatted'] = $this->formatRupiah($pph['pph_kurang_bayar']);
        
        // Hitung persentase dari penghasilan bruto
        $pph['persentase_pph_terhadap_bruto'] = $pph['penghasilan_bruto'] > 0 
            ? ($pph['pph_terutang'] / $pph['penghasilan_bruto']) * 100 
            : 0;
        
        $data['pph'] = $pph;
        
        return view('accounting/manajemen-pajak/pph-badan/detail', $data);
    }

    /**
     * Form edit perhitungan PPh Badan
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Perhitungan PPh Badan';
        
        $pph = $this->pphBadanModel->find($id);
        
        if (!$pph) {
            return redirect()->to('accounting/manajemen-pajak/pph-badan')
                ->with('error', 'Data perhitungan PPh Badan tidak ditemukan');
        }
        
        if ($pph['status'] !== 'Draft') {
            return redirect()->to('accounting/manajemen-pajak/pph-badan')
                ->with('error', 'Hanya perhitungan dengan status Draft yang dapat diedit');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['pph'] = $pph;
        
        $data['periodeOptions'] = ['Tahunan', 'Triwulan'];
        $data['triwulanOptions'] = [1 => 'Triwulan I (Jan-Mar)', 2 => 'Triwulan II (Apr-Jun)', 3 => 'Triwulan III (Jul-Sep)', 4 => 'Triwulan IV (Okt-Des)'];
        $data['tahunOptions'] = $this->getTahunOptions();
        
        return view('accounting/manajemen-pajak/pph-badan/edit', $data);
    }

    /**
     * Update perhitungan PPh Badan
     */
    public function update($id)
    {
        $pph = $this->pphBadanModel->find($id);
        
        if (!$pph) {
            return redirect()->to('accounting/manajemen-pajak/pph-badan')
                ->with('error', 'Data perhitungan PPh Badan tidak ditemukan');
        }
        
        if ($pph['status'] !== 'Draft') {
            return redirect()->to('accounting/manajemen-pajak/pph-badan')
                ->with('error', 'Hanya perhitungan dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'periode' => 'required|in_list[Tahunan,Triwulan]',
            'tahun' => 'required|numeric|min_length[4]|max_length[4]',
            'triwulan' => 'permit_empty|numeric|in_list[1,2,3,4]',
            'penghasilan_bruto' => 'required|numeric|greater_than_equal_to[0]',
            'biaya_fiskal' => 'required|numeric|greater_than_equal_to[0]',
            'kompensasi_kerugian' => 'required|numeric|greater_than_equal_to[0]',
            'kredit_pajak' => 'required|numeric|greater_than_equal_to[0]',
            'tarif' => 'required|numeric|greater_than[0]|less_than_equal_to[100]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $periode = $this->request->getPost('periode');
        $tahun = $this->request->getPost('tahun');
        $triwulan = $this->request->getPost('triwulan');
        
        // Validasi untuk periode triwulan harus memiliki triwulan
        if ($periode === 'Triwulan' && empty($triwulan)) {
            return redirect()->back()->withInput()
                ->with('error', 'Untuk periode Triwulan, harus memilih triwulan');
        }
        
        // Cek apakah sudah ada untuk periode yang sama (kecuali data sendiri)
        $existing = $this->pphBadanModel->where('periode', $periode)
            ->where('tahun', $tahun)
            ->where('id !=', $id);
        if ($periode === 'Triwulan' && $triwulan) {
            $existing->where('triwulan', $triwulan);
        }
        $existing = $existing->first();
        
        if ($existing) {
            $periodeText = $periode === 'Tahunan' ? "Tahunan $tahun" : "Triwulan $triwulan $tahun";
            return redirect()->back()->withInput()
                ->with('error', "Perhitungan PPh Badan untuk $periodeText sudah ada");
        }
        
        $data = [
            'id' => $id,
            'periode' => $periode,
            'tahun' => $tahun,
            'triwulan' => $triwulan ?: null,
            'penghasilan_bruto' => $this->cleanCurrency($this->request->getPost('penghasilan_bruto')),
            'biaya_fiskal' => $this->cleanCurrency($this->request->getPost('biaya_fiskal')),
            'kompensasi_kerugian' => $this->cleanCurrency($this->request->getPost('kompensasi_kerugian')),
            'kredit_pajak' => $this->cleanCurrency($this->request->getPost('kredit_pajak')),
            'tarif' => $this->request->getPost('tarif')
        ];
        
        // Hitung penghasilan neto fiskal
        $data['penghasilan_neto_fiskal'] = $data['penghasilan_bruto'] - $data['biaya_fiskal'];
        
        try {
            $this->db->transBegin();
            
            $updated = $this->pphBadanModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->pphBadanModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/pph-badan/detail/' . $id)
                ->with('success', 'Perhitungan PPh Badan berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate perhitungan PPh Badan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus perhitungan PPh Badan
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pph = $this->pphBadanModel->find($id);
        
        if (!$pph) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data perhitungan PPh Badan tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/pph-badan')
                    ->with('error', 'Data perhitungan PPh Badan tidak ditemukan');
            }
        }
        
        if ($pph['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya perhitungan dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/pph-badan')
                    ->with('error', 'Hanya perhitungan dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->pphBadanModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan PPh Badan berhasil dihapus',
                    'redirect' => site_url('accounting/manajemen-pajak/pph-badan')
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/pph-badan')
                    ->with('success', 'Perhitungan PPh Badan berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus perhitungan PPh Badan: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus perhitungan PPh Badan: ' . $e->getMessage());
            }
        }
    }

    /**
     * Mark as completed (Selesai)
     */
    public function markAsCompleted($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pph = $this->pphBadanModel->find($id);
        
        if (!$pph) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data perhitungan PPh Badan tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data perhitungan PPh Badan tidak ditemukan');
            }
        }
        
        if ($pph['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya perhitungan dengan status Draft yang dapat diselesaikan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya perhitungan dengan status Draft yang dapat diselesaikan');
            }
        }
        
        try {
            $result = $this->pphBadanModel->markAsCompleted($id);
            
            if (!$result) {
                throw new \Exception('Gagal menyelesaikan perhitungan');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan PPh Badan berhasil diselesaikan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/pph-badan/detail/' . $id)
                    ->with('success', 'Perhitungan PPh Badan berhasil diselesaikan');
            }
            
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', $e->getMessage());
            }
        }
    }

    /**
     * Mark as reported (Dilaporkan)
     */
    public function markAsReported($id)
    {
        $isAjax = $this->request->isAJAX();
        $tanggalLapor = $this->request->getPost('tanggal_lapor');
        
        $pph = $this->pphBadanModel->find($id);
        
        if (!$pph) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data perhitungan PPh Badan tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data perhitungan PPh Badan tidak ditemukan');
            }
        }
        
        if ($pph['status'] !== 'Selesai') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya perhitungan dengan status Selesai yang dapat dilaporkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya perhitungan dengan status Selesai yang dapat dilaporkan');
            }
        }
        
        try {
            $result = $this->pphBadanModel->markAsReported($id, $tanggalLapor);
            
            if (!$result) {
                throw new \Exception('Gagal menandai sebagai sudah dilaporkan');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan PPh Badan berhasil ditandai sebagai sudah dilaporkan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/pph-badan/detail/' . $id)
                    ->with('success', 'Perhitungan PPh Badan berhasil ditandai sebagai sudah dilaporkan');
            }
            
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', $e->getMessage());
            }
        }
    }

    /**
     * Calculate from financial (hitung otomatis dari laporan keuangan)
     */
    public function calculateFromFinancial()
    {
        $isAjax = $this->request->isAJAX();
        $tahun = $this->request->getPost('tahun') ?? date('Y');
        $periode = $this->request->getPost('periode') ?? 'Tahunan';
        $triwulan = $this->request->getPost('triwulan') ?? null;
        
        try {
            if ($periode === 'Tahunan') {
                $result = $this->pphBadanModel->calculateFromFinancial($tahun);
            } else {
                $result = $this->pphBadanModel->calculateFromFinancial($tahun, 'Triwulan', $triwulan);
            }
            
            // Format hasil
            $result['penghasilan_bruto_formatted'] = $this->formatRupiah($result['penghasilan_bruto']);
            $result['biaya_fiskal_formatted'] = $this->formatRupiah($result['biaya_fiskal']);
            $result['penghasilan_neto_fiskal_formatted'] = $this->formatRupiah($result['penghasilan_neto_fiskal']);
            $result['kompensasi_kerugian_formatted'] = $this->formatRupiah($result['kompensasi_kerugian']);
            $result['pkp_formatted'] = $this->formatRupiah($result['pkp']);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate for current year (buat perhitungan tahun berjalan)
     */
    public function generateCurrentYear()
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->pphBadanModel->calculateCurrentYear();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan PPh Badan tahun berjalan berhasil dibuat',
                    'redirect' => site_url('accounting/manajemen-pajak/pph-badan/detail/' . $result['id'])
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/pph-badan/detail/' . $result['id'])
                    ->with('success', 'Perhitungan PPh Badan tahun berjalan berhasil dibuat');
            }
            
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', $e->getMessage());
            }
        }
    }

    /**
     * Generate for quarter (buat perhitungan triwulan)
     */
    public function generateQuarter($tahun, $triwulan)
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->pphBadanModel->calculateQuarter($tahun, $triwulan);
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Perhitungan PPh Badan Triwulan $triwulan $tahun berhasil dibuat",
                    'redirect' => site_url('accounting/manajemen-pajak/pph-badan/detail/' . $result['id'])
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/pph-badan/detail/' . $result['id'])
                    ->with('success', "Perhitungan PPh Badan Triwulan $triwulan $tahun berhasil dibuat");
            }
            
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', $e->getMessage());
            }
        }
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'periode' => $this->request->getGet('periode'),
            'tahun' => $this->request->getGet('tahun'),
            'status' => $this->request->getGet('status')
        ];
        
        session()->set('filter_pph_badan', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/pph-badan');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_pph_badan') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_pph_badan', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/pph-badan');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_pph_badan');
        
        return redirect()->to('accounting/manajemen-pajak/pph-badan');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'tahun' => $this->request->getGet('tahun'),
            'periode' => $this->request->getGet('periode')
        ];
        
        $data = $this->pphBadanModel->getExportData($filters);
        
        if ($type === 'excel') {
            return $this->exportExcel($data, $filters);
        } else {
            return $this->exportPdf($data, $filters);
        }
    }

    /**
     * Export ke Excel
     */
    private function exportExcel($data, $filters)
    {
        try {
            $spreadsheet = new Spreadsheet();
            
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Daftar Perhitungan PPh Badan")
                ->setSubject("Daftar Perhitungan PPh Badan")
                ->setDescription("Daftar Perhitungan PPh Badan " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar PPh Badan');
            
            // Header laporan
            $sheet->mergeCells('A1:M1');
            $sheet->setCellValue('A1', 'DAFTAR PERHITUNGAN PPh BADAN');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:M2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:M3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Periode',
                'C' => 'Tahun',
                'D' => 'Penghasilan Bruto',
                'E' => 'Biaya Fiskal',
                'F' => 'Penghasilan Neto',
                'G' => 'Kompensasi',
                'H' => 'PKP',
                'I' => 'Tarif',
                'J' => 'PPh Terutang',
                'K' => 'Kredit Pajak',
                'L' => 'PPh Kurang Bayar',
                'M' => 'Status'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':M' . $startRow;
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
                $periodeText = $item['Periode'] === 'Tahunan' ? 'Tahunan' : 'Triwulan ' . substr($item['Periode'], -1);
                
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $periodeText);
                $sheet->setCellValue('C' . $row, $item['Tahun']);
                $sheet->setCellValue('D' . $row, $item['Penghasilan Bruto']);
                $sheet->setCellValue('E' . $row, $item['Biaya Fiskal']);
                $sheet->setCellValue('F' . $row, $item['Penghasilan Neto Fiskal']);
                $sheet->setCellValue('G' . $row, $item['Kompensasi Kerugian']);
                $sheet->setCellValue('H' . $row, $item['PKP']);
                $sheet->setCellValue('I' . $row, $item['Tarif']);
                $sheet->setCellValue('J' . $row, $item['PPh Terutang']);
                $sheet->setCellValue('K' . $row, $item['Kredit Pajak']);
                $sheet->setCellValue('L' . $row, $item['PPh Kurang Bayar']);
                $sheet->setCellValue('M' . $row, $item['Status']);
                
                // Format angka
                $sheet->getStyle('D' . $row . ':L' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                // Warna status
                if ($item['Status'] == 'Dilaporkan') {
                    $sheet->getStyle('M' . $row)->getFont()->getColor()->setARGB('FF008000');
                } elseif ($item['Status'] == 'Selesai') {
                    $sheet->getStyle('M' . $row)->getFont()->getColor()->setARGB('FF4F81BD');
                } else {
                    $sheet->getStyle('M' . $row)->getFont()->getColor()->setARGB('FFFFA500');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':M' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Daftar_PPh_Badan_' . date('Ymd_His') . '.xlsx';
            
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
    private function exportPdf($data, $filters)
    {
        try {
            $html = $this->generatePdfHtml($data, $filters);
            
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Daftar_PPh_Badan_' . date('Ymd_His') . '.pdf';
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
    private function generatePdfHtml($data, $filters)
    {
        $filterText = '';
        if (!empty($filters['tahun'])) {
            $filterText .= 'Tahun: ' . $filters['tahun'] . ' | ';
        }
        if (!empty($filters['periode'])) {
            $filterText .= 'Periode: ' . $filters['periode'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Perhitungan PPh Badan</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 9px;
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
                }
                th {
                    background-color: #4F81BD;
                    color: white;
                    padding: 6px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td {
                    padding: 5px;
                    border: 1px solid #000;
                    vertical-align: top;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .text-success {
                    color: #28a745;
                    font-weight: bold;
                }
                .text-warning {
                    color: #ffc107;
                    font-weight: bold;
                }
                .text-info {
                    color: #17a2b8;
                    font-weight: bold;
                }
                .footer {
                    margin-top: 15px;
                    padding-top: 8px;
                    border-top: 1px solid #000;
                    font-size: 8px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>DAFTAR PERHITUNGAN PPh BADAN</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="6%">Periode</th>
                        <th width="4%">Tahun</th>
                        <th width="10%">Penghasilan Bruto</th>
                        <th width="10%">Biaya Fiskal</th>
                        <th width="10%">Penghasilan Neto</th>
                        <th width="8%">Kompensasi</th>
                        <th width="10%">PKP</th>
                        <th width="5%">Tarif</th>
                        <th width="10%">PPh Terutang</th>
                        <th width="10%">Kredit Pajak</th>
                        <th width="10%">PPh Kurang Bayar</th>
                        <th width="4%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="13" class="text-center">Tidak ada data perhitungan PPh Badan</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $periodeText = $item['Periode'] === 'Tahunan' ? 'Tahunan' : 'Triwulan ' . substr($item['Periode'], -1);
                
                $statusClass = match($item['Status']) {
                    'Dilaporkan' => 'text-success',
                    'Selesai' => 'text-info',
                    default => 'text-warning'
                };
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-center">' . $periodeText . '</td>
                        <td class="text-center">' . $item['Tahun'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Penghasilan Bruto'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Biaya Fiskal'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Penghasilan Neto Fiskal'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Kompensasi Kerugian'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['PKP'], 0) . '</td>
                        <td class="text-center">' . $item['Tarif'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['PPh Terutang'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Kredit Pajak'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['PPh Kurang Bayar'], 0) . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Status'] . '</td>
                    </tr>';
                $no++;
            }
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left;">
                            <strong>Total Data:</strong> ' . count($data) . ' perhitungan
                        </td>
                        <td style="border: none; text-align: right;">
                            Dicetak oleh: ' . session()->get('name') . '
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * AJAX: Get kompensasi kerugian tersedia
     */
    public function ajaxGetKompensasiKerugian()
    {
        $tahun = $this->request->getGet('tahun');
        
        if (!$tahun) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tahun required'
            ]);
        }
        
        $kompensasi = $this->pphBadanModel->getKompensasiKerugianTersedia($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'kompensasi' => $kompensasi,
            'kompensasi_formatted' => $this->formatRupiah($kompensasi)
        ]);
    }

    /**
     * AJAX: Get total kredit pajak
     */
    public function ajaxGetTotalKreditPajak()
    {
        $tahun = $this->request->getGet('tahun');
        
        if (!$tahun) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tahun required'
            ]);
        }
        
        $totalKredit = $this->pphBadanModel->getTotalKreditPajak($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'total_kredit' => $totalKredit,
            'total_kredit_formatted' => $this->formatRupiah($totalKredit)
        ]);
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
}