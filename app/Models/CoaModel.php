<?php

namespace App\Models;

use CodeIgniter\Model;

class CoaModel extends Model
{
    protected $table = 'coa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'kategori',
        'saldo_normal',
        'deskripsi',
        'is_header',
        'parent_id',
        'level',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nama_akun' => 'required|max_length[200]',
        'tipe_akun' => 'required|in_list[Aset,Kewajiban,Ekuitas,Pendapatan,Beban]',
        'saldo_normal' => 'required|in_list[Debit,Kredit]',
        'is_header' => 'required|in_list[0,1]',
        'level' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]'
    ];

    protected $validationMessages = [
        'nama_akun' => [
            'required' => 'Nama akun harus diisi',
            'max_length' => 'Nama akun maksimal 200 karakter'
        ],
        'tipe_akun' => [
            'required' => 'Tipe akun harus dipilih',
            'in_list' => 'Tipe akun tidak valid'
        ],
        'saldo_normal' => [
            'required' => 'Saldo normal harus dipilih',
            'in_list' => 'Saldo normal tidak valid'
        ],
        'is_header' => [
            'required' => 'Jenis akun harus dipilih',
            'in_list' => 'Jenis akun tidak valid'
        ],
        'level' => [
            'required' => 'Level harus dipilih',
            'integer' => 'Level harus berupa angka',
            'greater_than_equal_to' => 'Level minimal 1',
            'less_than_equal_to' => 'Level maksimal 5'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeAkun', 'setCreatedBy'];
    protected $beforeUpdate = ['validateHierarchy', 'setUpdatedBy'];

    /**
     * Generate kode akun otomatis jika tidak diisi
     */
    protected function generateKodeAkun(array $data)
    {
        // Jika user sudah mengisi kode_akun, langsung return tanpa mengubah
        if (!empty($data['data']['kode_akun'])) {
            return $data;
        }
        
        // Kode di bawah ini HANYA dijalankan jika kode_akun KOSONG
        $prefix = [
            'Aset' => '1',
            'Kewajiban' => '2',
            'Ekuitas' => '3',
            'Pendapatan' => '4',
            'Beban' => '5'
        ];
        
        $tipe = $data['data']['tipe_akun'];
        $parentId = $data['data']['parent_id'] ?? null;
        $level = $data['data']['level'] ?? 1;
        
        if ($parentId) {
            // Generate kode untuk child account
            $parent = $this->find($parentId);
            if ($parent) {
                // Hitung sequence untuk child
                $lastChild = $this->where('parent_id', $parentId)
                    ->where('kode_akun IS NOT NULL')
                    ->orderBy('kode_akun', 'DESC')
                    ->first();
                
                if ($lastChild && !empty($lastChild['kode_akun'])) {
                    $childParts = explode('-', $lastChild['kode_akun']);
                    $lastSeq = (int)end($childParts);
                    $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
                    $data['data']['kode_akun'] = $parent['kode_akun'] . '-' . $nextSeq;
                } else {
                    $data['data']['kode_akun'] = $parent['kode_akun'] . '-001';
                }
            }
        } else {
            // Generate kode untuk root account (level 1)
            $lastCode = $this->where('tipe_akun', $tipe)
                ->where('level', 1)
                ->where('parent_id IS NULL')
                ->where('kode_akun IS NOT NULL')
                ->orderBy('kode_akun', 'DESC')
                ->first();
            
            if ($lastCode && !empty($lastCode['kode_akun'])) {
                $parts = explode('-', $lastCode['kode_akun']);
                $lastNum = (int)end($parts);
                $nextNum = str_pad($lastNum + 100, 4, '0', STR_PAD_LEFT);
                $data['data']['kode_akun'] = $prefix[$tipe] . '-' . $nextNum;
            } else {
                $data['data']['kode_akun'] = $prefix[$tipe] . '-1000';
            }
        }
        
        return $data;
    }

    /**
     * Validate hierarchy before update
     */
    protected function validateHierarchy(array $data)
    {
        if (isset($data['data']['parent_id'])) {
            $parentId = $data['data']['parent_id'];
            $id = $data['id'][0] ?? null;
            
            if ($parentId && $id) {
                if ($parentId == $id) {
                    throw new \RuntimeException('Akun tidak dapat menjadi parent dari dirinya sendiri');
                }
                $this->checkCircularReference($id, $parentId);
            }
        }
        return $data;
    }

    /**
     * Check for circular reference
     */
    private function checkCircularReference($id, $parentId)
    {
        $current = $parentId;
        while ($current) {
            if ($current == $id) {
                throw new \RuntimeException('Circular reference detected');
            }
            $parent = $this->find($current);
            $current = $parent['parent_id'] ?? null;
        }
    }

    /**
     * Set created_by
     */
    protected function setCreatedBy(array $data)
    {
        $session = session();
        if ($session && $session->get('user_id')) {
            $data['data']['created_by'] = $session->get('user_id');
        }
        return $data;
    }

    /**
     * Set updated_by
     */
    protected function setUpdatedBy(array $data)
    {
        $session = session();
        if ($session && $session->get('user_id')) {
            $data['data']['updated_by'] = $session->get('user_id');
        }
        return $data;
    }

    /**
     * Get COA dengan hirarki lengkap
     */
    public function getAllWithHierarchy($activeOnly = true)
    {
        $builder = $this->orderBy('kode_akun', 'ASC');
        
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        
        $coa = $builder->findAll();
        return $this->buildTree($coa);
    }

    /**
     * Build hierarchical tree
     */
    private function buildTree(array $elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    /**
     * Get flat list dengan indentasi untuk dropdown
     */
    public function getFlatList($activeOnly = true)
    {
        $coa = $this->getAllWithHierarchy($activeOnly);
        return $this->flattenTree($coa);
    }

    /**
     * Flatten tree dengan indentasi
     */
    private function flattenTree(array $tree, $level = 0, &$result = [])
    {
        foreach ($tree as $item) {
            $item['indent'] = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
            $result[] = $item;
            if (!empty($item['children'])) {
                $this->flattenTree($item['children'], $level + 1, $result);
            }
        }
        return $result;
    }

    /**
     * Get parent options untuk dropdown
     */
    public function getParentOptions($exceptId = null, $headerOnly = true)
    {
        $builder = $this->where('is_active', 1);
        
        if ($headerOnly) {
            $builder->where('is_header', 1);
        }
        
        if ($exceptId) {
            $builder->where('id !=', $exceptId);
        }
        
        $parents = $builder->orderBy('kode_akun', 'ASC')->findAll();
        
        $options = ['' => '-- Pilih Parent --'];
        foreach ($parents as $parent) {
            $options[$parent['id']] = "{$parent['kode_akun']} - {$parent['nama_akun']}";
        }
        
        return $options;
    }

    /**
     * Get COA untuk dropdown transaksi (hanya detail account)
     */
    public function getForTransaction($tipeAkun = null)
    {
        $builder = $this->where('is_header', 0)->where('is_active', 1);
        
        if ($tipeAkun) {
            $builder->where('tipe_akun', $tipeAkun);
        }
        
        return $builder->orderBy('kode_akun', 'ASC')->findAll();
    }

    /**
     * Get next available kode untuk parent tertentu
     */
    public function getNextChildCode($parentId)
    {
        $parent = $this->find($parentId);
        if (!$parent) {
            return null;
        }
        
        $lastChild = $this->where('parent_id', $parentId)
            ->orderBy('kode_akun', 'DESC')
            ->first();
        
        if ($lastChild && !empty($lastChild['kode_akun'])) {
            $childParts = explode('-', $lastChild['kode_akun']);
            $lastSeq = (int)end($childParts);
            $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
            return $parent['kode_akun'] . '-' . $nextSeq;
        }
        
        return $parent['kode_akun'] . '-001';
    }

    /**
     * Get account by kode
     */
    public function getByKode($kode)
    {
        return $this->where('kode_akun', $kode)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Get accounts by tipe
     */
    public function getByTipe($tipe, $headerOnly = false)
    {
        $builder = $this->where('tipe_akun', $tipe)->where('is_active', 1);
        
        if ($headerOnly) {
            $builder->where('is_header', 1);
        } else {
            $builder->where('is_header', 0);
        }
        
        return $builder->orderBy('kode_akun', 'ASC')->findAll();
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('is_active', 1)->countAllResults(),
            'inactive' => $this->where('is_active', 0)->countAllResults(),
            'header' => $this->where('is_header', 1)->countAllResults(),
            'detail' => $this->where('is_header', 0)->countAllResults(),
            'by_type' => []
        ];
        
        $types = ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'];
        foreach ($types as $type) {
            $stats['by_type'][$type] = [
                'total' => $this->where('tipe_akun', $type)->countAllResults(),
                'active' => $this->where('tipe_akun', $type)->where('is_active', 1)->countAllResults(),
                'header' => $this->where('tipe_akun', $type)->where('is_header', 1)->countAllResults(),
                'detail' => $this->where('tipe_akun', $type)->where('is_header', 0)->countAllResults()
            ];
        }
        
        return $stats;
    }

    /**
     * Check if account has children
     */
    public function hasChildren($id)
    {
        return $this->where('parent_id', $id)->countAllResults() > 0;
    }

    /**
     * Get children accounts
     */
    public function getChildren($parentId, $activeOnly = true)
    {
        $builder = $this->where('parent_id', $parentId);
        
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        
        return $builder->orderBy('kode_akun', 'ASC')->findAll();
    }

    /**
     * Get account path (breadcrumb)
     */
    public function getAccountPath($id)
    {
        $path = [];
        $current = $this->find($id);
        
        if (!$current) {
            return $path;
        }
        
        while ($current) {
            $path[] = $current;
            if ($current['parent_id']) {
                $current = $this->find($current['parent_id']);
            } else {
                $current = null;
            }
        }
        
        return array_reverse($path);
    }

    /**
     * Validate kode format
     */
    public function validateKodeFormat($kode, $tipeAkun)
    {
        $prefixMap = [
            'Aset' => '1',
            'Kewajiban' => '2',
            'Ekuitas' => '3',
            'Pendapatan' => '4',
            'Beban' => '5'
        ];
        
        $expectedPrefix = $prefixMap[$tipeAkun] ?? null;
        if (!$expectedPrefix) {
            return false;
        }
        
        return strpos($kode, $expectedPrefix . '-') === 0;
    }

    /**
     * Update account status (activate/deactivate)
     */
    public function updateStatus($id, $status)
    {
        $account = $this->find($id);
        if (!$account) {
            return false;
        }
        
        if ($status == 0 && $account['is_header'] == 1) {
            $this->where('parent_id', $id)
                ->set(['is_active' => 0, 'updated_by' => session()->get('user_id')])
                ->update();
        }
        
        return $this->update($id, ['is_active' => $status, 'updated_by' => session()->get('user_id')]);
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData()
    {
        $coa = $this->select('coa.*, creator.username as creator_name, updater.username as updater_name')
            ->join('users as creator', 'creator.id = coa.created_by', 'left')
            ->join('users as updater', 'updater.id = coa.updated_by', 'left')
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $exportData = [];
        foreach ($coa as $account) {
            $parentCode = '-';
            if ($account['parent_id']) {
                $parent = $this->find($account['parent_id']);
                if ($parent) {
                    $parentCode = $parent['kode_akun'];
                }
            }
            
            $exportData[] = [
                'Kode Akun' => $account['kode_akun'],
                'Nama Akun' => $account['nama_akun'],
                'Tipe Akun' => $account['tipe_akun'],
                'Kategori' => $account['kategori'] ?? '-',
                'Saldo Normal' => $account['saldo_normal'],
                'Level' => $account['level'],
                'Jenis' => $account['is_header'] ? 'Header' : 'Detail',
                'Status' => $account['is_active'] ? 'Aktif' : 'Nonaktif',
                'Parent' => $parentCode,
                'Deskripsi' => $account['deskripsi'] ?? '-',
                'created_by' => $account['creator_name'] ?? '-',
                'updated_by' => $account['updater_name'] ?? '-',
                'created_at' => $account['created_at'],
                'updated_at' => $account['updated_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if kode exists
     */
    public function isKodeExists($kode, $exceptId = null)
    {
        if (empty($kode)) {
            return false;
        }
        
        $builder = $this->where('kode_akun', $kode);
        
        if ($exceptId) {
            $builder->where('id !=', $exceptId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get descendant IDs for an account
     */
    public function getDescendantIds($parentId)
    {
        $descendants = [];
        $children = $this->where('parent_id', $parentId)->findAll();
        
        foreach ($children as $child) {
            $descendants[] = $child['id'];
            $grandChildren = $this->getDescendantIds($child['id']);
            $descendants = array_merge($descendants, $grandChildren);
        }
        
        return $descendants;
    }

    /**
     * Get account with full parent information
     */
    public function getAccountWithParent($id)
    {
        $account = $this->find($id);
        if (!$account) {
            return null;
        }
        
        if ($account['parent_id']) {
            $account['parent_info'] = $this->find($account['parent_id']);
        }
        
        return $account;
    }
}