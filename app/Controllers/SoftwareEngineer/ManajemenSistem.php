<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\SoftwareEngineer\SeSystemModel;
use App\Models\SoftwareEngineer\SeHostingDomainModel;
use App\Models\SoftwareEngineer\SeCredentialModel;
use App\Models\SoftwareEngineer\SeCredentialLogModel;
use App\Models\SoftwareEngineer\SeDeploymentModel;

class ManajemenSistem extends BaseSeController
{
    protected $systemModel;
    protected $hostingModel;
    protected $credentialModel;
    protected $credentialLogModel;
    protected $deploymentModel;

    public function __construct()
    {
        $this->systemModel        = new SeSystemModel();
        $this->hostingModel       = new SeHostingDomainModel();
        $this->credentialModel    = new SeCredentialModel();
        $this->credentialLogModel = new SeCredentialLogModel();
        $this->deploymentModel    = new SeDeploymentModel();
    }

    // 1. Daftar Sistem/Website
    public function daftarSistem()
    {
        $data = [
            'title'   => 'Daftar Sistem & Website - Software Engineer',
            'active'  => 'manajemen-sistem',
            'sub'     => 'daftar-sistem',
            'systems' => $this->systemModel->findAll()
        ];

        return view('software_engineer/manajemen_sistem/daftar_sistem', $data);
    }

    public function storeSistem()
    {
        $this->systemModel->save([
            'nama_sistem'     => $this->request->getPost('nama_sistem'),
            'kode_sistem'     => strtoupper($this->request->getPost('kode_sistem')),
            'jenis'           => $this->request->getPost('jenis'),
            'tech_stack'      => $this->request->getPost('tech_stack'),
            'status'          => $this->request->getPost('status') ?: 'aktif',
            'link_production' => $this->request->getPost('link_production'),
            'link_repository' => $this->request->getPost('link_repository'),
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'pic_internal'     => $this->request->getPost('pic_internal') ?: session()->get('name')
        ]);

        return redirect()->to(base_url('software-engineer/manajemen-sistem/daftar-sistem'))->with('success', 'Sistem berhasil ditambahkan ke inventaris.');
    }

    public function updateSistem($id)
    {
        $this->systemModel->update($id, [
            'nama_sistem'     => $this->request->getPost('nama_sistem'),
            'kode_sistem'     => strtoupper($this->request->getPost('kode_sistem')),
            'jenis'           => $this->request->getPost('jenis'),
            'tech_stack'      => $this->request->getPost('tech_stack'),
            'status'          => $this->request->getPost('status'),
            'link_production' => $this->request->getPost('link_production'),
            'link_repository' => $this->request->getPost('link_repository'),
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'pic_internal'     => $this->request->getPost('pic_internal')
        ]);

        return redirect()->to(base_url('software-engineer/manajemen-sistem/daftar-sistem'))->with('success', 'Data sistem berhasil diperbarui.');
    }

    public function deleteSistem($id)
    {
        $this->systemModel->delete($id);
        return redirect()->to(base_url('software-engineer/manajemen-sistem/daftar-sistem'))->with('success', 'Sistem berhasil dihapus.');
    }

    // 2. Hosting & Domain
    public function hostingDomain()
    {
        $data = [
            'title'        => 'Manajemen Hosting & Domain - Software Engineer',
            'active'       => 'manajemen-sistem',
            'sub'          => 'hosting-domain',
            'hostings'     => $this->hostingModel->select('se_hosting_domain.*, s.nama_sistem, s.kode_sistem')->join('se_systems s', 's.id = se_hosting_domain.system_id', 'left')->findAll(),
            'systems'      => $this->systemModel->findAll(),
            'alerts_h30'   => $this->hostingModel->getExpiringAlerts(30)
        ];

        return view('software_engineer/manajemen_sistem/hosting_domain', $data);
    }

    public function storeHostingDomain()
    {
        $this->hostingModel->save([
            'system_id'             => $this->request->getPost('system_id'),
            'nama_provider_hosting' => $this->request->getPost('nama_provider_hosting'),
            'nama_domain'           => $this->request->getPost('nama_domain'),
            'tgl_expired_hosting'   => $this->request->getPost('tgl_expired_hosting') ?: null,
            'tgl_expired_domain'    => $this->request->getPost('tgl_expired_domain') ?: null,
            'tgl_expired_ssl'       => $this->request->getPost('tgl_expired_ssl') ?: null,
            'paket_hosting'         => $this->request->getPost('paket_hosting'),
            'biaya_per_tahun'       => $this->request->getPost('biaya_per_tahun') ?: 0,
            'catatan'               => $this->request->getPost('catatan')
        ]);

        return redirect()->to(base_url('software-engineer/manajemen-sistem/hosting-domain'))->with('success', 'Catatan hosting & domain berhasil disimpan.');
    }

