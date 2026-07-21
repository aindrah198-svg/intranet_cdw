<?php

namespace App\Controllers\Sales;

// Tambahkan use statements untuk library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class Penawaran extends SalesController
{
    protected $penawaranModel;
    protected $clientModel;
    protected $projectModel;
    
    public function __construct()
    {
        parent::initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );
        
        // Load models
        $this->penawaranModel = new \App\Models\PenawaranModel();
        $this->clientModel = new \App\Models\ClientModel();
        $this->projectModel = new \App\Models\ProjectModel();
    }
    
  public function index()
{
    // Get all penawaran for this sales
    $userData = $this->getUserData();
    
    // DEBUG: Cek data user
    log_message('debug', 'User Data: ' . print_r($userData, true));
    
    // Periksa apakah userData memiliki id
    if (!isset($userData['id']) || empty($userData['id'])) {
        log_message('error', 'User ID tidak ditemukan dalam userData');
        session()->setFlashdata('error', 'Sesi login tidak valid. Silakan login kembali.');
        return redirect()->to('/login');
    }
    
    // Get penawaran list dengan pengecekan null
    $penawaranList = $this->penawaranModel->getPenawaranBySales($userData['id']) ?? [];
    
    // Filter by status if exists
    $status = $this->request->getGet('status');
    if ($status && in_array($status, ['draft', 'sent', 'revisi', 'diterima', 'ditolak', 'kadaluarsa'])) {
        $penawaranList = array_filter($penawaranList, function($item) use ($status) {
            return isset($item['status']) && $item['status'] === $status;
        });
    }
    
    $data = [
        'title' => 'Daftar Penawaran Harga',
        'subtitle' => 'Kelola penawaran harga Anda',
        'penawaranList' => $penawaranList,
        'active' => 'penawaran'
    ];
    
    return $this->renderView('sales/penawaran/index', $data);
}
    
    public function create()
    {
        // Generate nomor penawaran
        $nomorPenawaran = $this->penawaranModel->generateNomorPenawaran();
        
        // Get project options
        $projectOptions = $this->penawaranModel->getProjectOptions();
        
        // Get client list for reference
        $clientList = $this->clientModel->findAll();
        
        $data = [
            'title' => 'Buat Penawaran Baru',
            'subtitle' => 'Form Penawaran Harga',
            'nomorPenawaran' => $nomorPenawaran,
            'projectOptions' => $projectOptions,
            'clientList' => $clientList,
            'validation' => \Config\Services::validation(),
            'active' => 'penawaran'
        ];
        
        return $this->renderView('sales/penawaran/create', $data);
    }
    
    public function store()
    {
        // Validation rules
        $rules = [
            'nomor_penawaran' => 'required|is_unique[penawaran.nomor_penawaran]|max_length[100]',
            'project_id' => 'required|integer',
            'tanggal_penawaran' => 'required|valid_date',
            'tanggal_kadaluarsa' => 'permit_empty|valid_date',
            'keterangan' => 'permit_empty',
            'catatan_khusus' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Get user data
        $userData = $this->getUserData();
        
        // Prepare data
        $data = [
            'nomor_penawaran' => $this->request->getPost('nomor_penawaran'),
            'project_id' => $this->request->getPost('project_id'),
            'tanggal_penawaran' => $this->request->getPost('tanggal_penawaran'),
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa'),
            'status' => 'draft',
            'keterangan' => $this->request->getPost('keterangan'),
            'catatan_khusus' => $this->request->getPost('catatan_khusus'),
            'created_by' => $userData['id']
        ];
        
        // Start transaction
        $this->db->transBegin();
        
        try {
            // Save penawaran
            $penawaranId = $this->penawaranModel->insert($data, true);
            
            // Save penawaran items if exists
            $items = $this->request->getPost('items');
            if ($items && is_array($items)) {
                $itemModel = new \App\Models\PenawaranItemModel();
                
                foreach ($items as $item) {
                    if (!empty($item['nama_item']) && !empty($item['qty']) && !empty($item['harga_satuan'])) {
                        $itemData = [
                            'penawaran_id' => $penawaranId,
                            'nama_item' => $item['nama_item'],
                            'deskripsi' => $item['deskripsi'] ?? '',
                            'qty' => str_replace(',', '', $item['qty']),
                            'satuan' => $item['satuan'] ?? 'unit',
                            'harga_satuan' => str_replace(',', '', $item['harga_satuan'])
                        ];
                        
                        $itemModel->insert($itemData);
                    }
                }
            }
            
            // Update project status to 'penawaran'
            $projectData = [
                'id' => $data['project_id'],
                'status' => 'penawaran'
            ];
            $this->projectModel->save($projectData);
            
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                session()->setFlashdata('error', 'Gagal menyimpan penawaran. Silakan coba lagi.');
                return redirect()->back()->withInput();
            }
            
            $this->db->transCommit();
            session()->setFlashdata('success', 'Penawaran berhasil dibuat!');
            return redirect()->to('/sales/penawaran/detail/' . $penawaranId);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
    public function edit($id)
    {
        // Get penawaran data
        $penawaran = $this->penawaranModel->getPenawaranWithDetails($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Check if user owns this penawaran
        $userData = $this->getUserData();
        if ($penawaran['created_by'] != $userData['id'] && $penawaran['status'] != 'draft') {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk mengedit penawaran ini!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Get penawaran items
        $items = $this->penawaranModel->getPenawaranItems($id);
        
        // Get project options
        $projectOptions = $this->penawaranModel->getProjectOptions();
        
        $data = [
            'title' => 'Edit Penawaran',
            'subtitle' => 'Form Edit Penawaran Harga',
            'penawaran' => $penawaran,
            'items' => $items,
            'projectOptions' => $projectOptions,
            'validation' => \Config\Services::validation(),
            'active' => 'penawaran'
        ];
        
        return $this->renderView('sales/penawaran/edit', $data);
    }
    
    public function update($id)
    {
        // Get penawaran data
        $penawaran = $this->penawaranModel->find($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Check if user owns this penawaran
        $userData = $this->getUserData();
        if ($penawaran['created_by'] != $userData['id'] && $penawaran['status'] != 'draft') {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk mengedit penawaran ini!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Validation rules
        $rules = [
            'nomor_penawaran' => "required|is_unique[penawaran.nomor_penawaran,id,$id]|max_length[100]",
            'project_id' => 'required|integer',
            'tanggal_penawaran' => 'required|valid_date',
            'tanggal_kadaluarsa' => 'permit_empty|valid_date',
            'keterangan' => 'permit_empty',
            'catatan_khusus' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare data
        $data = [
            'id' => $id,
            'nomor_penawaran' => $this->request->getPost('nomor_penawaran'),
            'project_id' => $this->request->getPost('project_id'),
            'tanggal_penawaran' => $this->request->getPost('tanggal_penawaran'),
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa'),
            'keterangan' => $this->request->getPost('keterangan'),
            'catatan_khusus' => $this->request->getPost('catatan_khusus')
        ];
        
        // Start transaction
        $this->db->transBegin();
        
        try {
            // Update penawaran
            $this->penawaranModel->save($data);
            
            // Delete old items
            $itemModel = new \App\Models\PenawaranItemModel();
            $itemModel->where('penawaran_id', $id)->delete();
            
            // Save new items
            $items = $this->request->getPost('items');
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['nama_item']) && !empty($item['qty']) && !empty($item['harga_satuan'])) {
                        $itemData = [
                            'penawaran_id' => $id,
                            'nama_item' => $item['nama_item'],
                            'deskripsi' => $item['deskripsi'] ?? '',
                            'qty' => str_replace(',', '', $item['qty']),
                            'satuan' => $item['satuan'] ?? 'unit',
                            'harga_satuan' => str_replace(',', '', $item['harga_satuan'])
                        ];
                        
                        $itemModel->insert($itemData);
                    }
                }
            }
            
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                session()->setFlashdata('error', 'Gagal mengupdate penawaran. Silakan coba lagi.');
                return redirect()->back()->withInput();
            }
            
            $this->db->transCommit();
            session()->setFlashdata('success', 'Penawaran berhasil diupdate!');
            return redirect()->to('/sales/penawaran/detail/' . $id);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
    public function detail($id)
    {
        // Get penawaran data
        $penawaran = $this->penawaranModel->getPenawaranWithDetails($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Get penawaran items
        $items = $this->penawaranModel->getPenawaranItems($id);
        
        // Calculate total
        $total = $this->penawaranModel->calculateTotal($id);
        
        $data = [
            'title' => 'Detail Penawaran',
            'subtitle' => 'Informasi Lengkap Penawaran',
            'penawaran' => $penawaran,
            'items' => $items,
            'total' => $total,
            'active' => 'penawaran'
        ];
        
        return $this->renderView('sales/penawaran/detail', $data);
    }
    
    public function delete($id)
    {
        // Get penawaran data
        $penawaran = $this->penawaranModel->find($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Check if user owns this penawaran
        $userData = $this->getUserData();
        if ($penawaran['created_by'] != $userData['id']) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk menghapus penawaran ini!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Check if status allows deletion
        if (!in_array($penawaran['status'], ['draft', 'revisi'])) {
            session()->setFlashdata('error', 'Penawaran dengan status ' . $penawaran['status'] . ' tidak dapat dihapus!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Start transaction
        $this->db->transBegin();
        
        try {
            // Delete items first
            $itemModel = new \App\Models\PenawaranItemModel();
            $itemModel->where('penawaran_id', $id)->delete();
            
            // Delete penawaran
            $this->penawaranModel->delete($id);
            
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                session()->setFlashdata('error', 'Gagal menghapus penawaran. Silakan coba lagi.');
                return redirect()->to('/sales/penawaran');
            }
            
            $this->db->transCommit();
            session()->setFlashdata('success', 'Penawaran berhasil dihapus!');
            return redirect()->to('/sales/penawaran');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->to('/sales/penawaran');
        }
    }
    
    public function print($id)
    {
        // Get penawaran data
        $penawaran = $this->penawaranModel->getPenawaranWithDetails($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Get penawaran items
        $items = $this->penawaranModel->getPenawaranItems($id);
        
        // Calculate total
        $total = $this->penawaranModel->calculateTotal($id);
        
        $data = [
            'title' => 'Print Penawaran ' . $penawaran['nomor_penawaran'],
            'penawaran' => $penawaran,
            'items' => $items,
            'total' => $total
        ];
        
        // Return print view
        return view('sales/penawaran/print', $data);
    }
    
    public function send($id)
    {
        // Get penawaran data
        $penawaran = $this->penawaranModel->find($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Check if user owns this penawaran
        $userData = $this->getUserData();
        if ($penawaran['created_by'] != $userData['id']) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk mengirim penawaran ini!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Update status to 'sent'
        $data = [
            'id' => $id,
            'status' => 'sent',
            'tanggal_penawaran' => date('Y-m-d') // Update to current date
        ];
        
        if ($this->penawaranModel->save($data)) {
            session()->setFlashdata('success', 'Penawaran berhasil dikirim ke client!');
        } else {
            session()->setFlashdata('error', 'Gagal mengirim penawaran. Silakan coba lagi.');
        }
        
        return redirect()->to('/sales/penawaran/detail/' . $id);
    }
    
    public function updateStatus($id)
    {
        // Get penawaran data
        $penawaran = $this->penawaranModel->find($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        $status = $this->request->getPost('status');
        $validStatuses = ['draft', 'sent', 'revisi', 'diterima', 'ditolak', 'kadaluarsa'];
        
        if (!in_array($status, $validStatuses)) {
            session()->setFlashdata('error', 'Status tidak valid!');
            return redirect()->to('/sales/penawaran/detail/' . $id);
        }
        
        // Update status
        $data = [
            'id' => $id,
            'status' => $status
        ];
        
        if ($status == 'diterima') {
            // If accepted, update project status to 'deal'
            $projectData = [
                'id' => $penawaran['project_id'],
                'status' => 'deal'
            ];
            $this->projectModel->save($projectData);
        }
        
        if ($this->penawaranModel->save($data)) {
            session()->setFlashdata('success', 'Status penawaran berhasil diupdate menjadi ' . $status . '!');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate status penawaran.');
        }
        
        return redirect()->to('/sales/penawaran/detail/' . $id);
    }

 /**
 * Export single penawaran to Excel
 */
public function exportExcel($id)
{
    try {
        // Get penawaran data
        $penawaran = $this->penawaranModel->getPenawaranWithDetails($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Get items
        $items = $this->penawaranModel->getPenawaranItems($id);
        $total = $this->penawaranModel->calculateTotal($id);
        
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('CDW Sales System')
            ->setLastModifiedBy('CDW Sales System')
            ->setTitle('Penawaran ' . $penawaran['nomor_penawaran'])
            ->setSubject('Penawaran Harga')
            ->setDescription('Export Penawaran Harga');
        
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
        $sheet->setCellValue('A6', 'QUOTATION / SURAT PENAWARAN HARGA');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Empty row
        $sheet->mergeCells('A7:G7');
        $sheet->setCellValue('A7', '');
        
        // ============= CLIENT INFO =============
        // Client Info Header
        $sheet->setCellValue('A8', 'Kepada Yth:');
        $sheet->getStyle('A8')->getFont()->setBold(true);
        
        $sheet->setCellValue('A9', $penawaran['nama_perusahaan']);
        $sheet->setCellValue('A10', $penawaran['alamat_client'] ?? '-');
        $sheet->setCellValue('A11', 'Attn: ' . ($penawaran['nama_kontak'] ?? '-'));
        $sheet->setCellValue('A12', 'Telp: ' . ($penawaran['telepon'] ?? '-'));
        $sheet->setCellValue('A13', 'Email: ' . ($penawaran['email'] ?? '-'));
        
        // Penawaran Info
        $sheet->setCellValue('F8', 'Nomor:');
        $sheet->setCellValue('G8', $penawaran['nomor_penawaran']);
        $sheet->getStyle('F8')->getFont()->setBold(true);
        
        $sheet->setCellValue('F9', 'Tanggal:');
        $sheet->setCellValue('G9', date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])));
        
        $sheet->setCellValue('F10', 'Masa Berlaku:');
        $sheet->setCellValue('G10', $penawaran['tanggal_kadaluarsa'] ? date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) : '14 hari');
        
        $sheet->setCellValue('F11', 'Project:');
        $sheet->setCellValue('G11', $penawaran['nama_project']);
        
        // Empty row
        $sheet->mergeCells('A14:G14');
        $sheet->setCellValue('A14', '');
        
        // Opening text
        $sheet->mergeCells('A15:G15');
        $sheet->setCellValue('A15', 'Bersama ini kami mengajukan penawaran harga untuk ' . $penawaran['nama_project'] . ' sebagai berikut:');
        $sheet->getStyle('A15')->getFont()->setSize(11);
        
        // Empty row
        $sheet->mergeCells('A16:G16');
        $sheet->setCellValue('A16', '');
        
        // ============= ITEMS TABLE =============
        // Table header
        $headers = ['No.', 'DESKRIPSI ITEM', 'QTY', 'SATUAN', 'HARGA SATUAN (Rp)', 'DISKON (%)', 'SUBTOTAL (Rp)'];
        $col = 'A';
        
        foreach ($headers as $index => $header) {
            $sheet->setCellValue($col . '17', $header);
            $sheet->getStyle($col . '17')->getFont()->setBold(true);
            $sheet->getStyle($col . '17')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');
            $sheet->getStyle($col . '17')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($col . '17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        // Fill items
        $row = 18;
        $itemNumber = 1;
        
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $itemNumber);
            $sheet->setCellValue('B' . $row, $item['nama_item'] . "\n" . ($item['deskripsi'] ?? ''));
            $sheet->setCellValue('C' . $row, number_format($item['qty'], 2));
            $sheet->setCellValue('D' . $row, $item['satuan']);
            $sheet->setCellValue('E' . $row, number_format($item['harga_satuan'], 0, ',', '.'));
            $sheet->setCellValue('F' . $row, '-');
            $sheet->setCellValue('G' . $row, number_format($item['subtotal'], 0, ',', '.'));
            
            // Style for item rows
            $itemStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true]
            ];
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($itemStyle);
            $sheet->getStyle('E' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('C' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
            $itemNumber++;
        }
        
        // Total rows - PERBAIKAN DI SINI
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'SUB TOTAL');
        $sheet->setCellValue('G' . $row, number_format($total, 0, ',', '.'));
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        
        $row++;
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'PPN 11%');  // PERBAIKAN: menghapus koma tambahan
        $sheet->setCellValue('G' . $row, number_format($total * 0.11, 0, ',', '.'));
        $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
        
        $row++;
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'GRAND TOTAL');
        $sheet->setCellValue('G' . $row, number_format($total * 1.11, 0, ',', '.'));
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');
        $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Empty row
        $row++;
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('A' . $row, '');
        
        // ============= TERMS & CONDITIONS =============
        $row++;
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('A' . $row, 'SYARAT DAN KETENTUAN:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $terms = [
            '1. Harga belum termasuk PPN 11%',
            '2. Pembayaran: 50% DP, 50% sebelum pengiriman',
            '3. Masa garansi: 6 (enam) bulan',
            '4. Waktu pengiriman: 2-4 minggu setelah PO diterima',
            '5. Pembayaran via transfer ke: Bank Mandiri No. Rek: 101-000-676-607-3 a.n. PT. CIPTA DUTA WACANA',
            '6. ' . ($penawaran['keterangan'] ?? 'Masa berlaku penawaran 14 hari')
        ];
        
        foreach ($terms as $term) {
            $row++;
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->setCellValue('A' . $row, $term);
        }
        
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
        $sheet->getColumnDimension('G')->setWidth(15); // Subtotal
        
        // Auto fit semua baris
        for ($i = 1; $i <= $row; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
        }
        
        // Set row height untuk baris dengan teks panjang
        $sheet->getRowDimension(15)->setRowHeight(25);
        $sheet->getRowDimension(17)->setRowHeight(25);
        
        // Set alignment untuk angka
        $sheet->getStyle('E18:E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G18:G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Save Excel file
        $filename = 'Penawaran_' . str_replace('/', '_', $penawaran['nomor_penawaran']) . '_' . date('Ymd_His') . '.xlsx';
        
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
    
 /**
 * Export single penawaran to PDF
 */
public function exportPdf($id)
{
    try {
        // Get penawaran data
        $penawaran = $this->penawaranModel->getPenawaranWithDetails($id);
        
        if (!$penawaran) {
            session()->setFlashdata('error', 'Penawaran tidak ditemukan!');
            return redirect()->to('/sales/penawaran');
        }
        
        // Get items
        $items = $this->penawaranModel->getPenawaranItems($id);
        $total = $this->penawaranModel->calculateTotal($id);
        
        // Create HTML for PDF
        $html = $this->generatePdfHtml($penawaran, $items, $total);
        
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
        $filename = 'Penawaran_' . str_replace('/', '_', $penawaran['nomor_penawaran']) . '_' . date('Ymd_His') . '.pdf';
        
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

/**
 * Generate HTML for PDF
 */
private function generatePdfHtml($penawaran, $items, $total)
{
    $ppn = $total * 0.11;
    $grandTotal = $total + $ppn;
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Penawaran ' . htmlspecialchars($penawaran['nomor_penawaran']) . '</title>
        <style>
            /* RESET & BASE STYLES */
            * { 
                margin: 0; 
                padding: 0; 
                box-sizing: border-box; 
                font-family: "Arial", sans-serif;
            }
            
            @page { 
                margin: 15mm 20mm; /* Kiri-kanan lebih kecil, atas-bawah normal */
                padding: 0;
            }
            
            body { 
                font-size: 9.5pt; /* Ukuran font lebih kecil */
                line-height: 1.2; /* Line height lebih ketat */
                color: #000;
                margin: 0;
                padding: 0;
            }
            
            /* HEADER STYLES */
            .header-section {
                text-align: center;
                margin-bottom: 8px;
                padding-bottom: 6px;
                border-bottom: 1px solid #333;
            }
            
            .company-name {
                font-size: 11pt; /* Sedikit lebih kecil */
                font-weight: bold;
                margin-bottom: 2px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .company-address, 
            .company-contact {
                font-size: 8pt; /* Lebih kecil */
                line-height: 1.1;
                margin-bottom: 1px;
            }
            
            .document-title {
                font-size: 10.5pt; /* Lebih kecil dari sebelumnya */
                font-weight: bold;
                margin: 6px 0 4px;
                text-transform: uppercase;
            }
            
            /* INFO SECTION - Lebih kompak */
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
                padding: 3px 4px; /* Padding lebih kecil */
                vertical-align: top;
                border: 1px solid #ddd;
            }
            
            .info-table .label {
                font-weight: bold;
                width: 18%; /* Lebih sempit */
                background-color: #f5f5f5;
                padding: 3px 5px;
            }
            
            /* ITEMS TABLE - Sangat kompak */
            .items-section {
                margin: 8px 0;
                page-break-inside: avoid; /* Hindari terpotong antar halaman */
            }
            
            .items-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 8pt; /* Font sangat kecil untuk tabel */
                table-layout: fixed; /* Kontrol lebar kolom */
            }
            
            .items-table th {
                background-color: #333;
                color: white;
                padding: 4px 3px; /* Sangat kecil */
                text-align: center;
                font-weight: bold;
                border: 1px solid #555;
                font-size: 8pt;
                height: 22px; /* Tinggi tetap */
            }
            
            .items-table td {
                padding: 3px 2px; /* Sangat kecil */
                border: 0.5px solid #ddd; /* Border tipis */
                vertical-align: top;
                line-height: 1.1;
            }
            
            /* Lebar kolom yang optimal */
            .col-no { width: 25px; text-align: center; }
            .col-desc { width: 45%; padding-left: 3px !important; }
            .col-qty { width: 50px; text-align: center; }
            .col-unit { width: 50px; text-align: center; }
            .col-price { width: 90px; text-align: right; padding-right: 5px !important; }
            .col-subtotal { width: 90px; text-align: right; padding-right: 5px !important; }
            
            /* Deskripsi item yang kompak */
            .item-name {
                font-weight: bold;
                font-size: 8.5pt;
                margin-bottom: 1px;
            }
            
            .item-desc {
                font-size: 7.5pt;
                color: #555;
                line-height: 1.1;
                margin-top: 1px;
            }
            
            /* TOTAL SECTION */
            .total-section {
                margin-top: 10px;
                page-break-inside: avoid;
            }
            
            .total-table {
                width: 280px;
                border-collapse: collapse;
                float: right;
                font-size: 9pt;
                margin: 5px 0;
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
            
            /* TERMS SECTION */
            .terms-section {
                margin-top: 12px;
                font-size: 8pt;
                page-break-inside: avoid;
                clear: both;
            }
            
            .terms-title {
                font-size: 9pt;
                font-weight: bold;
                margin-bottom: 4px;
                border-bottom: 0.5px solid #ccc;
                padding-bottom: 2px;
            }
            
            .terms-list {
                list-style-type: none;
                padding-left: 15px;
                margin: 3px 0;
            }
            
            .terms-list li {
                margin-bottom: 2px;
                position: relative;
                padding-left: 12px;
            }
            
            .terms-list li:before {
                content: "•";
                position: absolute;
                left: 0;
                color: #333;
            }
            
            /* FOOTER & SIGNATURE */
            .footer-section {
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid #333;
                page-break-inside: avoid;
                clear: both;
            }
            
            .signature-box {
                float: right;
                text-align: center;
                width: 180px;
                margin-top: 15px;
            }
            
            .signature-line {
                width: 150px;
                border-top: 1px solid #000;
                margin: 15px auto 3px;
            }
            
            .signature-name {
                font-weight: bold;
                font-size: 9pt;
                margin-top: 2px;
            }
            
            .signature-title {
                font-size: 8pt;
                color: #555;
            }
            
            /* UTILITY CLASSES */
            .clear { clear: both; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .text-bold { font-weight: bold; }
            .mb-5 { margin-bottom: 5px; }
            .mb-8 { margin-bottom: 8px; }
            .mt-10 { margin-top: 10px; }
            .mt-15 { margin-top: 15px; }
            .mt-20 { margin-top: 20px; }
            
            /* NUMBER FORMATTING */
            .number {
                font-family: "Courier New", monospace;
                letter-spacing: -0.5px;
            }
            
            /* PAGE BREAK HANDLING */
            .page-break {
                page-break-before: always;
                margin-top: 20px;
            }
            
            /* RESPONSIVE FOR PDF */
            .break-word {
                word-break: break-word;
                overflow-wrap: break-word;
            }
            
            /* COMPACT LAYOUT */
            .compact {
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body>
        <div class="container compact">
            
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
                <div class="document-title">QUOTATION / SURAT PENAWARAN HARGA</div>
            </div>
            
            <!-- CLIENT & PENAWARAN INFO - Layout lebih kompak -->
            <div class="info-section">
                <table class="info-table">
                    <tr>
                        <td class="label">Kepada Yth:</td>
                        <td>' . htmlspecialchars($penawaran['nama_perusahaan']) . '</td>
                        <td class="label">No. Penawaran:</td>
                        <td>' . htmlspecialchars($penawaran['nomor_penawaran']) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat:</td>
                        <td style="width: 32%;">' . nl2br(htmlspecialchars(substr($penawaran['alamat_client'] ?? '-', 0, 100))) . '</td>
                        <td class="label">Tanggal:</td>
                        <td>' . date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Attn:</td>
                        <td>' . htmlspecialchars($penawaran['nama_kontak'] ?? '-') . '</td>
                        <td class="label">Masa Berlaku:</td>
                        <td>' . ($penawaran['tanggal_kadaluarsa'] ? date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) : '14 hari') . '</td>
                    </tr>
                    <tr>
                        <td class="label">Telp/Email:</td>
                        <td>' . htmlspecialchars($penawaran['telepon'] ?? '-') . ' / ' . htmlspecialchars($penawaran['email'] ?? '-') . '</td>
                        <td class="label">Project:</td>
                        <td>' . htmlspecialchars($penawaran['nama_project']) . '</td>
                    </tr>
                </table>
            </div>
            
            <!-- OPENING TEXT - Satu baris saja -->
            <div class="mb-8">
                <p style="font-size: 9pt;">Bersama ini kami mengajukan penawaran harga untuk <strong>' . htmlspecialchars($penawaran['nama_project']) . '</strong> sebagai berikut:</p>
            </div>
            
            <!-- ITEMS TABLE - Sangat kompak -->
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
            // Potong deskripsi jika terlalu panjang
            $description = htmlspecialchars($item['deskripsi'] ?? '');
            if (strlen($description) > 150) {
                $description = substr($description, 0, 147) . '...';
            }
            
            $html .= '
                    <tr>
                        <td class="col-no text-center">' . $itemNumber . '</td>
                        <td class="col-desc break-word">
                            <div class="item-name">' . htmlspecialchars($item['nama_item']) . '</div>';
            
            if (!empty($item['deskripsi'])) {
                $html .= '<div class="item-desc">' . nl2br($description) . '</div>';
            }
            
            $html .= '
                        </td>
                        <td class="col-qty text-center">' . number_format($item['qty'], 2) . '</td>
                        <td class="col-unit text-center">' . htmlspecialchars($item['satuan']) . '</td>
                        <td class="col-price number">' . number_format($item['harga_satuan'], 0, ',', '.') . '</td>
                        <td class="col-subtotal number text-bold">' . number_format($item['subtotal'], 0, ',', '.') . '</td>
                    </tr>';
            
            $itemNumber++;
            
            // Jika terlalu banyak item, buat page break
            if ($itemNumber > 15 && count($items) > 15) {
                $html .= '
                </tbody>
                </table>
                </div>
                <div class="page-break"></div>
                <div class="items-section">
                <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-desc">DESKRIPSI ITEM (Lanjutan)</th>
                        <th class="col-qty">QTY</th>
                        <th class="col-unit">SATUAN</th>
                        <th class="col-price">HARGA SATUAN (Rp)</th>
                        <th class="col-subtotal">SUBTOTAL (Rp)</th>
                    </tr>
                </thead>
                <tbody>';
            }
        }
    } else {
        $html .= '
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada item penawaran</td>
                    </tr>';
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
                        <td class="total-value number">' . number_format($total, 0, ',', '.') . '</td>
                    </tr>
                    <tr>
                        <td class="total-label">PPN 11%</td>
                        <td class="total-value number">' . number_format($ppn, 0, ',', '.') . '</td>
                    </tr>
                    <tr>
                        <td class="total-label grand-total">GRAND TOTAL</td>
                        <td class="total-value grand-total number">' . number_format($grandTotal, 0, ',', '.') . '</td>
                    </tr>
                </table>
                <div class="clear"></div>
            </div>
            
            <!-- TERBILANG -->
            <div class="mb-8" style="margin-top: 8px;">
                <p style="font-size: 8.5pt;"><strong>Terbilang:</strong> <em style="font-style: italic;">' . $this->terbilang($grandTotal) . ' Rupiah</em></p>
            </div>
            
            <!-- TERMS & CONDITIONS - Lebih kompak -->
            <div class="terms-section">
                <div class="terms-title">SYARAT DAN KETENTUAN:</div>
                <ul class="terms-list">';
    
    $terms = [
        'Harga belum termasuk PPN 11%',
        'Pembayaran: 50% DP saat order, 50% sebelum pengiriman',
        'Masa garansi: 6 (enam) bulan untuk material dan workmanship',
        'Waktu pengiriman: 2-4 minggu setelah PO dan DP diterima',
        'Pembayaran via transfer ke: Bank Mandiri No. Rek: 101-000-676-607-3 a.n. PT. CIPTA DUTA WACANA',
        'Masa berlaku penawaran: ' . ($penawaran['tanggal_kadaluarsa'] ? date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) : '14 hari') . ''
    ];
    
    foreach ($terms as $term) {
        $html .= '<li>' . htmlspecialchars($term) . '</li>';
    }
    
    if ($penawaran['keterangan']) {
        $html .= '<li>' . htmlspecialchars($penawaran['keterangan']) . '</li>';
    }
    
    if ($penawaran['catatan_khusus']) {
        $html .= '
                </ul>
                <div class="terms-title mt-10">CATATAN KHUSUS:</div>
                <p style="font-size: 8pt; margin: 3px 0 0 15px; line-height: 1.2;">' . nl2br(htmlspecialchars($penawaran['catatan_khusus'])) . '</p>';
    } else {
        $html .= '</ul>';
    }
    
    $html .= '
            </div>
            
            <!-- FOOTER & SIGNATURE -->
            <div class="footer-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p style="font-size: 8.5pt; margin: 2px 0;">Hormat kami,</p>
                    <p class="signature-name">Cecep Tri Hardiyanto</p>
                    <p class="signature-title">Direktur</p>
                </div>
                <div class="clear"></div>
            </div>
            
            <!-- FOOTNOTE -->
            <div style="font-size: 7pt; color: #666; text-align: center; margin-top: 10px; border-top: 0.5px solid #eee; padding-top: 5px;">
                <p>Dokumen ini dibuat secara otomatis oleh sistem CDW • ' . date('d/m/Y H:i') . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}
    
    
  /**
 * Export all penawaran to Excel
 */
public function exportExcelAll()
{
    try {
        // Get all penawaran for this sales
        $userData = $this->getUserData();
        $penawaranList = $this->penawaranModel->getPenawaranBySales($userData['id']);
        
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('CDW Sales System')
            ->setLastModifiedBy('CDW Sales System')
            ->setTitle('Daftar Penawaran Harga')
            ->setSubject('Daftar Penawaran Harga');
        
        // ============= HEADER =============
        $sheet->setCellValue('A1', 'DAFTAR PENAWARAN HARGA - PT. CIPTA DUTA WACANA');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Export Date: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A3', 'Sales: ' . $userData['name']);
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Empty row
        $sheet->setCellValue('A4', '');
        
        // ============= TABLE HEADER =============
        $headers = ['No.', 'Nomor Penawaran', 'Client', 'Project', 'Tanggal', 'Kadaluarsa', 'Status', 'Total (Rp)'];
        $col = 'A';
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $sheet->getStyle($col . '5')->getFont()->setBold(true);
            $sheet->getStyle($col . '5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getStyle($col . '5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        // ============= FILL DATA =============
        $row = 6;
        $no = 1;
        $grandTotal = 0;
        
        foreach ($penawaranList as $penawaran) {
            $total = $this->penawaranModel->calculateTotal($penawaran['id']);
            $grandTotal += $total;
            
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $penawaran['nomor_penawaran']);
            $sheet->setCellValue('C' . $row, $penawaran['nama_perusahaan']);
            $sheet->setCellValue('D' . $row, $penawaran['nama_project']);
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])));
            $sheet->setCellValue('F' . $row, $penawaran['tanggal_kadaluarsa'] ? date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) : '-');
            $sheet->setCellValue('G' . $row, ucfirst($penawaran['status']));
            $sheet->setCellValue('H' . $row, number_format($total, 0, ',', '.'));
            
            // Status color
            $statusColor = [
                'draft' => 'FFC4C4C4',
                'sent' => 'FFC6EFCE',
                'revisi' => 'FFFFEB9C',
                'diterima' => 'FF92D050',
                'ditolak' => 'FFFFC7CE',
                'kadaluarsa' => 'FFD9D9D9'
            ];
            
            $color = $statusColor[$penawaran['status']] ?? 'FFFFFFFF';
            $sheet->getStyle('G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            
            // Style
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            $row++;
            $no++;
        }
        
        // ============= SUMMARY =============
        $summaryRow = $row + 1;
        $sheet->mergeCells('A' . $summaryRow . ':G' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'TOTAL SELURUH PENAWARAN:');
        $sheet->setCellValue('H' . $summaryRow, number_format($grandTotal, 0, ',', '.'));
        
        $sheet->getStyle('A' . $summaryRow . ':H' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow . ':H' . $summaryRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');
        $sheet->getStyle('A' . $summaryRow . ':H' . $summaryRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('H' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // ============= STATISTICS =============
        $statsRow = $summaryRow + 2;
        $sheet->setCellValue('A' . $statsRow, 'STATISTIK:');
        $sheet->getStyle('A' . $statsRow)->getFont()->setBold(true);
        
        // Hitung per status
        $statusCounts = [];
        foreach ($penawaranList as $p) {
            $status = $p['status'];
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }
        
        $statsRow++;
        foreach ($statusCounts as $status => $count) {
            $sheet->setCellValue('A' . $statsRow, ucfirst($status) . ':');
            $sheet->setCellValue('B' . $statsRow, $count);
            $statsRow++;
        }
        
        // ============= AUTOFIT & FORMATTING =============
        // Auto fit semua kolom
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set lebar kolom tertentu
        $sheet->getColumnDimension('C')->setWidth(25); // Client
        $sheet->getColumnDimension('D')->setWidth(30); // Project
        $sheet->getColumnDimension('H')->setWidth(15); // Total
        
        // Auto fit semua baris
        for ($i = 1; $i <= $statsRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
        }
        
        // Set row height untuk header
        $sheet->getRowDimension(5)->setRowHeight(25);
        
        // Center align untuk beberapa kolom
        $sheet->getStyle('A6:A' . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E6:F' . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G6:G' . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Wrap text untuk kolom project
        $sheet->getStyle('D6:D' . ($row-1))->getAlignment()->setWrapText(true);
        
        // Save Excel file
        $filename = 'Daftar_Penawaran_' . date('Ymd_His') . '.xlsx';
        
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
    
/**
 * Export all penawaran to PDF
 */
public function exportPdfAll()
{
    try {
        // Get all penawaran for this sales
        $userData = $this->getUserData();
        $penawaranList = $this->penawaranModel->getPenawaranBySales($userData['id']);
        
        // Create HTML for PDF
        $html = $this->generateAllPdfHtml($penawaranList, $userData);
        
        // Setup Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output PDF
        $filename = 'Daftar_Penawaran_' . date('Ymd_His') . '.pdf';
        
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

/**
 * Generate HTML for All PDF
 */
private function generateAllPdfHtml($penawaranList, $userData)
{
    $grandTotal = 0;
    foreach ($penawaranList as $penawaran) {
        $grandTotal += $this->penawaranModel->calculateTotal($penawaran['id']);
    }
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Daftar Penawaran</title>
        <style>
            @page {
                margin: 15mm;
                size: landscape;
            }
            
            body {
                font-family: "Arial", sans-serif;
                font-size: 10pt;
                line-height: 1.3;
                color: #000;
                margin: 0;
                padding: 0;
            }
            
            .container {
                width: 100%;
            }
            
            .header {
                text-align: center;
                margin-bottom: 15px;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
            
            .company-name {
                font-size: 12pt;
                font-weight: bold;
                margin-bottom: 3px;
            }
            
            .report-title {
                font-size: 14pt;
                font-weight: bold;
                margin: 10px 0;
            }
            
            .subtitle {
                font-size: 9pt;
                margin-bottom: 5px;
            }
            
            .info-section {
                margin-bottom: 10px;
                font-size: 9pt;
            }
            
            .table {
                width: 100%;
                border-collapse: collapse;
                margin: 10px 0;
                font-size: 9pt;
            }
            
            .table th {
                background-color: #333;
                color: white;
                padding: 6px 3px;
                text-align: center;
                font-weight: bold;
                border: 1px solid #ddd;
            }
            
            .table td {
                padding: 5px 3px;
                border: 1px solid #ddd;
                vertical-align: middle;
            }
            
            .table .number {
                text-align: center;
                width: 30px;
            }
            
            .table .date {
                text-align: center;
                width: 70px;
            }
            
            .table .status {
                text-align: center;
                width: 70px;
            }
            
            .table .total {
                text-align: right;
                width: 90px;
            }
            
            .table .client {
                width: 120px;
            }
            
            .table .project {
                width: 150px;
            }
            
            .status-draft { background-color: #f0f0f0; }
            .status-sent { background-color: #e1f5e1; }
            .status-revisi { background-color: #fffacd; }
            .status-diterima { background-color: #d4edda; }
            .status-ditolak { background-color: #f8d7da; }
            .status-kadaluarsa { background-color: #e9ecef; }
            
            .summary {
                margin-top: 15px;
                font-size: 10pt;
            }
            
            .summary-row {
                font-weight: bold;
                background-color: #f5f5f5;
            }
            
            .footer {
                margin-top: 20px;
                font-size: 8pt;
                text-align: center;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 10px;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .text-bold {
                font-weight: bold;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .total-section {
                float: right;
                width: 300px;
                margin-top: 10px;
            }
            
            .clear {
                clear: both;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="company-name">PT. CIPTA DUTA WACANA</div>
                <div class="subtitle">Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41, Jakarta Selatan</div>
                <div class="report-title">DAFTAR PENAWARAN HARGA</div>
                <div class="info-section">
                    <div>Export Date: ' . date('d/m/Y H:i') . '</div>
                    <div>Sales: ' . htmlspecialchars($userData['name']) . '</div>
                    <div>Total Penawaran: ' . count($penawaranList) . '</div>
                </div>
            </div>
            
            <!-- Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th class="number">No.</th>
                        <th>Nomor Penawaran</th>
                        <th class="client">Client</th>
                        <th class="project">Project</th>
                        <th class="date">Tanggal</th>
                        <th class="date">Kadaluarsa</th>
                        <th class="status">Status</th>
                        <th class="total">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>';
    
    if (!empty($penawaranList)) {
        $no = 1;
        foreach ($penawaranList as $penawaran) {
            $total = $this->penawaranModel->calculateTotal($penawaran['id']);
            $statusClass = 'status-' . $penawaran['status'];
            
            $html .= '
                    <tr>
                        <td class="number">' . $no . '</td>
                        <td>' . htmlspecialchars($penawaran['nomor_penawaran']) . '</td>
                        <td class="client">' . htmlspecialchars($penawaran['nama_perusahaan']) . '</td>
                        <td class="project">' . htmlspecialchars($penawaran['nama_project']) . '</td>
                        <td class="date">' . date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])) . '</td>
                        <td class="date">' . ($penawaran['tanggal_kadaluarsa'] ? date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) : '-') . '</td>
                        <td class="status ' . $statusClass . '">' . ucfirst($penawaran['status']) . '</td>
                        <td class="total">' . number_format($total, 0, ',', '.') . '</td>
                    </tr>';
            $no++;
        }
    } else {
        $html .= '
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data penawaran</td>
                    </tr>';
    }
    
    $html .= '
                </tbody>
                <tfoot>
                    <tr class="summary-row">
                        <td colspan="7" class="text-right">GRAND TOTAL:</td>
                        <td class="total">' . number_format($grandTotal, 0, ',', '.') . '</td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Summary Section -->
            <div class="summary">
                <div class="total-section">
                    <table style="width: 100%;">
                        <tr>
                            <td><strong>Total Penawaran:</strong></td>
                            <td class="text-right">' . count($penawaranList) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Total Nilai:</strong></td>
                            <td class="text-right">Rp ' . number_format($grandTotal, 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td><strong>Total dengan PPN 11%:</strong></td>
                            <td class="text-right">Rp ' . number_format($grandTotal * 1.11, 0, ',', '.') . '</td>
                        </tr>
                    </table>
                </div>
                <div class="clear"></div>
                
                <!-- Status Summary -->
                <div style="margin-top: 15px;">
                    <strong>Ringkasan Status:</strong><br>';
    
    $statusCounts = [];
    foreach ($penawaranList as $p) {
        $status = $p['status'];
        $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
    }
    
    foreach ($statusCounts as $status => $count) {
        $html .= ucfirst($status) . ': ' . $count . ' &nbsp;&nbsp;&nbsp;';
    }
    
    $html .= '
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p>Generated by CDW Sales System</p>
                <p>Halaman 1/1</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
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
        } else if ($angka < 1000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000) . " Milyar" . $this->terbilang(fmod($angka, 1000000000));
        }
        
        return trim($terbilang);
    }
}