<?php
// \app\Controllers\UserProfile.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\PegawaiModel;
use App\Models\EmailTokenModel;
use App\ThirdParty\MythAuth\Controllers\AuthController;
use Config\Email;

class UserProfile extends BaseController
{
    protected $usersModel;
    protected $pegawaiModel;
    protected $emailTokenModel;
    protected $foto_default;
    protected $auth;
    protected $email;
    protected $myEmail;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->emailTokenModel = new EmailTokenModel();
        $this->foto_default = 'default.jpg';
        $this->auth = new AuthController();
        $this->email = \Config\Services::email();
        $this->myEmail = new Email();
    }

    public function index(): string
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());

        $data = [
            'title' => 'Profile',
            'user_profile' => $user_profile,
        ];

        return view('profile/index', $data);
    }

    public function editProfile()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());

        $data = [
            'title' => 'Profile',
            'user_profile' => $user_profile,
        ];

        return view('profile/edit-profile', $data);
    }

    public function hapusFoto()
    {
        $pegawai_db = $this->pegawaiModel->getPegawai(user()->username)['pegawai'];
        $foto_db = $pegawai_db->foto;

        if ($foto_db !== $this->foto_default) {
            $this->pegawaiModel->save([
                'id' => $pegawai_db->id,
                'foto' => $this->foto_default,
            ]);

            unlink('assets/img/user_profile/' . $foto_db);
        }

        session()->setFlashdata('berhasil', 'Foto berhasil dihapus');
        return redirect()->to(base_url('/profile/edit'));
    }

    public function update()
    {
        $data_db = $this->pegawaiModel->getPegawai(user()->username)['pegawai'];
        $username_edit = $this->request->getVar('username');

        if ($username_edit !== $data_db->username) {
            $rules_username = 'required|is_unique[users.username]';
        } else {
            $rules_username = 'required';
        }

        $rules = [
            'foto' => [
                'rules' => 'max_size[foto,2048]|mime_in[foto,image/png,image/jpeg,image/jpg]|ext_in[foto,png,jpg,jpeg]|is_image[foto]',
                'errors' => [
                    'max_size' => 'upload foto dengan ukuran maksimal 2MB',
                    'mime_in' => 'upload foto dengan ekstensi png, jpeg, atau jpg',
                    'ext_in' => 'upload foto dengan ekstensi png, jpeg, atau jpg',
                    'is_image' => 'upload foto dengan ekstensi png, jpeg, atau jpg',
                ]
            ],
            'nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama wajib diisi.',
                ],
            ],
            'username' => [
                'rules' => $rules_username,
                'errors' => [
                    'required' => 'Username wajib diisi',
                    'is_unique' => 'Username sudah terdaftar.'
                ]
            ],
            'jenis_kelamin' => [
                'rules' => 'required|in_list[Perempuan,Laki-laki]',
                'errors' => [
                    'required' => 'Jenis kelamin wajib diisi.',
                    'in_list' => 'Mohon pilih opsi jenis kelamin yang tersedia.'
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat wajib diisi.',
                ],
            ],
            'no_handphone' => [
                'rules' => 'required|regex_match[/^(?:\+62|62|0)(?:\d{8,15})$/]',
                'errors' => [
                    'required' => 'Nomor handphone wajib diisi.',
                    'regex_match' => 'Mohon isi nomor telepon dengan 8-15 digit angka.',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('profile/edit'))->withInput();
        }

        $file_foto = $this->request->getFile('foto');

        if ($file_foto->getError() == 4) {
            $nama_foto = $this->request->getPost('foto_lama');
        } else {
            $nama_foto = 'FotoProfile-' . user()->username . '-' . date('Y-m-d-His') . '.jpg';
            $file_foto->move(FCPATH . 'assets/img/user_profile/', $nama_foto);

            if ($this->request->getPost('foto_lama') !== $this->foto_default) {
                unlink('assets/img/user_profile/' . $this->request->getPost('foto_lama'));
            }
        }

        $this->pegawaiModel->save([
            'id' => $this->request->getPost('id'),
            'nama' => $this->request->getPost('nama'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'no_handphone' => $this->request->getPost('no_handphone'),
            'foto' => $nama_foto,
        ]);

        $this->usersModel->save([
            'id' => user_id(),
            'username' => $this->request->getPost('username'),
        ]);

        session()->setFlashdata('berhasil', 'Profil berhasil diupdate');
        return redirect()->to(base_url('/profile'));
    }

    public function passwordToken()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());
        $email = $user_profile->email;
        return $this->auth->attemptForgot($email);
    }

    public function emailToken()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());
        $email = $user_profile->email;

        $isEmailOnDB = $this->emailTokenModel->where('email', $email)->first() ? true : false;

        if ($isEmailOnDB) {
            $db = $this->emailTokenModel->where('email', $email)->first();
            $id = $db['id'];
            $this->emailTokenModel->delete($id);
        }

        $token = bin2hex(random_bytes(16));
        $email_token = [
            'email' => $email,
            'token' => $token,
            'created_time' => time(),
        ];

        $this->emailTokenModel->save($email_token);

        $from = $this->myEmail->fromEmail;
        $to = $email;
        $subject = 'Change Email Instruction';
        $variables = array();
        $variables['token'] = $token;

        // Send Instruction to user's current email
        if ($this->_sendEmail($from, $to, $subject, $variables)) {
            // Redirect ke feedback page dengan email
            return redirect()->to(base_url('change-email-feedback?email=' . urlencode($email)));
        } else {
            return redirect()->to('/profile')
                ->with('gagal', 'Email gagal dikirim. Hubungi admin.');
        }
    }

    public function changeEmail()
    {
        $token = $this->request->getGet('token') ? $this->request->getGet('token') : '';
        $email = $this->request->getGet('email') ? $this->request->getGet('email') : '';
        
        // Jika token dari URL, langsung validasi
        if (!empty($token) && !empty($email)) {
            $data = $this->emailTokenModel->where('email', $email)->first();
            
            if (!$data) {
                return redirect()->to('/profile')
                    ->with('gagal', 'Request perubahan email tidak ditemukan. Mulai ulang dari halaman profil.');
            }
            
            if ($data['token'] !== $token) {
                return redirect()->to('/profile')
                    ->with('gagal', 'Token tidak valid. Cek email Anda kembali.');
            }
            
            if (time() - $data['created_time'] > (5 * 60)) {
                $this->emailTokenModel->delete($data['id']);
                return redirect()->to('/profile')
                    ->with('gagal', 'Token sudah kadaluarsa. Minta ulang dari halaman profil.');
            }
            
            // Token valid, simpan ke session dan tampilkan form
            session()->set('email_change_token', $token);
            session()->set('email_change_old', $email);
        }
        
        // Jika session tidak ada, redirect ke profil
        if (!session()->get('email_change_token')) {
            return redirect()->to('/profile');
        }
        
        $data = [
            'token' => session()->get('email_change_token'),
            'email' => session()->get('email_change_old'),
        ];
        
        return view('auth/change-email', $data);
    }

    public function changeEmailFeedback()
    {
        $emailSent = $this->request->getGet('email');
        
        if (!$emailSent || !filter_var($emailSent, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/profile')
                ->with('gagal', 'Email tidak valid');
        }
        
        return view('auth/change-email-feedback', [
            'email' => $emailSent,
        ]);
    }

    public function attemptChangeEmail()
    {
        $token = session()->get('email_change_token');
        $oldEmail = session()->get('email_change_old');
        
        if (empty($token) || empty($oldEmail)) {
            return redirect()->to('/profile')
                ->with('gagal', 'Sesi perubahan email telah berakhir. Mulai ulang dari halaman profil.');
        }
        
        $rules = [
            'newEmail' => [
                'rules' => 'required|is_unique[users.email]|valid_email',
                'errors' => [
                    'required' => 'Email baru wajib diisi.',
                    'is_unique' => 'Email sudah terdaftar.',
                    'valid_email' => 'Format email tidak valid.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $newEmail = $this->request->getPost('newEmail');
        
        // Validasi token satu kali lagi
        $data = $this->emailTokenModel->where('email', $oldEmail)
            ->where('token', $token)->first();
        
        if (!$data) {
            session()->remove(['email_change_token', 'email_change_old']);
            return redirect()->to('/profile')
                ->with('gagal', 'Token tidak valid. Minta ulang dari halaman profil.');
        }
        
        if (time() - $data['created_time'] > (5 * 60)) {
            $this->emailTokenModel->delete($data['id']);
            session()->remove(['email_change_token', 'email_change_old']);
            return redirect()->to('/profile')
                ->with('gagal', 'Token sudah kadaluarsa. Minta ulang dari halaman profil.');
        }

        // Buat activation hash untuk email baru
        $activate_hash = bin2hex(random_bytes(16));

        // Update database dengan email baru
        $this->usersModel->save([
            'id' => user_id(),
            'email' => $newEmail,
            'active' => 0,
            'activate_hash' => $activate_hash,
        ]);

        // Hapus email token
        $this->emailTokenModel->delete($data['id']);
        session()->remove(['email_change_token', 'email_change_old']);

        // Kirim activation email ke email baru
        $this->auth->resendActivateAccount($newEmail);
        $this->auth->logout();

        return redirect()->to('/')
            ->with('message', 'Email berhasil diubah! Silakan aktivasi email baru Anda melalui link yang dikirim ke ' . $newEmail);
    }

    private function _sendEmail($from, $to, $subject, $variables)
    {
        $template = view('emails/change-email', $variables);

        $this->email->setFrom($from, $this->myEmail->fromName);
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($template);

        if ($this->email->send()) {
            return true;
        } else {
            return false;
        }
    }
}
