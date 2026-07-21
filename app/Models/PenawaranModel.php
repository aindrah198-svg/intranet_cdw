<?php

namespace App\Models;

use CodeIgniter\Model;

class PenawaranModel extends Model
{
    protected $table = 'penawaran';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_penawaran', 'project_id', 'tanggal_penawaran', 'tanggal_kadaluarsa',
        'status', 'keterangan', 'catatan_khusus', 'created_by', 'approved_by',
        'perusahaan_pengirim_id', 'perusahaan_pengirim_nama', 'perusahaan_pengirim_alamat',
        'perusahaan_pengirim_telepon', 'perusahaan_pengirim_fax', 'perusahaan_pengirim_email',
        'perusahaan_pengirim_website', 'perusahaan_pengirim_bank_account',
        'client_attention', 'client_contact', 'client_po_number', 'due_date',
        'customer_code', 'quot_format', 'quot_date_text', 'remarks', 'terms_payment',
        'in_words', 'ppn_percentage', 'ppn_amount', 'subtotal', 'total', 'valid_until',
        'is_ppn_included', 'currency', 'discount_percentage', 'discount_amount',
        'signature_name', 'signature_position', 'header_title', 'footer_note',
        'template_type', 'is_signed', 'signed_at', 'signed_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPenawaranWithDetails($limit = null, $offset = 0)
{
    $builder = $this->db->table('penawaran p');
    $builder->select('p.*, 
        pr.kode_project, pr.nama_project, pr.status as project_status,
        c.nama_perusahaan as client_nama, c.kode_client, c.nama_kontak,
        c.telepon as client_telepon, c.email as client_email,
        u.name as created_by_name, u.email as created_by_email,
        k.nama_lengkap as sales_name')
        ->join('project pr', 'pr.id = p.project_id', 'left')
        ->join('client c', 'c.id = pr.client_id', 'left')
        ->join('users u', 'u.id = p.created_by', 'left')
        ->join('karyawan k', 'k.id = c.sales_id', 'left')
        ->orderBy('p.created_at', 'DESC');
    
    if ($limit) {
        $builder->limit($limit, $offset);
    }
    
    return $builder->get()->getResultArray();
}

    public function getPenawaranDetail($id)
    {
        $db = \Config\Database::connect();
        
        $query = $db->table('penawaran p')
            ->select('p.*, 
                pr.kode_project, pr.nama_project, pr.nilai_project,
                c.nama_perusahaan as client_nama, c.alamat as client_alamat,
                c.telepon as client_telepon, c.email as client_email,
                c.npwp as client_npwp, c.kode_client,
                u.name as created_by_name, u.email as created_by_email')
            ->join('project pr', 'pr.id = p.project_id', 'left')
            ->join('client c', 'c.id = pr.client_id', 'left')
            ->join('users u', 'u.id = p.created_by', 'left')
            ->where('p.id', $id)
            ->get();
        
        return $query->getRowArray();
    }

    public function getItems($penawaranId)
    {
        $db = \Config\Database::connect();
        $query = $db->table('penawaran_item')
            ->where('penawaran_id', $penawaranId)
            ->orderBy('id', 'ASC')
            ->get();
        
        return $query->getResultArray();
    }

    public function insertItem($itemData)
    {
        $db = \Config\Database::connect();
        return $db->table('penawaran_item')->insert($itemData);
    }

    public function updatePenawaranTotals($penawaranId)
    {
        $items = $this->getItems($penawaranId);
        
        if (empty($items)) {
            return false;
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += floatval($item['subtotal'] ?? 0);
        }

        $penawaran = $this->find($penawaranId);
        if (!$penawaran) {
            return false;
        }

        // Apply discount if any
        $discountAmount = 0;
        if (isset($penawaran['discount_percentage']) && $penawaran['discount_percentage'] > 0) {
            $discountAmount = $subtotal * (floatval($penawaran['discount_percentage']) / 100);
        } else if (isset($penawaran['discount_amount'])) {
            $discountAmount = floatval($penawaran['discount_amount']);
        }

        $subtotalAfterDiscount = $subtotal - $discountAmount;
        
        // Calculate PPN
        $ppnPercentage = isset($penawaran['ppn_percentage']) ? floatval($penawaran['ppn_percentage']) : 11.00;
        $ppnAmount = $subtotalAfterDiscount * ($ppnPercentage / 100);
        
        // Calculate total
        if (isset($penawaran['is_ppn_included']) && $penawaran['is_ppn_included'] == 1) {
            $total = $subtotalAfterDiscount;
        } else {
            $total = $subtotalAfterDiscount + $ppnAmount;
        }

        // Update the quotation
        return $this->update($penawaranId, [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'ppn_amount' => $ppnAmount,
            'total' => $total,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    // Di PenawaranModel.php - Tambah method ini:
public function getBankAccounts($perusahaanId = 1)
{
    return $this->db->table('perusahaan_bank_account')
        ->where('perusahaan_id', $perusahaanId)
        ->orderBy('is_default', 'DESC')
        ->get()
        ->getResultArray();
}

// Tambah method untuk format bank account string
public function formatBankAccounts($accounts)
{
    if (empty($accounts)) {
        return '';
    }
    
    $formatted = '';
    foreach ($accounts as $account) {
        $formatted .= "{$account['bank_name']} - {$account['account_number']} (a/n {$account['account_name']})" . PHP_EOL;
        if (!empty($account['branch'])) {
            $formatted .= "Cabang: {$account['branch']}" . PHP_EOL;
        }
        if (!empty($account['swift_code'])) {
            $formatted .= "SWIFT: {$account['swift_code']}" . PHP_EOL;
        }
        $formatted .= "Mata Uang: {$account['currency']}" . PHP_EOL . PHP_EOL;
    }
    
    return $formatted;
}

}