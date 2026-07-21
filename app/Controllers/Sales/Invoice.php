<?php

namespace App\Controllers\Sales;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class Invoice extends SalesController
{
    protected $invoiceModel;
    protected $invoiceItemModel;
    protected $projectModel;
    protected $penawaranModel;
    protected $clientModel;
    protected $pembayaranModel;
    
    public function __construct()
    {
        parent::initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );
        
        // Load models
        $this->invoiceModel = new \App\Models\InvoiceModel();
        $this->invoiceItemModel = new \App\Models\InvoiceItemModel();
        $this->projectModel = new \App\Models\ProjectModel();
        $this->penawaranModel = new \App\Models\PenawaranModel();
        $this->clientModel = new \App\Models\ClientModel();
        $this->pembayaranModel = new \App\Models\PembayaranModel();
    }
    
public function index()
{
    // Get all invoice for this sales
    $userData = $this->getUserData();
    
    if (!isset($userData['id']) || empty($userData['id'])) {
        session()->setFlashdata('error', 'Sesi login tidak valid. Silakan login kembali.');
        return redirect()->to('/login');
    }
    
    // Get invoice list
    $invoiceList = $this->invoiceModel->getInvoiceBySales($userData['id']) ?? [];
    
    // Calculate totals and payments for each invoice
    foreach ($invoiceList as &$invoice) {
        // Gunakan data yang sudah dihitung dari model atau hitung langsung
        if (!isset($invoice['total_amount'])) {
            $invoice['total_amount'] = $this->invoiceModel->calculateTotal($invoice['id']);
        }
        
        if (!isset($invoice['payment_summary'])) {
            $invoice['payment_summary'] = $this->invoiceModel->getPaymentSummary($invoice['id']);
        }
        
        $invoice['remaining_amount'] = $invoice['total_amount'] - $invoice['payment_summary'];
        
        // Format untuk display
        $invoice['total_formatted'] = number_format($invoice['total_amount'], 0, ',', '.');
        $invoice['payment_formatted'] = number_format($invoice['payment_summary'], 0, ',', '.');
        $invoice['remaining_formatted'] = number_format($invoice['remaining_amount'], 0, ',', '.');
        
        // Hitung persentase pembayaran
        $invoice['payment_percentage'] = $invoice['total_amount'] > 0 ? 
            round(($invoice['payment_summary'] / $invoice['total_amount']) * 100, 1) : 0;
        
        // Check if overdue
        $invoice['is_overdue'] = (strtotime($invoice['tanggal_jatuh_tempo']) < time() && $invoice['status_pembayaran'] != 'lunas');
    }
    
    // Filter by status if exists
    $status = $this->request->getGet('status');
    if ($status && in_array($status, ['belum_bayar', 'sebagian', 'lunas', 'overdue'])) {
        $invoiceList = array_filter($invoiceList, function($item) use ($status) {
            return isset($item['status_pembayaran']) && $item['status_pembayaran'] === $status;
        });
    }
    
    // Get status count
    $statusCount = $this->invoiceModel->getStatusCount($userData['id']);
    
    // Calculate totals for footer
    $totalInvoiceValue = array_sum(array_column($invoiceList, 'total_amount'));
    $totalPaid = array_sum(array_column($invoiceList, 'payment_summary'));
    $totalRemaining = array_sum(array_column($invoiceList, 'remaining_amount'));
    
    $data = [
        'title' => 'Daftar Invoice',
        'subtitle' => 'Kelola invoice Anda',
        'invoiceList' => $invoiceList,
        'statusCount' => $statusCount,
        'totalInvoiceValue' => $totalInvoiceValue,
        'totalPaid' => $totalPaid,
        'totalRemaining' => $totalRemaining,
        'active' => 'invoice'
    ];
    
    return $this->renderView('sales/invoice/index', $data);
}
    
public function create()
{
    // Generate nomor invoice
    $nomorInvoice = $this->invoiceModel->generateNomorInvoice();
    
    // Get user data
    $userData = $this->getUserData();
    
    // Get project options
    $projectOptions = $this->invoiceModel->getProjectOptions($userData['id']);
    
    // Get penawaran options
    $penawaranOptions = $this->invoiceModel->getPenawaranOptions();
    
    // Get client options untuk modal project baru
    $clientOptions = $this->clientModel->where('status', 'active')
        ->where('sales_id', $userData['id'])
        ->orderBy('nama_perusahaan', 'ASC')
        ->findAll();
    
    // Set default jatuh tempo (14 hari dari sekarang)
    $defaultJatuhTempo = date('Y-m-d', strtotime('+14 days'));
    
    $data = [
        'title' => 'Buat Invoice Baru',
        'subtitle' => 'Form Invoice',
        'nomorInvoice' => $nomorInvoice,
        'projectOptions' => $projectOptions,
        'penawaranOptions' => $penawaranOptions,
        'clientOptions' => $clientOptions,
        'defaultJatuhTempo' => $defaultJatuhTempo,
        'validation' => \Config\Services::validation(),
        'active' => 'invoice'
    ];
    
    return $this->renderView('sales/invoice/create', $data);
}

