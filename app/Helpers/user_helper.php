<?php
// app/Helpers/user_helper.php

use App\Models\UserModel;
use App\Models\KaryawanModel;

if (!function_exists('autoConnectUserToKaryawan')) {
    /**
     * Otomatis hubungkan user dengan karyawan berdasarkan kriteria
     */
    function autoConnectUserToKaryawan($userId)
    {
        $userModel = new UserModel();
        $karyawanModel = new KaryawanModel();
        
        // Get user data
        $user = $userModel->find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Jika sudah punya karyawan_id, return true
        if (!empty($user['karyawan_id'])) {
            return true;
        }
        
        log_message('info', 'Trying to auto-connect user ' . $user['id'] . ' (' . $user['email'] . ') to karyawan');
        
        $karyawan = null;
        
        // Strategy 1: Cari berdasarkan email yang sama persis
        if (!empty($user['email'])) {
            $karyawan = $karyawanModel->where('email', $user['email'])->first();
            if ($karyawan) {
                log_message('info', 'Found karyawan by exact email: ' . $user['email']);
            }
        }
        
        // Strategy 2: Cari berdasarkan nama yang mirip
        if (!$karyawan && !empty($user['name'])) {
            // Hapus gelar dan spasi berlebih
            $cleanName = preg_replace('/\s+/', ' ', trim($user['name']));
            
            // Coba beberapa variasi pencarian
            $karyawan = $karyawanModel->like('nama_lengkap', $cleanName)->first();
            
            if (!$karyawan) {
                // Coba cari bagian dari nama
                $nameParts = explode(' ', $cleanName);
                if (count($nameParts) > 1) {
                    foreach ($nameParts as $part) {
                        if (strlen($part) > 2) { // Minimal 3 karakter
                            $karyawan = $karyawanModel->like('nama_lengkap', $part)->first();
                            if ($karyawan) break;
                        }
                    }
                }
            }
            
            if ($karyawan) {
                log_message('info', 'Found karyawan by name similarity: ' . $user['name']);
            }
        }
        
        // Strategy 3: Cari berdasarkan username (jika username mengandung NIK atau nama)
        if (!$karyawan && !empty($user['username'])) {
            // Coba cari apakah username mengandung NIK
            $karyawan = $karyawanModel->like('nik', $user['username'])->first();
            
            if (!$karyawan) {
                // Coba cari berdasarkan username sebagai nama
                $karyawan = $karyawanModel->like('nama_lengkap', $user['username'])->first();
            }
            
            if ($karyawan) {
                log_message('info', 'Found karyawan by username: ' . $user['username']);
            }
        }
        
        // Jika ditemukan karyawan, update user
        if ($karyawan) {
            $updateData = ['karyawan_id' => $karyawan['id']];
            
            // Update juga email user jika kosong tapi karyawan punya email
            if (empty($user['email']) && !empty($karyawan['email'])) {
                $updateData['email'] = $karyawan['email'];
            }
            
            // Update user
            $userModel->update($userId, $updateData);
            
            log_message('info', 'Auto-connected user ' . $userId . ' to karyawan ' . $karyawan['id'] . ' (' . $karyawan['nama_lengkap'] . ')');
            
            return $karyawan;
        }
        
        log_message('info', 'Could not auto-connect user ' . $userId . ' to any karyawan');
        return false;
    }
}

if (!function_exists('getUserKaryawanData')) {
    /**
     * Ambil data karyawan untuk user (dengan auto-connect jika perlu)
     */
    function getUserKaryawanData($userId)
    {
        $userModel = new UserModel();
        $karyawanModel = new KaryawanModel();
        
        $user = $userModel->find($userId);
        
        if (!$user) {
            return null;
        }
        
        // Jika user punya karyawan_id, ambil data karyawan
        if (!empty($user['karyawan_id'])) {
            return $karyawanModel->find($user['karyawan_id']);
        }
        
        // Coba auto-connect
        $karyawan = autoConnectUserToKaryawan($userId);
        
        if ($karyawan && is_array($karyawan)) {
            return $karyawan;
        }
        
        return null;
    }
}