<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\FakturPajakModel;
use App\Models\Accounting\PpnMasukanModel;
use App\Models\Accounting\PpnKeluaranModel;
use App\Models\Accounting\TarifPajakModel;
use App\Models\CoaModel;
use App\Models\InvoiceModel;
use App\Models\ProjectModel;
use App\Models\ClientModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class FakturPajak extends BaseController
{
    protected $fakturPajakModel;
    protected $ppnMasukanModel;
    protected $ppnKeluaranModel;
    protected $tarifPajakModel;
    protected $coaModel;
    protected $invoiceModel;
    protected $projectModel;
    protected $clientModel;
    protected $db;

    public function __construct()
    {
        $this->fakturPajakModel = new FakturPajakModel();
        $this->ppnMasukanModel = new PpnMasukanModel();
        $this->ppnKeluaranModel = new PpnKeluaranModel();
        $this->tarifPajakModel = new TarifPajakModel();
        $this->coaModel = new CoaModel();
        $this->invoiceModel = new InvoiceModel();
        $this->projectModel = new ProjectModel();
        $this->clientModel = new ClientModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number', 'filesystem']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Faktur Pajak
     */
    public function index()
    {
        $data['title'] = 'Daftar Faktur Pajak';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'jenis_faktur' => $this->request->getGet('jenis_faktur'),
            'status_approval' => $this->request->getGet('status_approval'),
            'status_lapor' => $this->request->getGet('status_lapor'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->fakturPajakModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['faktur'] = $result['data'];
        $data['pager'] = $this->fakturPajakModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['jenisFakturOptions'] = ['Masukan', 'Keluaran'];
        $data['statusApprovalOptions'] = ['Draft', 'Disetujui', 'Ditolak', 'Dibatalkan'];
        $data['statusLaporOptions'] = ['Belum Dilaporkan', 'Sudah Dilaporkan'];
        $data['tahunOptions'] = $this->getTahunOptions();
        
        $data['stats'] = $this->fakturPajakModel->getStats();
        
        return view('accounting/manajemen-pajak/faktur-pajak/index', $data);
    }

    /**
     * Form tambah faktur pajak
     */
    public function create()
    {
        $data['title'] = 'Tambah Faktur Pajak';
        $data['validation'] = \Config\Services::validation();
        
        $jenisFaktur = $this->request->getGet('jenis') ?? 'Keluaran';
        
        $data['jenis_faktur'] = $jenisFaktur;
        $data['jenisFakturOptions'] = ['Masukan', 'Keluaran'];
        
        // Ambil daftar invoice untuk faktur keluaran
        if ($jenisFaktur === 'Keluaran') {
            $data['invoiceOptions'] = $this->invoiceModel->select('id, nomor_invoice, tanggal_invoice, total')
                ->where('deleted_at IS NULL')
                ->orderBy('tanggal_invoice', 'DESC')
                ->findAll();
        }
        
        // Ambil tarif PPN terbaru
        $tarifPpn = $this->tarifPajakModel->getCurrentRate('PPN');
        $data['tarif_ppn'] = $tarifPpn;
        
        $data['faktur'] = [
            'tanggal_faktur' => date('Y-m-d'),
            'jenis_faktur' => $jenisFaktur,
            'npwp_pengusaha' => '',
            'nama_pengusaha' => '',
            'alamat_pengusaha' => '',
            'nilai_transaksi' => 0,
            'nilai_ppn' => 0,
            'tarif_ppn' => $tarifPpn,
            'masa_pajak' => date('m'),
            'tahun_pajak' => date('Y'),
            'keterangan' => '',
            'status_approval' => 'Draft'
        ];
        
        return view('accounting/manajemen-pajak/faktur-pajak/create', $data);
    }

    /**
     * Simpan faktur pajak baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'tanggal_faktur' => 'required|valid_date',
            'jenis_faktur' => 'required|in_list[Masukan,Keluaran]',
            'npwp_pengusaha' => 'required|min_length[15]|max_length[20]',
            'nama_pengusaha' => 'required',
            'nilai_transaksi' => 'required|numeric|greater_than[0]',
            'nilai_ppn' => 'required|numeric|greater_than_equal_to[0]',
            'tarif_ppn' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $jenisFaktur = $this->request->getPost('jenis_faktur');
        $invoiceId = $this->request->getPost('invoice_id');
        
        // Validasi untuk faktur keluaran harus memiliki invoice
        if ($jenisFaktur === 'Keluaran' && empty($invoiceId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Untuk faktur keluaran, harus memilih invoice');
        }
        
        $data = [
            'tanggal_faktur' => $this->request->getPost('tanggal_faktur'),
            'jenis_faktur' => $jenisFaktur,
            'invoice_id' => $invoiceId ?: null,
            'npwp_pengusaha' => $this->request->getPost('npwp_pengusaha'),
            'nama_pengusaha' => $this->request->getPost('nama_pengusaha'),
            'alamat_pengusaha' => $this->request->getPost('alamat_pengusaha'),
            'nilai_transaksi' => $this->cleanCurrency($this->request->getPost('nilai_transaksi')),
            'nilai_ppn' => $this->cleanCurrency($this->request->getPost('nilai_ppn')),
            'tarif_ppn' => $this->request->getPost('tarif_ppn'),
            'masa_pajak' => $this->request->getPost('masa_pajak'),
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status_approval' => 'Draft'
        ];
        
        // Upload file faktur
        $fileFaktur = $this->request->getFile('file_faktur');
        if ($fileFaktur && $fileFaktur->isValid() && !$fileFaktur->hasMoved()) {
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($fileFaktur->getMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Tipe file tidak diizinkan. Hanya PDF, JPG, PNG');
            }
            
            if ($fileFaktur->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran file maksimal 5MB');
            }
            
            $newName = 'faktur_' . date('Ymd_His') . '_' . uniqid() . '.' . $fileFaktur->getExtension();
            $uploadPath = 'uploads/faktur-pajak/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $fileFaktur->move(FCPATH . $uploadPath, $newName);
            $data['file_faktur'] = $uploadPath . '/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $fakturId = $this->fakturPajakModel->insert($data);
            
            if (!$fakturId) {
                throw new \Exception('Gagal menyimpan faktur: ' . json_encode($this->fakturPajakModel->errors()));
            }
            
            // Buat detail PPN Masukan atau Keluaran
            if ($jenisFaktur === 'Masukan') {
                $ppnMasukanData = [
                    'faktur_id' => $fakturId,
                    'tanggal_pembelian' => $data['tanggal_faktur'],
                    'supplier' => $data['nama_pengusaha'],
                    'npwp_supplier' => $data['npwp_pengusaha'],
                    'nilai_dpp' => $data['nilai_transaksi'],
                    'nilai_ppn' => $data['nilai_ppn'],
                    'masa_pajak' => $data['masa_pajak'],
                    'tahun_pajak' => $data['tahun_pajak'],
                    'status_kredit' => 'Belum Dikreditkan'
                ];
                $this->ppnMasukanModel->insert($ppnMasukanData);
                
            } else {
                // Ambil data invoice untuk detail
                $invoice = $this->invoiceModel->find($invoiceId);
                $customer = '';
                $npwpCustomer = '';
                if ($invoice) {
                    $project = $this->projectModel->find($invoice['project_id']);
                    if ($project) {
                        $client = $this->clientModel->find($project['client_id']);
                        if ($client) {
                            $customer = $client['nama_perusahaan'];
                            $npwpCustomer = $client['npwp'] ?? '';
                        }
                    }
                }
                
                $ppnKeluaranData = [
                    'faktur_id' => $fakturId,
                    'tanggal_penjualan' => $data['tanggal_faktur'],
                    'customer' => $customer ?: $data['nama_pengusaha'],
                    'npwp_customer' => $npwpCustomer ?: $data['npwp_pengusaha'],
                    'nomor_invoice' => $invoiceId,
                    'nilai_dpp' => $data['nilai_transaksi'],
                    'nilai_ppn' => $data['nilai_ppn'],
                    'masa_pajak' => $data['masa_pajak'],
                    'tahun_pajak' => $data['tahun_pajak'],
                    'status_setor' => 'Belum Disetor'
                ];
                $this->ppnKeluaranModel->insert($ppnKeluaranData);
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak/detail/' . $fakturId)
                ->with('success', 'Faktur pajak berhasil disimpan sebagai Draft.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan faktur pajak: ' . $e->getMessage());
        }
    }

    /**
     * Detail faktur pajak
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Faktur Pajak';
        
        $faktur = $this->fakturPajakModel->getWithDetails($id);
        
        if (!$faktur) {
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                ->with('error', 'Faktur pajak tidak ditemukan');
        }
        
        $faktur['nilai_transaksi_formatted'] = $this->formatRupiah($faktur['nilai_transaksi']);
        $faktur['nilai_ppn_formatted'] = $this->formatRupiah($faktur['nilai_ppn']);
        $faktur['terbilang'] = ucwords($this->terbilang($faktur['nilai_transaksi'])) . ' Rupiah';
        
        $data['faktur'] = $faktur;
        
        return view('accounting/manajemen-pajak/faktur-pajak/detail', $data);
    }

    /**
     * Form edit faktur pajak
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Faktur Pajak';
        
        $faktur = $this->fakturPajakModel->find($id);
        
        if (!$faktur) {
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                ->with('error', 'Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['status_approval'] !== 'Draft') {
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                ->with('error', 'Hanya faktur dengan status Draft yang dapat diedit');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['faktur'] = $faktur;
        $data['jenisFakturOptions'] = ['Masukan', 'Keluaran'];
        
        // Ambil daftar invoice untuk faktur keluaran
        if ($faktur['jenis_faktur'] === 'Keluaran') {
            $data['invoiceOptions'] = $this->invoiceModel->select('id, nomor_invoice, tanggal_invoice, total')
                ->where('deleted_at IS NULL')
                ->orderBy('tanggal_invoice', 'DESC')
                ->findAll();
        }
        
        return view('accounting/manajemen-pajak/faktur-pajak/edit', $data);
    }

    /**
     * Update faktur pajak
     */
    public function update($id)
    {
        $faktur = $this->fakturPajakModel->find($id);
        
        if (!$faktur) {
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                ->with('error', 'Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['status_approval'] !== 'Draft') {
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                ->with('error', 'Hanya faktur dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'tanggal_faktur' => 'required|valid_date',
            'jenis_faktur' => 'required|in_list[Masukan,Keluaran]',
            'npwp_pengusaha' => 'required|min_length[15]|max_length[20]',
            'nama_pengusaha' => 'required',
            'nilai_transaksi' => 'required|numeric|greater_than[0]',
            'nilai_ppn' => 'required|numeric|greater_than_equal_to[0]',
            'tarif_ppn' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'tanggal_faktur' => $this->request->getPost('tanggal_faktur'),
            'jenis_faktur' => $this->request->getPost('jenis_faktur'),
            'invoice_id' => $this->request->getPost('invoice_id') ?: null,
            'npwp_pengusaha' => $this->request->getPost('npwp_pengusaha'),
            'nama_pengusaha' => $this->request->getPost('nama_pengusaha'),
            'alamat_pengusaha' => $this->request->getPost('alamat_pengusaha'),
            'nilai_transaksi' => $this->cleanCurrency($this->request->getPost('nilai_transaksi')),
            'nilai_ppn' => $this->cleanCurrency($this->request->getPost('nilai_ppn')),
            'tarif_ppn' => $this->request->getPost('tarif_ppn'),
            'masa_pajak' => $this->request->getPost('masa_pajak'),
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        // Upload file faktur baru
        $fileFaktur = $this->request->getFile('file_faktur');
        if ($fileFaktur && $fileFaktur->isValid() && !$fileFaktur->hasMoved()) {
            // Hapus file lama
            if (!empty($faktur['file_faktur']) && file_exists(FCPATH . $faktur['file_faktur'])) {
                unlink(FCPATH . $faktur['file_faktur']);
            }
            
            $newName = 'faktur_' . date('Ymd_His') . '_' . uniqid() . '.' . $fileFaktur->getExtension();
            $uploadPath = 'uploads/faktur-pajak/' . date('Y/m');
            
            if (!is_dir(FCPATH . $uploadPath)) {
                mkdir(FCPATH . $uploadPath, 0755, true);
            }
            
            $fileFaktur->move(FCPATH . $uploadPath, $newName);
            $data['file_faktur'] = $uploadPath . '/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->fakturPajakModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate faktur: ' . json_encode($this->fakturPajakModel->errors()));
            }
            
            // Update detail PPN Masukan atau Keluaran
            if ($faktur['jenis_faktur'] === 'Masukan') {
                $ppnMasukan = $this->ppnMasukanModel->where('faktur_id', $id)->first();
                if ($ppnMasukan) {
                    $this->ppnMasukanModel->update($ppnMasukan['id'], [
                        'tanggal_pembelian' => $data['tanggal_faktur'],
                        'supplier' => $data['nama_pengusaha'],
                        'npwp_supplier' => $data['npwp_pengusaha'],
                        'nilai_dpp' => $data['nilai_transaksi'],
                        'nilai_ppn' => $data['nilai_ppn'],
                        'masa_pajak' => $data['masa_pajak'],
                        'tahun_pajak' => $data['tahun_pajak']
                    ]);
                }
            } else {
                $ppnKeluaran = $this->ppnKeluaranModel->where('faktur_id', $id)->first();
                if ($ppnKeluaran) {
                    $invoice = $this->invoiceModel->find($data['invoice_id']);
                    $customer = '';
                    $npwpCustomer = '';
                    if ($invoice) {
                        $project = $this->projectModel->find($invoice['project_id']);
                        if ($project) {
                            $client = $this->clientModel->find($project['client_id']);
                            if ($client) {
                                $customer = $client['nama_perusahaan'];
                                $npwpCustomer = $client['npwp'] ?? '';
                            }
                        }
                    }
                    
                    $this->ppnKeluaranModel->update($ppnKeluaran['id'], [
                        'tanggal_penjualan' => $data['tanggal_faktur'],
                        'customer' => $customer ?: $data['nama_pengusaha'],
                        'npwp_customer' => $npwpCustomer ?: $data['npwp_pengusaha'],
                        'nomor_invoice' => $data['invoice_id'],
                        'nilai_dpp' => $data['nilai_transaksi'],
                        'nilai_ppn' => $data['nilai_ppn'],
                        'masa_pajak' => $data['masa_pajak'],
                        'tahun_pajak' => $data['tahun_pajak']
                    ]);
                }
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak/detail/' . $id)
                ->with('success', 'Faktur pajak berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate faktur pajak: ' . $e->getMessage());
        }
    }

    /**
     * Hapus faktur pajak
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $faktur = $this->fakturPajakModel->find($id);
        
        if (!$faktur) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Faktur pajak tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                    ->with('error', 'Faktur pajak tidak ditemukan');
            }
        }
        
        if ($faktur['status_approval'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya faktur dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                    ->with('error', 'Hanya faktur dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            // Hapus file faktur
            if (!empty($faktur['file_faktur']) && file_exists(FCPATH . $faktur['file_faktur'])) {
                unlink(FCPATH . $faktur['file_faktur']);
            }
            
            // Hapus detail PPN
            if ($faktur['jenis_faktur'] === 'Masukan') {
                $this->ppnMasukanModel->where('faktur_id', $id)->delete();
            } else {
                $this->ppnKeluaranModel->where('faktur_id', $id)->delete();
            }
            
            // Hapus faktur
            $this->fakturPajakModel->delete($id);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Faktur pajak berhasil dihapus',
                    'redirect' => site_url('accounting/manajemen-pajak/faktur-pajak')
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                    ->with('success', 'Faktur pajak berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus faktur pajak: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus faktur pajak: ' . $e->getMessage());
            }
        }
    }

    /**
     * Approve faktur pajak
     */
    public function approve($id)
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->fakturPajakModel->approve($id);
            
            if (!$result) {
                throw new \Exception('Gagal menyetujui faktur');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Faktur pajak berhasil disetujui'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'Faktur pajak berhasil disetujui');
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
     * Reject faktur pajak
     */
    public function reject($id)
    {
        $isAjax = $this->request->isAJAX();
        $keterangan = $this->request->getPost('keterangan');
        
        try {
            $result = $this->fakturPajakModel->reject($id, $keterangan);
            
            if (!$result) {
                throw new \Exception('Gagal menolak faktur');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Faktur pajak berhasil ditolak'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'Faktur pajak berhasil ditolak');
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
     * Cancel faktur pajak
     */
    public function cancel($id)
    {
        $isAjax = $this->request->isAJAX();
        $keterangan = $this->request->getPost('keterangan');
        
        try {
            $result = $this->fakturPajakModel->cancel($id, $keterangan);
            
            if (!$result) {
                throw new \Exception('Gagal membatalkan faktur');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Faktur pajak berhasil dibatalkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'Faktur pajak berhasil dibatalkan');
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
     * Mark as reported (sudah dilaporkan ke DJP)
     */
    public function markAsReported($id)
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->fakturPajakModel->markAsReported($id);
            
            if (!$result) {
                throw new \Exception('Gagal menandai faktur sebagai sudah dilaporkan');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Faktur pajak berhasil ditandai sebagai sudah dilaporkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'Faktur pajak berhasil ditandai sebagai sudah dilaporkan');
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
     * Download file faktur
     */
    public function download($id)
    {
        $faktur = $this->fakturPajakModel->find($id);
        
        if (!$faktur) {
            return redirect()->to('accounting/manajemen-pajak/faktur-pajak')
                ->with('error', 'Faktur pajak tidak ditemukan');
        }
        
        if (empty($faktur['file_faktur']) || !file_exists(FCPATH . $faktur['file_faktur'])) {
            return redirect()->back()
                ->with('error', 'File faktur tidak ditemukan');
        }
        
        return $this->response->download(FCPATH . $faktur['file_faktur'], null);
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'jenis_faktur' => $this->request->getGet('jenis_faktur'),
            'status_approval' => $this->request->getGet('status_approval'),
            'status_lapor' => $this->request->getGet('status_lapor'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan')
        ];
        
        session()->set('filter_faktur_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/faktur-pajak');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_faktur_pajak') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_faktur_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/faktur-pajak');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_faktur_pajak');
        
        return redirect()->to('accounting/manajemen-pajak/faktur-pajak');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'jenis_faktur' => $this->request->getGet('jenis_faktur'),
            'status_approval' => $this->request->getGet('status_approval'),
            'status_lapor' => $this->request->getGet('status_lapor'),
            'tahun' => $this->request->getGet('tahun')
        ];
        
        $data = $this->fakturPajakModel->getExportData($filters);
        
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
                ->setTitle("Daftar Faktur Pajak")
                ->setSubject("Daftar Faktur Pajak")
                ->setDescription("Daftar Faktur Pajak " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar Faktur Pajak');
            
            // Header laporan
            $sheet->mergeCells('A1:L1');
            $sheet->setCellValue('A1', 'DAFTAR FAKTUR PAJAK');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:L2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:L3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Nomor Faktur',
                'C' => 'Tanggal Faktur',
                'D' => 'Jenis Faktur',
                'E' => 'NPWP',
                'F' => 'Nama Pengusaha',
                'G' => 'Nilai Transaksi',
                'H' => 'Tarif PPN',
                'I' => 'Nilai PPN',
                'J' => 'Masa Pajak',
                'K' => 'Status Approval',
                'L' => 'Status Lapor'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':L' . $startRow;
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
                $sheet->setCellValue('B' . $row, $item['Nomor Faktur']);
                $sheet->setCellValue('C' . $row, $item['Tanggal Faktur']);
                $sheet->setCellValue('D' . $row, $item['Jenis Faktur']);
                $sheet->setCellValue('E' . $row, $item['NPWP']);
                $sheet->setCellValue('F' . $row, $item['Nama Pengusaha']);
                $sheet->setCellValue('G' . $row, $item['Nilai Transaksi']);
                $sheet->setCellValue('H' . $row, $item['Tarif PPN']);
                $sheet->setCellValue('I' . $row, $item['Nilai PPN']);
                $sheet->setCellValue('J' . $row, $item['Masa Pajak']);
                $sheet->setCellValue('K' . $row, $item['Status Approval']);
                $sheet->setCellValue('L' . $row, $item['Status Lapor']);
                
                // Format angka
                $sheet->getStyle('G' . $row . ':I' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                // Warna status
                if ($item['Status Approval'] == 'Disetujui') {
                    $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB('FF008000');
                } elseif ($item['Status Approval'] == 'Ditolak') {
                    $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                } elseif ($item['Status Approval'] == 'Draft') {
                    $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB('FFFFA500');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':L' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Daftar_Faktur_Pajak_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_Faktur_Pajak_' . date('Ymd_His') . '.pdf';
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
        if (!empty($filters['jenis_faktur'])) {
            $filterText .= 'Jenis: ' . $filters['jenis_faktur'] . ' | ';
        }
        if (!empty($filters['status_approval'])) {
            $filterText .= 'Status: ' . $filters['status_approval'] . ' | ';
        }
        if (!empty($filters['tahun'])) {
            $filterText .= 'Tahun: ' . $filters['tahun'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Faktur Pajak</title>
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
                <h1>DAFTAR FAKTUR PAJAK</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Nomor Faktur</th>
                        <th width="8%">Tanggal</th>
                        <th width="6%">Jenis</th>
                        <th width="10%">NPWP</th>
                        <th width="12%">Nama Pengusaha</th>
                        <th width="8%">Nilai Transaksi</th>
                        <th width="6%">Tarif</th>
                        <th width="8%">Nilai PPN</th>
                        <th width="6%">Masa</th>
                        <th width="7%">Status</th>
                        <th width="6%">Lapor</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="12" class="text-center">Tidak ada data faktur pajak</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $statusClass = match($item['Status Approval']) {
                    'Disetujui' => 'text-success',
                    'Ditolak' => 'text-danger',
                    'Draft' => 'text-warning',
                    default => ''
                };
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Nomor Faktur'] . '</td>
                        <td class="text-center">' . $item['Tanggal Faktur'] . '</td>
                        <td class="text-center">' . $item['Jenis Faktur'] . '</td>
                        <td>' . $item['NPWP'] . '</td>
                        <td>' . $item['Nama Pengusaha'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai Transaksi'], 0) . '</td>
                        <td class="text-center">' . $item['Tarif PPN'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai PPN'], 0) . '</td>
                        <td class="text-center">' . $item['Masa Pajak'] . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Status Approval'] . '</td>
                        <td class="text-center">' . $item['Status Lapor'] . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' faktur
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
     * AJAX: Get invoice details for faktur keluaran
     */
    public function ajaxGetInvoiceDetails()
    {
        $invoiceId = $this->request->getGet('invoice_id');
        
        if (!$invoiceId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice ID required']);
        }
        
        $invoice = $this->invoiceModel->getWithDetails($invoiceId);
        
        if (!$invoice) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice not found']);
        }
        
        $tarifPpn = $this->tarifPajakModel->getCurrentRate('PPN');
        $nilaiPpn = $invoice['total'] * ($tarifPpn / 100);
        
        return $this->response->setJSON([
            'success' => true,
            'invoice' => [
                'id' => $invoice['id'],
                'nomor' => $invoice['nomor_invoice'],
                'tanggal' => $invoice['tanggal_invoice'],
                'total' => $invoice['total'],
                'total_formatted' => $this->formatRupiah($invoice['total']),
                'client_nama' => $invoice['client_nama'] ?? '',
                'client_npwp' => $invoice['client_npwp'] ?? '',
                'client_alamat' => $invoice['client_alamat'] ?? ''
            ],
            'tarif_ppn' => $tarifPpn,
            'nilai_ppn' => $nilaiPpn,
            'nilai_ppn_formatted' => $this->formatRupiah($nilaiPpn)
        ]);
    }

    /**
     * AJAX: Get PPN summary for SPT Masa
     */
    public function ajaxGetPpnSummary()
    {
        $masaPajak = $this->request->getGet('masa_pajak');
        $tahunPajak = $this->request->getGet('tahun_pajak');
        
        if (!$masaPajak || !$tahunPajak) {
            return $this->response->setJSON(['success' => false, 'message' => 'Masa dan tahun pajak required']);
        }
        
        $summary = $this->fakturPajakModel->getTotalPpnForSpt($masaPajak, $tahunPajak);
        
        return $this->response->setJSON([
            'success' => true,
            'masa_pajak' => $masaPajak,
            'tahun_pajak' => $tahunPajak,
            'total_ppn_masukan' => $summary['total_ppn_masukan'],
            'total_ppn_keluaran' => $summary['total_ppn_keluaran'],
            'ppn_kurang_bayar' => $summary['ppn_kurang_bayar'],
            'total_ppn_masukan_formatted' => $this->formatRupiah($summary['total_ppn_masukan']),
            'total_ppn_keluaran_formatted' => $this->formatRupiah($summary['total_ppn_keluaran']),
            'ppn_kurang_bayar_formatted' => $this->formatRupiah($summary['ppn_kurang_bayar'])
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
     * Fungsi untuk mendapatkan teks terbilang
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
        
        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return $baca[$angka - 10] . ' belas';
        } elseif ($angka < 100) {
            return $baca[floor($angka / 10)] . ' puluh ' . $baca[$angka % 10];
        } elseif ($angka < 200) {
            return 'seratus ' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $baca[floor($angka / 100)] . ' ratus ' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(floor($angka / 1000)) . ' ribu ' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(floor($angka / 1000000)) . ' juta ' . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return $this->terbilang(floor($angka / 1000000000)) . ' miliar ' . $this->terbilang($angka % 1000000000);
        } else {
            return $this->terbilang(floor($angka / 1000000000000)) . ' triliun ' . $this->terbilang($angka % 1000000000000);
        }
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