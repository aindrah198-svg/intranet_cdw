<?php
namespace App\Models;

use CodeIgniter\Model;

class PerusahaanModel extends Model
{
    protected $table = 'perusahaan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'kode_perusahaan', 'nama_perusahaan', 'alamat', 
        'telepon', 'email', 'website', 'logo_path', 'logo_full_path'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'kode_perusahaan' => 'required|is_unique[perusahaan.kode_perusahaan,id,{id}]|max_length[20]',
        'nama_perusahaan' => 'required|max_length[200]'
    ];
    
    /**
     * Get perusahaan by kode
     */
    public function getByKode($kode)
    {
        return $this->where('kode_perusahaan', $kode)->first();
    }
    
    /**
     * Get perusahaan CDW (default)
     */
    public function getPerusahaanCDW()
    {
        $perusahaan = $this->where('kode_perusahaan', 'CDW')->first();
        
        // Default data jika tidak ada di database
        if (!$perusahaan) {
            $perusahaan = [
                'kode_perusahaan' => 'CDW',
                'nama_perusahaan' => 'PT. Cipta Duta Wacana',
                'alamat' => 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226',
                'website' => 'www.cdw-engineering.com',
                'logo_path' => 'assets/img/logo/logo_cdw.jpg'
            ];
        }
        
        return $perusahaan;
    }
    
    /**
     * Get full logo URL
     */
    public function getLogoUrl($perusahaanData)
    {
        $logoPath = $perusahaanData['logo_path'] ?? 'assets/img/logo/logo_cdw.jpg';
        
        // Cek file exists
        $fullPath = FCPATH . $logoPath;
        if (!file_exists($fullPath)) {
            // Fallback ke default logo
            return base_url('assets/img/logo/default_logo.jpg');
        }
        
        return base_url($logoPath);
    }
    
    /**
     * Get base64 encoded logo for PDF
     */
    public function getLogoBase64($perusahaanData)
    {
        $logoPath = $perusahaanData['logo_path'] ?? 'assets/img/logo/logo_cdw.jpg';
        $fullPath = FCPATH . $logoPath;
        
        if (file_exists($fullPath)) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        return null;
    }
}