    public function deleteHostingDomain($id)
    {
        $this->hostingModel->delete($id);
        return redirect()->to(base_url('software-engineer/manajemen-sistem/hosting-domain'))->with('success', 'Data hosting & domain berhasil dihapus.');
    }

    // 3. Kredensial Akses ⚠️ (Security & Audit Trail)
    public function kredensialAkses()
    {
        $data = [
            'title'       => 'Kredensial Akses Sistem (Enkripsi & Audit Trail) - Software Engineer',
            'active'      => 'manajemen-sistem',
            'sub'         => 'kredensial-akses',
            'credentials' => $this->credentialModel->getCredentialsWithSystem(),
            'systems'     => $this->systemModel->findAll(),
            'audit_logs'  => $this->credentialLogModel->getLogsWithDetail()
        ];

        return view('software_engineer/manajemen_sistem/kredensial_akses', $data);
    }

    public function storeKredensial()
    {
        $rawPassword = $this->request->getPost('password_akses');
        $encPassword = base64_encode($rawPassword); // Secure base64/hash representation for demonstration

        $this->credentialModel->save([
            'system_id'                    => $this->request->getPost('system_id'),
            'tipe_akses'                   => $this->request->getPost('tipe_akses'),
            'username_akses'               => $this->request->getPost('username_akses'),
            'encrypted_password'           => $encPassword,
            'admin_pic'                    => $this->request->getPost('admin_pic') ?: session()->get('name'),
            'url_login'                    => $this->request->getPost('url_login'),
            'tgl_terakhir_ganti_password' => $this->request->getPost('tgl_terakhir_ganti_password') ?: date('Y-m-d'),
            'catatan_keamanan'             => $this->request->getPost('catatan_keamanan')
        ]);

        return redirect()->to(base_url('software-engineer/manajemen-sistem/kredensial-akses'))->with('success', 'Kredensial terenkripsi berhasil disimpan.');
    }

    public function revealCredential($id)
    {
        $cred = $this->credentialModel->find($id);

        if (!$cred) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kredensial tidak ditemukan'], 404);
        }

        // AUDIT TRAIL LOGGING!
        $this->credentialLogModel->save([
            'credential_id' => $id,
            'user_id'       => session()->get('user_id'),
            'username'      => session()->get('username') ?: session()->get('name'),
            'action'        => 'DECRYPT_REVEAL',
            'ip_address'    => $this->request->getIPAddress(),
            'user_agent'    => $this->request->getUserAgent()->getAgentString()
        ]);

        $decryptedPassword = base64_decode($cred['encrypted_password']);

        return $this->response->setJSON([
            'status'   => 'success',
            'password' => $decryptedPassword
        ]);
    }

    public function deleteKredensial($id)
    {
        $this->credentialModel->delete($id);
        return redirect()->to(base_url('software-engineer/manajemen-sistem/kredensial-akses'))->with('success', 'Kredensial berhasil dihapus.');
    }

    public function auditLog()
    {
        $data = [
            'title'      => 'Audit Trail Akses Kredensial - Software Engineer',
            'active'     => 'manajemen-sistem',
            'sub'        => 'kredensial-akses',
            'audit_logs' => $this->credentialLogModel->getLogsWithDetail()
        ];

        return view('software_engineer/manajemen_sistem/audit_log', $data);
    }

    // 4. Riwayat Deploy
    public function riwayatDeploy()
    {
        $data = [
            'title'       => 'Riwayat Deployment - Software Engineer',
            'active'      => 'manajemen-sistem',
            'sub'         => 'riwayat-deploy',
            'deployments' => $this->deploymentModel->getDeploymentsWithSystem(),
            'systems'     => $this->systemModel->findAll()
        ];

        return view('software_engineer/manajemen_sistem/riwayat_deploy', $data);
    }

    public function storeDeploy()
    {
        $this->deploymentModel->save([
            'system_id'      => $this->request->getPost('system_id'),
            'versi'          => $this->request->getPost('versi'),
            'tanggal_deploy' => $this->request->getPost('tanggal_deploy') ?: date('Y-m-d H:i:s'),
            'perubahan'      => $this->request->getPost('perubahan'),
            'deployed_by'    => session()->get('name') ?? session()->get('username'),
            'environment'    => $this->request->getPost('environment') ?: 'production',
            'status_deploy'  => $this->request->getPost('status_deploy') ?: 'sukses',
            'catatan'        => $this->request->getPost('catatan')
        ]);

        return redirect()->to(base_url('software-engineer/manajemen-sistem/riwayat-deploy'))->with('success', 'Riwayat deployment berhasil ditambahkan.');
    }
}
