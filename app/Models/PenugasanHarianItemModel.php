<?php
namespace App\Models;

use CodeIgniter\Model;

class PenugasanHarianItemModel extends Model
{
    protected $table            = 'penugasan_harian_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'penugasan_id',
        'judul_item',
        'deskripsi_item',
        'status_item',
        'alasan_ditunda',
        'catatan_penyelesaian',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Pastikan tabel `penugasan_harian_items` dibuat jika belum ada
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
                'penugasan_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'judul_item' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'deskripsi_item' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'status_item' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'proses', 'selesai', 'ditunda'],
                    'default'    => 'pending',
                ],
                'alasan_ditunda' => [
                    'type' => 'TEXT',
                    'null' => true,
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
            ]);
            $forge->addKey('id', true);
            $forge->addForeignKey('penugasan_id', 'penugasan_harian', 'id', 'CASCADE', 'CASCADE');
            $forge->createTable($this->table, true);
        }
    }
}
