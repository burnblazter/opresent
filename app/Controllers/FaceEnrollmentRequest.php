<?php

namespace App\Controllers;

use App\Models\FaceDescriptorsRequestModel;
use App\Models\UsersModel; // 1. Tambahkan USE model ini
use CodeIgniter\HTTP\ResponseInterface;

class FaceEnrollmentRequest extends BaseController
{
    protected $requestModel;
    protected $usersModel; // 2. Siapkan properti properti
    protected $validation;

    public function __construct()
    {
        $this->requestModel = new FaceDescriptorsRequestModel();
        $this->usersModel = new UsersModel(); // 3. Load Model Users disini
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $userId = user_id();
        
        // 4. Ambil data user menggunakan method yang SAMA dengan controller HariLibur
        // Method ini kemungkinan mengembalikan OBJECT, bukan Array.
        $user_profile = $this->usersModel->getUserInfo($userId); 
        
        $data = [
            'title' => 'Request Pendaftaran Wajah',
            'requests' => $this->requestModel->getRequestsByPegawai($userId),
            'todayCount' => $this->requestModel->getTodayRequestCount($userId),
            
            // 5. Masukkan ke data view
            'user_profile' => $user_profile 
        ];

        return view('face_enrollment/user_request', $data);
    }

    /**
     * Submit request enrollment (AJAX)
     */
    public function submitRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $userId = user_id();

        // Cek batas request hari ini (max 3x)
        $todayCount = $this->requestModel->getTodayRequestCount($userId);
        if ($todayCount >= 3) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Anda sudah mencapai batas maksimal 3 request per hari!'
            ]);
        }

        $descriptor = $this->request->getPost('descriptor');
        $label = $this->request->getPost('label');
        $reason = $this->request->getPost('reason') ?? 'Self-enrollment via webcam';
        $imageData = $this->request->getPost('image_data'); // Base64 image

        // Validasi
        if (empty($descriptor) || empty($label)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap!'
            ]);
        }

        // Simpan gambar untuk verifikasi admin
        $imagePath = null;
        if ($imageData) {
            $imagePath = $this->saveImageFromBase64($imageData, $userId);
        }

        $data = [
            'id_pegawai' => $userId,
            'descriptor' => $descriptor,
            'label' => $label,
            'reason' => $reason,
            'image_path' => $imagePath,
            'status' => 'pending',
            'model_version' => 'human-v1'
        ];

        if ($this->requestModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => '✅ Request berhasil dikirim! Menunggu approval admin.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => '❌ Gagal menyimpan request!'
        ]);
    }

    /**
     * Save base64 image to file
     */
    private function saveImageFromBase64($base64Data, $userId)
    {
        // Remove data:image/png;base64, prefix
        $image = str_replace('data:image/png;base64,', '', $base64Data);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        $fileName = 'face_request_' . $userId . '_' . time() . '.png';
        $uploadPath = WRITEPATH . 'uploads/face_requests/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filePath = $uploadPath . $fileName;
        
        if (file_put_contents($filePath, $imageData)) {
            return 'face_requests/' . $fileName;
        }

        return null;
    }

    /**
     * Batalkan request (hanya yang pending)
     */
    public function cancelRequest($id)
    {
        $userId = user_id();
        $request = $this->requestModel->find($id);

        if (!$request || $request->id_pegawai != $userId) {
            return redirect()->back()->with('error', '❌ Request tidak ditemukan!');
        }

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', '❌ Hanya request pending yang bisa dibatalkan!');
        }

        // Hapus gambar jika ada
        if ($request->image_path) {
            $fullPath = WRITEPATH . 'uploads/' . $request->image_path;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $this->requestModel->delete($id);

        return redirect()->back()->with('success', '✅ Request berhasil dibatalkan!');
    }
}