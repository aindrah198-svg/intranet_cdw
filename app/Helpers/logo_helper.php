<?php
if (!function_exists('get_company_logo')) {
    function get_company_logo($companyCode = 'CDW')
    {
        $perusahaanModel = new \App\Models\PerusahaanModel();
        $company = $perusahaanModel->getByKode($companyCode);
        
        if ($company && !empty($company['logo_path'])) {
            $logoPath = FCPATH . $company['logo_path'];
            if (file_exists($logoPath)) {
                return base_url($company['logo_path']);
            }
        }
        
        // Default logo
        return base_url('assets/img/logo/default_logo.jpg');
    }
}

if (!function_exists('get_company_logo_base64')) {
    function get_company_logo_base64($companyCode = 'CDW')
    {
        $perusahaanModel = new \App\Models\PerusahaanModel();
        $company = $perusahaanModel->getByKode($companyCode);
        
        if ($company && !empty($company['logo_path'])) {
            $logoPath = FCPATH . $company['logo_path'];
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
        
        return null;
    }
}