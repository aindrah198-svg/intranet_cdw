<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\MutasiBankModel;
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use App\Models\Teknisi\SpkInstalasiModel;
use App\Models\Teknisi\SpkInstalasiPengeluaranModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Dompdf\Dompdf;
use Dompdf\Options;

class MutasiBank extends BaseController
{
    protected $mutasiBankModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $spkModel;
    protected $db;

    public function __construct()
    {
        $this->mutasiBankModel = new MutasiBankModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->spkModel = new \App\Models\Teknisi\SpkInstalasiModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Mutasi Bank
     */
    public function index()
    {
        $data['title'] = 'Daftar Mutasi Bank';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'tipe' => $this->request->getGet('tipe'),
            'status' => $this->request->getGet('status'),
            'bank' => $this->request->getGet('bank'),
            'coa_bank_id' => $this->request->getGet('coa_bank_id')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->mutasiBankModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['mutasi'] = $result['data'];
        $data['pager'] = $this->mutasiBankModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        $data['filters'] = $filters;
        
        $data['bankOptions'] = $this->mutasiBankModel->getCoaOptions(null, true);
        $data['statusOptions'] = ['Draft', 'Posted', 'Dibatalkan'];
        $data['tipeOptions'] = ['Masuk', 'Keluar'];
        
        // Hitung statistik
        $data['stats'] = $this->mutasiBankModel->getStats(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        // Ringkasan per bank
        $data['ringkasanBank'] = $this->mutasiBankModel->getRingkasanPerBank(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        return view('accounting/kas-bank/mutasi-bank/index', $data);
    }

    /**
     * Form tambah mutasi bank
     */
    public function create()
    {
        $data['title'] = 'Tambah Mutasi Bank';
        $data['validation'] = \Config\Services::validation();
        
        $data['tipe'] = $this->request->getGet('tipe') ?? 'Masuk';
        
        $data['coaBankOptions'] = $this->mutasiBankModel->getCoaOptions(null, true);
        $data['coaLawanOptions'] = $this->mutasiBankModel->getAllCoaOptions(false);
        $data['akunMasukOptions'] = $this->mutasiBankModel->getAllCoaOptions(false);
        $data['akunKeluarOptions'] = $this->mutasiBankModel->getAllCoaOptions(false);
        
        $data['spk_list'] = $this->spkModel
            ->select('id, nomor_spk, judul_pekerjaan, status')
            ->orderBy('id', 'DESC')
            ->findAll();
        
        $data['mutasi'] = [
            'tanggal' => date('Y-m-d'),
            'tipe' => $data['tipe'],
            'jumlah' => 0,
            'status' => 'Draft',
            'spk_id' => null
        ];
        
        return view('accounting/kas-bank/mutasi-bank/create', $data);
    }

    /**
     * Simpan mutasi bank baru
     */
    public function store()
    {
        $rules = [
            'tanggal' => 'required|valid_date',
            'tipe' => 'required|in_list[Masuk,Keluar]',
            'jumlah' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[3]',
            'spk_id' => 'permit_empty|numeric'
        ];
        
        $tipeUser = $this->request->getPost('tipe');
        
        if ($tipeUser === 'Keluar') {
            $rules['coa_id_debit'] = 'required|numeric';
            $rules['bank_asal'] = 'required|min_length[3]';
        } else {
            $rules['coa_id_kredit'] = 'required|numeric';
            $rules['bank_tujuan'] = 'required|min_length[3]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $jumlahPost = $this->request->getPost('jumlah');
        $jumlah = $this->cleanCurrency($jumlahPost);
        
        $tipeDatabase = ($tipeUser === 'Masuk') ? 'Kredit' : 'Debit';
        
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'tipe' => $tipeDatabase,
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'no_referensi' => $this->request->getPost('no_referensi'),
            'bank_asal' => $this->request->getPost('bank_asal'),
            'bank_tujuan' => $this->request->getPost('bank_tujuan'),
            'status' => 'Draft',
            'spk_id' => $this->request->getPost('spk_id') ?: null
        ];
        
        if ($tipeUser === 'Keluar') {
            $data['coa_id_debit'] = $this->request->getPost('coa_id_debit');
            $data['coa_id_kredit'] = null;
        } else {
            $data['coa_id_kredit'] = $this->request->getPost('coa_id_kredit');
            $data['coa_id_debit'] = null;
        }
        
        $lampiran = $this->request->getFile('lampiran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/mutasi-bank', $newName);
            $data['lampiran'] = 'uploads/mutasi-bank/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->mutasiBankModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->mutasiBankModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('success', 'Mutasi bank berhasil disimpan sebagai Draft.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan mutasi bank: ' . $e->getMessage());
        }
    }

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

    private function formatRupiah($angka)
    {
        if (!$angka || $angka == 0) return '0';
        return number_format($angka, 0, ',', '.');
    }

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

    public function ajaxGetTerbilang()
    {
        try {
            $jumlah = $this->request->getGet('jumlah');
            
            if (empty($jumlah)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jumlah tidak boleh kosong',
                    'terbilang' => ''
                ]);
            }
            
            $jumlahBersih = $this->cleanCurrency($jumlah);
            
            if ($jumlahBersih > 0) {
                $terbilang = ucwords($this->terbilang($jumlahBersih)) . ' Rupiah';
                
                return $this->response->setJSON([
                    'success' => true,
                    'terbilang' => $terbilang,
                    'jumlah_original' => $jumlah,
                    'jumlah_bersih' => $jumlahBersih
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jumlah harus lebih dari 0',
                'terbilang' => ''
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in ajaxGetTerbilang: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'terbilang' => ''
            ]);
        }
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Mutasi Bank';
        
        $mutasi = $this->mutasiBankModel->getWithDetails($id);
        
        if (!$mutasi) {
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('error', 'Mutasi bank tidak ditemukan');
        }
        
        $mutasi['tipe_user'] = $mutasi['tipe'] === 'Kredit' ? 'Masuk' : 'Keluar';
        $mutasi['jumlah_formatted'] = $this->formatRupiah($mutasi['jumlah']);
        
        $data['mutasi'] = $mutasi;
        $data['coaBankOptions'] = $this->mutasiBankModel->getCoaOptions(null, true);
        
        $selectedBankId = null;
        if ($mutasi['tipe'] == 'Debit' && !empty($mutasi['bank_asal'])) {
            foreach ($data['coaBankOptions'] as $bank) {
                if (stripos($bank['nama_akun'], $mutasi['bank_asal']) !== false) {
                    $selectedBankId = $bank['id'];
                    break;
                }
            }
        } elseif ($mutasi['tipe'] == 'Kredit' && !empty($mutasi['bank_tujuan'])) {
            foreach ($data['coaBankOptions'] as $bank) {
                if (stripos($bank['nama_akun'], $mutasi['bank_tujuan']) !== false) {
                    $selectedBankId = $bank['id'];
                    break;
                }
            }
        }
        
        $data['selectedBankId'] = $selectedBankId;
        
        return view('accounting/kas-bank/mutasi-bank/detail', $data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Mutasi Bank';
        
        $mutasi = $this->mutasiBankModel->find($id);
        
        if (!$mutasi) {
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('error', 'Mutasi bank tidak ditemukan');
        }
        
        if ($mutasi['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('error', 'Hanya mutasi dengan status Draft yang dapat diedit');
        }
        
        $mutasi['tipe_user'] = $mutasi['tipe'] === 'Kredit' ? 'Masuk' : 'Keluar';
        $mutasi['jumlah_formatted'] = $this->formatRupiah($mutasi['jumlah']);
        
        $data['validation'] = \Config\Services::validation();
        $data['mutasi'] = $mutasi;
        $data['tipe'] = $mutasi['tipe_user'];
        
        $data['coaBankOptions'] = $this->mutasiBankModel->getCoaOptions(null, true);
        $data['akunMasukOptions'] = $this->mutasiBankModel->getAllCoaOptions(false);
        $data['akunKeluarOptions'] = $this->mutasiBankModel->getAllCoaOptions(false);
        
        $data['spk_list'] = $this->spkModel
            ->select('id, nomor_spk, judul_pekerjaan, status')
            ->orderBy('id', 'DESC')
            ->findAll();
        
        return view('accounting/kas-bank/mutasi-bank/edit', $data);
    }

    public function update($id)
    {
        $mutasi = $this->mutasiBankModel->find($id);
        if (!$mutasi) {
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('error', 'Mutasi bank tidak ditemukan');
        }
        
        if ($mutasi['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('error', 'Hanya mutasi dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'tanggal' => 'required|valid_date',
            'tipe' => 'required|in_list[Masuk,Keluar]',
            'jumlah' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[3]',
            'spk_id' => 'permit_empty|numeric'
        ];
        
        $tipeUser = $this->request->getPost('tipe');
        
        if ($tipeUser === 'Keluar') {
            $rules['coa_id_debit'] = 'required|numeric';
            $rules['bank_asal'] = 'required|min_length[3]';
        } else {
            $rules['coa_id_kredit'] = 'required|numeric';
            $rules['bank_tujuan'] = 'required|min_length[3]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $jumlahPost = $this->request->getPost('jumlah');
        $jumlah = $this->cleanCurrency($jumlahPost);
        
        $tipeDatabase = ($tipeUser === 'Masuk') ? 'Kredit' : 'Debit';
        
        $data = [
            'id' => $id,
            'tanggal' => $this->request->getPost('tanggal'),
            'tipe' => $tipeDatabase,
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'no_referensi' => $this->request->getPost('no_referensi'),
            'bank_asal' => $this->request->getPost('bank_asal'),
            'bank_tujuan' => $this->request->getPost('bank_tujuan'),
            'spk_id' => $this->request->getPost('spk_id') ?: null
        ];
        
        if ($tipeUser === 'Keluar') {
            $data['coa_id_debit'] = $this->request->getPost('coa_id_debit');
            $data['coa_id_kredit'] = null;
        } else {
            $data['coa_id_kredit'] = $this->request->getPost('coa_id_kredit');
            $data['coa_id_debit'] = null;
        }
        
        $lampiran = $this->request->getFile('lampiran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            if (!empty($mutasi['lampiran']) && file_exists($mutasi['lampiran'])) {
                unlink($mutasi['lampiran']);
            }
            
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/mutasi-bank', $newName);
            $data['lampiran'] = 'uploads/mutasi-bank/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->mutasiBankModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->mutasiBankModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('success', 'Mutasi bank berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate mutasi bank: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $mutasi = $this->mutasiBankModel->find($id);
        if (!$mutasi) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Mutasi bank tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/mutasi-bank')
                    ->with('error', 'Mutasi bank tidak ditemukan');
            }
        }
        
        if ($mutasi['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya mutasi dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/mutasi-bank')
                    ->with('error', 'Hanya mutasi dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if (!empty($mutasi['lampiran']) && file_exists(FCPATH . $mutasi['lampiran'])) {
                unlink(FCPATH . $mutasi['lampiran']);
            }
            
            $deleted = $this->mutasiBankModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Mutasi bank berhasil dihapus',
                    'redirect' => site_url('accounting/kas-bank/mutasi-bank')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/mutasi-bank')
                    ->with('success', 'Mutasi bank berhasil dihapus.');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus mutasi bank: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus mutasi bank: ' . $e->getMessage());
            }
        }
    }

    /**
     * Posting mutasi bank ke jurnal
     */
    public function post($id)
    {
        $mutasi = $this->mutasiBankModel->find($id);
        if (!$mutasi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mutasi bank tidak ditemukan'
            ]);
        }
        
        if ($mutasi['status'] !== 'Draft') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya mutasi dengan status Draft yang bisa diposting'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            // Tentukan COA Bank
            $coaBankId = null;
            
            if ($mutasi['tipe'] === 'Debit') {
                $coaBank = $this->coaModel->where('is_header', 0)
                    ->like('kode_akun', '1-11', 'after')
                    ->groupStart()
                        ->like('nama_akun', $mutasi['bank_asal'])
                        ->orLike('kode_akun', $mutasi['bank_asal'])
                    ->groupEnd()
                    ->where('is_active', 1)
                    ->first();
                
                if (!$coaBank) {
                    $coaBank = $this->coaModel->where('is_header', 0)
                        ->like('kode_akun', '1-11', 'after')
                        ->where('is_active', 1)
                        ->orderBy('kode_akun', 'ASC')
                        ->first();
                }
            } else {
                $coaBank = $this->coaModel->where('is_header', 0)
                    ->like('kode_akun', '1-11', 'after')
                    ->groupStart()
                        ->like('nama_akun', $mutasi['bank_tujuan'])
                        ->orLike('kode_akun', $mutasi['bank_tujuan'])
                    ->groupEnd()
                    ->where('is_active', 1)
                    ->first();
                
                if (!$coaBank) {
                    $coaBank = $this->coaModel->where('is_header', 0)
                        ->like('kode_akun', '1-11', 'after')
                        ->where('is_active', 1)
                        ->orderBy('kode_akun', 'ASC')
                        ->first();
                }
            }
            
            if (!$coaBank) {
                throw new \Exception('Tidak ada akun kas/bank yang aktif. Harap pilih akun bank terlebih dahulu.');
            }
            
            $coaBankId = $coaBank['id'];
            
            // Buat jurnal
            $this->jurnalModel->skipValidation(true);
            
            $jurnalData = [
                'tanggal' => $mutasi['tanggal'],
                'keterangan' => $mutasi['keterangan'] . ' (' . $mutasi['kode_transaksi'] . ')',
                'referensi' => $mutasi['kode_transaksi'],
                'tipe_referensi' => 'mutasi_bank',
                'status' => 'posted',
                'posted_by' => session()->get('user_id'),
                'posted_at' => date('Y-m-d H:i:s'),
                'total_debit' => $mutasi['jumlah'],
                'total_kredit' => $mutasi['jumlah'],
                'created_by' => session()->get('user_id')
            ];
            
            $jurnalId = $this->jurnalModel->insert($jurnalData);
            if (!$jurnalId) {
                $errors = $this->jurnalModel->errors();
                log_message('error', 'Gagal insert jurnal: ' . json_encode($errors));
                $this->jurnalModel->skipValidation(false);
                throw new \Exception('Gagal membuat jurnal: ' . json_encode($errors));
            }
            
            $this->jurnalModel->skipValidation(false);
            
            $detailData = [];
            
            if ($mutasi['tipe'] === 'Kredit') {
                // Kredit = UANG MASUK ke perusahaan
                // Jurnal: Debit (Bank) - Kredit (Akun Lawan)
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaBankId,
                    'kode_akun' => $coaBank['kode_akun'],
                    'nama_akun' => $coaBank['nama_akun'],
                    'debit' => $mutasi['jumlah'],
                    'kredit' => 0,
                    'keterangan' => 'Penerimaan: ' . $mutasi['keterangan']
                ];
                
                $coaLawan = $this->coaModel->find($mutasi['coa_id_kredit']);
                if (!$coaLawan) {
                    throw new \Exception('Akun lawan tidak ditemukan');
                }
                
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $mutasi['coa_id_kredit'],
                    'kode_akun' => $coaLawan['kode_akun'],
                    'nama_akun' => $coaLawan['nama_akun'],
                    'debit' => 0,
                    'kredit' => $mutasi['jumlah'],
                    'keterangan' => 'Penerimaan: ' . $mutasi['keterangan']
                ];
                
