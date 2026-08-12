<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuratKaryawanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nomor_surat'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jenis_surat'   => ['type' => 'ENUM', 'constraint' => ['Kontrak Kerja','Surat Peringatan (SP1)','Surat Peringatan (SP2)','Surat Peringatan (SP3)','Surat Keterangan Kerja','Surat Tugas','Surat Pernyataan','Lainnya']],
            'karyawan_id'   => ['type' => 'INT', 'unsigned' => true],
            'tanggal_surat' => ['type' => 'DATE'],
            'perihal'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'isi_surat'     => ['type' => 'TEXT', 'null' => true],
            'catatan'       => ['type' => 'TEXT', 'null' => true],
            'dibuat_oleh'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'        => ['type' => 'ENUM', 'constraint' => ['draft','diterbitkan','dibatalkan'], 'default' => 'draft'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('karyawan_id');
        $this->forge->addKey('jenis_surat');
        $this->forge->createTable('surat_karyawan', true);
    }

    public function down()
    {
        $this->forge->dropTable('surat_karyawan', true);
    }
}
