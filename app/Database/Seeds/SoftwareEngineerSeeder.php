<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SoftwareEngineerSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Cek jika ERP CDW sudah terdaftar
        $existing = $db->table('se_systems')->where('kode_sistem', 'ERP-CDW')->get()->getRowArray();
        
        if (!$existing) {
            $now = date('Y-m-d H:i:s');
            $systemId = $db->table('se_systems')->insert([
                'nama_sistem'     => 'CDW Mini ERP (Intranet CDW)',
                'kode_sistem'     => 'ERP-CDW',
                'jenis'           => 'internal',
                'tech_stack'      => 'PHP 8.2, CodeIgniter 4, MySQL, Bootstrap 5',
                'status'          => 'aktif',
                'link_production' => base_url(),
                'link_repository' => 'https://github.com/cdw-engineering/intranet_cdw',
                'deskripsi'       => 'Sistem Informasi Manajemen ERP Internal PT. CDW Engineering',
                'pic_internal'     => 'Software Engineer',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $systemId = $db->insertID();

            // Seed Hosting & Domain record
            $db->table('se_hosting_domain')->insert([
                'system_id'             => $systemId,
                'nama_provider_hosting' => 'Niagahoster / Cloud VPS CDW',
                'nama_domain'           => 'intranet.cdw.co.id',
                'tgl_expired_hosting'   => date('Y-m-d', strtotime('+1 year')),
                'tgl_expired_domain'    => date('Y-m-d', strtotime('+1 year')),
                'tgl_expired_ssl'       => date('Y-m-d', strtotime('+90 days')),
                'paket_hosting'         => 'Cloud VPS Linux (4 vCPU, 8GB RAM)',
                'biaya_per_tahun'       => 4500000.00,
                'catatan'               => 'Main internal ERP server hosting',
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);

            // Seed Credential record
            $db->table('se_credentials')->insert([
                'system_id'                    => $systemId,
                'tipe_akses'                   => 'cPanel / Server Admin',
                'username_akses'               => 'admin_cdw_erp',
                'encrypted_password'           => base64_encode('SuperAdminCDW2026!'), // Encrypted representation
                'admin_pic'                    => 'Lead Software Engineer',
                'url_login'                    => base_url('login'),
                'tgl_terakhir_ganti_password' => date('Y-m-d'),
                'catatan_keamanan'             => 'Akses sensitif root ERP CDW. Ganti password setiap 90 hari.',
                'created_at'                    => $now,
                'updated_at'                    => $now,
            ]);

            // Seed Initial Deployment
            $db->table('se_deployments')->insert([
                'system_id'      => $systemId,
                'versi'          => 'v1.0.0-PROD',
                'tanggal_deploy' => $now,
                'perubahan'      => 'Initial launch ERP CDW Intranet with 8 Roles modules',
                'deployed_by'    => 'Software Engineer',
                'environment'    => 'production',
                'status_deploy'  => 'sukses',
                'catatan'        => 'First stable deployment.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            // Seed Initial Technical Doc
            $db->table('se_technical_docs')->insert([
                'system_id'  => $systemId,
                'kategori'   => 'arsitektur',
                'judul'      => 'Arsitektur & Schema Database Intranet CDW',
                'versi_doc'  => '1.0',
                'content'    => "### Arsitektur Intranet CDW\n\nSistem dibangun menggunakan MVC Framework CodeIgniter 4 dengan arsitektur modular multi-role (Admin/HRD, Direktur, Accounting, Sales, Teknisi, Staff, dan Software Engineer).\n\nDatabase: MySQL InnoDB.",
                'updated_by' => 'Software Engineer',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
