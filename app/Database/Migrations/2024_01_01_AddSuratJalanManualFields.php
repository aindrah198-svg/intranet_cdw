<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuratJalanManualFields extends Migration
{
    public function up()
    {
        // ===== UPDATE TABLE surat_jalan =====
        $fields = [
            // Field format nomor
            'kode_format' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'DN-CDW',
                'null' => true,
                'after' => 'nomor_surat_jalan'
            ],
            'bulan_format' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => 'kode_format'
            ],
            'tahun_format' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
                'null' => true,
                'after' => 'bulan_format'
            ],
            
            // Field penerima detail
            'penerima_perusahaan' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'after' => 'penerima'
            ],
            'penerima_up' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'penerima_perusahaan'
            ],
            'penerima_telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'penerima_up'
            ],
            'lokasi_proyek' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'alamat_pengiriman'
            ],
            
            // Field pengiriman
            'sopir_telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'sopir'
            ],
            
            // Field penandatanganan
            'disiapkan_oleh' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'no_kendaraan'
            ],
            'disiapkan_telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'disiapkan_oleh'
            ],
            'disiapkan_jabatan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'disiapkan_telepon'
            ],
            'dikirim_oleh' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'disiapkan_jabatan'
            ],
            'dikirim_telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'dikirim_oleh'
            ],
            'diterima_oleh' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'dikirim_telepon'
            ],
            'diterima_telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'diterima_oleh'
            ],
            'diterima_perusahaan' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'after' => 'diterima_telepon'
            ],
            
            // Field status terima
            'status_terima' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'diterima', 'ditolak'],
                'default' => 'pending',
                'after' => 'status'
            ],
            'tanggal_terima' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status_terima'
            ],
            
            // Field catatan
            'catatan_barang' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'keterangan'
            ]
        ];
        
        $this->forge->addColumn('surat_jalan', $fields);
        
        // ===== UPDATE TABLE surat_jalan_item =====
        $itemFields = [
            'no_urut' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'after' => 'id'
            ],
            'berat' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'after' => 'satuan'
            ],
            'satuan_berat' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'kg',
                'after' => 'berat'
            ]
        ];
        
        $this->forge->addColumn('surat_jalan_item', $itemFields);
        
        // ===== CREATE TABLE perusahaan =====
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
        
        // ===== INSERT DATA DEFAULT CDW =====
        $this->db->table('perusahaan')->insert([
            'kode_perusahaan' => 'CDW',
            'nama_perusahaan' => 'PT. Cipta Duta Wacana',
            'alamat' => 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226',
            'website' => 'www.cdw-engineering.com',
            'logo_path' => 'assets/img/logo/logo_cdw.jpg'
        ]);
        
        // ===== UPDATE ENUM STATUS surat_jalan =====
        $this->db->query("ALTER TABLE surat_jalan MODIFY COLUMN status ENUM('draft','diproses','dikirim','diterima','ditolak','dibatalkan') DEFAULT 'draft'");
    }
    
    public function down()
    {
        // Drop kolom yang ditambahkan
        $this->forge->dropColumn('surat_jalan', [
            'kode_format', 'bulan_format', 'tahun_format',
            'penerima_perusahaan', 'penerima_up', 'penerima_telepon',
            'lokasi_proyek', 'sopir_telepon',
            'disiapkan_oleh', 'disiapkan_telepon', 'disiapkan_jabatan',
            'dikirim_oleh', 'dikirim_telepon',
            'diterima_oleh', 'diterima_telepon', 'diterima_perusahaan',
            'status_terima', 'tanggal_terima', 'catatan_barang'
        ]);
        
        $this->forge->dropColumn('surat_jalan_item', [
            'no_urut', 'berat', 'satuan_berat'
        ]);
        
        $this->forge->dropTable('perusahaan', true);
    }
}