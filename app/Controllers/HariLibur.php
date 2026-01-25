<?php

namespace App\Controllers;

use App\Models\HariLiburModel;
use App\Models\UsersModel; // 1. Load UsersModel

class HariLibur extends BaseController
{
    protected $hariLiburModel;
    protected $usersModel; // 2. Tambahkan properti

    public function __construct()
    {
        $this->hariLiburModel = new HariLiburModel();
        $this->usersModel = new UsersModel(); // 3. Inisialisasi
    }

    public function index()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id()); // 4. Ambil data user

        $data = [
            'title' => 'Daftar Hari Libur',
            'user_profile' => $user_profile, // 5. Kirim ke View
            'data_libur' => $this->hariLiburModel->orderBy('tanggal', 'DESC')->findAll()
        ];

        return view('hari_libur/index', $data);
    }

    public function tambah()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id()); // Ambil data user

        $data = [
            'title' => 'Tambah Hari Libur',
            'user_profile' => $user_profile, // Kirim ke View
            'validation' => \Config\Services::validation()
        ];
        return view('hari_libur/tambah', $data);
    }

    public function simpan()
    {
        // Validasi
        if (!$this->validate([
            'tanggal' => [
                'rules' => 'required|is_unique[hari_libur.tanggal]',
                'errors' => [
                    'required' => 'Tanggal harus diisi.',
                    'is_unique' => 'Tanggal ini sudah terdaftar sebagai hari libur.'
                ]
            ],
            'keterangan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Keterangan libur harus diisi.'
                ]
            ]
        ])) {
            return redirect()->to(base_url('admin/hari-libur/tambah'))->withInput();
        }

        $this->hariLiburModel->save([
            'tanggal' => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        session()->setFlashdata('berhasil', 'Hari libur berhasil ditambahkan.');
        return redirect()->to(base_url('admin/hari-libur'));
    }

    public function hapus($id)
    {
        $this->hariLiburModel->delete($id);
        session()->setFlashdata('berhasil', 'Hari libur berhasil dihapus.');
        return redirect()->to(base_url('admin/hari-libur'));
    }
}