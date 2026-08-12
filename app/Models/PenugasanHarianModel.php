<?php
namespace App\Models;

use CodeIgniter\Model;

class PenugasanHarianModel extends Model
{
    protected $table            = 'penugasan_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'pemberi_id',
        'pemberi_role',
        'penerima_role',
        'penerima_id',
        'judul_tugas',
        'deskripsi_tugas',
        'tanggal_tugas',
        'tenggat_waktu',
        'prioritas',
        'status',
        'alasan_ditunda',
        'laporan_harian_id',
        'catatan_penyelesaian',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'pemberi_role'   => 'required',
        'penerima_role'  => 'required',
        'judul_tugas'    => 'required|min_length[3]|max_length[255]',
        'tanggal_tugas'  => 'required|valid_date[Y-m-d]',
        'prioritas'      => 'required|in_list[rendah,sedang,tinggi,mendesak]',
        'status'         => 'required|in_list[pending,proses,selesai,ditunda]'
    ];

    /**
     * Otomatis membuat tabel `penugasan_harian` & `penugasan_harian_items` jika belum ada di database
     */
    public function ensureTableExists()
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists($this->table)) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'pemberi_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'pemberi_role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'direktur',
                ],
                'penerima_role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'accounting',
                ],
                'penerima_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'judul_tugas' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'deskripsi_tugas' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'tanggal_tugas' => [
                    'type' => 'DATE',
                ],
                'tenggat_waktu' => [
                    'type' => 'TIME',
                    'null' => true,
                ],
                'prioritas' => [
                    'type'       => 'ENUM',
                    'constraint' => ['rendah', 'sedang', 'tinggi', 'mendesak'],
                    'default'    => 'sedang',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'proses', 'selesai', 'ditunda'],
                    'default'    => 'pending',
                ],
                'alasan_ditunda' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'laporan_harian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'catatan_penyelesaian' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $forge->addKey('id', true);
            $forge->createTable($this->table, true);
        }

        // Ensure item table exists as well
        $itemModel = new PenugasanHarianItemModel();
        $itemModel->ensureTableExists();
    }

    /**
     * Ambil penugasan beserta item-item checklistnya
     */
    public function getTaskWithItems($id)
    {
        $task = $this->find($id);
        if (!$task) return null;

        $itemModel = new PenugasanHarianItemModel();
        $task['items'] = $itemModel->where('penugasan_id', $id)->findAll();
        
        $total = count($task['items']);
        $selesai = 0;
        $proses = 0;
        $ditunda = 0;
        $pending = 0;

        foreach ($task['items'] as $item) {
            if ($item['status_item'] === 'selesai') $selesai++;
            elseif ($item['status_item'] === 'proses') $proses++;
            elseif ($item['status_item'] === 'ditunda') $ditunda++;
            else $pending++;
        }

        $task['total_items']   = $total;
        $task['items_selesai'] = $selesai;
        $task['items_proses']  = $proses;
        $task['items_ditunda'] = $ditunda;
        $task['items_pending'] = $pending;
        $task['progress_pct']  = $total > 0 ? round(($selesai / $total) * 100) : 0;

        return $task;
    }

    /**
     * Hitung ulang status induk berdasarkan status per-item
     */
    public function recalculateTaskStatus($penugasanId)
    {
        $itemModel = new PenugasanHarianItemModel();
        $items = $itemModel->where('penugasan_id', $penugasanId)->findAll();
        
        if (empty($items)) {
            return;
        }

        $total = count($items);
        $selesai = 0;
        $proses = 0;
        $ditunda = 0;

        foreach ($items as $item) {
            if ($item['status_item'] === 'selesai') $selesai++;
            elseif ($item['status_item'] === 'proses') $proses++;
            elseif ($item['status_item'] === 'ditunda') $ditunda++;
        }

        $newStatus = 'pending';
        if ($selesai === $total) {
            $newStatus = 'selesai';
        } elseif ($selesai > 0 || $proses > 0) {
            $newStatus = 'proses';
        } elseif ($ditunda > 0) {
            $newStatus = 'ditunda';
        }

        $this->update($penugasanId, ['status' => $newStatus]);
    }

    /**
     * Mengambil statistik status tugas
     */
    public function getStatusStats($filterRole = null, $filterTanggal = null)
    {
        $builder = $this->builder();
        
        if ($filterRole) {
            $builder->where('penerima_role', $filterRole);
        }
        if ($filterTanggal) {
            $builder->where('tanggal_tugas', $filterTanggal);
        }

        $all = $builder->get()->getResultArray();
        
        $stats = [
            'total'   => count($all),
            'pending' => 0,
            'proses'  => 0,
            'selesai' => 0,
            'ditunda' => 0,
        ];

        foreach ($all as $item) {
            if (isset($stats[$item['status']])) {
                $stats[$item['status']]++;
            }
        }

        return $stats;
    }
}