                $this->mutasiBankModel->update($id, [
                    'coa_id_debit' => $coaBankId,
                    'coa_id_kredit' => $mutasi['coa_id_kredit']
                ]);
                
            } else {
                // Debit = UANG KELUAR dari perusahaan
                // Jurnal: Debit (Akun Lawan) - Kredit (Bank)
                $coaLawan = $this->coaModel->find($mutasi['coa_id_debit']);
                if (!$coaLawan) {
                    throw new \Exception('Akun lawan tidak ditemukan');
                }
                
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $mutasi['coa_id_debit'],
                    'kode_akun' => $coaLawan['kode_akun'],
                    'nama_akun' => $coaLawan['nama_akun'],
                    'debit' => $mutasi['jumlah'],
                    'kredit' => 0,
                    'keterangan' => 'Pengeluaran: ' . $mutasi['keterangan']
                ];
                
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaBankId,
                    'kode_akun' => $coaBank['kode_akun'],
                    'nama_akun' => $coaBank['nama_akun'],
                    'debit' => 0,
                    'kredit' => $mutasi['jumlah'],
                    'keterangan' => 'Pengeluaran: ' . $mutasi['keterangan']
                ];
                
                $this->mutasiBankModel->update($id, [
                    'coa_id_debit' => $mutasi['coa_id_debit'],
                    'coa_id_kredit' => $coaBankId
                ]);
            }
            
            foreach ($detailData as $detail) {
                if (!$this->jurnalDetailModel->insert($detail)) {
                    $errors = $this->jurnalDetailModel->errors();
                    log_message('error', 'Gagal insert detail jurnal: ' . json_encode($errors));
                    throw new \Exception('Gagal menyimpan detail jurnal: ' . json_encode($errors));
                }
            }
            
            $this->mutasiBankModel->postMutasi($id, $jurnalId);
            
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Mutasi bank berhasil diposting ke jurnal',
                'redirect' => site_url('accounting/kas-bank/mutasi-bank/detail/' . $id)
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error posting mutasi bank: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memposting mutasi bank: ' . $e->getMessage()
            ]);
        }
    }

    public function batalkan($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $mutasi = $this->mutasiBankModel->find($id);
        if (!$mutasi) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Mutasi bank tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/mutasi-bank')
                    ->with('error', 'Mutasi bank tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if ($mutasi['status'] === 'Posted' && !empty($mutasi['jurnal_id'])) {
                $this->jurnalModel->update($mutasi['jurnal_id'], ['status' => 'void']);
            }
            
            $this->mutasiBankModel->batalkanMutasi($id);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Mutasi bank berhasil dibatalkan',
                    'redirect' => site_url('accounting/kas-bank/mutasi-bank')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/mutasi-bank')
                    ->with('success', 'Mutasi bank berhasil dibatalkan');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan mutasi bank: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membatalkan mutasi bank: ' . $e->getMessage());
            }
        }
    }

   /**
 * Export PDF Laporan Mutasi Bank
 */
