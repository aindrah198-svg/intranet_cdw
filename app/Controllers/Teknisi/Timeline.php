<?php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Models\Teknisi\TimelineModel;

class Timeline extends BaseController
{
    protected $timelineModel;
    
    public function __construct()
    {
        $this->timelineModel = new TimelineModel();
        
        // Cek login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        $data['title'] = 'Timeline Proyek SPK';
        
        // Ambil filter tahun
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $data['filter'] = [
            'tahun' => $tahun
        ];
        
        // Ambil data timeline
        $data['timeline'] = $this->timelineModel->getTimelineData($tahun);
        
        // Data untuk dropdown tahun
        $data['available_years'] = $this->timelineModel->getAvailableYears();
        
        // Statistik ringkasan
        $data['total_spk'] = count($data['timeline']);
        $data['total_selesai'] = $this->timelineModel->where('status', 'Selesai')
                                 ->where('YEAR(tanggal_mulai)', $tahun)
                                 ->countAllResults();
        
        return view('teknisi/tugas_proyek/timeline/index', $data);
    }
    
public function detail($id)
{
    $data['title'] = 'Detail Proyek';
    
    // Ambil data SPK
    $spk = $this->timelineModel->find($id);
    
    if (!$spk) {
        return redirect()->to('teknisi/tugas-proyek/timeline')
            ->with('error', 'Data tidak ditemukan');
    }
    
    // Pastikan data yang dikirim ke view adalah string
    $data['spk'] = $spk;
    
    // Parse tim_teknisi JSON jika ada
    $data['tim_teknisi'] = [];
    if (!empty($spk->tim_teknisi)) {
        $teknisi_ids = json_decode($spk->tim_teknisi);
        if (!empty($teknisi_ids)) {
            $db = \Config\Database::connect();
            $data['tim_teknisi'] = $db->table('karyawan')
                ->select('id, nama_lengkap, jabatan')
                ->whereIn('id', $teknisi_ids)
                ->get()
                ->getResult();
        }
    }
    
    return view('teknisi/tugas_proyek/timeline/detail', $data);
}
}