<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKeluhanKaryawanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'karyawan_id'   => ['type' => 'INT', 'unsigned' => true],
            'tanggal'       => ['type' => 'DATE'],
            'kategori'      => ['type' => 'ENUM', 'constraint' => ['Lingkungan Kerja','Hubungan Rekan Kerja','Atasan/Manajemen','Gaji & Tunjangan','Fasilitas','Beban Kerja','Lainnya']],
            'judul'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'     => ['type' => 'TEXT'],
            'status'        => ['type' => 'ENUM', 'constraint' => ['baru','diproses','selesai','ditolak'], 'default' => 'baru'],
            'tanggapan'     => ['type' => 'TEXT', 'null' => true],
            'ditanggapi_oleh' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'tanggal_tanggapan' => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('karyawan_id');
        $this->forge->addKey('status');
        $this->forge->createTable('keluhan_karyawan', true);
    }

    public function down()
    {
        $this->forge->dropTable('keluhan_karyawan', true);
    }
}
