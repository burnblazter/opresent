<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Activation extends BaseController
{
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }

    public function activateAccount()
    {
        $token = $this->request->getGet('token');

        if (empty($token)) {
            session()->setFlashdata('gagal', 'Token aktivasi tidak valid');
            return redirect()->to('/login');
        }

        // Cek token di database - gunakan asArray() untuk konsistensi
        $user = $this->usersModel->asArray()->where('activate_hash', $token)->first();

        if (!$user) {
            session()->setFlashdata('gagal', 'Token aktivasi tidak valid atau sudah digunakan');
            return redirect()->to('/login');
        }

        // Cek apakah sudah punya password (sudah pernah aktivasi)
        if ($user['password_hash'] !== null) {
            session()->setFlashdata('info', 'Akun sudah aktif, silakan login');
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Aktivasi Akun',
            'token' => $token,
            'email' => $user['email'],
            'username' => $user['username'],
        ];

        return view('auth/activate', $data);
    }

    public function attemptActivate()
    {
        $rules = [
            'token' => 'required',
            'password' => [
                'rules' => 'required|min_length[8]|max_length[255]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 8 karakter',
                    'max_length' => 'Password maksimal 255 karakter',
                ]
            ],
            'pass_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi',
                    'matches' => 'Konfirmasi password tidak sama',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Cek token - gunakan asArray()
        $user = $this->usersModel->asArray()->where('activate_hash', $token)->first();

        if (!$user) {
            session()->setFlashdata('gagal', 'Token aktivasi tidak valid');
            return redirect()->to('/login');
        }

        // PENTING: Gunakan method hashPassword dari UsersModel!
        $password_hash = $this->usersModel->hashPassword($password);
        
        // Update user: set password, aktifkan akun, hapus token
        // Gunakan Query Builder langsung untuk bypass callbacks
        $this->usersModel->db->table('users')
            ->where('id', $user['id'])
            ->update([
                'password_hash' => $password_hash,
                'active' => 1,
                'activate_hash' => null,
            ]);

        session()->setFlashdata('berhasil', 'Akun berhasil diaktifkan! Silakan login dengan password yang Anda buat');
        return redirect()->to('/login');
    }
}