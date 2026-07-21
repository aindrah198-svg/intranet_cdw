<?php

namespace App\Controllers\Sales;

class Client extends SalesController
{
    public function __construct()
    {
        parent::initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );
    }
    
    public function index()
    {
        // Get all clients with sales info
        $clients = $this->clientModel->getAllClientsWithSales();
        
        // Filter berdasarkan status jika ada
        $status = $this->request->getGet('status');
        if ($status && in_array($status, ['active', 'inactive', 'potensial'])) {
            $clients = array_filter($clients, function($client) use ($status) {
                return $client['status'] === $status;
            });
        }
        
        $data = [
            'title' => 'Data Client',
            'subtitle' => 'Daftar Client/Pelanggan',
            'clients' => $clients,
            'active' => 'client'
        ];
        
        return $this->renderView('sales/client/index', $data);
    }
    
    public function create()
    {
        // Generate kode client otomatis
        $kode_client = $this->clientModel->generateKodeClient();
        
        // Get sales data untuk dropdown
        $salesList = $this->karyawanModel->findAll();
        
        $data = [
            'title' => 'Tambah Client Baru',
            'subtitle' => 'Form Tambah Client',
            'kode_client' => $kode_client,
            'salesList' => $salesList,
            'validation' => \Config\Services::validation(),
            'active' => 'client'
        ];
        
        return $this->renderView('sales/client/create', $data);
    }
    
    public function store()
    {
        // Validasi input
        $rules = $this->clientModel->validationRules;
        
        if (!$this->validate($rules, $this->clientModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare data - untuk sales, auto-set sales_id dari session
        $userData = $this->getUserData();
        $data = [
            'kode_client' => $this->request->getPost('kode_client'),
            'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
            'nama_kontak' => $this->request->getPost('nama_kontak'),
            'telepon' => $this->request->getPost('telepon'),
            'email' => $this->request->getPost('email'),
            'alamat' => $this->request->getPost('alamat'),
            'npwp' => $this->request->getPost('npwp'),
            'kategori' => $this->request->getPost('kategori'),
            'status' => $this->request->getPost('status'),
            'sales_id' => $userData['karyawan_id'] ?? 1
        ];
        
        // Simpan data
        if ($this->clientModel->save($data)) {
            session()->setFlashdata('success', 'Client berhasil ditambahkan!');
            return redirect()->to('/sales/client');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan client. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }
    
    public function edit($id)
    {
        $client = $this->clientModel->getClientWithSales($id);
        
        if (!$client) {
            session()->setFlashdata('error', 'Client tidak ditemukan!');
            return redirect()->to('/sales/client');
        }
        
        // Get sales data untuk dropdown
        $salesList = $this->karyawanModel->findAll();
        
        $data = [
            'title' => 'Edit Client',
            'subtitle' => 'Form Edit Client',
            'client' => $client,
            'salesList' => $salesList,
            'validation' => \Config\Services::validation(),
            'active' => 'client'
        ];
        
        return $this->renderView('sales/client/edit', $data);
    }
    
 public function update($id)
{
    // Cek login sudah di handle oleh parent class
    $userData = $this->getUserData();
    $client = $this->clientModel->find($id);
    
    if (!$client) {
        session()->setFlashdata('error', 'Client tidak ditemukan!');
        return redirect()->to('/sales/client');
    }
    
    // Validasi input
    $rules = $this->clientModel->validationRules;
    $rules['kode_client'] = "required|is_unique[client.kode_client,id,$id]|max_length[50]";
    
    if (!$this->validate($rules, $this->clientModel->validationMessages)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Prepare data
    $data = [
        'id' => $id,
        'kode_client' => $this->request->getPost('kode_client'),
        'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
        'nama_kontak' => $this->request->getPost('nama_kontak'),
        'telepon' => $this->request->getPost('telepon'),
        'email' => $this->request->getPost('email'),
        'alamat' => $this->request->getPost('alamat'),
        'npwp' => $this->request->getPost('npwp'),
        'kategori' => $this->request->getPost('kategori'),
        'status' => $this->request->getPost('status')
    ];
    
    // Simpan data
    if ($this->clientModel->save($data)) {
        session()->setFlashdata('success', 'Client berhasil diperbarui!');
        return redirect()->to('/sales/client');
    } else {
        $error = $this->clientModel->errors();
        session()->setFlashdata('error', 'Gagal memperbarui client. Silakan coba lagi.');
        return redirect()->back()->withInput();
    }
}
    
    public function delete($id)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            session()->setFlashdata('error', 'Client tidak ditemukan!');
            return redirect()->to('/sales/client');
        }
        
        // Hapus client
        if ($this->clientModel->delete($id)) {
            session()->setFlashdata('success', 'Client berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus client. Silakan coba lagi.');
        }
        
        return redirect()->to('/sales/client');
    }
    
    public function detail($id)
    {
        $client = $this->clientModel->getClientWithSales($id);
        
        if (!$client) {
            session()->setFlashdata('error', 'Client tidak ditemukan!');
            return redirect()->to('/sales/client');
        }
        
        // Konversi alamat ke string jika perlu
        $client['alamat'] = is_array($client['alamat']) ? implode(' ', $client['alamat']) : $client['alamat'];
        
        $data = [
            'title' => 'Detail Client',
            'subtitle' => 'Detail Informasi Client',
            'client' => $client,
            'active' => 'client'
        ];
        
        return $this->renderView('sales/client/detail', $data);
    }
}