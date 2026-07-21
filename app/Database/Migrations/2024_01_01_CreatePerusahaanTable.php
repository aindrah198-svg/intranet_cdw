<?php
// File: app/Database/Migrations/2024_01_01_CreatePerusahaanTable.php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerusahaanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'kode_perusahaan' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true
            ],
            'nama_perusahaan' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'logo_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Path relatif dari public/assets/img/logo/'
            ],
            'logo_full_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Path lengkap untuk referensi'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('kode_perusahaan');
        $this->forge->createTable('perusahaan');
        
        // Insert default data CDW
        $this->db->table('perusahaan')->insert([
            'kode_perusahaan' => 'CDW',
            'nama_perusahaan' => 'PT. Cipta Duta Wacana',
            'alamat' => 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226',
            'website' => 'www.cdw-engineering.com',
            'logo_path' => 'assets/img/logo/logo_cdw.jpg'
        ]);
    }
    
    public function down()
    {
        $this->forge->dropTable('perusahaan');
    }
}