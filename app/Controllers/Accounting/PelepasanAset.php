<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\AsetTetapModel;
use App\Models\Accounting\AsetTetapKategoriModel;
use App\Models\Accounting\PelepasanAsetModel;
use App\Models\Accounting\PenyusutanModel;
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use App\Models\KaryawanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PelepasanAset extends BaseController
{
    protected $asetModel;
    protected $kategoriModel;
    protected $pelepasanModel;
    protected $penyusutanModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $karyawanModel;
    protected $db;

    public function __construct()
    {
        $this->asetModel = new AsetTetapModel();
        $this->kategoriModel = new AsetTetapKategoriModel();
        $this->pelepasanModel = new PelepasanAsetModel();
        $this->penyusutanModel = new PenyusutanModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->karyawanModel = new KaryawanModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Pelepasan Aset
     */
    public function index()
    {
        $data['title'] = 'Daftar Pelepasan Aset Tetap';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'aset_id' => $this->request->getGet('aset_id'),
            'kategori_id' => $this->request->getGet('kategori_id'),
            'jenis_pelepasan' => $this->request->getGet('jenis_pelepasan'),
            'status' => $this->request->getGet('status'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->pelepasanModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['pelepasan'] = $result['data'];
        $data['pager'] = $this->pelepasanModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        // Options untuk filter
        $data['asetOptions'] = $this->asetModel->getAsetOptions();
        $data['kategoriOptions'] = $this->kategoriModel->getActiveOptions();
        $data['jenisPelepasanOptions'] = ['Dijual', 'Dihibahkan', 'Dimusnahkan', 'Hilang', 'Tukar Tambah'];
        $data['statusOptions'] = ['Draft', 'Disetujui', 'Selesai', 'Dibatalkan'];
        
        $data['stats'] = $this->pelepasanModel->getStats();
        $data['statsPerJenis'] = $this->pelepasanModel->getRingkasanPerJenis();
        
        return view('accounting/aset-tetap/pelepasan/index', $data);
    }

    /**
     * Form tambah pelepasan aset
     */
    public function create()
    {
        $data['title'] = 'Tambah Pelepasan Aset Tetap';
        $data['validation'] = \Config\Services::validation();
        
        // Aset yang dapat dilepas (status Aktif dan belum ada pelepasan)
        $asetList = $this->asetModel->where('status', 'Aktif')
            ->where('deleted_at IS NULL')
            ->orderBy('kode_aset', 'ASC')
            ->findAll();
        
        $asetOptions = [];
        foreach ($asetList as $aset) {
            $nilaiBuku = $this->asetModel->getNilaiBuku($aset['id']);
            $aset['nilai_buku'] = $nilaiBuku;
            $aset['nilai_buku_formatted'] = $this->formatRupiah($nilaiBuku);
            $asetOptions[] = $aset;
        }
        
        $data['asetOptions'] = $asetOptions;
        $data['jenisPelepasanOptions'] = ['Dijual', 'Dihibahkan', 'Dimusnahkan', 'Hilang', 'Tukar Tambah'];
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        $data['pelepasan'] = [
            'aset_id' => '',
            'tanggal_pelepasan' => date('Y-m-d'),
            'jenis_pelepasan' => 'Dijual',
            'harga_jual' => 0,
            'biaya_pelepasan' => 0,
            'nilai_buku_saat_pelepasan' => 0,
            'laba_rugi' => 0,
            'keterangan' => '',
            'pembeli_penerima' => '',
            'status' => 'Draft'
        ];
        
        return view('accounting/aset-tetap/pelepasan/create', $data);
    }

    /**
     * Simpan pelepasan aset baru
     */
    public function store()
    {
        $rules = [
            'aset_id' => 'required|is_natural_no_zero',
            'tanggal_pelepasan' => 'required|valid_date',
            'jenis_pelepasan' => 'required|in_list[Dijual,Dihibahkan,Dimusnahkan,Hilang,Tukar Tambah]',
            'harga_jual' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'biaya_pelepasan' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'keterangan' => 'required',
            'pembeli_penerima' => 'permit_empty|string'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $asetId = $this->request->getPost('aset_id');
        
        // Cek apakah aset dapat dilepas
        if (!$this->pelepasanModel->canRelease($asetId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Aset tidak dapat dilepas. Pastikan aset berstatus Aktif dan belum ada pelepasan sebelumnya.');
        }
        
        // Ambil nilai buku aset
        $nilaiBuku = $this->asetModel->getNilaiBuku($asetId);
        
        $data = [
            'aset_id' => $asetId,
            'tanggal_pelepasan' => $this->request->getPost('tanggal_pelepasan'),
            'jenis_pelepasan' => $this->request->getPost('jenis_pelepasan'),
            'harga_jual' => $this->cleanCurrency($this->request->getPost('harga_jual')) ?: 0,
            'biaya_pelepasan' => $this->cleanCurrency($this->request->getPost('biaya_pelepasan')) ?: 0,
            'nilai_buku_saat_pelepasan' => $nilaiBuku,
            'keterangan' => $this->request->getPost('keterangan'),
            'pembeli_penerima' => $this->request->getPost('pembeli_penerima'),
            'status' => 'Draft'
        ];
        
        // Upload dokumen pelepasan
        $dokumenPelepasan = $this->request->getFile('dokumen_pelepasan');
        if ($dokumenPelepasan && $dokumenPelepasan->isValid() && !$dokumenPelepasan->hasMoved()) {
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($dokumenPelepasan->getMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Tipe file dokumen tidak diizinkan. Hanya PDF, JPG, PNG');
            }
            
            if ($dokumenPelepasan->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran file dokumen maksimal 5MB');
            }
            
            $newName = 'pelepasan_' . date('Ymd_His') . '_' . uniqid() . '.' . $dokumenPelepasan->getExtension();
            $uploadPath = 'uploads/aset-tetap/pelepasan/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $dokumenPelepasan->move(FCPATH . $uploadPath, $newName);
            $data['dokumen_pelepasan'] = $uploadPath . '/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->pelepasanModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->pelepasanModel->errors()));
            }
            
            $this->db->transCommit();
            
            $id = $this->pelepasanModel->insertID();
            
            return redirect()->to('accounting/aset-tetap/pelepasan/detail/' . $id)
                ->with('success', 'Pelepasan aset berhasil disimpan sebagai Draft.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan pelepasan aset: ' . $e->getMessage());
        }
    }

    /**
     * Detail pelepasan aset
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Pelepasan Aset Tetap';
        
        $pelepasan = $this->pelepasanModel->getWithDetails($id);
        
        if (!$pelepasan) {
            return redirect()->to('accounting/aset-tetap/pelepasan')
                ->with('error', 'Data pelepasan aset tidak ditemukan');
        }
        
        $pelepasan['harga_jual_formatted'] = $this->formatRupiah($pelepasan['harga_jual']);
        $pelepasan['biaya_pelepasan_formatted'] = $this->formatRupiah($pelepasan['biaya_pelepasan']);
        $pelepasan['nilai_buku_saat_pelepasan_formatted'] = $this->formatRupiah($pelepasan['nilai_buku_saat_pelepasan']);
        $pelepasan['laba_rugi_formatted'] = $this->formatRupiah($pelepasan['laba_rugi']);
        
        $data['pelepasan'] = $pelepasan;
        
        return view('accounting/aset-tetap/pelepasan/detail', $data);
    }

    /**
     * Form edit pelepasan aset
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Pelepasan Aset Tetap';
        
        $pelepasan = $this->pelepasanModel->find($id);
        
        if (!$pelepasan) {
            return redirect()->to('accounting/aset-tetap/pelepasan')
                ->with('error', 'Data pelepasan aset tidak ditemukan');
        }
        
        if ($pelepasan['status'] !== 'Draft') {
            return redirect()->to('accounting/aset-tetap/pelepasan')
                ->with('error', 'Hanya pelepasan dengan status Draft yang dapat diedit');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['pelepasan'] = $pelepasan;
        $data['jenisPelepasanOptions'] = ['Dijual', 'Dihibahkan', 'Dimusnahkan', 'Hilang', 'Tukar Tambah'];
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        return view('accounting/aset-tetap/pelepasan/edit', $data);
    }

    /**
     * Update pelepasan aset
     */
    public function update($id)
    {
        $pelepasan = $this->pelepasanModel->find($id);
        
        if (!$pelepasan) {
            return redirect()->to('accounting/aset-tetap/pelepasan')
                ->with('error', 'Data pelepasan aset tidak ditemukan');
        }
        
        if ($pelepasan['status'] !== 'Draft') {
            return redirect()->to('accounting/aset-tetap/pelepasan')
                ->with('error', 'Hanya pelepasan dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'tanggal_pelepasan' => 'required|valid_date',
            'jenis_pelepasan' => 'required|in_list[Dijual,Dihibahkan,Dimusnahkan,Hilang,Tukar Tambah]',
            'harga_jual' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'biaya_pelepasan' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'keterangan' => 'required',
            'pembeli_penerima' => 'permit_empty|string'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Ambil nilai buku aset terbaru
        $nilaiBuku = $this->asetModel->getNilaiBuku($pelepasan['aset_id']);
        
        $data = [
            'id' => $id,
            'tanggal_pelepasan' => $this->request->getPost('tanggal_pelepasan'),
            'jenis_pelepasan' => $this->request->getPost('jenis_pelepasan'),
            'harga_jual' => $this->cleanCurrency($this->request->getPost('harga_jual')) ?: 0,
            'biaya_pelepasan' => $this->cleanCurrency($this->request->getPost('biaya_pelepasan')) ?: 0,
            'nilai_buku_saat_pelepasan' => $nilaiBuku,
            'keterangan' => $this->request->getPost('keterangan'),
            'pembeli_penerima' => $this->request->getPost('pembeli_penerima')
        ];
        
        // Upload dokumen pelepasan baru
        $dokumenPelepasan = $this->request->getFile('dokumen_pelepasan');
        if ($dokumenPelepasan && $dokumenPelepasan->isValid() && !$dokumenPelepasan->hasMoved()) {
            // Hapus dokumen lama
            if (!empty($pelepasan['dokumen_pelepasan']) && file_exists(FCPATH . $pelepasan['dokumen_pelepasan'])) {
                unlink(FCPATH . $pelepasan['dokumen_pelepasan']);
            }
            
            $newName = 'pelepasan_' . date('Ymd_His') . '_' . uniqid() . '.' . $dokumenPelepasan->getExtension();
            $uploadPath = 'uploads/aset-tetap/pelepasan/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $dokumenPelepasan->move(FCPATH . $uploadPath, $newName);
            $data['dokumen_pelepasan'] = $uploadPath . '/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->pelepasanModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->pelepasanModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/aset-tetap/pelepasan/detail/' . $id)
                ->with('success', 'Pelepasan aset berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate pelepasan aset: ' . $e->getMessage());
        }
    }

    /**
     * Approve pelepasan aset
     */
    public function approve($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pelepasan = $this->pelepasanModel->find($id);
        
        if (!$pelepasan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data pelepasan aset tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data pelepasan aset tidak ditemukan');
            }
        }
        
        if ($pelepasan['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pelepasan dengan status Draft yang dapat disetujui'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya pelepasan dengan status Draft yang dapat disetujui');
            }
        }
        
        try {
            $result = $this->pelepasanModel->approve($id);
            
            if (!$result) {
                throw new \Exception('Gagal menyetujui pelepasan aset');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pelepasan aset berhasil disetujui'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/pelepasan/detail/' . $id)
                    ->with('success', 'Pelepasan aset berhasil disetujui');
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
     * Reject / batalkan pelepasan aset
     */
    public function reject($id)
    {
        $isAjax = $this->request->isAJAX();
        $alasan = $this->request->getPost('alasan');
        
        $pelepasan = $this->pelepasanModel->find($id);
        
        if (!$pelepasan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data pelepasan aset tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data pelepasan aset tidak ditemukan');
            }
        }
        
        if ($pelepasan['status'] !== 'Draft' && $pelepasan['status'] !== 'Disetujui') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pelepasan dengan status Draft atau Disetujui yang dapat dibatalkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya pelepasan dengan status Draft atau Disetujui yang dapat dibatalkan');
            }
        }
        
        try {
            $result = $this->pelepasanModel->reject($id, $alasan);
            
            if (!$result) {
                throw new \Exception('Gagal membatalkan pelepasan aset');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pelepasan aset berhasil dibatalkan'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/pelepasan')
                    ->with('success', 'Pelepasan aset berhasil dibatalkan');
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
     * Complete pelepasan aset (selesaikan dan posting ke jurnal)
     */
    public function complete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pelepasan = $this->pelepasanModel->getWithDetails($id);
        
        if (!$pelepasan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data pelepasan aset tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data pelepasan aset tidak ditemukan');
            }
        }
        
        if ($pelepasan['status'] !== 'Disetujui') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pelepasan dengan status Disetujui yang dapat diselesaikan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya pelepasan dengan status Disetujui yang dapat diselesaikan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            // Ambil data aset
            $aset = $this->asetModel->find($pelepasan['aset_id']);
            if (!$aset) {
                throw new \Exception('Aset tidak ditemukan');
            }
            
            // Ambil COA yang diperlukan
            $coaAset = $this->coaModel->find($aset['coa_aset_id']);
            $coaAkumulasi = $this->coaModel->find($aset['coa_akumulasi_id']);
            
            // COA untuk laba/rugi pelepasan (menggunakan akun pendapatan lain-lain atau beban lain-lain)
            $coaLaba = $this->coaModel->where('kode_akun', '4-1302')->first(); // Pendapatan lain-lain
            $coaRugi = $this->coaModel->where('kode_akun', '5-1901')->first(); // Beban lain-lain
            
            if (!$coaAset || !$coaAkumulasi) {
                throw new \Exception('COA untuk aset tidak lengkap');
            }
            
            // Buat jurnal
            $jurnalData = [
                'tanggal' => $pelepasan['tanggal_pelepasan'],
                'keterangan' => 'Pelepasan aset ' . $aset['nama_aset'] . ' (' . $pelepasan['jenis_pelepasan'] . ')',
                'referensi' => $aset['kode_aset'],
                'tipe_referensi' => 'pelepasan_aset',
                'total_debit' => 0,
                'total_kredit' => 0,
                'status' => 'posted',
                'posted_by' => session()->get('user_id'),
                'posted_at' => date('Y-m-d H:i:s'),
                'created_by' => session()->get('user_id')
            ];
            
            $jurnalId = $this->jurnalModel->insert($jurnalData);
            
            if (!$jurnalId) {
                throw new \Exception('Gagal membuat jurnal');
            }
            
            $totalDebit = 0;
            $totalKredit = 0;
            $detailData = [];
            
            // 1. Kredit akun aset (mengurangi aset)
            $detailData[] = [
                'jurnal_id' => $jurnalId,
                'coa_id' => $coaAset['id'],
                'kode_akun' => $coaAset['kode_akun'],
                'nama_akun' => $coaAset['nama_akun'],
                'debit' => 0,
                'kredit' => $aset['harga_perolehan'],
                'keterangan' => 'Pengurangan aset ' . $aset['nama_aset']
            ];
            $totalKredit += $aset['harga_perolehan'];
            
            // 2. Debit akumulasi penyusutan
            $akumulasi = $this->penyusutanModel->getLatestAkumulasiByAset($pelepasan['aset_id']);
            
            $detailData[] = [
                'jurnal_id' => $jurnalId,
                'coa_id' => $coaAkumulasi['id'],
                'kode_akun' => $coaAkumulasi['kode_akun'],
                'nama_akun' => $coaAkumulasi['nama_akun'],
                'debit' => $akumulasi,
                'kredit' => 0,
                'keterangan' => 'Penghapusan akumulasi penyusutan ' . $aset['nama_aset']
            ];
            $totalDebit += $akumulasi;
            
            // 3. Jika dijual, Debit Kas/Bank (harga jual)
            if ($pelepasan['jenis_pelepasan'] === 'Dijual' && $pelepasan['harga_jual'] > 0) {
                // Cari COA Kas/Bank default
                $coaKas = $this->coaModel->where('kode_akun', '1-1101')->first();
                if (!$coaKas) {
                    $coaKas = $this->coaModel->like('nama_akun', 'Kas')->first();
                }
                
                if ($coaKas) {
                    $detailData[] = [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaKas['id'],
                        'kode_akun' => $coaKas['kode_akun'],
                        'nama_akun' => $coaKas['nama_akun'],
                        'debit' => $pelepasan['harga_jual'],
                        'kredit' => 0,
                        'keterangan' => 'Penerimaan kas dari penjualan aset ' . $aset['nama_aset']
                    ];
                    $totalDebit += $pelepasan['harga_jual'];
                }
            }
            
            // 4. Jika ada biaya pelepasan, Kredit Kas/Bank
            if ($pelepasan['biaya_pelepasan'] > 0) {
                $coaKas = $this->coaModel->where('kode_akun', '1-1101')->first();
                if (!$coaKas) {
                    $coaKas = $this->coaModel->like('nama_akun', 'Kas')->first();
                }
                
                if ($coaKas) {
                    $detailData[] = [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaKas['id'],
                        'kode_akun' => $coaKas['kode_akun'],
                        'nama_akun' => $coaKas['nama_akun'],
                        'debit' => 0,
                        'kredit' => $pelepasan['biaya_pelepasan'],
                        'keterangan' => 'Biaya pelepasan aset ' . $aset['nama_aset']
                    ];
                    $totalKredit += $pelepasan['biaya_pelepasan'];
                }
            }
            
            // 5. Laba/Rugi pelepasan
            if ($pelepasan['laba_rugi'] != 0) {
                if ($pelepasan['laba_rugi'] > 0) {
                    // Laba: Kredit akun pendapatan lain-lain
                    if ($coaLaba) {
                        $detailData[] = [
                            'jurnal_id' => $jurnalId,
                            'coa_id' => $coaLaba['id'],
                            'kode_akun' => $coaLaba['kode_akun'],
                            'nama_akun' => $coaLaba['nama_akun'],
                            'debit' => 0,
                            'kredit' => $pelepasan['laba_rugi'],
                            'keterangan' => 'Laba pelepasan aset ' . $aset['nama_aset']
                        ];
                        $totalKredit += $pelepasan['laba_rugi'];
                    }
                } else {
                    // Rugi: Debit akun beban lain-lain
                    if ($coaRugi) {
                        $detailData[] = [
                            'jurnal_id' => $jurnalId,
                            'coa_id' => $coaRugi['id'],
                            'kode_akun' => $coaRugi['kode_akun'],
                            'nama_akun' => $coaRugi['nama_akun'],
                            'debit' => abs($pelepasan['laba_rugi']),
                            'kredit' => 0,
                            'keterangan' => 'Rugi pelepasan aset ' . $aset['nama_aset']
                        ];
                        $totalDebit += abs($pelepasan['laba_rugi']);
                    }
                }
            }
            
            // Validasi balance
            if ($totalDebit != $totalKredit) {
                throw new \Exception('Jurnal tidak balance: Debit ' . $totalDebit . ' vs Kredit ' . $totalKredit);
            }
            
            // Update total jurnal
            $this->jurnalModel->update($jurnalId, [
                'total_debit' => $totalDebit,
                'total_kredit' => $totalKredit
            ]);
            
            // Simpan detail jurnal
            foreach ($detailData as $detail) {
                if (!$this->jurnalDetailModel->insert($detail)) {
                    throw new \Exception('Gagal menyimpan detail jurnal');
                }
            }
            
            // Complete pelepasan
            $this->pelepasanModel->complete($id, $jurnalId);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pelepasan aset berhasil diselesaikan dan jurnal telah dibuat'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/pelepasan/detail/' . $id)
                    ->with('success', 'Pelepasan aset berhasil diselesaikan dan jurnal telah dibuat');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
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
     * Hapus pelepasan aset
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pelepasan = $this->pelepasanModel->find($id);
        
        if (!$pelepasan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data pelepasan aset tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/pelepasan')
                    ->with('error', 'Data pelepasan aset tidak ditemukan');
            }
        }
        
        if ($pelepasan['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pelepasan dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/pelepasan')
                    ->with('error', 'Hanya pelepasan dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            // Hapus dokumen
            if (!empty($pelepasan['dokumen_pelepasan']) && file_exists(FCPATH . $pelepasan['dokumen_pelepasan'])) {
                unlink(FCPATH . $pelepasan['dokumen_pelepasan']);
            }
            
            $deleted = $this->pelepasanModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pelepasan aset berhasil dihapus',
                    'redirect' => site_url('accounting/aset-tetap/pelepasan')
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/pelepasan')
                    ->with('success', 'Pelepasan aset berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus pelepasan aset: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus pelepasan aset: ' . $e->getMessage());
            }
        }
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'aset_id' => $this->request->getGet('aset_id'),
            'kategori_id' => $this->request->getGet('kategori_id'),
            'jenis_pelepasan' => $this->request->getGet('jenis_pelepasan'),
            'status' => $this->request->getGet('status'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai')
        ];
        
        session()->set('filter_pelepasan', $filters);
        
        return redirect()->to('accounting/aset-tetap/pelepasan');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_pelepasan') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_pelepasan', $filters);
        
        return redirect()->to('accounting/aset-tetap/pelepasan');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_pelepasan');
        
        return redirect()->to('accounting/aset-tetap/pelepasan');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'tahun' => $this->request->getGet('tahun'),
            'jenis_pelepasan' => $this->request->getGet('jenis_pelepasan')
        ];
        
        $data = $this->pelepasanModel->getExportData($filters);
        
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
                ->setTitle("Laporan Pelepasan Aset Tetap")
                ->setSubject("Laporan Pelepasan Aset Tetap")
                ->setDescription("Laporan Pelepasan Aset Tetap " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Pelepasan Aset');
            
            // Header laporan
            $sheet->mergeCells('A1:K1');
            $sheet->setCellValue('A1', 'LAPORAN PELEPASAN ASET TETAP');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:K3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Kode Aset',
                'C' => 'Nama Aset',
                'D' => 'Tanggal Pelepasan',
                'E' => 'Jenis Pelepasan',
                'F' => 'Harga Jual',
                'G' => 'Biaya',
                'H' => 'Nilai Buku',
                'I' => 'Laba/Rugi',
                'J' => 'Pembeli/Penerima',
                'K' => 'Status'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':K' . $startRow;
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
                $sheet->setCellValue('B' . $row, $item['Kode Aset']);
                $sheet->setCellValue('C' . $row, $item['Nama Aset']);
                $sheet->setCellValue('D' . $row, $item['Tanggal Pelepasan']);
                $sheet->setCellValue('E' . $row, $item['Jenis Pelepasan']);
                $sheet->setCellValue('F' . $row, $item['Harga Jual']);
                $sheet->setCellValue('G' . $row, $item['Biaya Pelepasan']);
                $sheet->setCellValue('H' . $row, $item['Nilai Buku']);
                $sheet->setCellValue('I' . $row, $item['Laba/Rugi']);
                $sheet->setCellValue('J' . $row, $item['Pembeli/Penerima']);
                $sheet->setCellValue('K' . $row, $item['Status']);
                
                // Format angka
                $sheet->getStyle('F' . $row . ':I' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                // Warna laba/rugi
                if ($item['Laba/Rugi'] > 0) {
                    $sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FF008000');
                } elseif ($item['Laba/Rugi'] < 0) {
                    $sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':K' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Laporan_Pelepasan_Aset_Tetap_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Laporan_Pelepasan_Aset_Tetap_' . date('Ymd_His') . '.pdf';
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
            $filterText .= 'Tahun: ' . $filters['tahun'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Pelepasan Aset Tetap</title>
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
                .text-danger {
                    color: #dc3545;
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
                <h1>LAPORAN PELEPASAN ASET TETAP</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Kode Aset</th>
                        <th width="12%">Nama Aset</th>
                        <th width="8%">Tanggal</th>
                        <th width="8%">Jenis</th>
                        <th width="8%">Harga Jual</th>
                        <th width="6%">Biaya</th>
                        <th width="8%">Nilai Buku</th>
                        <th width="8%">Laba/Rugi</th>
                        <th width="10%">Pembeli</th>
                        <th width="6%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="11" class="text-center">Tidak ada data pelepasan aset</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $labaRugiClass = $item['Laba/Rugi'] > 0 ? 'text-success' : ($item['Laba/Rugi'] < 0 ? 'text-danger' : '');
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Kode Aset'] . '</td>
                        <td>' . $item['Nama Aset'] . '</td>
                        <td class="text-center">' . $item['Tanggal Pelepasan'] . '</td>
                        <td>' . $item['Jenis Pelepasan'] . '</td>
                        <td class="text-end">' . number_format($item['Harga Jual'], 0) . '</td>
                        <td class="text-end">' . number_format($item['Biaya Pelepasan'], 0) . '</td>
                        <td class="text-end">' . number_format($item['Nilai Buku'], 0) . '</td>
                        <td class="text-end ' . $labaRugiClass . '">' . number_format($item['Laba/Rugi'], 0) . '</td>
                        <td>' . ($item['Pembeli/Penerima'] ?? '-') . '</td>
                        <td class="text-center">' . $item['Status'] . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' pelepasan
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
     * AJAX: Get nilai buku aset
     */
    public function ajaxGetNilaiBuku()
    {
        $asetId = $this->request->getGet('aset_id');
        
        if (!$asetId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aset ID required'
            ]);
        }
        
        $aset = $this->asetModel->find($asetId);
        
        if (!$aset) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aset tidak ditemukan'
            ]);
        }
        
        $nilaiBuku = $this->asetModel->getNilaiBuku($asetId);
        
        return $this->response->setJSON([
            'success' => true,
            'nilai_buku' => $nilaiBuku,
            'nilai_buku_formatted' => $this->formatRupiah($nilaiBuku),
            'harga_perolehan' => $aset['harga_perolehan'],
            'harga_perolehan_formatted' => $this->formatRupiah($aset['harga_perolehan'])
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
}