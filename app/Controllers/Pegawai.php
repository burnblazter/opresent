<?php
// \app\Controllers\Pegawai.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Controllers;

use App\Models\FaceDescriptorModel;
use App\Models\RoleModel;
use App\Models\UsersModel;
use App\Models\JabatanModel;
use App\Models\PegawaiModel;
use App\Models\UsersRoleModel;
use App\Models\LokasiPresensiModel;
use App\ThirdParty\MythAuth\Models\PermissionModel;
use App\ThirdParty\MythAuth\Controllers\AuthController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Pegawai extends BaseController
{
    protected $usersModel;
    protected $pegawaiModel;
    protected $jabatanModel;
    protected $roleModel;
    protected $lokasiModel;
    protected $usersRoleModel;
    protected $permissionModel;
    protected $foto_default;
    protected $auth;
    protected $faceDescriptorModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->jabatanModel = new JabatanModel();
        $this->roleModel = new RoleModel();
        $this->lokasiModel = new LokasiPresensiModel();
        $this->usersRoleModel = new UsersRoleModel();
        $this->permissionModel = new PermissionModel();
        $this->foto_default = 'default.jpg';
        $this->auth = new AuthController();
        $this->faceDescriptorModel = new faceDescriptorModel();
    }

public function index(): string
    {
        $limit = $this->request->getVar('limit') ? (int)$this->request->getVar('limit') : 10;
        $currentPage = $this->request->getVar('page_pegawai') ? $this->request->getVar('page_pegawai') : 1;

        $filter = [
            'keyword' => $this->request->getGet('keyword') ?? '',
            'jabatan' => $this->request->getGet('jabatan') ?? '',
            'role' => $this->request->getGet('role') ?? '',
            'status' => $this->request->getGet('status') ?? '',
            'jenis-kelamin' => $this->request->getGet('jenis-kelamin') ?? '',
            'lokasi-presensi' => $this->request->getGet('lokasi-presensi') ?? '',
        ];

        $pegawaiData = $this->pegawaiModel->getPegawai(false, $filter, false, $limit);
        // Logika untuk mengambil jumlah descriptor wajah
        $list_pegawai = $pegawaiData['pegawai'];
        $pegawaiIds = [];
        
        // 1. Ambil semua ID pegawai di halaman ini
        foreach ($list_pegawai as $p) {
            $pegawaiIds[] = $p->id;
        }

        $descriptorCounts = [];
        if (!empty($pegawaiIds)) {
            // 2. Query hitung jumlah descriptor berdasarkan ID pegawai yang ada
            $counts = $this->faceDescriptorModel
                ->select('id_pegawai, COUNT(*) as total')
                ->whereIn('id_pegawai', $pegawaiIds)
                ->groupBy('id_pegawai')
                ->findAll();

            // 3. Mapping hasil query ke array [id_pegawai => total]
            foreach ($counts as $c) {
                $descriptorCounts[$c->id_pegawai] = $c->total;
            }
        }

        // 4. Masukkan jumlah ke dalam object pegawai
        foreach ($pegawaiData['pegawai'] as &$pegawai) {
            // Jika ada di array counts ambil nilainya, jika tidak 0
            $pegawai->jumlah_wajah = $descriptorCounts[$pegawai->id] ?? 0;
        }
        $data_jabatan = $this->jabatanModel->get()->getResultArray();
        $data_lokasi = $this->lokasiModel->get()->getResultArray();
        $data_role = $this->roleModel->findAll();

        $filtered = false;
        if (
            ($filter['jabatan'] !== '') || 
            ($filter['role'] !== '') || 
            ($filter['status'] !== '') || 
            ($filter['jenis-kelamin'] !== '') || 
            ($filter['lokasi-presensi'] !== '')
        ) {
            $filtered = true;
        }

        $data = [
            'title' => 'Data Pengguna',
            'user_profile' => $this->usersModel->getUserInfo(user_id()),
            'data_pegawai' => $pegawaiData['pegawai'],
            'pager' => $pegawaiData['links'],
            'total' => $pegawaiData['total'],
            'perPage' => $pegawaiData['perPage'],
            'data_jabatan' => $data_jabatan,
            'data_lokasi' => $data_lokasi,
            'data_role' => $data_role,
            'currentPage' => $currentPage,
            'limit' => $limit,
            'isFiltered' => $filtered,
            'filter' => $filter,
        ];

        return view('data_pegawai/index', $data, ['escape' => 'html']);
    }

    public function pencarianPegawai()
    {
        $currentPage = $this->request->getVar('page_pegawai') ? $this->request->getVar('page_pegawai') : 1;
        $limit = $this->request->getVar('limit') ? (int)$this->request->getVar('limit') : 10;
    
        $filter = [
            'keyword' => $this->request->getGet('keyword') ?? '',
            'jabatan' => $this->request->getGet('jabatan'),
            'role' => $this->request->getGet('role'),
            'status' => $this->request->getGet('status'),
            'jenis-kelamin' => $this->request->getGet('jenis-kelamin'),
            'lokasi-presensi' => $this->request->getGet('lokasi-presensi'),
        ];

        $result = $this->pegawaiModel->getPegawai(false, $filter, false, $limit);
        
        $data = [
            'data_pegawai' => $result['pegawai'],
            'pager' => $result['links'],
            'total' => $result['total'],
            'perPage' => $result['perPage'],
            'currentPage' => $currentPage,
            'limit' => $limit,
        ];

        return view('data_pegawai/hasil-pencarian', $data);
    }

    public function dataPegawaiExcel()
    {
        $filter = [
            'keyword' => $this->request->getPost('keyword'),
            'jabatan' => $this->request->getPost('jabatan'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
            'jenis-kelamin' => $this->request->getPost('jenis-kelamin'),
            'lokasi-presensi' => $this->request->getPost('lokasi-presensi'),
        ];
        
        $result = $this->pegawaiModel->getPegawai(false, $filter, true);
        $data_pegawai = $result['pegawai'];

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        if ($filter['jabatan'] === '') {
            $filter['jabatan'] = 'Semua Unit';
        }
        if ($filter['role'] === '') {
            $filter['role'] = 'Semua Role';
        }
        if ($filter['status'] === '') {
            $filter['status'] = 'Semua Status';
        }
        if ($filter['jenis-kelamin'] === '') {
            $filter['jenis-kelamin'] = 'Semua Jenis Kelamin';
        }
        if ($filter['lokasi-presensi'] === '') {
            $filter['lokasi-presensi'] = 'Semua Lokasi Presensi';
        }

        $worksheet->setCellValue('A1', 'Data Pegawai');
        $worksheet->setCellValue('A3', 'Filter Unit');
        $worksheet->setCellValue('A4', 'Filter Role Akun');
        $worksheet->setCellValue('A5', 'Filter Status');
        $worksheet->setCellValue('A6', 'Filter Jenis Kelamin');
        $worksheet->setCellValue('A7', 'Filter Lokasi Presensi');
        $worksheet->setCellValue('C3', $filter['jabatan']);
        $worksheet->setCellValue('C4', $filter['role']);
        $worksheet->setCellValue('C5', $filter['status']);
        $worksheet->setCellValue('C6', $filter['jenis-kelamin']);
        $worksheet->setCellValue('C7', $filter['lokasi-presensi']);
        $worksheet->setCellValue('A9', '#');
        $worksheet->setCellValue('B9', 'NAMA');
        $worksheet->setCellValue('C9', 'NOMOR INDUK');
        $worksheet->setCellValue('D9', 'UNIT');
        $worksheet->setCellValue('E9', 'ROLE AKUN');
        $worksheet->setCellValue('F9', 'USERNAME');
        $worksheet->setCellValue('G9', 'EMAIL');
        $worksheet->setCellValue('H9', 'NO. HANDPHONE');
        $worksheet->setCellValue('I9', 'ALAMAT');
        $worksheet->setCellValue('J9', 'JENIS KELAMIN');
        $worksheet->setCellValue('K9', 'LOKASI PRESENSI');
        $worksheet->setCellValue('L9', 'STATUS');

        $worksheet->mergeCells('A1:L1');
        $worksheet->mergeCells('A3:B3');
        $worksheet->mergeCells('A4:B4');
        $worksheet->mergeCells('A5:B5');
        $worksheet->mergeCells('A6:B6');
        $worksheet->mergeCells('A7:B7');

        $data_start_row = 10;
        $nomor = 1;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ]
        ];

        if (!empty($data_pegawai)) {
            foreach ($data_pegawai as $data) {
                if ($data->active == 0) {
                    $status = 'Belum Aktivasi';
                } else if ($data->active == 1) {
                    $status = 'Sudah Aktivasi';
                }
                $worksheet->setCellValue('A' . $data_start_row, $nomor++);
                $worksheet->setCellValue('B' . $data_start_row, $data->nama);
                $worksheet->setCellValue('C' . $data_start_row, $data->nomor_induk);
                $worksheet->setCellValue('D' . $data_start_row, $data->jabatan);
                $worksheet->setCellValue('E' . $data_start_row, $data->role);
                $worksheet->setCellValue('F' . $data_start_row, $data->username);
                $worksheet->setCellValue('G' . $data_start_row, $data->email);
                $worksheet->setCellValue('H' . $data_start_row, $data->no_handphone);
                $worksheet->setCellValue('I' . $data_start_row, $data->alamat);
                $worksheet->setCellValue('J' . $data_start_row, $data->jenis_kelamin);
                $worksheet->setCellValue('K' . $data_start_row, $data->lokasi_presensi);
                $worksheet->setCellValue('L' . $data_start_row, $status);

                $worksheet->getStyle('A' . $data_start_row - 1 . ':L' . $data_start_row)->applyFromArray($styleArray);
                $worksheet->getStyle('A' . $data_start_row - 1 . ':L' . $data_start_row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                $worksheet->getStyle('I')->getAlignment()->setWrapText(true);
                $data_start_row++;
            }
        } else {
            $worksheet->setCellValue('A' . $data_start_row, 'Tidak Ada Data');
            $worksheet->mergeCells('A' . $data_start_row . ':L' . $data_start_row);
            $worksheet->getStyle('A' . $data_start_row - 1 . ':L' . $data_start_row)->applyFromArray($styleArray);
        }

        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L'];
        foreach ($columns as $column) {
            $worksheet->getColumnDimension($column)->setAutoSize(true);
        }
        $worksheet->getColumnDimension('I')->setWidth(300, 'px');

        $worksheet->getStyle('A3:C7')->applyFromArray($styleArray);
        $worksheet->getStyle('A3:A7')->getFont()->setBold(true);
        $worksheet->getStyle('A3:C7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('A1')->getFont()->setBold(true);
        $worksheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffff00');
        $worksheet->getStyle('A9:L9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A9:L9')->getFont()->setBold(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PresenSi_Data Pegawai_' . date('Y-m-d-His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function detail($id): string
    {
        // Check if $id is numeric or username
        if (is_numeric($id)) {
            $data_pegawai = $this->pegawaiModel->getPegawaiById($id);
        } else {
            $data_pegawai = $this->pegawaiModel->getPegawai($id)['pegawai'];
        }

        if (empty($data_pegawai)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Pegawai ' . $id . ' Tidak Ditemukan');
        }

        $data = [
            'title' => 'Detail Data Pengguna ' . $data_pegawai->nama,
            'user_profile' => $this->usersModel->getUserInfo(user_id()),
            'data_pegawai' => $data_pegawai,
        ];

        return view('data_pegawai/detail', $data);
    }

    public function add(): string
    {    
        $data = [
            'title' => 'Tambah Data Pengguna',
            'user_profile' => $this->usersModel->getUserInfo(user_id()),
            'jabatan' => $this->jabatanModel->getJabatan(false, false, 10, true)['jabatan'],
            'role' => $this->roleModel->findAll(),
            'lokasi' => $this->lokasiModel->get()->getResultArray(),
        ];

        return view('data_pegawai/tambah', $data);
    }

    public function store()
    {
        $rules = [
             'nomor_induk' => [
                'rules' => 'required|is_unique[pegawai.nomor_induk]|max_length[50]',
                'errors' => [
                    'required' => 'Mohon isi nomor induk (NIS/NIP)',
                    'is_unique' => 'Nomor induk sudah terdaftar',
                    'max_length' => 'Nomor induk maksimal 50 karakter',
                ]
        ],
            'nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Mohon isi nama pengguna',
                ]
            ],
            'jenis_kelamin' => [
                'rules' => 'required|in_list[Perempuan,Laki-laki]',
                'errors' => [
                    'required' => 'Mohon isi jenis kelamin pegawai',
                    'in_list' => 'Mohon pilih jenis kelamin yang tersedia',
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Mohon isi alamat domisili pegawai',
                ]
            ],
            'no_handphone' => [
                'rules' => 'required|regex_match[/^(?:\+62|62|0)(?:\d{8,15})$/]',
                'errors' => [
                    'required' => 'Mohon isi nomor telepon pegawai',
                    'regex_match' => 'Mohon isi nomor telepon dengan 8-15 digit',
                ]
            ],
            'jabatan' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Mohon isi unit pengguna',
                    'numeric' => 'Mohon pilih unit pengguna yang tersedia',
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Mohon isi alamat email pegawai',
                    'valid_email' => 'Mohon isi alamat email yang valid',
                    'is_unique' => 'Alamat email sudah terdaftar',
                ]
            ],
            'lokasi_presensi' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Mohon isi lokasi untuk presensi pegawai',
                    'numeric' => 'Mohon pilih lokasi yang tersedia untuk presensi pegawai',
                ]
            ],
            'username' => [
                'rules' => 'required|alpha_numeric|min_length[5]|max_length[30]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Mohon isi username untuk akun pegawai',
                    'is_unique' => 'Username sudah terdaftar',
                    'min_length' => 'Username harus terdiri dari 5-30 karakter',
                    'max_length' => 'Username harus terdiri dari 5-30 karakter'
                ],
            ],
            'role' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Mohon isi role untuk akun pegawai',
                    'numeric' => 'Mohon pilih role yag tersedia untuk akun pegawai',
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/tambah-data-pegawai')->withInput();
        }

        /*
        * Mengambil cara aktivasi yang dipilih
        */
        $caraAktivasi = $this->request->getPost('aktivasi');
        
        // Generate activate_hash SELALU untuk semua cara aktivasi
        $activate_hash = bin2hex(random_bytes(16));
        
        if ($caraAktivasi != 2) {
            // Jika aktivasi manual (via email), active = 0
            $active = 0;
        } else {
            // Jika aktivasi otomatis, active = 1 tapi tetap ada hash untuk buat password
            $active = 1;
        }

    $this->pegawaiModel->save([
        'nomor_induk' => $this->request->getVar('nomor_induk'),
        'nama' => $this->request->getVar('nama'),
        'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
        'alamat' => $this->request->getVar('alamat'),
        'no_handphone' => $this->request->getVar('no_handphone'),
        'id_jabatan' => $this->request->getVar('jabatan'),
        'id_lokasi_presensi' => $this->request->getVar('lokasi_presensi'),
        'foto' => $this->foto_default,
    ]);

        // Mendapatkan ID terakhir dari model pegawai
        $id_pegawai = $this->pegawaiModel->insertID();

        $email = $this->request->getVar('email');
        $username =  $this->request->getVar('username');

        $this->usersModel->save([
            'id_pegawai' => $id_pegawai,
            'email' => $email,
            'username' => $username,
            'password_hash' => null, // Password akan dibuat saat aktivasi
            'active' => $active,
            'activate_hash' => $activate_hash, // PENTING: Hash selalu di-set
        ]);

        // Mendapatkan ID terakhir dari model users
        $user_id = $this->usersModel->insertID();

        $this->usersRoleModel->save([
            'group_id' => $this->request->getVar('role'),
            'user_id' => $user_id,
        ]);

        // Jika memilih cara aktivasi Melalui Email (aktivasi = 1), kirim langsung Activation Email
        if ($caraAktivasi == 1) {
            $this->auth->resendActivateAccount($email);
        }

        session()->setFlashdata('berhasil', 'Data pegawai berhasil ditambahkan');
        return redirect()->to('/data-pegawai');
    }

    public function edit($id): string
    {
        // Check if $id is numeric or username
        if (is_numeric($id)) {
            $data_pegawai = $this->pegawaiModel->getPegawaiById($id);
        } else {
            $data_pegawai = $this->pegawaiModel->getPegawai($id)['pegawai'];
        }

        if (empty($data_pegawai)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Pegawai ' . $id . ' Tidak Ditemukan');
        }

        $data = [
            'title' => 'Edit Data Pengguna ' . $data_pegawai->nama,
            'user_profile' => $this->usersModel->getUserInfo(user_id()),
            'data_pegawai' => $data_pegawai,
            'jabatan' => $this->jabatanModel->getJabatan(false, false, 10, true)['jabatan'],
            'role' => $this->roleModel->findAll(),
            'lokasi' => $this->lokasiModel->get()->getResultArray(),
        ];

        return view('data_pegawai/edit', $data);
    }