public function exportPdf()
{
    try {
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'tipe' => $this->request->getGet('tipe'),
            'status' => $this->request->getGet('status'),
            'coa_bank_id' => $this->request->getGet('coa_bank_id'),
            'spk_id' => $this->request->getGet('spk_id')
        ];
        
        // Gunakan method khusus untuk PDF dengan urutan ASC (terlama ke terbaru)
        $data = $this->mutasiBankModel->getExportDataForPdf($filters);
        $stats = $this->mutasiBankModel->getStats(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        // Dapatkan ringkasan per bank untuk ditampilkan di PDF
        $ringkasanBank = $this->mutasiBankModel->getRingkasanPerBank(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        // Format periode text
        $periodeText = '';
        if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai'])) {
            $periodeText = date('d/m/Y', strtotime($filters['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
        } elseif (!empty($filters['tanggal_mulai'])) {
            $periodeText = 'Mulai ' . date('d/m/Y', strtotime($filters['tanggal_mulai']));
        } elseif (!empty($filters['tanggal_selesai'])) {
            $periodeText = 'Sampai ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
        } else {
            $periodeText = 'Semua Periode';
        }
        
        $filterInfo = [];
        if (!empty($filters['tipe'])) $filterInfo[] = 'Tipe: ' . $filters['tipe'];
        if (!empty($filters['status'])) $filterInfo[] = 'Status: ' . $filters['status'];
        $filterText = implode(' | ', $filterInfo);
        
        $viewData = [
            'title' => 'Laporan Mutasi Bank',
            'data' => $data,
            'stats' => $stats,
            'ringkasanBank' => $ringkasanBank,
            'periode_text' => $periodeText,
            'filter_text' => $filterText,
            'user_name' => session()->get('name') ?? 'System',
            'date_generated' => date('d/m/Y H:i:s')
        ];
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml(view('accounting/kas-bank/mutasi-bank/pdf_template', $viewData));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $dompdf->stream('Laporan_Mutasi_Bank_' . date('Ymd_His') . '.pdf', ['Attachment' => 1]);
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Export PDF Mutasi Bank Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
    }
}

    public function ajaxGetCoa()
    {
        $tipe = $this->request->getGet('tipe');
        $isBank = $this->request->getGet('is_bank') === 'true';
        $allAkun = $this->request->getGet('all_akun') === 'true';
        
        if ($allAkun) {
            $coa = $this->mutasiBankModel->getAllCoaOptions($isBank);
        } else {
            $coa = $this->mutasiBankModel->getCoaOptions($tipe, $isBank);
        }
        
        $options = [];
        foreach ($coa as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_akun'] . ' - ' . $item['nama_akun']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    public function ajaxGetAllCoa()
    {
        $isBank = $this->request->getGet('is_bank') === 'true';
        
        $coa = $this->mutasiBankModel->getAllCoaOptions($isBank);
        
        $options = [];
        foreach ($coa as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_akun'] . ' - ' . $item['nama_akun']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    public function ajaxGetSaldo()
    {
        $coaBankId = $this->request->getGet('coa_bank_id');
        $tanggal = $this->request->getGet('tanggal');
        
        $saldo = $this->mutasiBankModel->getSaldoBank($coaBankId, $tanggal);
        
        return $this->response->setJSON([
            'success' => true,
            'saldo' => $this->formatRupiah($saldo),
            'saldo_raw' => $saldo
        ]);
    }

    public function ajaxValidate()
    {
        $tipe = $this->request->getPost('tipe');
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        $coaBankId = $this->request->getPost('coa_bank_id');
        
        $errors = [];
        
        if ($tipe === 'Keluar' && $coaBankId) {
            $saldo = $this->mutasiBankModel->getSaldoBank($coaBankId);
            if ($saldo < $jumlah) {
                $errors[] = 'Saldo tidak mencukupi. Saldo saat ini: Rp ' . $this->formatRupiah($saldo);
            }
        }
        
        return $this->response->setJSON([
            'success' => empty($errors),
            'errors' => $errors
        ]);
    }

    public function recalculateSaldo()
    {
        try {
            $this->mutasiBankModel->recalculateSaldo();
            
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('success', 'Rekalkulasi saldo berhasil dilakukan');
                
        } catch (\Exception $e) {
            return redirect()->to('accounting/kas-bank/mutasi-bank')
                ->with('error', 'Gagal rekalkulasi saldo: ' . $e->getMessage());
        }
    }
}