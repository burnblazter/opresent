<?php
// \app\Models\FaceDescriptorModel.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Models;

use CodeIgniter\Model;

class FaceDescriptorModel extends Model
{
    protected $table            = 'face_descriptors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pegawai', 'descriptor', 'label', 'model_version', 'created_at', 'updated_at', 'deleted_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Ambil semua descriptor berdasarkan ID Pegawai
     */
    public function getDescriptorsByPegawai($id_pegawai)
    {
        return $this->where('id_pegawai', $id_pegawai)
                    ->where('deleted_at', null)
                    ->findAll();
    }

    /**
     * Ambil semua descriptor aktif untuk recognition
     */
    public function getAllActiveDescriptors()
    {
        return $this->select('face_descriptors.*, pegawai.nama, pegawai.nomor_induk')
                    ->join('pegawai', 'pegawai.id = face_descriptors.id_pegawai')
                    ->where('face_descriptors.deleted_at', null)
                    ->where('pegawai.deleted_at', null)
                    ->findAll();
    }

    /**
     * Hapus descriptor berdasarkan ID
     */
    public function deleteDescriptor($id)
    {
        return $this->delete($id);
    }

    /**
     * Update label descriptor
     */
    public function updateLabel($id, $label)
    {
        return $this->update($id, ['label' => $label]);
    }
}