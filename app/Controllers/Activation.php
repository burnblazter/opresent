<?php
// \app\Controllers\Activation.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Controllers;

use App\Models\UsersModel;
use App\ThirdParty\MythAuth\Password;

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
            session()->setFlashdata('error', 'Token aktivasi tidak ditemukan.');
            return redirect()->to('/login');
        }

        $user = $this->usersModel->where('activate_hash', $token)->first();

        if (!$user) {
            session()->setFlashdata('error', 'Token aktivasi tidak valid atau sudah kadaluarsa.');
            return redirect()->to('/login');
        }

        // Check if account is already fully activated (both password_hash set AND active = 1)
        if (!empty($user->password_hash) && $user->active == 1) {
            session()->setFlashdata('message', 'Akun ini sudah aktif. Silakan login.');
            return redirect()->to('/login');
        }

        $data = [
            'title'    => 'Aktivasi Akun',
            'token'    => $token,
            'email'    => $user->email,
            'username' => $user->username,
        ];

        return view('auth/activate', $data);
    }

    public function attemptActivate()
    {
        $rules = [
            'token'    => 'required',
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[8]',
                'errors' => [
                    'required'   => '{field} wajib diisi',
                    'min_length' => '{field} minimal 8 karakter',
                ]
            ],
            'pass_confirm' => [
                'label'  => 'Konfirmasi Password',
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'matches'  => '{field} tidak sama dengan password',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token    = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $this->usersModel->where('activate_hash', $token)->first();

        if (!$user) {
            session()->setFlashdata('error', 'Token aktivasi tidak valid.');
            return redirect()->to('/login');
        }

        $hashPassword = Password::hash($password);

        $user->password_hash = $hashPassword;
        $user->activate_hash = null;
        $user->active        = 1;

        try {
            if ($this->usersModel->save($user)) {
                session()->setFlashdata('message', 'Sukses! Akun aktif. Silakan login.');
                return redirect()->to('/login');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->usersModel->errors());
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}