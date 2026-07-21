<?php

namespace App\Models\Teknisi;

use CodeIgniter\Model;

class SpkInstalasiItemModel extends Model
{
    protected $table = 'spk_instalasi_item';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'spk_id',
        'nama_item',
        'deskripsi',
        'qty',
        'satuan',
        'harga',
        'total',
        'keterangan',
        'status',
        'urutan'
    ];

    // Nonaktifkan timestamps otomatis
    protected $useTimestamps = false;

    // Validation rules
    protected $validationRules = [
        'spk_id' => 'required|numeric',
        'nama_item' => 'required',
        'qty' => 'required|numeric|greater_than[0]',
        'harga' => 'permit_empty|numeric'
    ];

    protected $validationMessages = [
        'spk_id' => [
            'required' => 'SPK ID wajib diisi',
            'numeric' => 'SPK ID harus angka'
        ],
        'nama_item' => [
            'required' => 'Nama item wajib diisi'
        ],
        'qty' => [
            'required' => 'Quantity wajib diisi',
            'numeric' => 'Quantity harus angka',
            'greater_than' => 'Quantity harus lebih dari 0'
        ]
    ];

    /**
     * Get items by SPK ID
     */
    public function getBySpkId($spk_id)
    {
        return $this->where('spk_id', $spk_id)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    /**
     * Update status item
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Hitung total biaya item untuk SPK tertentu
     */
    public function getTotalBySpkId($spk_id)
    {
        $builder = $this->select('SUM(total) as total')
            ->where('spk_id', $spk_id);
        $result = $builder->get()->getRow();
        
        return $result ? (float) $result->total : 0;
    }

    /**
     * Hitung progress item berdasarkan status selesai
     */
    public function getProgressBySpkId($spk_id)
    {
        $total = $this->where('spk_id', $spk_id)->countAllResults();
        
        if ($total == 0) {
            return 0;
        }
        
        $selesai = $this->where('spk_id', $spk_id)
            ->where('status', 'Selesai')
            ->countAllResults();
        
        return round(($selesai / $total) * 100, 2);
    }

    /**
     * Get statistik item
     */
    public function getStatistik($spk_id = null)
    {
        $builder = $this->select('
            COUNT(*) as total_items,
            SUM(CASE WHEN status = "Pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = "Bermasalah" THEN 1 ELSE 0 END) as bermasalah,
            SUM(total) as total_biaya,
            SUM(qty) as total_qty
        ');
        
        if ($spk_id) {
            $builder->where('spk_id', $spk_id);
        }
        
        return $builder->get()->getRow();
    }

   /**
 * Simpan multiple items sekaligus
 */
public function saveItems($spk_id, $items)
{
    // Validasi
    if (empty($spk_id) || !is_numeric($spk_id)) {
        log_message('error', 'saveItems: Invalid spk_id');
        return false;
    }

    // Hapus items lama
    $this->where('spk_id', $spk_id)->delete();
    
    if (empty($items)) {
        log_message('debug', 'saveItems: No items to save');
        return true;
    }
    
    $data = [];
    $urutan = 1;
    
    foreach ($items as $item) {
        // Validasi item
        if (empty($item['nama_item'])) {
            log_message('debug', 'saveItems: Skipping item with empty nama_item');
            continue;
        }
        
        // Bersihkan dan hitung total
        $qty = isset($item['qty']) ? (float) $item['qty'] : 1;
        $harga = isset($item['harga']) ? (float) $item['harga'] : 0;
        $total = $qty * $harga;
        
        // Pastikan status tersimpan dengan benar
        $status = isset($item['status']) && !empty($item['status']) ? $item['status'] : 'Pending';
        
        log_message('debug', "saveItems: Item $urutan - nama: {$item['nama_item']}, status: $status, qty: $qty, harga: $harga");
        
        $data[] = [
            'spk_id' => $spk_id,
            'nama_item' => trim($item['nama_item']),
            'deskripsi' => $item['deskripsi'] ?? '',
            'qty' => $qty,
            'satuan' => $item['satuan'] ?? 'unit',
            'harga' => $harga,
            'total' => $total,
            'keterangan' => $item['keterangan'] ?? '',
            'status' => $status,
            'urutan' => $urutan++
        ];
    }
    
    if (empty($data)) {
        log_message('debug', 'saveItems: No valid items after filtering');
        return true;
    }
    
    log_message('debug', 'saveItems: Inserting ' . count($data) . ' items');
    
    // Insert batch
    $result = $this->insertBatch($data);
    
    if ($result) {
        log_message('debug', 'saveItems: Success');
    } else {
        log_message('error', 'saveItems: Failed - ' . json_encode($this->errors()));
    }
    
    return $result;
}
}