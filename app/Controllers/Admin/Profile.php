<?php
// C:\xampp\htdocs\intranet_cdw\app\Controllers\Admin\Profile.php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KaryawanModel;

class Profile extends BaseController
{
    protected $userModel;
    protected $karyawanModel;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->karyawanModel = new KaryawanModel();
        $this->validation = \Config\Services::validation();
        
      
    }

    /**
     * Display profile page
     */
    public function index()
    {
        $userData = session()->get('user');
        if (!$userData) {
            return redirect()->to('admin/dashboard')->with('error', 'Data user tidak ditemukan.');
        }

        // Get user data with karyawan info
        $user = $this->userModel->getUserWithKaryawan($userData['id']);
        
        if (!$user) {
            return redirect()->to('admin/dashboard')->with('error', 'Data user tidak ditemukan.');
        }

        // Get karyawan data if exists
        $karyawan = null;
        if ($user['karyawan_id']) {
            $karyawan = $this->karyawanModel->getKaryawanById($user['karyawan_id']);
        }

        $data = [
            'title' => 'Profil Saya',
            'subtitle' => 'Kelola informasi profil dan akun Anda',
            'user' => session()->get('user'),
            'active' => 'profile',
            'profile_user' => $user, // rename to avoid conflict with session user
            'karyawan' => $karyawan,
            'validation' => $this->validation,
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Profil', 'url' => base_url('admin/profile')]
            ]
        ];

        // Add flash messages to data
        if (session()->has('success')) {
            $data['success'] = session()->get('success');
        }
        if (session()->has('error')) {
            $data['error'] = session()->get('error');
        }
        if (session()->has('errors')) {
            $data['validation_errors'] = session()->get('errors');
        }

        return view('admin/profile/index', $data);
    }

    /**
     * Update profile information
     */
    public function update()
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Method tidak diizinkan.');
        }

        $userData = session()->get('user');
        $userId = $userData['id'];

        // Validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,$userId]",
            'phone' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $address = $this->request->getPost('address');

        // Update user data
        $userUpdateData = [
            'name' => $name,
            'email' => $email
        ];

        // Also update karyawan data if exists
        $user = $this->userModel->find($userId);
        if ($user && $user['karyawan_id']) {
            $karyawanUpdateData = [
                'telepon' => $phone,
                'alamat' => $address
            ];
            
            // Update additional karyawan fields if provided
            if ($this->request->getPost('tempat_lahir')) {
                $karyawanUpdateData['tempat_lahir'] = $this->request->getPost('tempat_lahir');
            }
            if ($this->request->getPost('tanggal_lahir')) {
                $karyawanUpdateData['tanggal_lahir'] = $this->request->getPost('tanggal_lahir');
            }
            if ($this->request->getPost('agama')) {
                $karyawanUpdateData['agama'] = $this->request->getPost('agama');
            }
            if ($this->request->getPost('status_pernikahan')) {
                $karyawanUpdateData['status_pernikahan'] = $this->request->getPost('status_pernikahan');
            }

            $this->karyawanModel->updateKaryawan($user['karyawan_id'], $karyawanUpdateData);
        }

        if ($this->userModel->update($userId, $userUpdateData)) {
            // Update session
            session()->set([
                'name' => $name,
                'email' => $email
            ]);

            return redirect()->to('admin/profile')->with('success', 'Profil berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui profil.');
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Method tidak diizinkan.');
        }

        $userData = session()->get('user');
        $userId = $userData['id'];

        // Validation rules
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]|matches[confirm_password]',
            'confirm_password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get current user
        $user = $this->userModel->find($userId);
        
        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password saat ini salah.');
        }

        // Update password
        $newPassword = $this->request->getPost('new_password');
        $updateData = [
            'password' => $newPassword, // akan dihash otomatis di model
            'password_changed_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->update($userId, $updateData)) {
            return redirect()->to('admin/profile')->with('success', 'Password berhasil diubah!');
        }

        return redirect()->back()->with('error', 'Gagal mengubah password.');
    }

    /**
     * Update profile photo
     */
    public function updatePhoto()
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Method tidak diizinkan.');
        }

        $userData = session()->get('user');
        $userId = $userData['id'];

        // Check if file uploaded
        $file = $this->request->getFile('photo');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File foto tidak valid.');
        }

        // Validate file
        $validationRules = [
            'photo' => [
                'rules' => 'uploaded[photo]|max_size[photo,2048]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png,image/gif]',
                'errors' => [
                    'uploaded' => 'Pilih foto terlebih dahulu.',
                    'max_size' => 'Ukuran foto maksimal 2MB.',
                    'is_image' => 'File harus berupa gambar.',
                    'mime_in' => 'Format file harus JPG, JPEG, PNG, atau GIF.'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->with('error', $this->validator->getError('photo'));
        }

        // Get user data
        $user = $this->userModel->find($userId);
        
        // Delete old photo if exists
        $oldPhotoPath = null;
        if ($user['karyawan_id']) {
            $karyawan = $this->karyawanModel->getKaryawanById($user['karyawan_id']);
            if ($karyawan && !empty($karyawan['foto'])) {
                $oldPhotoPath = WRITEPATH . '../public/uploads/foto/' . $karyawan['foto'];
            }
        }

        // Generate unique filename
        $newName = $file->getRandomName();
        
        // Move file to uploads directory
        if ($file->move(WRITEPATH . '../public/uploads/foto/', $newName)) {
            // Update karyawan photo if karyawan exists
            if ($user['karyawan_id']) {
                $this->karyawanModel->updateFoto($user['karyawan_id'], $newName);
            }

            // Delete old photo
            if ($oldPhotoPath && file_exists($oldPhotoPath)) {
                @unlink($oldPhotoPath);
            }

            return redirect()->to('admin/profile')->with('success', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Gagal mengupload foto.');
    }

    /**
     * Show activity log
     */
    public function activityLog()
    {
        $data = [
            'title' => 'Log Aktivitas',
            'subtitle' => 'Riwayat aktivitas akun Anda',
            'user' => session()->get('user'),
            'active' => 'profile',
            'subactive' => 'activity',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Profil', 'url' => base_url('admin/profile')],
                ['name' => 'Log Aktivitas', 'url' => base_url('admin/profile/activity')]
            ]
        ];

        // Get recent activities (this would come from an ActivityModel)
        // For now, we'll use dummy data
        $activities = [];

        $data['activities'] = $activities;

        return view('admin/profile/activity', $data);
    }

    /**
     * Download CV if exists
     */
    public function downloadCV()
    {
        $userData = session()->get('user');
        $user = $this->userModel->getUserWithKaryawan($userData['id']);

        if (!$user || !$user['karyawan_id']) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $karyawan = $this->karyawanModel->getKaryawanById($user['karyawan_id']);
        
        if (!$karyawan || empty($karyawan['cv_path'])) {
            return redirect()->back()->with('error', 'CV tidak ditemukan.');
        }

        $filePath = WRITEPATH . '../public/uploads/cv/' . $karyawan['cv_path'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File CV tidak ditemukan.');
        }

        return $this->response->download($filePath, null);
    }
}