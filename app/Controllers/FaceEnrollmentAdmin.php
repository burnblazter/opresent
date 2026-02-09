<?php

namespace App\Controllers;

use App\Models\FaceDescriptorsRequestModel;
use App\Models\FaceDescriptorModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class FaceEnrollmentAdmin extends BaseController
{
    protected $requestModel;
    protected $descriptorModel;
    protected $usersModel;

    public function __construct()
    {
        $this->requestModel = new FaceDescriptorsRequestModel();
        $this->descriptorModel = new FaceDescriptorModel();
        $this->usersModel = new UsersModel();
    }

    /**
     * Halaman daftar request pending
     */
    public function index()
    {
        // 4. Ambil user profile untuk header
        $user_profile = $this->usersModel->getUserInfo(user_id());

        $data = [
            'title' => 'Request Pendaftaran Wajah',
            'requests' => $this->requestModel->getPendingRequestsWithPegawai(),
            'user_profile' => $user_profile // 5. Kirim ke view
        ];

        return view('face_enrollment/admin_approval', $data);
    }

    /**
     * Detail request untuk approval
     */
    public function detail($id)
    {
        $request = $this->requestModel->getRequestWithPegawai($id);
        $user_profile = $this->usersModel->getUserInfo(user_id()); // Ambil profile admin

        if (!$request) {
            return redirect()->to('/kelola-face-enrollment')->with('error', '❌ Request tidak ditemukan!');
        }

        $data = [
            'title' => 'Detail Request - ' . $request->nama,
            'request' => $request,
            'user_profile' => $user_profile // Kirim ke view
        ];

        return view('face_enrollment/admin_detail', $data);
    }

    /**
     * Approve request
     */
    public function approve($id)
    {
        $request = $this->requestModel->find($id);

        if (!$request || $request->status !== 'pending') {
            return redirect()->back()->with('error', '❌ Request tidak valid!');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Salin descriptor ke tabel utama
            $descriptorData = [
                'id_pegawai' => $request->id_pegawai,
                'descriptor' => $request->descriptor,
                'label' => $request->label,
                'model_version' => $request->model_version
            ];

            $this->descriptorModel->insert($descriptorData);

            // 2. Update status request
            $this->requestModel->update($id, [
                'status' => 'approved',
                'approved_by' => user_id(),
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            // 3. Hapus gambar untuk menghemat storage
            if ($request->image_path) {
                $fullPath = WRITEPATH . 'uploads/' . $request->image_path;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }

                $this->requestModel->update($id, ['image_path' => null]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/kelola-face-enrollment')->with('success', '✅ Request berhasil di-approve!');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Approve error: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Gagal approve request!');
        }
    }

    /**
     * Reject request
     */
    public function reject($id)
    {
        $request = $this->requestModel->find($id);

        if (!$request || $request->status !== 'pending') {
            return redirect()->back()->with('error', '❌ Request tidak valid!');
        }

        $reason = $this->request->getPost('rejection_reason') ?? 'Ditolak oleh admin';

        // Update status
        $this->requestModel->update($id, [
            'status' => 'rejected',
            'approved_by' => user_id(),
            'approved_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason
        ]);

        // Hapus gambar
        if ($request->image_path) {
            $fullPath = WRITEPATH . 'uploads/' . $request->image_path;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        return redirect()->to('/kelola-face-enrollment')->with('success', '✅ Request berhasil ditolak!');
    }

    /**
     * Lihat gambar request (untuk verifikasi)
     */
    public function viewImage($id)
    {
        $request = $this->requestModel->find($id);

        if (!$request || !$request->image_path) {
            return $this->response->setStatusCode(404)->setBody('Image not found');
        }

        $fullPath = WRITEPATH . 'uploads/' . $request->image_path;

        if (!file_exists($fullPath)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        $this->response->setHeader('Content-Type', 'image/png');
        return $this->response->setBody(file_get_contents($fullPath));
    }
}