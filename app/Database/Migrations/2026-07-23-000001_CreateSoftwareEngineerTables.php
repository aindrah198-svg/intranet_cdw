<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoftwareEngineerTables extends Migration
{
    public function up()
    {
        // 1. se_systems
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_sistem'     => ['type' => 'VARCHAR', 'constraint' => 150],
            'kode_sistem'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jenis'           => ['type' => 'ENUM', 'constraint' => ['internal', 'eksternal', 'klien'], 'default' => 'internal'],
            'tech_stack'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['aktif', 'maintenance', 'nonaktif'], 'default' => 'aktif'],
            'link_production' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'link_repository' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'deskripsi'       => ['type' => 'TEXT', 'null' => true],
            'client_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'pic_internal'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('se_systems', true);

        // 2. se_hosting_domain
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'             => ['type' => 'INT', 'unsigned' => true],
            'nama_provider_hosting' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_domain'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'tgl_expired_hosting'   => ['type' => 'DATE', 'null' => true],
            'tgl_expired_domain'    => ['type' => 'DATE', 'null' => true],
            'tgl_expired_ssl'       => ['type' => 'DATE', 'null' => true],
            'paket_hosting'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'biaya_per_tahun'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true, 'default' => 0.00],
            'catatan'               => ['type' => 'TEXT', 'null' => true],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('system_id');
        $this->forge->createTable('se_hosting_domain', true);

        // 3. se_credentials
        $this->forge->addField([
            'id'                           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'                    => ['type' => 'INT', 'unsigned' => true],
            'tipe_akses'                   => ['type' => 'VARCHAR', 'constraint' => 50], // e.g. cPanel, Hosting, DB, SSH, Web Admin
            'username_akses'               => ['type' => 'VARCHAR', 'constraint' => 150],
            'encrypted_password'           => ['type' => 'TEXT', 'null' => true],
            'admin_pic'                    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'url_login'                    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tgl_terakhir_ganti_password' => ['type' => 'DATE', 'null' => true],
            'catatan_keamanan'             => ['type' => 'TEXT', 'null' => true],
            'created_at'                   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'                   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('system_id');
        $this->forge->createTable('se_credentials', true);

        // 4. se_credential_access_logs
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'credential_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'action'        => ['type' => 'VARCHAR', 'constraint' => 50], // VIEW, COPY, DECRYPT, UPDATE
            'ip_address'    => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('credential_id');
        $this->forge->createTable('se_credential_access_logs', true);

        // 5. se_deployments
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'      => ['type' => 'INT', 'unsigned' => true],
            'versi'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'tanggal_deploy' => ['type' => 'DATETIME'],
            'perubahan'      => ['type' => 'TEXT'],
            'deployed_by'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'environment'    => ['type' => 'ENUM', 'constraint' => ['production', 'staging', 'testing'], 'default' => 'production'],
            'status_deploy'  => ['type' => 'ENUM', 'constraint' => ['sukses', 'gagal', 'rollback'], 'default' => 'sukses'],
            'catatan'        => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('system_id');
        $this->forge->createTable('se_deployments', true);

        // 6. se_bugs
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'        => ['type' => 'INT', 'unsigned' => true],
            'judul_bug'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'deskripsi'        => ['type' => 'TEXT', 'null' => true],
            'severity'         => ['type' => 'ENUM', 'constraint' => ['low', 'medium', 'high', 'critical'], 'default' => 'medium'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['open', 'in_progress', 'fixed', 'verified', 'closed'], 'default' => 'open'],
            'reporter'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'assigned_to'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tgl_ditemukan'    => ['type' => 'DATE', 'null' => true],
            'tgl_diselesaikan' => ['type' => 'DATE', 'null' => true],
            'solusi'           => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('system_id');
        $this->forge->createTable('se_bugs', true);

        // 7. se_maintenance_schedule
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'         => ['type' => 'INT', 'unsigned' => true],
            'judul_maintenance' => ['type' => 'VARCHAR', 'constraint' => 200],
            'jenis_maintenance' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tgl_rencana'       => ['type' => 'DATETIME'],
            'estimasi_downtime' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status'            => ['type' => 'ENUM', 'constraint' => ['terjadwal', 'proses', 'selesai', 'dibatalkan'], 'default' => 'terjadwal'],
            'penanggung_jawab'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'catatan'           => ['type' => 'TEXT', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('system_id');
        $this->forge->createTable('se_maintenance_schedule', true);

        // 8. se_backup_logs
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'      => ['type' => 'INT', 'unsigned' => true],
            'jenis_backup'   => ['type' => 'ENUM', 'constraint' => ['database', 'files', 'full_system'], 'default' => 'database'],
            'tanggal_backup' => ['type' => 'DATETIME'],
            'ukuran_mb'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'lokasi_simpan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status_backup'  => ['type' => 'ENUM', 'constraint' => ['sukses', 'gagal'], 'default' => 'sukses'],
            'petugas'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'catatan'        => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('system_id');
        $this->forge->createTable('se_backup_logs', true);

        // 9. se_technical_docs
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'  => ['type' => 'INT', 'unsigned' => true],
            'kategori'   => ['type' => 'ENUM', 'constraint' => ['technical_doc', 'arsitektur', 'db_schema', 'api_doc'], 'default' => 'technical_doc'],
            'judul'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'versi_doc'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'default' => '1.0'],
            'content'    => ['type' => 'LONGTEXT', 'null' => true],
            'link_file'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('system_id');
        $this->forge->createTable('se_technical_docs', true);

        // 10. se_tasks
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'system_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'proyek_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'task_name'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'deskripsi'        => ['type' => 'TEXT', 'null' => true],
            'milestone_sprint' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'priority'         => ['type' => 'ENUM', 'constraint' => ['low', 'medium', 'high', 'urgent'], 'default' => 'medium'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['todo', 'in_progress', 'review', 'done'], 'default' => 'todo'],
            'due_date'         => ['type' => 'DATE', 'null' => true],
            'assigned_to'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('se_tasks', true);
    }

    public function down()
    {
        $this->forge->dropTable('se_tasks', true);
        $this->forge->dropTable('se_technical_docs', true);
        $this->forge->dropTable('se_backup_logs', true);
        $this->forge->dropTable('se_maintenance_schedule', true);
        $this->forge->dropTable('se_bugs', true);
        $this->forge->dropTable('se_deployments', true);
        $this->forge->dropTable('se_credential_access_logs', true);
        $this->forge->dropTable('se_credentials', true);
        $this->forge->dropTable('se_hosting_domain', true);
        $this->forge->dropTable('se_systems', true);
    }
}