public function store()
{
    // Validation rules
    $rules = [
        'nomor_invoice' => 'required|is_unique[invoice.nomor_invoice]|max_length[100]',
        'project_id' => 'required|integer',
        'penawaran_id' => 'permit_empty|integer',
        'tanggal_invoice' => 'required|valid_date',
        'tanggal_jatuh_tempo' => 'required|valid_date',
        'metode_pembayaran' => 'permit_empty|max_length[50]',
        'keterangan' => 'permit_empty'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Get user data
    $userData = $this->getUserData();
    
    // Start transaction
    $this->db->transBegin();
    
    try {
        // Prepare data
        $data = [
            'nomor_invoice' => $this->request->getPost('nomor_invoice'),
            'project_id' => $this->request->getPost('project_id'),
            'penawaran_id' => $this->request->getPost('penawaran_id') ?: null,
            'tanggal_invoice' => $this->request->getPost('tanggal_invoice'),
            'tanggal_jatuh_tempo' => $this->request->getPost('tanggal_jatuh_tempo'),
            'status_pembayaran' => 'belum_bayar',
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'keterangan' => $this->request->getPost('keterangan'),
            'created_by' => $userData['id']
        ];
        
        // Save invoice
        $invoiceId = $this->invoiceModel->insert($data, true);
        
        // Save invoice items if exists
        $items = $this->request->getPost('items');
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (!empty($item['nama_item']) && !empty($item['qty']) && !empty($item['harga_satuan'])) {
                    $itemData = [
                        'invoice_id' => $invoiceId,
                        'nama_item' => $item['nama_item'],
                        'deskripsi' => $item['deskripsi'] ?? '',
                        'qty' => str_replace(',', '', $item['qty']),
                        'satuan' => $item['satuan'] ?? 'unit',
                        'harga_satuan' => str_replace(',', '', $item['harga_satuan'])
                    ];
                    
                    $this->invoiceItemModel->insert($itemData);
                }
            }
        }
        
        // Update project status to 'on_progress' jika masih deal
        $projectData = $this->projectModel->find($data['project_id']);
        if ($projectData && $projectData['status'] == 'deal') {
            $updateProject = [
                'id' => $data['project_id'],
                'status' => 'on_progress'
            ];
            $this->projectModel->save($updateProject);
        }
        
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Gagal menyimpan invoice. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
        
        $this->db->transCommit();
        session()->setFlashdata('success', 'Invoice berhasil dibuat!');
        return redirect()->to('/sales/invoice/detail/' . $invoiceId);
        
    } catch (\Exception $e) {
        $this->db->transRollback();
        session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

public function createProjectAjax()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON([
            'success' => false,
            'message' => 'Access denied'
        ]);
    }
    
    // Disable CSRF sementara untuk testing (jika diperlukan)
    // $config = new \Config\App();
    // $config->CSRFProtection = false;
    
    // Get CSRF token from header
    $csrfToken = $this->request->getHeaderLine('X-CSRF-TOKEN');
    if (empty($csrfToken)) {
        // Fallback to post data
        $csrfToken = $this->request->getPost('csrf_test_name');
    }
    
    // Validation rules
    $rules = [
        'kode_project' => 'required|is_unique[project.kode_project]|max_length[50]',
        'nama_project' => 'required|max_length[200]',
        'client_id' => 'required|integer',
        'nilai_project' => 'permit_empty|decimal',
        'deskripsi' => 'permit_empty|max_length[500]',
        'tanggal_mulai' => 'permit_empty|valid_date'
    ];
    
    $validation = \Config\Services::validation();
    $validation->setRules($rules);
    
    if (!$validation->run($this->request->getPost())) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => $validation->getErrors(),
            'message' => 'Validasi gagal'
        ]);
    }
    
    try {
        // Start transaction
        $this->db->transBegin();
        
        $data = [
            'kode_project' => $this->request->getPost('kode_project'),
            'nama_project' => $this->request->getPost('nama_project'),
            'client_id' => $this->request->getPost('client_id'),
            'deskripsi' => $this->request->getPost('deskripsi') ?? null,
            'nilai_project' => $this->request->getPost('nilai_project') ? 
                str_replace(',', '', $this->request->getPost('nilai_project')) : 0,
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?? date('Y-m-d'),
            'status' => 'deal',
            'project_manager_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Save project
        $projectId = $this->projectModel->insert($data);
        
        if (!$projectId) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan project ke database',
                'errors' => $this->projectModel->errors()
            ]);
        }
        
        // Get client info
        $clientModel = new \App\Models\ClientModel();
        $client = $clientModel->find($data['client_id']);
        
        $this->db->transCommit();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Project berhasil dibuat',
            'project' => [
                'id' => $projectId,
                'kode_project' => $data['kode_project'],
                'nama_project' => $data['nama_project'],
                'client_name' => $client ? $client['nama_perusahaan'] : ''
            ],
            'debug' => [
                'data_saved' => $data,
                'project_id' => $projectId
            ]
        ]);
        
    } catch (\Exception $e) {
        $this->db->transRollback();
        
        // Log error
        log_message('error', 'Create Project Error: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'debug' => [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
}

// Tambahkan method ini di Sales/Invoice controller
public function getProjectOptions()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }
    
    // Get user data
    $userData = $this->getUserData();
    
    // Get project options
    $projects = $this->invoiceModel->getProjectOptions($userData['id']);
    
    return $this->response->setJSON([
        'success' => true,
        'projects' => $projects
    ]);
}
    
 public function edit($id)
{
    // Get invoice data with details
    $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
    
    if (!$invoice) {
        session()->setFlashdata('error', 'Invoice tidak ditemukan!');
        return redirect()->to('/sales/invoice');
    }
    
    // Check if status allows editing
    if (!in_array($invoice['status_pembayaran'], ['belum_bayar', 'sebagian'])) {
        session()->setFlashdata('error', 'Invoice dengan status pembayaran ' . $invoice['status_pembayaran'] . ' tidak dapat diedit!');
        return redirect()->to('/sales/invoice');
    }
    
    // Get invoice items
    $items = $this->invoiceModel->getInvoiceItems($id);
    
    // Calculate totals
    $total = $this->invoiceModel->calculateTotal($id);
    
    // Get payment history
    $payments = $this->pembayaranModel->getPaymentsByInvoice($id);
    
    // Calculate payment summary
    $paymentSummary = $this->invoiceModel->getPaymentSummary($id);
    $remaining = $total - $paymentSummary;
    
    // Get user data
    $userData = $this->getUserData();
    
    // Get project options
    $projectOptions = $this->invoiceModel->getProjectOptions($userData['id']);
    
    // Get penawaran options - khusus untuk project yang dipilih
    $penawaranOptions = $this->invoiceModel->getPenawaranOptions($invoice['project_id']);
    
    // Get client options for new project modal
    $clientOptions = $this->clientModel->where('status', 'active')
        ->where('sales_id', $userData['id'])
        ->orderBy('nama_perusahaan', 'ASC')
        ->findAll();
    
    $data = [
        'title' => 'Edit Invoice',
        'subtitle' => 'Form Edit Invoice',
        'invoice' => $invoice,
        'items' => $items,
        'payments' => $payments,
        'total' => $total,
        'paymentSummary' => $paymentSummary,
        'remaining' => $remaining,
        'projectOptions' => $projectOptions,
        'penawaranOptions' => $penawaranOptions,
        'clientOptions' => $clientOptions,
        'validation' => \Config\Services::validation(),
        'active' => 'invoice'
    ];
    
    return $this->renderView('sales/invoice/edit', $data);
}
    
   public function update($id)
{
    // Get invoice data
    $invoice = $this->invoiceModel->find($id);
    
    if (!$invoice) {
        session()->setFlashdata('error', 'Invoice tidak ditemukan!');
        return redirect()->to('/sales/invoice');
    }
    
    // Check if status allows editing
    if (!in_array($invoice['status_pembayaran'], ['belum_bayar', 'sebagian'])) {
        session()->setFlashdata('error', 'Invoice dengan status pembayaran ' . $invoice['status_pembayaran'] . ' tidak dapat diedit!');
        return redirect()->to('/sales/invoice');
    }
    
    // Validation rules
    $rules = [
        'nomor_invoice' => "required|is_unique[invoice.nomor_invoice,id,$id]|max_length[100]",
        'project_id' => 'required|integer',
        'penawaran_id' => 'permit_empty|integer',
        'tanggal_invoice' => 'required|valid_date',
        'tanggal_jatuh_tempo' => 'required|valid_date',
        'metode_pembayaran' => 'permit_empty|max_length[50]',
        'keterangan' => 'permit_empty'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Start transaction
    $this->db->transBegin();
    
    try {
        // Prepare data
        $data = [
            'id' => $id,
            'nomor_invoice' => $this->request->getPost('nomor_invoice'),
            'project_id' => $this->request->getPost('project_id'),
            'penawaran_id' => $this->request->getPost('penawaran_id') ?: null,
            'tanggal_invoice' => $this->request->getPost('tanggal_invoice'),
            'tanggal_jatuh_tempo' => $this->request->getPost('tanggal_jatuh_tempo'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        // Update invoice
        $this->invoiceModel->save($data);
        
        // Delete old items
        $this->invoiceItemModel->where('invoice_id', $id)->delete();
        
        // Save new items
        $items = $this->request->getPost('items');
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                if (!empty($item['nama_item']) && !empty($item['qty']) && !empty($item['harga_satuan'])) {
                    $itemData = [
                        'invoice_id' => $id,
                        'nama_item' => $item['nama_item'],
                        'deskripsi' => $item['deskripsi'] ?? '',
                        'qty' => str_replace(',', '', $item['qty']),
                        'satuan' => $item['satuan'] ?? 'unit',
                        'harga_satuan' => str_replace(',', '', $item['harga_satuan'])
                    ];
                    
                    $this->invoiceItemModel->insert($itemData);
                }
            }
        }
        
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Gagal mengupdate invoice. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
        
        $this->db->transCommit();
        
        // Update invoice status based on payments
        $this->invoiceModel->updateInvoiceStatus($id);
        
        session()->setFlashdata('success', 'Invoice berhasil diupdate!');
        return redirect()->to('/sales/invoice/detail/' . $id);
        
    } catch (\Exception $e) {
        $this->db->transRollback();
        session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

public function addPayment($invoiceId)
{
    // Get invoice data
    $invoice = $this->invoiceModel->find($invoiceId);
    
    if (!$invoice) {
        session()->setFlashdata('error', 'Invoice tidak ditemukan!');
        return redirect()->to('/sales/invoice');
    }
    
    // Calculate remaining amount
    $total = $this->invoiceModel->calculateTotal($invoiceId);
    $paid = $this->invoiceModel->getPaymentSummary($invoiceId);
    $remaining = $total - $paid;
    
    if ($remaining <= 0) {
        session()->setFlashdata('error', 'Invoice sudah lunas!');
        return redirect()->to('/sales/invoice/detail/' . $invoiceId);
    }
    
    // Validation rules
    $rules = [
        'tanggal_bayar' => 'required|valid_date',
        'jumlah_bayar' => 'required|decimal|greater_than[0]',
        'metode_bayar' => 'required|in_list[transfer,tunai,cek,giro]',
        'no_referensi' => 'permit_empty|max_length[100]',
        'keterangan' => 'permit_empty'
    ];
    
    // Custom validation for payment amount
    $validation = \Config\Services::validation();
    $validation->setRules($rules);
    
    if (!$validation->withRequest($this->request)->run()) {
        $errors = $validation->getErrors();
        
        // Check if payment exceeds remaining amount
        $jumlahBayar = str_replace(',', '', $this->request->getPost('jumlah_bayar'));
        if ($jumlahBayar > $remaining) {
            $errors['jumlah_bayar'] = 'Jumlah bayar tidak boleh melebihi sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ')';
        }
        
        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }
    }
    
    try {
        // Generate payment number
        $paymentNumber = $this->pembayaranModel->generateNomorPembayaran();
        
        // Get user data
        $userData = $this->getUserData();
        
        // PERBAIKAN: Hanya gunakan created_at, bukan updated_at
        $paymentData = [
            'invoice_id' => $invoiceId,
            'nomor_pembayaran' => $paymentNumber,
            'tanggal_bayar' => $this->request->getPost('tanggal_bayar'),
            'jumlah_bayar' => str_replace(',', '', $this->request->getPost('jumlah_bayar')),
            'metode_bayar' => $this->request->getPost('metode_bayar'),
            'bank' => $this->request->getPost('bank'),
            'no_referensi' => $this->request->getPost('no_referensi'),
            'keterangan' => $this->request->getPost('keterangan'),
            'created_by' => $userData['id'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Debug: Cek data sebelum insert
        log_message('debug', 'Payment data to insert: ' . print_r($paymentData, true));
        
        // Save payment
        $paymentId = $this->pembayaranModel->insert($paymentData);
        
        if (!$paymentId) {
            throw new \Exception('Gagal menyimpan pembayaran ke database');
        }
        
        // Update invoice status
        $this->invoiceModel->updateInvoiceStatus($invoiceId);
        
        session()->setFlashdata('success', 'Pembayaran berhasil direkam!');
        return redirect()->to('/sales/invoice/detail/' . $invoiceId);
        
    } catch (\Exception $e) {
        log_message('error', 'Payment error: ' . $e->getMessage());
        session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

public function deletePayment($paymentId)
{
    // Debug log
    log_message('debug', 'deletePayment called with paymentId: ' . $paymentId);
    
    // Get payment data
    $payment = $this->pembayaranModel->find($paymentId);
    
    if (!$payment) {
        session()->setFlashdata('error', 'Data pembayaran tidak ditemukan!');
        return redirect()->to('/sales/invoice');
    }
    
    $invoiceId = $payment['invoice_id'];
    
    try {
        // Delete payment
        $deleted = $this->pembayaranModel->delete($paymentId);
        
        if (!$deleted) {
            throw new \Exception('Gagal menghapus data pembayaran');
        }
        
        // Update invoice status
        $this->invoiceModel->updateInvoiceStatus($invoiceId);
        
        session()->setFlashdata('success', 'Data pembayaran berhasil dihapus!');
        return redirect()->to('/sales/invoice/detail/' . $invoiceId);
        
    } catch (\Exception $e) {
        log_message('error', 'Delete payment error: ' . $e->getMessage());
        session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        return redirect()->to('/sales/invoice/detail/' . $invoiceId);
    }
}
    public function detail($id)
    {
        // Get invoice data
        $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
        
        if (!$invoice) {
            session()->setFlashdata('error', 'Invoice tidak ditemukan!');
            return redirect()->to('/sales/invoice');
        }
        
        // Get invoice items
        $items = $this->invoiceModel->getInvoiceItems($id);
        
        // Calculate total
        $total = $this->invoiceModel->calculateTotal($id);
        
        // Get payment summary
        $paymentSummary = $this->invoiceModel->getPaymentSummary($id);
        
        // Get payment history
        $payments = $this->pembayaranModel->getPaymentsByInvoice($id);
        
        // Calculate remaining amount
        $remaining = $total - $paymentSummary;
        
        $data = [
            'title' => 'Detail Invoice',
            'subtitle' => 'Informasi Lengkap Invoice',
            'invoice' => $invoice,
            'items' => $items,
            'total' => $total,
            'paymentSummary' => $paymentSummary,
            'payments' => $payments,
            'remaining' => $remaining,
            'active' => 'invoice'
        ];
        
        return $this->renderView('sales/invoice/detail', $data);
    }
    
   public function delete($id)
{
    // Get invoice data
    $invoice = $this->invoiceModel->find($id);
    
    if (!$invoice) {
        session()->setFlashdata('error', 'Invoice tidak ditemukan!');
        return redirect()->to('/sales/invoice');
    }
    
    // Check if status allows deletion
    if ($invoice['status_pembayaran'] != 'belum_bayar') {
        session()->setFlashdata('error', 'Invoice dengan status pembayaran ' . $invoice['status_pembayaran'] . ' tidak dapat dihapus!');
        return redirect()->to('/sales/invoice');
    }
    
    // Start transaction
    $this->db->transBegin();
    
    try {
        // Delete items first
        $this->invoiceItemModel->where('invoice_id', $id)->delete();
        
        // Delete invoice (hard delete karena tidak ada soft delete)
        $this->invoiceModel->delete($id, true); // true untuk force delete
        
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Gagal menghapus invoice. Silakan coba lagi.');
            return redirect()->to('/sales/invoice');
        }
        
        $this->db->transCommit();
        session()->setFlashdata('success', 'Invoice berhasil dihapus!');
        return redirect()->to('/sales/invoice');
        
    } catch (\Exception $e) {
        $this->db->transRollback();
        session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        return redirect()->to('/sales/invoice');
    }
}
    
    public function print($id)
    {
        // Get invoice data
        $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
        
        if (!$invoice) {
            session()->setFlashdata('error', 'Invoice tidak ditemukan!');
            return redirect()->to('/sales/invoice');
        }
        
        // Get invoice items
        $items = $this->invoiceModel->getInvoiceItems($id);
        
        // Calculate total
        $total = $this->invoiceModel->calculateTotal($id);
        
        // Calculate PPN 11%
        $ppn = $total * 0.11;
        $grandTotal = $total + $ppn;
        
        $data = [
            'title' => 'Print Invoice ' . $invoice['nomor_invoice'],
            'invoice' => $invoice,
            'items' => $items,
            'total' => $total,
            'ppn' => $ppn,
            'grandTotal' => $grandTotal
        ];
        
        // Return print view
        return view('sales/invoice/print', $data);
    }
    
    public function updateStatus($id)
    {
        // Get invoice data
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            session()->setFlashdata('error', 'Invoice tidak ditemukan!');
            return redirect()->to('/sales/invoice');
        }
        
        $status = $this->request->getPost('status');
        $validStatuses = ['belum_bayar', 'sebagian', 'lunas', 'overdue'];
        
        if (!in_array($status, $validStatuses)) {
            session()->setFlashdata('error', 'Status tidak valid!');
            return redirect()->to('/sales/invoice/detail/' . $id);
        }
        
        // Update status
        $data = [
            'id' => $id,
            'status_pembayaran' => $status
        ];
        
        if ($this->invoiceModel->save($data)) {
            session()->setFlashdata('success', 'Status invoice berhasil diupdate menjadi ' . $status . '!');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate status invoice.');
        }
        
        return redirect()->to('/sales/invoice/detail/' . $id);
    }
    
    public function exportExcel($id)
    {
        try {
            // Get invoice data
            $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
            
            if (!$invoice) {
                session()->setFlashdata('error', 'Invoice tidak ditemukan!');
                return redirect()->to('/sales/invoice');
            }
            
            // Get items
            $items = $this->invoiceModel->getInvoiceItems($id);
            $total = $this->invoiceModel->calculateTotal($id);
            $ppn = $total * 0.11;
            $grandTotal = $total + $ppn;
            
            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('CDW Sales System')
                ->setLastModifiedBy('CDW Sales System')
                ->setTitle('Invoice ' . $invoice['nomor_invoice'])
                ->setSubject('Invoice')
                ->setDescription('Export Invoice');
            
            // ============= HEADER =============
            // Company Info
            $sheet->mergeCells('A1:G1');
            $sheet->setCellValue('A1', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:G2');
            $sheet->setCellValue('A2', 'Beltway Office Park Tower B Lantai 5');
            $sheet->getStyle('A2')->getFont()->setSize(10);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:G3');
            $sheet->setCellValue('A3', 'Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan');
            $sheet->getStyle('A3')->getFont()->setSize(10);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A4:G4');
            $sheet->setCellValue('A4', 'Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201');
            $sheet->getStyle('A4')->getFont()->setSize(10);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Empty row
            $sheet->mergeCells('A5:G5');
            $sheet->setCellValue('A5', '');
            
            // Document Title
            $sheet->mergeCells('A6:G6');
            $sheet->setCellValue('A6', 'INVOICE');
            $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Empty row
            $sheet->mergeCells('A7:G7');
            $sheet->setCellValue('A7', '');
            
            // ============= CLIENT INFO =============
            // Client Info Header
            $sheet->setCellValue('A8', 'Kepada:');
            $sheet->getStyle('A8')->getFont()->setBold(true);
            
            $sheet->setCellValue('A9', $invoice['nama_perusahaan']);
            $sheet->setCellValue('A10', $invoice['alamat_client'] ?? '-');
            $sheet->setCellValue('A11', 'Attn: ' . ($invoice['nama_kontak'] ?? '-'));
            $sheet->setCellValue('A12', 'Telp: ' . ($invoice['telepon'] ?? '-'));
            $sheet->setCellValue('A13', 'Email: ' . ($invoice['email'] ?? '-'));
            
            // Invoice Info
            $sheet->setCellValue('F8', 'Invoice No:');
            $sheet->setCellValue('G8', $invoice['nomor_invoice']);
            $sheet->getStyle('F8')->getFont()->setBold(true);
            
            $sheet->setCellValue('F9', 'Tanggal:');
            $sheet->setCellValue('G9', date('d/m/Y', strtotime($invoice['tanggal_invoice'])));
            
            $sheet->setCellValue('F10', 'Jatuh Tempo:');
            $sheet->setCellValue('G10', date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])));
            
            $sheet->setCellValue('F11', 'Project:');
            $sheet->setCellValue('G11', $invoice['nama_project']);
            
            if ($invoice['nomor_penawaran']) {
                $sheet->setCellValue('F12', 'No. Penawaran:');
                $sheet->setCellValue('G12', $invoice['nomor_penawaran']);
            }
            
            // Empty row
            $sheet->mergeCells('A14:G14');
            $sheet->setCellValue('A14', '');
            
            // ============= ITEMS TABLE =============
            // Table header
            $headers = ['No.', 'DESKRIPSI ITEM', 'QTY', 'SATUAN', 'HARGA SATUAN (Rp)', 'SUBTOTAL (Rp)'];
            $col = 'A';
            
            foreach ($headers as $index => $header) {
                $sheet->setCellValue($col . '15', $header);
                $sheet->getStyle($col . '15')->getFont()->setBold(true);
                $sheet->getStyle($col . '15')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');
                $sheet->getStyle($col . '15')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($col . '15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            
            // Fill items
            $row = 16;
            $itemNumber = 1;
            
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $itemNumber);
                $sheet->setCellValue('B' . $row, $item['nama_item'] . "\n" . ($item['deskripsi'] ?? ''));
                $sheet->setCellValue('C' . $row, number_format($item['qty'], 2));
                $sheet->setCellValue('D' . $row, $item['satuan']);
                $sheet->setCellValue('E' . $row, number_format($item['harga_satuan'], 0, ',', '.'));
                $sheet->setCellValue('F' . $row, number_format($item['subtotal'], 0, ',', '.'));
                
                // Style for item rows
                $itemStyle = [
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true]
                ];
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($itemStyle);
                $sheet->getStyle('E' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $row++;
                $itemNumber++;
            }
            
            // Total rows
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->setCellValue('A' . $row, 'SUB TOTAL');
            $sheet->setCellValue('F' . $row, number_format($total, 0, ',', '.'));
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            
            $row++;
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->setCellValue('A' . $row, 'PPN 11%');
            $sheet->setCellValue('F' . $row, number_format($ppn, 0, ',', '.'));
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
            
            $row++;
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->setCellValue('A' . $row, 'GRAND TOTAL');
            $sheet->setCellValue('F' . $row, number_format($grandTotal, 0, ',', '.'));
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Empty row
            $row++;
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->setCellValue('A' . $row, '');
            
            // ============= PAYMENT INFO =============
            $row++;
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->setCellValue('A' . $row, 'INFORMASI PEMBAYARAN:');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            
            $row++;
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->setCellValue('A' . $row, 'Transfer ke: Bank Mandiri No. Rek: 101.000.676.6073 a.n. PT. CIPTA DUTA WACANA');
            
            // Empty row
            $row++;
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->setCellValue('A' . $row, '');
            
            // ============= NOTES =============
            if ($invoice['keterangan']) {
                $row++;
                $sheet->mergeCells('A' . $row . ':G' . $row);
                $sheet->setCellValue('A' . $row, 'Catatan: ' . $invoice['keterangan']);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
            }
            
            // ============= TERBILANG =============
            $row++;
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->setCellValue('A' . $row, 'Terbilang: ' . $this->terbilang($grandTotal) . ' Rupiah');
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
            
            // Empty row
            $row++;
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->setCellValue('A' . $row, '');
            
            // ============= SIGNATURE =============
            $row++;
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->setCellValue('E' . $row, 'Hormat kami,');
            $sheet->getStyle('E' . $row)->getFont()->setBold(true);
            
            $row += 3;
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->setCellValue('E' . $row, '___________________________');
            
            $row++;
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->setCellValue('E' . $row, 'Cecep Tri Hardiyanto');
            $sheet->getStyle('E' . $row)->getFont()->setBold(true);
            
            $row++;
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->setCellValue('E' . $row, 'Direktur');
            
            // ============= AUTOFIT COLUMNS & ROWS =============
            // Auto fit semua kolom
            foreach (range('A', 'G') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Set lebar kolom tertentu
            $sheet->getColumnDimension('B')->setWidth(40); // Deskripsi lebih lebar
            $sheet->getColumnDimension('E')->setWidth(15); // Harga
            $sheet->getColumnDimension('F')->setWidth(15); // Subtotal
            
            // Auto fit semua baris
            for ($i = 1; $i <= $row; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
            }
            
            // Set alignment untuk angka
            $sheet->getStyle('E16:E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('F16:F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            // Save Excel file
            $filename = 'Invoice_' . str_replace('/', '_', $invoice['nomor_invoice']) . '_' . date('Ymd_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export Excel Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal export Excel: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    public function exportPdf($id)
    {
        try {
            // Get invoice data
            $invoice = $this->invoiceModel->getInvoiceWithDetails($id);
            
            if (!$invoice) {
                session()->setFlashdata('error', 'Invoice tidak ditemukan!');
                return redirect()->to('/sales/invoice');
            }
            
            // Get items
            $items = $this->invoiceModel->getInvoiceItems($id);
            $total = $this->invoiceModel->calculateTotal($id);
            $ppn = $total * 0.11;
            $grandTotal = $total + $ppn;
            
            // Create HTML for PDF
            $html = $this->generatePdfHtml($invoice, $items, $total, $ppn, $grandTotal);
            
            // Setup Dompdf
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isPhpEnabled', true);
            $options->set('isFontSubsettingEnabled', true);
            $options->set('isJavascriptEnabled', false);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Output PDF
            $filename = 'Invoice_' . str_replace('/', '_', $invoice['nomor_invoice']) . '_' . date('Ymd_His') . '.pdf';
            
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            echo $dompdf->output();
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export PDF Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal export PDF: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    private function generatePdfHtml($invoice, $items, $total, $ppn, $grandTotal)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Invoice ' . htmlspecialchars($invoice['nomor_invoice']) . '</title>
            <style>
                * { 
                    margin: 0; 
                    padding: 0; 
                    box-sizing: border-box; 
                    font-family: "Arial", sans-serif;
                }
                
                @page { 
                    margin: 15mm 20mm;
                    padding: 0;
                }
                
                body { 
                    font-size: 9.5pt;
                    line-height: 1.2;
                    color: #000;
                    margin: 0;
                    padding: 0;
                }
                
                .header-section {
                    text-align: center;
                    margin-bottom: 8px;
                    padding-bottom: 6px;
                    border-bottom: 1px solid #333;
                }
                
                .company-name {
                    font-size: 11pt;
                    font-weight: bold;
                    margin-bottom: 2px;
                    text-transform: uppercase;
                }
                
                .company-address, 
                .company-contact {
                    font-size: 8pt;
                    line-height: 1.1;
                    margin-bottom: 1px;
                }
                
                .document-title {
                    font-size: 10.5pt;
                    font-weight: bold;
                    margin: 6px 0 4px;
                    text-transform: uppercase;
                }
                
                .info-section {
                    margin-bottom: 10px;
                }
                
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 8.5pt;
                    margin: 6px 0;
                }
                
                .info-table td {
                    padding: 3px 4px;
                    vertical-align: top;
                    border: 1px solid #ddd;
                }
                
                .info-table .label {
                    font-weight: bold;
                    width: 18%;
                    background-color: #f5f5f5;
                }
                
                .items-section {
                    margin: 8px 0;
                    page-break-inside: avoid;
                }
                
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 8pt;
                }
                
                .items-table th {
                    background-color: #333;
                    color: white;
                    padding: 4px 3px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #555;
                    font-size: 8pt;
                }
                
                .items-table td {
                    padding: 3px 2px;
                    border: 0.5px solid #ddd;
                    vertical-align: top;
                }
                
                .col-no { width: 25px; text-align: center; }
                .col-desc { width: 50%; }
                .col-qty { width: 50px; text-align: center; }
                .col-unit { width: 50px; text-align: center; }
                .col-price { width: 80px; text-align: right; }
                .col-subtotal { width: 80px; text-align: right; }
                
                .total-section {
                    margin-top: 10px;
                    page-break-inside: avoid;
                }
                
                .total-table {
                    width: 280px;
                    border-collapse: collapse;
                    float: right;
                    font-size: 9pt;
                }
                
                .total-table td {
                    padding: 4px 5px;
                    border: 0.5px solid #ddd;
                }
                
                .total-label {
                    text-align: left;
                    font-weight: bold;
                    background-color: #f9f9f9;
                }
                
                .total-value {
                    text-align: right;
                    font-weight: bold;
                    width: 100px;
                }
                
                .grand-total {
                    background-color: #f0f0f0;
                    font-size: 10pt;
                    border-top: 1px solid #333 !important;
                }
                
                .footer-section {
                    margin-top: 20px;
                    padding-top: 10px;
                    border-top: 1px solid #333;
                    clear: both;
                }
                
                .signature-box {
                    float: right;
                    text-align: center;
                    width: 180px;
                }
                
                .clear { clear: both; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .text-bold { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <!-- HEADER -->
                <div class="header-section">
                    <div class="company-name">PT. CIPTA DUTA WACANA</div>
                    <div class="company-address">
                        Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41<br>
                        Ragunan-Pasar Minggu, Jakarta Selatan
                    </div>
                    <div class="company-contact">
                        Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201
                    </div>
                    <div class="document-title">INVOICE</div>
                </div>
                
                <!-- CLIENT & INVOICE INFO -->
                <div class="info-section">
                    <table class="info-table">
                        <tr>
                            <td class="label">Kepada:</td>
                            <td>' . htmlspecialchars($invoice['nama_perusahaan']) . '</td>
                            <td class="label">No. Invoice:</td>
                            <td>' . htmlspecialchars($invoice['nomor_invoice']) . '</td>
                        </tr>
                        <tr>
                            <td class="label">Alamat:</td>
                            <td>' . nl2br(htmlspecialchars($invoice['alamat_client'] ?? '-')) . '</td>
                            <td class="label">Tanggal:</td>
                            <td>' . date('d/m/Y', strtotime($invoice['tanggal_invoice'])) . '</td>
                        </tr>
                        <tr>
                            <td class="label">Attn:</td>
                            <td>' . htmlspecialchars($invoice['nama_kontak'] ?? '-') . '</td>
                            <td class="label">Jatuh Tempo:</td>
                            <td>' . date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])) . '</td>
                        </tr>
                        <tr>
                            <td class="label">Telp/Email:</td>
                            <td>' . htmlspecialchars($invoice['telepon'] ?? '-') . ' / ' . htmlspecialchars($invoice['email'] ?? '-') . '</td>
                            <td class="label">Project:</td>
                            <td>' . htmlspecialchars($invoice['nama_project']) . '</td>
                        </tr>
                    </table>
                </div>
                
                <!-- ITEMS TABLE -->
                <div class="items-section">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th class="col-no">No.</th>
                                <th class="col-desc">DESKRIPSI ITEM</th>
                                <th class="col-qty">QTY</th>
                                <th class="col-unit">SATUAN</th>
                                <th class="col-price">HARGA SATUAN (Rp)</th>
                                <th class="col-subtotal">SUBTOTAL (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        if (!empty($items)) {
            $itemNumber = 1;
            foreach ($items as $item) {
                $html .= '
                            <tr>
                                <td class="col-no text-center">' . $itemNumber . '</td>
                                <td class="col-desc">
                                    <strong>' . htmlspecialchars($item['nama_item']) . '</strong><br>
                                    <small>' . nl2br(htmlspecialchars($item['deskripsi'] ?? '')) . '</small>
                                </td>
                                <td class="col-qty text-center">' . number_format($item['qty'], 2) . '</td>
                                <td class="col-unit text-center">' . htmlspecialchars($item['satuan']) . '</td>
                                <td class="col-price">' . number_format($item['harga_satuan'], 0, ',', '.') . '</td>
                                <td class="col-subtotal text-bold">' . number_format($item['subtotal'], 0, ',', '.') . '</td>
                            </tr>';
                $itemNumber++;
            }
        }
        
        $html .= '
                        </tbody>
                    </table>
                </div>
                
                <!-- TOTAL CALCULATION -->
                <div class="total-section">
                    <table class="total-table">
                        <tr>
                            <td class="total-label">SUB TOTAL</td>
                            <td class="total-value">' . number_format($total, 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td class="total-label">PPN 11%</td>
                            <td class="total-value">' . number_format($ppn, 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td class="total-label grand-total">GRAND TOTAL</td>
                            <td class="total-value grand-total">' . number_format($grandTotal, 0, ',', '.') . '</td>
                        </tr>
                    </table>
                    <div class="clear"></div>
                </div>
                
                <!-- TERBILANG & NOTES -->
                <div style="margin-top: 10px;">
                    <p><strong>Terbilang:</strong> <em>' . $this->terbilang($grandTotal) . ' Rupiah</em></p>';
        
        if ($invoice['keterangan']) {
            $html .= '<p><strong>Catatan:</strong> ' . htmlspecialchars($invoice['keterangan']) . '</p>';
        }
        
        $html .= '
                </div>
                
                <!-- PAYMENT INFO -->
                <div style="margin-top: 15px; padding: 8px; background-color: #f9f9f9; border: 1px solid #ddd;">
                    <p class="text-bold">Informasi Pembayaran:</p>
                    <p>Transfer ke: Bank Mandiri No. Rek: 101.000.676.6073</p>
                    <p>Atas Nama: PT. CIPTA DUTA WACANA</p>
                </div>
                
                <!-- FOOTER & SIGNATURE -->
                <div class="footer-section">
                    <div class="signature-box">
                        <div style="border-top: 1px solid #000; width: 150px; margin: 15px auto 3px;"></div>
                        <p style="font-size: 8.5pt; margin: 2px 0;">Hormat kami,</p>
                        <p class="text-bold">Cecep Tri Hardiyanto</p>
                        <p>Direktur</p>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <!-- FOOTNOTE -->
                <div style="font-size: 7pt; color: #666; text-align: center; margin-top: 10px;">
                    <p>Invoice ini sah dan dapat digunakan sebagai dokumen tagihan resmi</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Get items from penawaran for auto-fill
     */
    public function getPenawaranItems($penawaranId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/sales/invoice');
        }
        
        $items = $this->penawaranModel->getPenawaranItems($penawaranId);
        
        return $this->response->setJSON([
            'success' => true,
            'items' => $items
        ]);
    }
    
    /**
     * Helper function: Convert number to words (Indonesian)
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $terbilang = "";
        
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . " Puluh" . $this->terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " Seratus" . $this->terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . " Ratus" . $this->terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " Seribu" . $this->terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . " Ribu" . $this->terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . " Juta" . $this->terbilang($angka % 1000000);
        }
        
        return trim($terbilang);
    }

    public function getPenawaranOptionsByProject($projectId)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }
    
    try {
        $builder = $this->db->table('penawaran p');
        $builder->select('p.id, p.nomor_penawaran, p.project_id, pr.nama_project')
            ->join('project pr', 'pr.id = p.project_id')
            ->where('p.status', 'diterima')
            ->where('p.project_id', $projectId)
            ->orderBy('p.tanggal_penawaran', 'DESC');
        
        $penawarans = $builder->get()->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'penawarans' => $penawarans
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function exportExcelAll()
{
    try {
        // Get user data
        $userData = $this->getUserData();
        
        // Get all invoices for this sales
        $invoiceList = $this->invoiceModel->getInvoiceBySales($userData['id']) ?? [];
        
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'No', 'No. Invoice', 'Project', 'Client', 
            'Tanggal Invoice', 'Jatuh Tempo', 'Status', 
            'Total (Rp)', 'Terbayar (Rp)', 'Sisa (Rp)',
            'Persentase'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $col++;
        }
        
        // Add data
        $row = 2;
        $no = 1;
        foreach ($invoiceList as $invoice) {
            // Calculate values
            $total = $this->invoiceModel->calculateTotal($invoice['id']);
            $paid = $this->invoiceModel->getPaymentSummary($invoice['id']);
            $remaining = $total - $paid;
            $percentage = $total > 0 ? round(($paid / $total) * 100, 1) : 0;
            
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $invoice['nomor_invoice']);
            $sheet->setCellValue('C' . $row, $invoice['nama_project']);
            $sheet->setCellValue('D' . $row, $invoice['nama_perusahaan']);
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($invoice['tanggal_invoice'])));
            $sheet->setCellValue('F' . $row, date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])));
            $sheet->setCellValue('G' . $row, $invoice['status_pembayaran']);
            $sheet->setCellValue('H' . $row, $total);
            $sheet->setCellValue('I' . $row, $paid);
            $sheet->setCellValue('J' . $row, $remaining);
            $sheet->setCellValue('K' . $row, $percentage . '%');
            
            // Format angka
            $sheet->getStyle('H' . $row . ':J' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $row++;
            $no++;
        }
        
        // Auto size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add summary
        $row++;
        $sheet->setCellValue('G' . $row, 'TOTAL:');
        $sheet->mergeCells('G' . $row . ':H' . $row);
        $sheet->getStyle('G' . $row . ':H' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('G' . $row, 'Jumlah Invoice:');
        $sheet->setCellValue('H' . $row, count($invoiceList));
        
        $row++;
        $sheet->setCellValue('G' . $row, 'Total Nilai:');
        $sheet->setCellValue('H' . $row, array_sum(array_column($invoiceList, 'nilai_project')));
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Save Excel file
        $filename = 'Daftar_Invoice_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Export All Excel Error: ' . $e->getMessage());
        session()->setFlashdata('error', 'Gagal export Excel: ' . $e->getMessage());
        return redirect()->back();
    }
}

public function exportPdfAll()
{
    try {
        // Get user data
        $userData = $this->getUserData();
        
        // Get all invoices for this sales
        $invoiceList = $this->invoiceModel->getInvoiceBySales($userData['id']) ?? [];
        
        // Create HTML for PDF
        $html = $this->generateAllInvoicesPdfHtml($invoiceList);
        
        // Setup Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('isPhpEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output PDF
        $filename = 'Daftar_Invoice_' . date('Ymd_His') . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        echo $dompdf->output();
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Export All PDF Error: ' . $e->getMessage());
        session()->setFlashdata('error', 'Gagal export PDF: ' . $e->getMessage());
        return redirect()->back();
    }
}

private function generateAllInvoicesPdfHtml($invoices)
{
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Daftar Invoice</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
            body { font-size: 9pt; margin: 15px; }
            .header { text-align: center; margin-bottom: 15px; }
            .company-name { font-size: 14pt; font-weight: bold; }
            .title { font-size: 12pt; font-weight: bold; margin: 10px 0; }
            .date { font-size: 9pt; color: #666; margin-bottom: 15px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background-color: #333; color: white; padding: 6px; text-align: center; border: 1px solid #555; }
            td { padding: 5px; border: 1px solid #ddd; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .summary { margin-top: 20px; padding: 10px; background-color: #f5f5f5; border: 1px solid #ddd; }
            .footer { margin-top: 20px; font-size: 8pt; color: #666; text-align: center; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="company-name">PT. CIPTA DUTA WACANA</div>
            <div class="title">DAFTAR INVOICE</div>
            <div class="date">Dicetak: ' . date('d/m/Y H:i:s') . '</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>No. Invoice</th>
                    <th>Project</th>
                    <th>Client</th>
                    <th>Tanggal</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Terbayar</th>
                    <th>Sisa</th>
                </tr>
            </thead>
            <tbody>';
    
    $totalAll = 0;
    $totalPaidAll = 0;
    $totalRemainingAll = 0;
    $no = 1;
    
    foreach ($invoices as $invoice) {
        $total = $this->invoiceModel->calculateTotal($invoice['id']);
        $paid = $this->invoiceModel->getPaymentSummary($invoice['id']);
        $remaining = $total - $paid;
        
        $totalAll += $total;
        $totalPaidAll += $paid;
        $totalRemainingAll += $remaining;
        
        $html .= '
                <tr>
                    <td class="text-center">' . $no . '</td>
                    <td>' . htmlspecialchars($invoice['nomor_invoice']) . '</td>
                    <td>' . htmlspecialchars($invoice['nama_project']) . '</td>
                    <td>' . htmlspecialchars($invoice['nama_perusahaan']) . '</td>
                    <td>' . date('d/m/Y', strtotime($invoice['tanggal_invoice'])) . '</td>
                    <td>' . date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])) . '</td>
                    <td>' . htmlspecialchars($invoice['status_pembayaran']) . '</td>
                    <td class="text-right">' . number_format($total, 0, ',', '.') . '</td>
                    <td class="text-right">' . number_format($paid, 0, ',', '.') . '</td>
                    <td class="text-right">' . number_format($remaining, 0, ',', '.') . '</td>
                </tr>';
        $no++;
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="summary">
            <h4>Ringkasan:</h4>
            <p>Jumlah Invoice: ' . count($invoices) . '</p>
            <p>Total Nilai Invoice: Rp ' . number_format($totalAll, 0, ',', '.') . '</p>
            <p>Total Terbayar: Rp ' . number_format($totalPaidAll, 0, ',', '.') . '</p>
            <p>Total Sisa: Rp ' . number_format($totalRemainingAll, 0, ',', '.') . '</p>
        </div>
        
        <div class="footer">
            <p>Dokumen ini dicetak secara otomatis dari sistem PT. Cipta Duta Wacana</p>
        </div>
    </body>
    </html>';
    
    return $html;
}


}