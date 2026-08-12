<?php

namespace App\Controllers\Direktur\Keuangan;

use App\Controllers\BaseController;
use App\Models\Direktur\KasbonModel;
use App\Models\KaryawanModel;

class KasbonController extends BaseController
{
    protected $db;
    protected $kasbonModel;
    protected $karyawanModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->kasbonModel = new KasbonModel();
        $this->karyawanModel = new KaryawanModel();
        $this->checkSchema();
    }

    private function checkSchema()
    {
        if ($this->db->tableExists('form_kasbon')) {
            $fields = $this->db->getFieldNames('form_kasbon');
            if (!in_array('sisa_pinjaman', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `sisa_pinjaman` DECIMAL(15,2) DEFAULT NULL");
                $this->db->query("UPDATE `form_kasbon` SET `sisa_pinjaman` = `jumlah_kasbon` WHERE `sisa_pinjaman` IS NULL");
            }
            if (!in_array('catatan', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `catatan` TEXT DEFAULT NULL");
            }
        }
    }

    private function parseNominal($val)
    {
        if (empty($val)) return 0;
        $str = (string)$val;
        $str = preg_replace('/[,.]00$/', '', $str);
        $clean = preg_replace('/[^0-9]/', '', $str);
        return floatval($clean);
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        
        $builder = $this->db->table('form_kasbon k')
            ->select('k.*, kar.nik, kar.nama_lengkap, kar.jabatan, kar.divisi, kar.foto')
            ->join('karyawan kar', 'kar.id = k.karyawan_id', 'left')
            ->where('k.deleted_at', null);

        if ($status === 'pending') {
            $builder->where('k.status_direktur', 'Menunggu');
        } elseif ($status === 'approved') {
            $builder->where('k.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('k.status_direktur', 'Ditolak');
        } elseif ($status === 'lunas') {
            $builder->where('k.status_keseluruhan', 'Lunas');
        }

        $kasbon = $builder->orderBy('k.id', 'DESC')->get()->getResultArray();

        // Hitung Ringkasan Statistiks
        $totalPinjaman = 0;
        $totalSisa = 0;
        $totalLunasCount = 0;
        $totalPendingCount = 0;

        foreach ($kasbon as $kb) {
            $totalPinjaman += floatval($kb['jumlah_kasbon'] ?? 0);
            $totalSisa += floatval($kb['sisa_pinjaman'] ?? $kb['jumlah_kasbon'] ?? 0);
            if (($kb['status_keseluruhan'] ?? '') === 'Lunas') {
                $totalLunasCount++;
            }
            if (($kb['status_direktur'] ?? '') === 'Menunggu') {
                $totalPendingCount++;
            }
        }

        $karyawanList = $this->karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title' => 'Persetujuan & Data Kasbon Karyawan',
            'kasbon' => $kasbon,
            'karyawanList' => $karyawanList,
            'filterStatus' => $status,
            'totalPinjaman' => $totalPinjaman,
            'totalSisa' => $totalSisa,
            'totalLunasCount' => $totalLunasCount,
            'totalPendingCount' => $totalPendingCount
        ];

        return view('direktur/keuangan/kasbon', $data);
    }

    /**
     * Tambah atau Edit Kasbon Karyawan oleh Direktur
     */
    public function simpan()
    {
        $id = $this->request->getPost('id');
        $karyawanId = $this->request->getPost('karyawan_id');
        $jumlahKasbon = $this->parseNominal($this->request->getPost('jumlah_kasbon'));
        $sisaPinjaman = $this->request->getPost('sisa_pinjaman') !== null ? $this->parseNominal($this->request->getPost('sisa_pinjaman')) : $jumlahKasbon;
        $alasanInput = $this->request->getPost('alasan') ?: $this->request->getPost('keperluan');
        $tglDibutuhkan = $this->request->getPost('tanggal_dibutuhkan') ?? date('Y-m-d');
        $metodeInput = $this->request->getPost('metode_pembayaran') ?: ($this->request->getPost('rencana_pelunasan') ?: 'Potong Gaji');
        $angsuranInput = intval($this->request->getPost('jumlah_angsuran') ?: 1);
        $statusDirektur = $this->request->getPost('status_direktur') ?? 'Disetujui';

        if (!$karyawanId || $jumlahKasbon <= 0) {
            return redirect()->back()->with('error', 'Karyawan dan jumlah kasbon harus diisi dengan benar.');
        }

        $statusKeseluruhan = ($sisaPinjaman <= 0) ? 'Lunas' : ($statusDirektur === 'Ditolak' ? 'Ditolak' : 'Belum Lunas');

        $data = [
            'karyawan_id'        => $karyawanId,
            'jumlah_kasbon'      => $jumlahKasbon,
            'sisa_pinjaman'      => $sisaPinjaman,
            'alasan'             => $alasanInput,
            'keperluan'          => $alasanInput,
            'metode_pembayaran'  => $metodeInput,
            'jumlah_angsuran'    => $angsuranInput,
            'tanggal_dibutuhkan' => $tglDibutuhkan,
            'rencana_pelunasan'  => $metodeInput,
            'status_hrd'         => 'Disetujui HRD',
            'status_direktur'    => $statusDirektur,
            'status_keseluruhan' => $statusKeseluruhan,
            'updated_at'         => date('Y-m-d H:i:s')
        ];

        if ($id) {
            $this->db->table('form_kasbon')->where('id', $id)->update($data);
            $msg = 'Data kasbon karyawan berhasil diperbarui.';
        } else {
            $data['nomor_kasbon'] = 'KSB-' . date('Ym') . '-' . sprintf('%03d', rand(1, 999));
            $data['tanggal_pengajuan'] = date('Y-m-d');
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('form_kasbon')->insert($data);
            $msg = 'Pengajuan kasbon baru berhasil ditambahkan.';
        }

        return redirect()->to('direktur/keuangan/kasbon')->with('success', $msg);
    }

    /**
     * Hapus Kasbon Karyawan
     */
    public function delete($id)
    {
        if ($id) {
            $this->db->table('form_kasbon')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
            return redirect()->to('direktur/keuangan/kasbon')->with('success', 'Data kasbon berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Gagal menghapus data kasbon.');
    }

    public function approve()
    {
        $id = $this->request->getPost('id');
        $catatan = $this->request->getPost('catatan') ?? 'Disetujui Direktur';

        if ($id) {
            $kasbon = $this->db->table('form_kasbon')->where('id', $id)->get()->getRowArray();
            $sisa = isset($kasbon['sisa_pinjaman']) && $kasbon['sisa_pinjaman'] !== null ? $kasbon['sisa_pinjaman'] : $kasbon['jumlah_kasbon'];
            
            $this->db->table('form_kasbon')->where('id', $id)->update([
                'status_direktur'    => 'Disetujui',
                'status_keseluruhan' => 'Belum Lunas',
                'sisa_pinjaman'      => $sisa,
                'catatan'            => $catatan,
                'updated_at'         => date('Y-m-d H:i:s')
            ]);
            return redirect()->back()->with('success', 'Pengajuan kasbon berhasil disetujui.');
        }

        return redirect()->back()->with('error', 'Gagal memproses persetujuan kasbon.');
    }

    public function reject()
    {
        $id = $this->request->getPost('id');
        $alasan = $this->request->getPost('alasan') ?? 'Ditolak Direktur';

        if ($id) {
            $this->db->table('form_kasbon')->where('id', $id)->update([
                'status_direktur'          => 'Ditolak',
                'status_keseluruhan'       => 'Ditolak',
                'alasan_penolakan_direktur'=> $alasan,
                'updated_at'               => date('Y-m-d H:i:s')
            ]);
            return redirect()->back()->with('success', 'Pengajuan kasbon telah ditolak.');
        }

        return redirect()->back()->with('error', 'Gagal memproses penolakan kasbon.');
    }

    public function cetak()
    {
        $status = $this->request->getGet('status');
        
        $builder = $this->db->table('form_kasbon k')
            ->select('k.*, kar.nik, kar.nama_lengkap, kar.jabatan, kar.divisi')
            ->join('karyawan kar', 'kar.id = k.karyawan_id', 'left')
            ->where('k.deleted_at', null);

        if ($status === 'pending') {
            $builder->where('k.status_direktur', 'Menunggu');
        } elseif ($status === 'approved') {
            $builder->where('k.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('k.status_direktur', 'Ditolak');
        } elseif ($status === 'lunas') {
            $builder->where('k.status_keseluruhan', 'Lunas');
        }

        $kasbon = $builder->orderBy('k.id', 'DESC')->get()->getResultArray();

        $data = [
            'title' => 'Rekapitulasi Persetujuan Kasbon Karyawan',
            'kasbon' => $kasbon,
            'filterStatus' => $status,
            'tanggalCetak' => date('d F Y')
        ];

        return view('direktur/keuangan/cetak_kasbon', $data);
    }

    public function export_excel()
    {
        $status = $this->request->getGet('status');
        
        $builder = $this->db->table('form_kasbon k')
            ->select('k.*, kar.nik, kar.nama_lengkap, kar.jabatan, kar.divisi')
            ->join('karyawan kar', 'kar.id = k.karyawan_id', 'left')
            ->where('k.deleted_at', null);

        if ($status === 'pending') {
            $builder->where('k.status_direktur', 'Menunggu');
        } elseif ($status === 'approved') {
            $builder->where('k.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('k.status_direktur', 'Ditolak');
        } elseif ($status === 'lunas') {
            $builder->where('k.status_keseluruhan', 'Lunas');
        }

        $kasbon = $builder->orderBy('k.id', 'DESC')->get()->getResultArray();

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Rekap_Kasbon_Karyawan.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<thead><tr>
                <th>No</th>
                <th>No Kasbon</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Tanggal Pengajuan</th>
                <th>Jumlah Kasbon (Rp)</th>
                <th>Sisa Pinjaman (Rp)</th>
                <th>Alasan</th>
                <th>Status Direktur</th>
                <th>Status Keseluruhan</th>
              </tr></thead>";
        echo "<tbody>";

        $no = 1;
        foreach ($kasbon as $k) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . esc($k['nomor_kasbon'] ?? '-') . "</td>";
            echo "<td>" . esc($k['nik'] ?? '-') . "</td>";
            echo "<td>" . esc($k['nama_lengkap'] ?? '-') . "</td>";
            echo "<td>" . esc($k['jabatan'] ?? '-') . "</td>";
            echo "<td>" . esc($k['tanggal_pengajuan'] ?? '-') . "</td>";
            echo "<td>" . ($k['jumlah_kasbon'] ?? 0) . "</td>";
            echo "<td>" . ($k['sisa_pinjaman'] ?? $k['jumlah_kasbon'] ?? 0) . "</td>";
            echo "<td>" . esc($k['alasan'] ?? '-') . "</td>";
            echo "<td>" . esc($k['status_direktur'] ?? 'Menunggu') . "</td>";
            echo "<td>" . esc($k['status_keseluruhan'] ?? 'Belum Lunas') . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        exit;
    }
}
