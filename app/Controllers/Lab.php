<?php

namespace App\Controllers;

use App\Models\FaceDescriptorModel;
use App\Models\PegawaiModel;
use App\Models\UsersModel;

class Lab extends BaseController
{
    protected $faceDescriptorModel;
    protected $pegawaiModel;
    protected $usersModel;

    public function __construct()
    {
        $this->faceDescriptorModel = new FaceDescriptorModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->usersModel = new UsersModel();
    }

    /**
     * LAB: Face Recognition Playground (Admin Only)
     */
public function index()
    {
        // Authorization: hanya head, admin, helper
        if (!in_groups(['head', 'admin', 'helper'])) {
            return redirect()->to('/')->with('error', 'Akses ditolak. Halaman hanya untuk admin.');
        }

        $data = [
            'title' => '',
            'user_profile' => $this->usersModel->getUserInfo(user_id()), 
            'pegawai_list' => $this->pegawaiModel->asObject()->findAll(),
        ];

        return view('lab/index', $data);
    }

    /**
     * API: Load descriptor by pegawai ID
     */
    public function loadDescriptorByPegawai($idPegawai)
    {
        if (!in_groups(['head', 'admin', 'helper'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $descriptors = $this->faceDescriptorModel->where('id_pegawai', $idPegawai)->findAll();
        
        $result = [];
        foreach ($descriptors as $desc) {
            $descriptorArray = json_decode($desc->descriptor, true);
            
            if (!is_array($descriptorArray) || count($descriptorArray) < 100) {
                continue;
            }
            
            $result[] = [
                'id' => $desc->id,
                'label' => $desc->label,
                'descriptor' => $descriptorArray,
                'created_at' => $desc->created_at
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'descriptors' => $result
        ]);
    }

    /**
     * API: Save temporary session descriptor (untuk testing)
     */
    public function saveSessionDescriptor()
    {
        if (!in_groups(['head', 'admin', 'helper'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $json = $this->request->getJSON(true);
        
        $sessionKey = 'lab_temp_descriptors_' . ($json['session_id'] ?? 'default');
        $descriptors = session($sessionKey) ?? [];
        
        $descriptors[] = [
            'label' => $json['label'] ?? 'Temp ' . date('H:i:s'),
            'descriptor' => $json['descriptor'],
            'timestamp' => time()
        ];
        
        session()->set($sessionKey, $descriptors);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Descriptor saved to session',
            'count' => count($descriptors)
        ]);
    }

    /**
     * API: Clear session descriptors
     */
    public function clearSessionDescriptors()
    {
        if (!in_groups(['head', 'admin', 'helper'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $sessionId = $this->request->getGet('session_id') ?? 'default';
        session()->remove('lab_temp_descriptors_' . $sessionId);
        
        return $this->response->setJSON(['success' => true]);
    }
}