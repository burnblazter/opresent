<?php
// \app\Models\FaceDescriptorsRequestModel.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Models;

use CodeIgniter\Model;

class FaceDescriptorsRequestModel extends Model
{
    protected $table            = 'face_descriptors_request';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pegawai',
        'descriptor',
        'image_path',
        'reason',
        'status',
        'label',
        'model_version',
        'approved_by',
        'approved_at',
        'rejection_reason'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_pegawai' => 'required|integer',
        'descriptor' => 'required',
        'status'     => 'in_list[pending,approved,rejected]',
    ];

    /**
     * Get request count for today by pegawai
     */
    public function getTodayRequestCount($idPegawai)
    {
        return $this->where('id_pegawai', $idPegawai)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();
    }

    /**
     * Get all pending requests with pegawai info
     */
    public function getPendingRequestsWithPegawai()
    {
        return $this->select('face_descriptors_request.*, pegawai.nama, pegawai.nomor_induk, pegawai.foto, jabatan.jabatan')
                    ->join('pegawai', 'pegawai.id = face_descriptors_request.id_pegawai')
                    ->join('jabatan', 'jabatan.id = pegawai.id_jabatan', 'left') // Tambahan Join Jabatan
                    ->where('status', 'pending')
                    ->findAll();
    }

    /**
     * Get all requests by pegawai
     */
    public function getRequestsByPegawai($idPegawai)
    {
        return $this->where('id_pegawai', $idPegawai)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get request with pegawai info
     */
    public function getRequestWithPegawai($id)
    {
        return $this->select('face_descriptors_request.*, pegawai.nama, pegawai.nomor_induk, pegawai.foto, jabatan.jabatan')
            ->join('pegawai', 'pegawai.id = face_descriptors_request.id_pegawai')
            ->join('jabatan', 'jabatan.id = pegawai.id_jabatan', 'left') // Tambahan Join Jabatan
            ->where('face_descriptors_request.id', $id)
            ->first();
    }

    /**
     * Count pending requests
     */
    public function countPending()
    {
        return $this->where('status', 'pending')->countAllResults();
    }
}