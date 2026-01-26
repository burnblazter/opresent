<?php

namespace App\Models;

use CodeIgniter\Model;

class HariLiburModel extends Model
{
    protected $table            = 'hari_libur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['tanggal', 'keterangan', 'source', 'approved'];
    protected $useTimestamps    = true;

    // Helper untuk mengecek apakah tanggal tertentu adalah libur
    public function cekHariLibur($tanggal)
    {
        return $this->where('tanggal', $tanggal)
                    ->where('approved', 1)
                    ->first();
    }

    // Ambil data yang belum diapprove
    public function getPendingApproval()
    {
        return $this->where('approved', 0)->orderBy('tanggal', 'ASC')->findAll();
    }

    // Ambil data yang sudah diapprove
    public function getApproved()
    {
        return $this->where('approved', 1)->orderBy('tanggal', 'DESC')->findAll();
    }
}