<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\AsetTetapModel;
use App\Models\Accounting\AsetTetapKategoriModel;
use App\Models\Accounting\PenyusutanModel;
use App\Models\CoaModel;
use App\Models\KaryawanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class AsetTetapRegister extends BaseController
{
    protected $asetModel;
    protected $kategoriModel;
    protected $penyusutanModel;
    protected $coaModel;
    protected $karyawanModel;
    protected $db;

    public function __construct()
    {
        $this->asetModel = new AsetTetapModel();
        $this->kategoriModel = new AsetTetapKategoriModel();
        $this->penyusutanModel = new PenyusutanModel();
        $this->coaModel = new CoaModel();
        $this->karyawanModel = new KaryawanModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Aset Tetap
     */
    public function index()
    {
        $data['title'] = 'Register Aset Tetap';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status' => $this->request->getGet('status'),
            'kondisi' => $this->request->getGet('kondisi'),
            'lokasi' => $this->request->getGet('lokasi'),
            'departemen' => $this->request->getGet('departemen'),
            'penanggung_jawab' => $this->request->getGet('penanggung_jawab'),
            'tanggal_perolehan_mulai' => $this->request->getGet('tanggal_perolehan_mulai'),
            'tanggal_perolehan_selesai' => $this->request->getGet('tanggal_perolehan_selesai'),
            'min_harga' => $this->request->getGet('min_harga'),
            'max_harga' => $this->request->getGet('max_harga')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->asetModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['aset'] = $result['data'];
        $data['pager'] = $this->asetModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        // Options untuk filter
        $data['kategoriOptions'] = $this->kategoriModel->getActiveOptions();
        $data['statusOptions'] = ['Aktif', 'Rusak', 'Dilepas', 'Dalam Perbaikan', 'Dihapus'];
        $data['kondisiOptions'] = ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Perlu Perbaikan'];
        $data['lokasiOptions'] = $this->getLokasiOptions();
        $data['departemenOptions'] = $this->getDepartemenOptions();
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan')->findAll();
        
        $data['stats'] = $this->asetModel->getStats();
        
        return view('accounting/aset-tetap/register/index', $data);
    }

    /**
     * Form tambah aset tetap
     */
    public function create()
    {
        $data['title'] = 'Tambah Aset Tetap';
        $data['validation'] = \Config\Services::validation();
        
        $data['kategoriOptions'] = $this->kategoriModel->getActiveOptions();
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        $data['metodePenyusutanOptions'] = ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi'];
        $data['statusOptions'] = ['Aktif', 'Rusak', 'Dalam Perbaikan'];
        $data['kondisiOptions'] = ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Perlu Perbaikan'];
        
        $data['aset'] = [
            'kode_aset' => '',
            'kategori_id' => '',
            'nama_aset' => '',
            'merk' => '',
            'model' => '',
            'serial_number' => '',
            'deskripsi' => '',
            'tanggal_perolehan' => date('Y-m-d'),
            'harga_perolehan' => 0,
            'nilai_residu' => 0,
            'masa_manfaat_tahun' => 5,
            'metode_penyusutan' => 'Garis Lurus',
            'lokasi' => '',
            'departemen' => '',
            'penanggung_jawab' => '',
            'status' => 'Aktif',
            'kondisi' => 'Baik',
            'catatan' => ''
        ];
        
        return view('accounting/aset-tetap/register/create', $data);
    }

    /**
     * Simpan aset tetap baru
     */
    public function store()
    {
        $rules = [
            'kategori_id' => 'required|is_natural_no_zero',
            'nama_aset' => 'required',
            'tanggal_perolehan' => 'required|valid_date',
            'harga_perolehan' => 'required|numeric|greater_than[0]',
            'nilai_residu' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'masa_manfaat_tahun' => 'permit_empty|is_natural|greater_than[0]',
            'metode_penyusutan' => 'permit_empty|in_list[Garis Lurus,Saldo Menurun,Unit Produksi]',
            'status' => 'permit_empty|in_list[Aktif,Rusak,Dalam Perbaikan]',
            'kondisi' => 'permit_empty|in_list[Baik,Rusak Ringan,Rusak Berat,Perlu Perbaikan]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'kategori_id' => $this->request->getPost('kategori_id'),
            'nama_aset' => $this->request->getPost('nama_aset'),
            'merk' => $this->request->getPost('merk'),
            'model' => $this->request->getPost('model'),
            'serial_number' => $this->request->getPost('serial_number'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan'),
            'harga_perolehan' => $this->cleanCurrency($this->request->getPost('harga_perolehan')),
            'nilai_residu' => $this->cleanCurrency($this->request->getPost('nilai_residu')) ?: 0,
            'masa_manfaat_tahun' => $this->request->getPost('masa_manfaat_tahun') ?: 5,
            'metode_penyusutan' => $this->request->getPost('metode_penyusutan') ?: 'Garis Lurus',
            'lokasi' => $this->request->getPost('lokasi'),
            'departemen' => $this->request->getPost('departemen'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab') ?: null,
            'status' => $this->request->getPost('status') ?: 'Aktif',
            'kondisi' => $this->request->getPost('kondisi') ?: 'Baik',
            'catatan' => $this->request->getPost('catatan')
        ];
        
        // Upload foto aset
        $fotoAset = $this->request->getFile('foto_aset');
        if ($fotoAset && $fotoAset->isValid() && !$fotoAset->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($fotoAset->getMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Tipe file foto tidak diizinkan. Hanya JPG, JPEG, PNG');
            }
            
            if ($fotoAset->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran file foto maksimal 2MB');
            }
            
            $newName = 'aset_' . date('Ymd_His') . '_' . uniqid() . '.' . $fotoAset->getExtension();
            $uploadPath = 'uploads/aset-tetap/foto/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $fotoAset->move(FCPATH . $uploadPath, $newName);
            $data['foto_aset'] = $uploadPath . '/' . $newName;
        }
        
        // Upload dokumen pembelian
        $dokumenPembelian = $this->request->getFile('dokumen_pembelian');
        if ($dokumenPembelian && $dokumenPembelian->isValid() && !$dokumenPembelian->hasMoved()) {
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($dokumenPembelian->getMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Tipe file dokumen tidak diizinkan. Hanya PDF, JPG, PNG');
            }
            
            if ($dokumenPembelian->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran file dokumen maksimal 5MB');
            }
            
            $newName = 'dokumen_' . date('Ymd_His') . '_' . uniqid() . '.' . $dokumenPembelian->getExtension();
            $uploadPath = 'uploads/aset-tetap/dokumen/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $dokumenPembelian->move(FCPATH . $uploadPath, $newName);
            $data['dokumen_pembelian'] = $uploadPath . '/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->asetModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->asetModel->errors()));
            }
            
            $this->db->transCommit();
            
            $id = $this->asetModel->insertID();
            
            return redirect()->to('accounting/aset-tetap/register/detail/' . $id)
                ->with('success', 'Aset tetap berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan aset tetap: ' . $e->getMessage());
        }
    }

    /**
     * Detail aset tetap
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Aset Tetap';
        
        $aset = $this->asetModel->getWithDetails($id);
        
        if (!$aset) {
            return redirect()->to('accounting/aset-tetap/register')
                ->with('error', 'Aset tetap tidak ditemukan');
        }
        
        $aset['harga_perolehan_formatted'] = $this->formatRupiah($aset['harga_perolehan']);
        $aset['nilai_residu_formatted'] = $this->formatRupiah($aset['nilai_residu']);
        $aset['akumulasi_penyusutan_formatted'] = $this->formatRupiah($aset['akumulasi_penyusutan']);
        $aset['nilai_buku_formatted'] = $this->formatRupiah($aset['nilai_buku']);
        
        $data['aset'] = $aset;
        
        return view('accounting/aset-tetap/register/detail', $data);
    }

    /**
     * Form edit aset tetap
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Aset Tetap';
        
        $aset = $this->asetModel->find($id);
        
        if (!$aset) {
            return redirect()->to('accounting/aset-tetap/register')
                ->with('error', 'Aset tetap tidak ditemukan');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['aset'] = $aset;
        
        $data['kategoriOptions'] = $this->kategoriModel->getActiveOptions();
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        $data['metodePenyusutanOptions'] = ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi'];
        $data['statusOptions'] = ['Aktif', 'Rusak', 'Dalam Perbaikan', 'Dilepas', 'Dihapus'];
        $data['kondisiOptions'] = ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Perlu Perbaikan'];
        
        return view('accounting/aset-tetap/register/edit', $data);
    }

    /**
     * Update aset tetap
     */
    public function update($id)
    {
        $aset = $this->asetModel->find($id);
        
        if (!$aset) {
            return redirect()->to('accounting/aset-tetap/register')
                ->with('error', 'Aset tetap tidak ditemukan');
        }
        
        $rules = [
            'kategori_id' => 'required|is_natural_no_zero',
            'nama_aset' => 'required',
            'tanggal_perolehan' => 'required|valid_date',
            'harga_perolehan' => 'required|numeric|greater_than[0]',
            'nilai_residu' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'masa_manfaat_tahun' => 'permit_empty|is_natural|greater_than[0]',
            'metode_penyusutan' => 'permit_empty|in_list[Garis Lurus,Saldo Menurun,Unit Produksi]',
            'status' => 'permit_empty|in_list[Aktif,Rusak,Dalam Perbaikan,Dilepas,Dihapus]',
            'kondisi' => 'permit_empty|in_list[Baik,Rusak Ringan,Rusak Berat,Perlu Perbaikan]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'kategori_id' => $this->request->getPost('kategori_id'),
            'nama_aset' => $this->request->getPost('nama_aset'),
            'merk' => $this->request->getPost('merk'),
            'model' => $this->request->getPost('model'),
            'serial_number' => $this->request->getPost('serial_number'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan'),
            'harga_perolehan' => $this->cleanCurrency($this->request->getPost('harga_perolehan')),
            'nilai_residu' => $this->cleanCurrency($this->request->getPost('nilai_residu')) ?: 0,
            'masa_manfaat_tahun' => $this->request->getPost('masa_manfaat_tahun') ?: 5,
            'metode_penyusutan' => $this->request->getPost('metode_penyusutan') ?: 'Garis Lurus',
            'lokasi' => $this->request->getPost('lokasi'),
            'departemen' => $this->request->getPost('departemen'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab') ?: null,
            'status' => $this->request->getPost('status') ?: 'Aktif',
            'kondisi' => $this->request->getPost('kondisi') ?: 'Baik',
            'catatan' => $this->request->getPost('catatan')
        ];
        
        // Upload foto aset baru
        $fotoAset = $this->request->getFile('foto_aset');
        if ($fotoAset && $fotoAset->isValid() && !$fotoAset->hasMoved()) {
            // Hapus foto lama
            if (!empty($aset['foto_aset']) && file_exists(FCPATH . $aset['foto_aset'])) {
                unlink(FCPATH . $aset['foto_aset']);
            }
            
            $newName = 'aset_' . date('Ymd_His') . '_' . uniqid() . '.' . $fotoAset->getExtension();
            $uploadPath = 'uploads/aset-tetap/foto/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $fotoAset->move(FCPATH . $uploadPath, $newName);
            $data['foto_aset'] = $uploadPath . '/' . $newName;
        }
        
        // Upload dokumen pembelian baru
        $dokumenPembelian = $this->request->getFile('dokumen_pembelian');
        if ($dokumenPembelian && $dokumenPembelian->isValid() && !$dokumenPembelian->hasMoved()) {
            // Hapus dokumen lama
            if (!empty($aset['dokumen_pembelian']) && file_exists(FCPATH . $aset['dokumen_pembelian'])) {
                unlink(FCPATH . $aset['dokumen_pembelian']);
            }
            
            $newName = 'dokumen_' . date('Ymd_His') . '_' . uniqid() . '.' . $dokumenPembelian->getExtension();
            $uploadPath = 'uploads/aset-tetap/dokumen/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $dokumenPembelian->move(FCPATH . $uploadPath, $newName);
            $data['dokumen_pembelian'] = $uploadPath . '/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->asetModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->asetModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/aset-tetap/register/detail/' . $id)
                ->with('success', 'Aset tetap berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate aset tetap: ' . $e->getMessage());
        }
    }

    /**
     * Hapus aset tetap
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $aset = $this->asetModel->find($id);
        
        if (!$aset) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Aset tetap tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/register')
                    ->with('error', 'Aset tetap tidak ditemukan');
            }
        }
        
        // Cek apakah bisa dihapus
        if (!$this->asetModel->canDelete($id)) {
            $message = 'Aset tidak dapat dihapus karena sudah memiliki riwayat penyusutan atau pelepasan';
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message
                ]);
            } else {
                return redirect()->back()
                    ->with('error', $message);
            }
        }
        
        try {
            $this->db->transBegin();
            
            // Hapus file foto
            if (!empty($aset['foto_aset']) && file_exists(FCPATH . $aset['foto_aset'])) {
                unlink(FCPATH . $aset['foto_aset']);
            }
            
            // Hapus file dokumen
            if (!empty($aset['dokumen_pembelian']) && file_exists(FCPATH . $aset['dokumen_pembelian'])) {
                unlink(FCPATH . $aset['dokumen_pembelian']);
            }
            
            $deleted = $this->asetModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Aset tetap berhasil dihapus',
                    'redirect' => site_url('accounting/aset-tetap/register')
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/register')
                    ->with('success', 'Aset tetap berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus aset tetap: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus aset tetap: ' . $e->getMessage());
            }
        }
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status' => $this->request->getGet('status'),
            'kondisi' => $this->request->getGet('kondisi'),
            'lokasi' => $this->request->getGet('lokasi'),
            'departemen' => $this->request->getGet('departemen'),
            'penanggung_jawab' => $this->request->getGet('penanggung_jawab'),
            'tanggal_perolehan_mulai' => $this->request->getGet('tanggal_perolehan_mulai'),
            'tanggal_perolehan_selesai' => $this->request->getGet('tanggal_perolehan_selesai'),
            'min_harga' => $this->request->getGet('min_harga'),
            'max_harga' => $this->request->getGet('max_harga')
        ];
        
        session()->set('filter_aset_register', $filters);
        
        return redirect()->to('accounting/aset-tetap/register');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_aset_register') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_aset_register', $filters);
        
        return redirect()->to('accounting/aset-tetap/register');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_aset_register');
        
        return redirect()->to('accounting/aset-tetap/register');
    }

    /**
     * Generate barcode untuk aset
     */
    public function generateBarcode($id)
    {
        $aset = $this->asetModel->find($id);
        
        if (!$aset) {
            return redirect()->to('accounting/aset-tetap/register')
                ->with('error', 'Aset tetap tidak ditemukan');
        }
        
        // Generate barcode menggunakan library
        $data['aset'] = $aset;
        $data['barcode'] = $aset['kode_aset'];
        
        return view('accounting/aset-tetap/register/barcode', $data);
    }

    /**
     * Print label aset
     */
    public function printLabel($id)
    {
        $aset = $this->asetModel->find($id);
        
        if (!$aset) {
            return redirect()->to('accounting/aset-tetap/register')
                ->with('error', 'Aset tetap tidak ditemukan');
        }
        
        $data['aset'] = $aset;
        
        return view('accounting/aset-tetap/register/print_label', $data);
    }

    /**
     * Batch print labels
     */
    public function batchPrintLabels()
    {
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return redirect()->back()
                ->with('error', 'Tidak ada aset yang dipilih');
        }
        
        $asetList = $this->asetModel->whereIn('id', $ids)->findAll();
        
        $data['aset_list'] = $asetList;
        
        return view('accounting/aset-tetap/register/batch_labels', $data);
    }

    /**
     * Import aset dari Excel
     */
    public function import()
    {
        $data['title'] = 'Import Aset Tetap';
        $data['validation'] = \Config\Services::validation();
        
        return view('accounting/aset-tetap/register/import', $data);
    }

    /**
     * Process import
     */
    public function processImport()
    {
        $file = $this->request->getFile('file_excel');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->withInput()
                ->with('error', 'File tidak valid');
        }
        
        $extension = $file->getExtension();
        if (!in_array($extension, ['xlsx', 'xls'])) {
            return redirect()->back()->withInput()
                ->with('error', 'File harus berformat Excel (.xlsx atau .xls)');
        }
        
        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            if ($extension === 'xls') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }
            
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Hapus header
            array_shift($rows);
            
            $success = 0;
            $failed = 0;
            $errors = [];
            
            $this->db->transBegin();
            
            foreach ($rows as $index => $row) {
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }
                
                try {
                    // Cari kategori berdasarkan nama
                    $kategori = $this->kategoriModel->where('nama_kategori', $row[2])->first();
                    if (!$kategori) {
                        throw new \Exception("Kategori '{$row[2]}' tidak ditemukan");
                    }
                    
                    $data = [
                        'kategori_id' => $kategori['id'],
                        'nama_aset' => $row[1],
                        'merk' => $row[3] ?? null,
                        'serial_number' => $row[4] ?? null,
                        'tanggal_perolehan' => $row[5] ?? date('Y-m-d'),
                        'harga_perolehan' => (float) $row[6],
                        'nilai_residu' => (float) ($row[7] ?? 0),
                        'masa_manfaat_tahun' => (int) ($row[8] ?? 5),
                        'metode_penyusutan' => $row[9] ?? 'Garis Lurus',
                        'lokasi' => $row[10] ?? null,
                        'departemen' => $row[11] ?? null,
                        'status' => $row[12] ?? 'Aktif',
                        'kondisi' => $row[13] ?? 'Baik'
                    ];
                    
                    $this->asetModel->save($data);
                    $success++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            $this->db->transCommit();
            
            $message = "Import selesai: {$success} berhasil, {$failed} gagal";
            if (!empty($errors)) {
                session()->setFlashdata('import_errors', $errors);
                return redirect()->back()->with('warning', $message);
            }
            
            return redirect()->to('accounting/aset-tetap/register')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    /**
     * Download template import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $headers = ['Kode Aset (kosongkan)', 'Nama Aset*', 'Kategori*', 'Merk', 'Serial Number', 'Tanggal Perolehan', 'Harga Perolehan*', 'Nilai Residu', 'Masa Manfaat', 'Metode Penyusutan', 'Lokasi', 'Departemen', 'Status', 'Kondisi'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Contoh data
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', 'Komputer Dell');
        $sheet->setCellValue('C2', 'Peralatan Kantor');
        $sheet->setCellValue('D2', 'Dell');
        $sheet->setCellValue('E2', 'SN123456');
        $sheet->setCellValue('F2', '2024-01-15');
        $sheet->setCellValue('G2', '15000000');
        $sheet->setCellValue('H2', '1000000');
        $sheet->setCellValue('I2', '5');
        $sheet->setCellValue('J2', 'Garis Lurus');
        $sheet->setCellValue('K2', 'Jakarta');
        $sheet->setCellValue('L2', 'IT');
        $sheet->setCellValue('M2', 'Aktif');
        $sheet->setCellValue('N2', 'Baik');
        
        // Style header
        $headerRange = 'A1:N1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4F81BD');
        $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
        
        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'Template_Import_Aset_Tetap.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    /**
     * Get lokasi options untuk dropdown
     */
    private function getLokasiOptions()
    {
        $asetList = $this->asetModel->findAll();
        $lokasi = array_unique(array_column($asetList, 'lokasi'));
        $lokasi = array_filter($lokasi);
        sort($lokasi);
        
        return $lokasi;
    }

    /**
     * Get departemen options
     */
    private function getDepartemenOptions()
    {
        $asetList = $this->asetModel->findAll();
        $departemen = array_unique(array_column($asetList, 'departemen'));
        $departemen = array_filter($departemen);
        sort($departemen);
        
        return $departemen;
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