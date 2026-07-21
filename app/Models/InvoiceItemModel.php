<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceItemModel extends Model
{
    protected $table = 'invoice_item';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_id', 'nama_item', 'deskripsi', 'qty', 
        'satuan', 'harga_satuan'
    ];
    
    protected $useTimestamps = false;
    
    /**
     * Get items by invoice ID
     */
    public function getItemsByInvoice($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)->findAll();
    }
    
    /**
     * Delete items by invoice ID
     */
    public function deleteByInvoice($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)->delete();
    }
    
    /**
     * Calculate subtotal for invoice
     */
    public function calculateInvoiceTotal($invoiceId)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('subtotal');
        $builder->where('invoice_id', $invoiceId);
        $result = $builder->get()->getRowArray();
        
        return $result ? floatval($result['subtotal']) : 0;
    }
}