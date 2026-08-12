<?php

namespace App\Models;

use CodeIgniter\Model;

class KeluhanKaryawanModel extends Model
{
    protected $table      = 'keluhan_karyawan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'karyawan_id', 'tanggal', 'kategori', 'judul', 'deskripsi',
        'status', 'tanggapan', 'ditanggapi_oleh', 'tanggal_tanggapan'
    ];

    public $kategoriList = [
        'Lingkungan Kerja',
        'Hubungan Rekan Kerja',
        'Atasan/Manajemen',
        'Gaji & Tunjangan',
        'Fasilitas',
        'Beban Kerja',
        'Lainnya',
    ];

    public function getFilteredKeluhan($status = null, $kategori = null, $search = null)
    {
        $builder = $this->select('keluhan_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan, karyawan.foto, karyawan.email')
                        ->join('karyawan', 'karyawan.id = keluhan_karyawan.karyawan_id', 'left');

        if (!empty($status)) {
            $builder->where('keluhan_karyawan.status', $status);
        }

        if (!empty($kategori)) {
            $builder->where('keluhan_karyawan.kategori', $kategori);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('karyawan.nama_lengkap', $search)
                    ->orLike('karyawan.nik', $search)
                    ->orLike('keluhan_karyawan.judul', $search)
                    ->orLike('keluhan_karyawan.deskripsi', $search)
                    ->groupEnd();
        }

        return $builder->orderBy('keluhan_karyawan.tanggal', 'DESC')
                       ->orderBy('keluhan_karyawan.id', 'DESC')
                       ->findAll();
    }

    public function getAllWithKaryawan()
    {
        return $this->getFilteredKeluhan();
    }

    public function getDetailWithKaryawan($id)
    {
        return $this->select('keluhan_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan, karyawan.foto, karyawan.email, karyawan.telepon, users.name as nama_penanggap, users.role as role_penanggap')
                    ->join('karyawan', 'karyawan.id = keluhan_karyawan.karyawan_id', 'left')
                    ->join('users', 'users.id = keluhan_karyawan.ditanggapi_oleh', 'left')
                    ->where('keluhan_karyawan.id', $id)
                    ->first();
    }

    public function getStatistik()
    {
        return [
            'total'     => $this->countAllResults(),
            'baru'      => (new static())->where('status', 'baru')->countAllResults(),
            'diproses'  => (new static())->where('status', 'diproses')->countAllResults(),
            'selesai'   => (new static())->where('status', 'selesai')->countAllResults(),
            'ditolak'   => (new static())->where('status', 'ditolak')->countAllResults(),
        ];
    }
}
