<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanItemModel extends Model
{
    protected $table = 'surat_jalan_item';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'surat_jalan_id', 
        'nama_barang', 
        'qty', 
        'satuan', 
        'keterangan',
        'berat', 
        'satuan_berat', 
        'no_urut'
    ];
    
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'surat_jalan_id' => 'required|integer',
        'nama_barang' => 'required|max_length[200]',
        'qty' => 'required|numeric|greater_than[0]',
        'satuan' => 'required|max_length[20]',
        'no_urut' => 'permit_empty|integer'
    ];
    
    protected $validationMessages = [
        'surat_jalan_id' => [
            'required' => 'ID Surat Jalan wajib diisi',
            'integer' => 'ID Surat Jalan harus berupa angka'
        ],
        'nama_barang' => [
            'required' => 'Nama barang wajib diisi',
            'max_length' => 'Nama barang maksimal 200 karakter'
        ],
        'qty' => [
            'required' => 'Quantity wajib diisi',
            'numeric' => 'Quantity harus berupa angka',
            'greater_than' => 'Quantity harus lebih dari 0'
        ],
        'satuan' => [
            'required' => 'Satuan wajib diisi',
            'max_length' => 'Satuan maksimal 20 karakter'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get items by surat jalan ID
     */
    public function getItemsBySuratJalan($suratJalanId)
    {
        return $this->where('surat_jalan_id', $suratJalanId)
                    ->orderBy('no_urut', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get items dengan format untuk cetak/display
     */
    public function getItemsForDisplay($suratJalanId)
    {
        $items = $this->getItemsBySuratJalan($suratJalanId);
        
        // Format data untuk display
        foreach ($items as &$item) {
            // Format qty dengan 2 desimal jika perlu
            $item['qty_formatted'] = (floatval($item['qty']) == intval($item['qty'])) 
                ? intval($item['qty']) 
                : number_format($item['qty'], 2);
            
            // Format berat jika ada
            if (!empty($item['berat'])) {
                $item['berat_formatted'] = number_format($item['berat'], 2) . ' ' . ($item['satuan_berat'] ?? 'kg');
            } else {
                $item['berat_formatted'] = '-';
            }
            
            // Tampilkan keterangan jika ada
            $item['display_keterangan'] = !empty($item['keterangan']) 
                ? '(' . $item['keterangan'] . ')' 
                : '';
        }
        
        return $items;
    }
    
    /**
     * Delete items by surat jalan ID
     */
    public function deleteBySuratJalan($suratJalanId)
    {
        return $this->where('surat_jalan_id', $suratJalanId)->delete();
    }
    
    /**
     * Get total items count dan summary
     */
    public function getTotalItemsCount($suratJalanId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(*) as total_items, 
                         SUM(qty) as total_qty,
                         SUM(berat) as total_berat')
                ->where('surat_jalan_id', $suratJalanId);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Batch insert items
     */
    public function insertBatchItems($suratJalanId, $items)
    {
        // Hapus items lama jika ada
        $this->deleteBySuratJalan($suratJalanId);
        
        // Tambahkan surat_jalan_id ke setiap item
        $batchData = [];
        $urut = 1;
        
        foreach ($items as $item) {
            if (!empty($item['nama_barang'])) {
                $batchData[] = [
                    'surat_jalan_id' => $suratJalanId,
                    'nama_barang' => $item['nama_barang'],
                    'qty' => $item['qty'] ?? 1,
                    'satuan' => $item['satuan'] ?? 'unit',
                    'keterangan' => $item['keterangan'] ?? null,
                    'berat' => $item['berat'] ?? null,
                    'satuan_berat' => $item['satuan_berat'] ?? 'kg',
                    'no_urut' => $item['no_urut'] ?? $urut++
                ];
            }
        }
        
        if (!empty($batchData)) {
            return $this->insertBatch($batchData);
        }
        
        return false;
    }
    
    /**
     * Update item dengan validasi
     */
    public function updateItem($id, $data)
    {
        // Validasi data
        $validation = \Config\Services::validation();
        
        if (!$validation->run($data, 'surat_jalan_item')) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }
        
        $success = $this->update($id, $data);
        
        return [
            'success' => $success,
            'data' => $this->find($id)
        ];
    }
    
    /**
     * Get item summary untuk dashboard/laporan
     */
    public function getItemSummary($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('surat_jalan_item si');
        $builder->select('si.nama_barang, 
                         COUNT(si.id) as jumlah_kirim,
                         SUM(si.qty) as total_qty,
                         sj.status')
                ->join('surat_jalan sj', 'sj.id = si.surat_jalan_id');
        
        if ($startDate) {
            $builder->where('sj.tanggal_kirim >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('sj.tanggal_kirim <=', $endDate);
        }
        
        $builder->groupBy('si.nama_barang')
                ->orderBy('total_qty', 'DESC')
                ->limit(10);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Cek apakah surat jalan memiliki items
     */
    public function hasItems($suratJalanId)
    {
        return $this->where('surat_jalan_id', $suratJalanId)->countAllResults() > 0;
    }
    
    /**
     * Get items grouped by kategori (untuk laporan)
     */
    public function getItemsGroupedByCategory($suratJalanId)
    {
        $items = $this->getItemsBySuratJalan($suratJalanId);
        $grouped = [];
        
        foreach ($items as $item) {
            // Coba ekstrak kategori dari nama barang atau keterangan
            $category = $this->extractCategory($item['nama_barang']);
            
            if (!isset($grouped[$category])) {
                $grouped[$category] = [
                    'category' => $category,
                    'items' => [],
                    'total_qty' => 0
                ];
            }
            
            $grouped[$category]['items'][] = $item;
            $grouped[$category]['total_qty'] += floatval($item['qty']);
        }
        
        return array_values($grouped);
    }
    
    /**
     * Helper untuk ekstrak kategori dari nama barang
     */
    private function extractCategory($namaBarang)
    {
        $keywords = [
            'valve' => 'Valve',
            'pump' => 'Pump',
            'flowmeter' => 'Flowmeter',
            'controller' => 'Controller',
            'sensor' => 'Sensor',
            'pipe' => 'Pipa',
            'fitting' => 'Fitting',
            'cable' => 'Kabel',
            'tool' => 'Tool'
        ];
        
        $lowerName = strtolower($namaBarang);
        
        foreach ($keywords as $key => $category) {
            if (strpos($lowerName, $key) !== false) {
                return $category;
            }
        }
        
        return 'Lainnya';
    }
    
    /**
     * Validasi custom untuk items
     */
    public function validateItems($items)
    {
        $errors = [];
        
        foreach ($items as $index => $item) {
            if (empty($item['nama_barang'])) {
                $errors[] = "Nama barang pada baris " . ($index + 1) . " tidak boleh kosong";
            }
            
            if (!isset($item['qty']) || $item['qty'] <= 0) {
                $errors[] = "Quantity pada baris " . ($index + 1) . " harus lebih dari 0";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}