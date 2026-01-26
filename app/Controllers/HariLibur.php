<?php

namespace App\Controllers;

use App\Models\HariLiburModel;
use App\Models\UsersModel;

class HariLibur extends BaseController
{
    protected $hariLiburModel;
    protected $usersModel;

    public function __construct()
    {
        $this->hariLiburModel = new HariLiburModel();
        $this->usersModel = new UsersModel();
    }

    public function index()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());

        $data = [
            'title' => 'Daftar Hari Libur',
            'user_profile' => $user_profile,
            'data_libur' => $this->hariLiburModel->getApproved(),
            'pending_libur' => $this->hariLiburModel->getPendingApproval()
        ];

        return view('hari_libur/index', $data);
    }

    public function tambah()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());

        $data = [
            'title' => 'Tambah Hari Libur',
            'user_profile' => $user_profile,
            'validation' => \Config\Services::validation()
        ];
        return view('hari_libur/tambah', $data);
    }

    public function simpan()
    {
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
            return redirect()->to(base_url('hari-libur/tambah'))->withInput();
        }

        $this->hariLiburModel->save([
            'tanggal' => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'source' => 'manual',
            'approved' => 1 // Manual input langsung approved
        ]);

        session()->setFlashdata('berhasil', 'Hari libur berhasil ditambahkan.');
        return redirect()->to(base_url('hari-libur'));
    }

    public function syncFromAPI()
    {
        try {
            $tahun = $this->request->getGet('tahun') ?? date('Y');
            
            $apiUrl = "https://libur.deno.dev/api?year={$tahun}";
            
            // Gunakan file_get_contents dengan stream context
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
            
            $response = file_get_contents($apiUrl, false, $context);
            
            if ($response === false) {
                throw new \Exception('Gagal mengambil data dari API');
            }

            $apiData = json_decode($response, true);
            
            if (empty($apiData)) {
                session()->setFlashdata('gagal', 'Tidak ada data libur untuk tahun ' . $tahun);
                return redirect()->to(base_url('hari-libur'));
            }

            $inserted = 0;
            $skipped = 0;

            foreach ($apiData as $item) {
                $existing = $this->hariLiburModel->where('tanggal', $item['date'])->first();
                
                if (!$existing) {
                    $this->hariLiburModel->save([
                        'tanggal' => $item['date'],
                        'keterangan' => $item['name'],
                        'source' => 'api_libur_deno',
                        'approved' => 0
                    ]);
                    $inserted++;
                } else {
                    $skipped++;
                }
            }

            session()->setFlashdata('berhasil', "Berhasil menambahkan {$inserted} hari libur baru. {$skipped} data duplikat dilewati. Silakan verifikasi data yang belum disetujui.");
            return redirect()->to(base_url('hari-libur'));

        } catch (\Exception $e) {
            session()->setFlashdata('gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->to(base_url('hari-libur'));
        }
    }

    public function approve($id)
    {
        $libur = $this->hariLiburModel->find($id);
        
        if (!$libur) {
            session()->setFlashdata('gagal', 'Data tidak ditemukan.');
            return redirect()->to(base_url('hari-libur'));
        }

        $this->hariLiburModel->update($id, ['approved' => 1]);
        
        session()->setFlashdata('berhasil', 'Hari libur berhasil disetujui.');
        return redirect()->to(base_url('hari-libur'));
    }

    public function approveAll()
    {
        $this->hariLiburModel->where('approved', 0)->set(['approved' => 1])->update();
        
        session()->setFlashdata('berhasil', 'Semua hari libur pending berhasil disetujui.');
        return redirect()->to(base_url('hari-libur'));
    }

    public function hapus($id)
    {
        $this->hariLiburModel->delete($id);
        session()->setFlashdata('berhasil', 'Hari libur berhasil dihapus.');
        return redirect()->to(base_url('hari-libur'));
    }

    public function rejectAll()
    {
        $this->hariLiburModel->where('approved', 0)->delete();
        
        session()->setFlashdata('berhasil', 'Semua hari libur pending berhasil ditolak dan dihapus.');
        return redirect()->to(base_url('hari-libur'));
    }
}