public function update()
    {
        $username_db = $this->request->getVar('username_db');
        $data_pegawai_db = $this->pegawaiModel->getPegawai($username_db)['pegawai'];
        $email_db = $data_pegawai_db->email;
        $nomor_induk_db = $data_pegawai_db->nomor_induk;
        $nomor_induk_input = $this->request->getVar('nomor_induk');
    
        if ($nomor_induk_db == $nomor_induk_input) {
            $rules_nomor_induk = 'required|max_length[50]';
        } else {
            $rules_nomor_induk = 'required|is_unique[pegawai.nomor_induk]|max_length[50]';
        }

        $email_input = $this->request->getVar('email');
        if ($email_db == $email_input) {
            $rules_email = 'required|valid_email';
        } else {
            $rules_email = 'required|valid_email|is_unique[users.email]';
        }

        $username_input = $this->request->getVar('username');
        if ($username_db == $username_input) {
            $rules_username = 'required|alpha_numeric|min_length[5]|max_length[30]';
        } else {
            $rules_username = 'required|alpha_numeric|min_length[5]|max_length[30]|is_unique[users.username]';
        }

        $rules = [
            'nomor_induk' => [
                'rules' => $rules_nomor_induk,
                'errors' => [
                    'required' => 'Mohon isi nomor induk',
                    'is_unique' => 'Nomor induk sudah terdaftar',
                    'max_length' => 'Nomor induk maksimal 50 karakter',
                ]
            ],
            'nama' => [
                'rules' => 'required',
                'errors' => ['required' => 'Mohon isi nama pengguna']
            ],
            'jenis_kelamin' => [
                'rules' => 'required|in_list[Perempuan,Laki-laki]',
                'errors' => ['required' => 'Mohon isi jenis kelamin pengguna']
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => ['required' => 'Mohon isi alamat domisili pegawai']
            ],
            'no_handphone' => [
                'rules' => 'required|regex_match[/^(?:\+62|62|0)(?:\d{8,15})$/]',
                'errors' => ['required' => 'Mohon isi nomor telepon', 'regex_match' => 'Format nomor telepon salah']
            ],
            'jabatan' => [
                'rules' => 'required',
                'errors' => ['required' => 'Mohon isi unit pengguna']
            ],
            'email' => [
                'rules' => $rules_email,
                'errors' => ['required' => 'Mohon isi email', 'valid_email' => 'Email tidak valid', 'is_unique' => 'Email sudah terdaftar']
            ],
            'lokasi_presensi' => [
                'rules' => 'required',
                'errors' => ['required' => 'Mohon isi lokasi presensi']
            ],
            'username' => [
                'rules' => $rules_username,
                'errors' => ['required' => 'Mohon isi username', 'is_unique' => 'Username sudah dipakai']
            ],
            'role' => [
                'rules' => 'required',
                'errors' => ['required' => 'Mohon isi role']
            ]
        ];

        $password_baru = $this->request->getVar('password_baru');
        
        if (!empty($password_baru)) {
            $rules['password_baru'] = [
                'rules' => 'min_length[8]',
                'errors' => [
                    'min_length' => 'Password minimal 8 karakter'
                ]
            ];
            $rules['konfirmasi_password'] = [
                'rules' => 'matches[password_baru]',
                'errors' => [
                    'matches' => 'Konfirmasi password tidak sesuai'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/data-pegawai/edit/' . $username_db)->withInput();
        }

        $this->pegawaiModel->save([
            'id' => $this->request->getVar('id'),
            'nomor_induk' => $this->request->getVar('nomor_induk'),
            'nama' => $this->request->getVar('nama'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'alamat' => $this->request->getVar('alamat'),
            'no_handphone' => $this->request->getVar('no_handphone'),
            'id_jabatan' => $this->request->getVar('jabatan'),
            'id_lokasi_presensi' => $this->request->getVar('lokasi_presensi'),
        ]);

        $id_pegawai = $this->request->getVar('id_pegawai');
        $email = $this->request->getVar('email');
        $username =  $this->request->getVar('username');
        $id_user = $this->request->getVar('id_user');

        $userData = [
            'id' => $id_user,
            'id_pegawai' => $id_pegawai,
            'email' => $email,
            'username' => $username,
        ];

        if (!empty($password_baru)) {
            $password_hash = $this->usersModel->hashPassword($password_baru);
            $userData['password_hash'] = $password_hash;
            
            // If user inactive and password is changed by admin, set active to 1
            $userData['active'] = 1; 
        }

        $this->usersModel->save($userData);

        $role = $this->request->getVar('role');
        $role_db = $this->request->getVar('role_db');

        if ($role !== $role_db) {
            $groupModel = new \App\ThirdParty\MythAuth\Models\GroupModel();
            $groupModel->addUserToGroup($id_user, (int)$role);
            $groupModel->removeUserFromGroup($id_user, (int)$role_db);
        }

        session()->setFlashdata('berhasil', 'Data pengguna ' . $data_pegawai_db->nama . ' berhasil diedit');
        return redirect()->to('/data-pegawai');
    }

    public function hapusFoto($username)
    {
        $pegawai_db = $this->pegawaiModel->getPegawai($username)['pegawai'];
        $foto_db = $pegawai_db->foto;

        if ($foto_db !== $this->foto_default) {
            $this->pegawaiModel->save([
                'id' => $pegawai_db->id,
                'foto' => $this->foto_default,
            ]);

            unlink('assets/img/user_profile/' . $foto_db);
        }

        session()->setFlashdata('berhasil', 'Foto pengguna ' . $pegawai_db->nama . ' berhasil dihapus');
        return redirect()->to(base_url('/data-pegawai/edit/' . $username));
    }

    public function delete($id)
    {
        $this->pegawaiModel->delete($id);

        session()->setFlashdata('berhasil', 'Data Pengguna Berhasil Dihapus');
        return redirect()->to('/data-pegawai');
    }

    // Method untuk download template Excel
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Header
        $worksheet->setCellValue('A1', 'NAMA');
        $worksheet->setCellValue('B1', 'JENIS KELAMIN');
        $worksheet->setCellValue('C1', 'ALAMAT');
        $worksheet->setCellValue('D1', 'NOMOR HANDPHONE');
        $worksheet->setCellValue('E1', 'UNIT');
        $worksheet->setCellValue('F1', 'LOKASI PRESENSI');
        $worksheet->setCellValue('G1', 'ALAMAT EMAIL');
        $worksheet->setCellValue('H1', 'USERNAME');
        $worksheet->setCellValue('I1', 'ROLE AKUN');
        $worksheet->setCellValue('J1', 'AKTIVASI SEKARANG');
        $worksheet->setCellValue('K1', 'PASSWORD');
        $worksheet->setCellValue('L1', 'NOMOR INDUK (NIS/NIP)');

        // Style header
        $worksheet->getStyle('A1:L1')->getFont()->setBold(true);
        $worksheet->getStyle('A1:L1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD3D3D3');
        
        // Set width kolom
        foreach (range('A', 'L') as $col) {
            $worksheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Contoh data
        $worksheet->setCellValue('A2', 'John Doe');
        $worksheet->setCellValue('B2', 'Laki-laki');
        $worksheet->setCellValue('C2', 'Jl. Example No. 123');
        $worksheet->setCellValue('D2', '081234567890');
        $worksheet->setCellValue('E2', 'Kelas XII-10');
        $worksheet->setCellValue('F2', 'SMA Negeri 1 Balikpapan');
        $worksheet->setCellValue('G2', 'john@example.com');
        $worksheet->setCellValue('H2', 'johndoe');
        $worksheet->setCellValue('I2', 'pegawai');
        $worksheet->setCellValue('J2', 'YA');
        $worksheet->setCellValue('K2', 'kucingberlari');
        $worksheet->setCellValue('L2', '21781');
        
        $worksheet->getStyle('A4:A10')->getFont()->setItalic(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Template_Import_Pengguna.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    // Method untuk import Excel
    public function importExcel()
    {
        $file = $this->request->getFile('file_excel');
        
        if (!$file->isValid()) {
            session()->setFlashdata('gagal', 'File tidak valid');
            return redirect()->to('/data-pegawai');
        }

        $extension = $file->getClientExtension();
        if ($extension != 'xlsx' && $extension != 'xls') {
            session()->setFlashdata('gagal', 'Format file harus .xlsx atau .xls');
            return redirect()->to('/data-pegawai');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $success = 0;
            $failed = 0;
            $errors = [];

            // Database connection
            $db = \Config\Database::connect();

            // Mulai dari row 2 (skip header di row 1)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip jika row kosong
                if (empty($row[0])) continue;

                $nama = trim($row[0]);
                $jenis_kelamin = trim($row[1]);
                $alamat = trim($row[2]);
                $no_handphone = trim($row[3]);
                $jabatan_nama = trim($row[4]);
                $lokasi_nama = trim($row[5]);
                $email = trim($row[6]);
                $username = trim($row[7]);
                $role_nama = trim($row[8]); // ini akan selalu 'pegawai'
                $aktivasi = strtoupper(trim($row[9])) === 'YA' ? 1 : 0;
                $password = trim($row[10]);
                $nomor_induk = trim($row[11]);

                // Validasi data wajib
                if (empty($nama) || empty($email) || empty($username) || empty($password) || empty($nomor_induk)) {
                    $errors[] = "Baris " . ($i + 1) . ": Nama, Email, Username, Password, atau Nomor Induk kosong";
                    $failed++;
                    continue;
                }

                // Validasi jenis kelamin
                if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
                    $errors[] = "Baris " . ($i + 1) . ": Jenis kelamin harus 'Laki-laki' atau 'Perempuan'";
                    $failed++;
                    continue;
                }

                // Cek email sudah ada atau belum - GUNAKAN QUERY BUILDER
                $emailCheck = $db->table('users')->where('email', $email)->get()->getNumRows();
                if ($emailCheck > 0) {
                    $errors[] = "Baris " . ($i + 1) . ": Email '$email' sudah terdaftar";
                    $failed++;
                    continue;
                }

                // Cek username sudah ada atau belum - GUNAKAN QUERY BUILDER
                $usernameCheck = $db->table('users')->where('username', $username)->get()->getNumRows();
                if ($usernameCheck > 0) {
                    $errors[] = "Baris " . ($i + 1) . ": Username '$username' sudah terdaftar";
                    $failed++;
                    continue;
                }

                $nomorIndukCheck = $db->table('pegawai')->where('nomor_induk', $nomor_induk)->get()->getNumRows();
                if ($nomorIndukCheck > 0) {
                    $errors[] = "Baris " . ($i + 1) . ": Nomor Induk '$nomor_induk' sudah terdaftar";
                    $failed++;
                    continue;
                }

                // Cari ID jabatan berdasarkan nama - GUNAKAN QUERY BUILDER
                $jabatanQuery = $db->table('jabatan')->where('jabatan', $jabatan_nama)->get()->getRow();
                if (!$jabatanQuery) {
                    $errors[] = "Baris " . ($i + 1) . ": Unit '$jabatan_nama' tidak ditemukan";
                    $failed++;
                    continue;
                }
                $id_jabatan = $jabatanQuery->id;

                // Cari ID lokasi berdasarkan nama - GUNAKAN QUERY BUILDER
                $lokasiQuery = $db->table('lokasi_presensi')->where('nama_lokasi', $lokasi_nama)->get()->getRow();
                if (!$lokasiQuery) {
                    $errors[] = "Baris " . ($i + 1) . ": Lokasi presensi '$lokasi_nama' tidak ditemukan";
                    $failed++;
                    continue;
                }
                $id_lokasi = $lokasiQuery->id;

                // Role selalu pegawai (id = 3)
                $role_id = 3;

                // Generate activate hash
                $activate_hash = bin2hex(random_bytes(16));

                // Hash password
                $password_hash = null;
                if (!empty($password)) {
                    if (strlen($password) < 8) {
                        $errors[] = "Baris " . ($i + 1) . ": Password minimal 8 karakter";
                        $failed++;
                        continue;
                    }
                    $password_hash = $this->usersModel->hashPassword($password);
                }

                try {
                    // Simpan data pegawai
                    $this->pegawaiModel->save([
                        'nomor_induk' => $nomor_induk,
                        'nama' => $nama,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => $alamat,
                        'no_handphone' => $no_handphone,
                        'id_jabatan' => $id_jabatan,
                        'id_lokasi_presensi' => $id_lokasi,
                        'foto' => $this->foto_default,
                    ]);

                    $id_pegawai = $this->pegawaiModel->insertID();

                    // Simpan data user
                    $this->usersModel->save([
                        'id_pegawai' => $id_pegawai,
                        'email' => $email,
                        'username' => $username,
                        'password_hash' => $password_hash,
                        'active' => $aktivasi,
                        'activate_hash' => $activate_hash,
                    ]);

                    $user_id = $this->usersModel->insertID();

                    // Simpan role (selalu pegawai = 3)
                    $this->usersRoleModel->save([
                        'group_id' => $role_id,
                        'user_id' => $user_id,
                    ]);

                    $success++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i + 1) . ": Gagal menyimpan data - " . $e->getMessage();
                    $failed++;
                    
                    // Rollback jika pegawai sudah tersimpan tapi user gagal
                    if (isset($id_pegawai) && $id_pegawai > 0) {
                        $this->pegawaiModel->delete($id_pegawai);
                    }
                    continue;
                }
            }

            $message = "Berhasil import $success pegawai";
            if ($failed > 0) {
                $message .= ", Gagal: $failed pegawai";
            }

            session()->setFlashdata('berhasil', $message);
            if (!empty($errors)) {
                session()->setFlashdata('errors', $errors);
            }
            
        } catch (\Exception $e) {
            session()->setFlashdata('gagal', 'Error: ' . $e->getMessage());
        }
        
        return redirect()->to('/data-pegawai');
    }

    // Face Descriptor Management
    public function manageFaceDescriptors($id_pegawai)
    {
        $pegawai = $this->pegawaiModel->find($id_pegawai);
        
        if (!$pegawai) {
            session()->setFlashdata('gagal', 'Data pegawai tidak ditemukan');
            return redirect()->to(base_url('data-pegawai'));
        }

        // Convert array to object if needed
        if (is_array($pegawai)) {
            $pegawai = (object) $pegawai;
        }

        $descriptors = $this->faceDescriptorModel->getDescriptorsByPegawai($id_pegawai);

        $data = [
            'title' => 'Manajemen Face Recognition - ' . $pegawai->nama,
            'user_profile' => $this->usersModel->getUserInfo(user_id()),
            'pegawai' => $pegawai,
            'descriptors' => $descriptors
        ];

        return view('data_pegawai/face_descriptors', $data);
    }

    /**
     * API: Simpan face descriptor baru (dari webcam atau upload)
     */
    public function saveFaceDescriptor()
    {
        $id_pegawai = $this->request->getPost('id_pegawai');
        $descriptor = $this->request->getPost('descriptor');
        $label = $this->request->getPost('label') ?: 'Foto ' . date('Y-m-d H:i:s');

        if (!$descriptor) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Descriptor tidak valid'
            ]);
        }

        // ========== FIX: VALIDASI DESCRIPTOR ==========
        // Decode dari string ke array
        if (is_string($descriptor)) {
            $descriptor = json_decode($descriptor, true);
        }
        
        // Validasi apakah array valid
        if (!is_array($descriptor) || count($descriptor) < 100) {
            log_message('error', 'Descriptor invalid - bukan array atau terlalu pendek: ' . count($descriptor));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Descriptor tidak valid (format salah)'
            ]);
        }
        
        // Validasi apakah semua elemen adalah angka
        foreach ($descriptor as $val) {
            if (!is_numeric($val)) {
                log_message('error', 'Descriptor contains non-numeric value');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Descriptor mengandung nilai non-numerik'
                ]);
            }
        }
        
        log_message('info', 'Saving descriptor with ' . count($descriptor) . ' dimensions');

        try {
            $this->faceDescriptorModel->save([
                'id_pegawai' => $id_pegawai,
                'descriptor' => json_encode($descriptor), // ← Simpan sebagai JSON array
                'label' => $label,
                'model_version' => 'human-v1'
            ]);

            log_message('info', 'Face descriptor saved successfully for pegawai ' . $id_pegawai);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Face descriptor berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error save face descriptor: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan face descriptor: ' . $e->getMessage()
            ]);
        }
    }

    public function updateDescriptorLabel()
    {
        $id = $this->request->getPost('id');
        $label = $this->request->getPost('label');

        if (!$label) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Label tidak boleh kosong'
            ]);
        }

        try {
            $this->faceDescriptorModel->updateLabel($id, $label);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Label berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui label'
            ]);
        }
    }

    public function deleteDescriptor($id)
    {
        try {
            $this->faceDescriptorModel->delete($id);
            session()->setFlashdata('berhasil', 'Face descriptor berhasil dihapus');
        } catch (\Exception $e) {
            session()->setFlashdata('gagal', 'Gagal menghapus face descriptor');
        }

        return redirect()->back();
    }

    // Method untuk reset password oleh admin
    public function resetPassword()
    {
        $id_user = $this->request->getPost('id_user');
        $password_baru = $this->request->getPost('password_baru');
        $konfirmasi_password = $this->request->getPost('konfirmasi_password');

        if ($password_baru !== $konfirmasi_password) {
            session()->setFlashdata('gagal', 'Password dan konfirmasi password tidak sama');
            return redirect()->back()->withInput();
        }

        if (strlen($password_baru) < 8) {
            session()->setFlashdata('gagal', 'Password minimal 8 karakter');
            return redirect()->back()->withInput();
        }

        $password_hash = $this->usersModel->hashPassword($password_baru);

        $this->usersModel->save([
            'id' => $id_user,
            'password_hash' => $password_hash,
        ]);

        session()->setFlashdata('berhasil', 'Password berhasil diubah');
        return redirect()->back();
    }

    public function bulkDelete()
    {
        if ($this->request->isAJAX()) {
            $ids = $this->request->getPost('ids');

            if (!empty($ids) && is_array($ids)) {
                try {
                    $this->pegawaiModel->delete($ids);

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => count($ids) . ' data pegawai berhasil dihapus permanen.'
                    ]);
                } catch (\Exception $e) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal menghapus data: ' . $e->getMessage()
                    ]);
                }
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
    }

    public function bulkUpdateUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $ids = $this->request->getPost('ids');
        $newJabatanId = $this->request->getPost('jabatan');

        if (empty($ids) || empty($newJabatanId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap.'
            ]);
        }

        $pegawaiModel = new \App\Models\PegawaiModel(); 

        try {
            $pegawaiModel->update($ids, ['id_jabatan' => $newJabatanId]);

            return $this->response->setJSON([
                'success' => true,
                'message' => count($ids) . ' pegawai berhasil dipindahkan unitnya.'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function cetakBarcode()
    {
        $filter = [
            'keyword'         => $this->request->getPost('keyword') ?? '',
            'jabatan'         => $this->request->getPost('jabatan') ?? '',
            'role'            => 'pegawai',
            'status'          => $this->request->getPost('status') ?? '',
            'jenis-kelamin'   => $this->request->getPost('jenis-kelamin') ?? '',
            'lokasi-presensi' => $this->request->getPost('lokasi-presensi') ?? '',
        ];

        $per_page = max(1, (int)($this->request->getPost('per_page') ?? 8));

        $result       = $this->pegawaiModel->getPegawai(false, $filter, true);
        $data_pegawai = $result['pegawai'];

        return view('data_pegawai/cetak_barcode', [
            'data_pegawai' => $data_pegawai,
            'filter'       => $filter,
            'base_url'     => base_url(),
            'per_page'     => $per_page,
        ]);
    }
}