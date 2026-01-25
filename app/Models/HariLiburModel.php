<?php

namespace App\Models;

use CodeIgniter\Model;

class HariLiburModel extends Model
{
    protected $table            = 'hari_libur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['tanggal', 'keterangan'];
    protected $useTimestamps    = true;

    // Helper untuk mengecek apakah tanggal tertentu adalah libur
    public function cekHariLibur($tanggal)
    {
        return $this->where('tanggal', $tanggal)->first();
    }
}