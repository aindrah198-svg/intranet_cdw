<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\LabaRugiModel;

class ModalPemilik extends BaseController
{
    protected $labaRugiModel;
    protected $db;

    public function __construct()
    {
        $this->labaRugiModel = new LabaRugiModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-01-01');
        $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = date('Y-01-01');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = date('Y-m-t');
        }

        // Laba Rugi Periode
        $laporanLabaRugi = $this->labaRugiModel->getLaporanLabaRugi($startDate, $endDate);
        $labaBersih = $laporanLabaRugi['laba_bersih'] ?? 0;

        // Modal Awal (Modal Pemilik COA type 'Ekuitas' / 'Modal' sebelum tanggal_mulai)
        $modalAwal = 0;
        if ($this->db->tableExists('buku_besar')) {
            $rowModal = $this->db->table('coa')
                ->select('(SUM(buku_besar.kredit) - SUM(buku_besar.debit)) as saldo')
                ->join('buku_besar', 'buku_besar.coa_id = coa.id')
                ->where('coa.tipe_akun', 'Ekuitas')
                ->where('coa.nama_akun NOT LIKE', '%Prive%')
                ->where('coa.nama_akun NOT LIKE', '%Deviden%')
                ->where('buku_besar.tanggal <', $startDate)
                ->where('buku_besar.status', 'processed')
                ->where('buku_besar.is_void', 0)
                ->get()->getRowArray();
            $modalAwal = (float)($rowModal['saldo'] ?? 0);
        }

        // Setoran Modal Tambahan Periode Ini
        $setoranModal = 0;
        if ($this->db->tableExists('buku_besar')) {
            $rowSetoran = $this->db->table('coa')
                ->select('(SUM(buku_besar.kredit) - SUM(buku_besar.debit)) as saldo')
                ->join('buku_besar', 'buku_besar.coa_id = coa.id')
                ->where('coa.tipe_akun', 'Ekuitas')
                ->where('coa.nama_akun NOT LIKE', '%Prive%')
                ->where('coa.nama_akun NOT LIKE', '%Deviden%')
                ->where('buku_besar.tanggal >=', $startDate)
                ->where('buku_besar.tanggal <=', $endDate)
                ->where('buku_besar.status', 'processed')
                ->where('buku_besar.is_void', 0)
                ->get()->getRowArray();
            $setoranModal = (float)($rowSetoran['saldo'] ?? 0);
        }

        // Prive / Penarikan Pemilik Periode Ini
        $prive = 0;
        if ($this->db->tableExists('buku_besar')) {
            $rowPrive = $this->db->table('coa')
                ->select('(SUM(buku_besar.debit) - SUM(buku_besar.kredit)) as saldo')
                ->join('buku_besar', 'buku_besar.coa_id = coa.id')
                ->where('coa.tipe_akun', 'Ekuitas')
                ->groupStart()
                    ->like('coa.nama_akun', 'Prive')
                    ->orLike('coa.nama_akun', 'Deviden')
                    ->orLike('coa.nama_akun', 'Penarikan')
                ->groupEnd()
                ->where('buku_besar.tanggal >=', $startDate)
                ->where('buku_besar.tanggal <=', $endDate)
                ->where('buku_besar.status', 'processed')
                ->where('buku_besar.is_void', 0)
                ->get()->getRowArray();
            $prive = (float)($rowPrive['saldo'] ?? 0);
        }

        // Modal Akhir = Modal Awal + Setoran Modal + Laba Bersih - Prive
        $modalAkhir = $modalAwal + $setoranModal + $labaBersih - $prive;

        $data = [
            'title' => 'Laporan Perubahan Modal Pemilik',
            'active' => 'laporan-keuangan',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'modalAwal' => $modalAwal,
            'setoranModal' => $setoranModal,
            'labaBersih' => $labaBersih,
            'prive' => $prive,
            'modalAkhir' => $modalAkhir,
        ];

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/laporan-keuangan/modal-pemilik/index', $data)
             . view('accounting/templates/footer', $data);
    }

    public function export()
    {
        return $this->index();
    }

    public function print()
    {
        return $this->index();
    }
}
