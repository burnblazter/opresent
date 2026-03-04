<?php
// \app\Controllers\PlaygroundController.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Controllers;

use CodeIgniter\Controller;

class PlaygroundController extends Controller
{

    public function index()
    {
        $savedFaces = session()->get('pg_faces') ?? [];

        return view('playground/index', [
            'title'      => 'Playground',
            'model_path' => base_url('assets/models/'),
            'saved_faces'=> $savedFaces
        ]);
    }
    public function registerFace()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $session = session();
        $sandbox = $session->get('pg_faces') ?? [];

        if (count($sandbox) >= 5) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi sandbox penuh (maks 5 wajah).',
            ]);
        }

        $descriptor = $this->request->getPost('descriptor');
        $label      = esc(trim($this->request->getPost('label') ?? 'Tamu'));

        if (empty($descriptor)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Descriptor tidak valid.']);
        }

        $decoded = json_decode($descriptor, true);
        if (!is_array($decoded) || count($decoded) < 128) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format descriptor salah.']);
        }

        $id       = uniqid('pg_');
        $sandbox[] = [
            'id'         => $id,
            'label'      => $label,
            'descriptor' => $decoded,
            'registered' => date('H:i:s'),
        ];

        $session->set('pg_faces', $sandbox);

        return $this->response->setJSON([
            'success' => true,
            'id'      => $id,
            'label'   => $label,
            'total'   => count($sandbox),
        ]);
    }

    public function clearSession()
    {
        session()->remove('pg_faces');
        return $this->response->setJSON(['success' => true]);
    }

    public function submitPresensi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        
        $name       = esc($this->request->getPost('name')       ?? 'Tamu');
        $score      = (float) ($this->request->getPost('score') ?? 0);
        $lat        = (float) ($this->request->getPost('lat')   ?? 0);
        $lng        = (float) ($this->request->getPost('lng')   ?? 0);
        $time       = esc($this->request->getPost('time')       ?? date('H:i'));
        $emotion    = esc($this->request->getPost('emotion')    ?? 'neutral');

        $receiptId = strtoupper(substr(md5(uniqid()), 0, 8));

        return $this->response->setJSON([
            'success'    => true,
            'receipt_id' => $receiptId,
            'name'       => $name,
            'score'      => round($score * 100, 1),
            'lat'        => $lat,
            'lng'        => $lng,
            'time'       => $time,
            'emotion'    => $emotion,
            'timestamp'  => date('d M Y, H:i:s'),
        ]);
    }
}