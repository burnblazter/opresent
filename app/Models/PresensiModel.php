<?php
// \app\Models\PresensiModel.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Models;

use CodeIgniter\Model;

class PresensiModel extends Model
{
    protected $db, $builder;
    protected $table = 'presensi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_pegawai', 'tanggal_masuk', 'jam_masuk', 'foto_masuk', 'tanggal_keluar', 'jam_keluar', 'foto_keluar'];
    protected $useTimestamps = true;
    protected $returnType = 'object';

    public function __construct()
    {
        parent::__construct();    
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('presensi');
    }

    public function getDataPresensi($id_pegawai, $tanggal_dari = false, $tanggal_sampai = false, $print = false, $perPage = 10)
    {
        $pager = service('pager');
        $pager->setPath('rekap-presensi', 'rekap');

        $page = (@$_GET['page_rekap']) ? $_GET['page_rekap'] : 1;
        $offset = ($page - 1) * $perPage;

        $this->builder->select('presensi.*, pegawai.nomor_induk, pegawai.nama, pegawai.id_lokasi_presensi, lokasi_presensi.nama_lokasi as lokasi_presensi, lokasi_presensi.jam_masuk as jam_masuk_kantor');
        $this->builder->join('pegawai', 'pegawai.id = presensi.id_pegawai');
        $this->builder->join('lokasi_presensi', 'lokasi_presensi.id = pegawai.id_lokasi_presensi');
        $this->builder->where('presensi.id_pegawai', $id_pegawai);
        $this->builder->orderBy('presensi.tanggal_masuk', 'DESC');

        $total = 0;

        if ($tanggal_dari || $tanggal_sampai) {
            $this->builder->where('tanggal_masuk BETWEEN ' . "'" . $tanggal_dari . "'" . ' AND ' . "'" . $tanggal_sampai . "'");
        }

        $countQuery = clone $this->builder;
        $total = $countQuery->countAllResults();

        if ($print) {
            $result = $this->builder->get()->getResult();
        } else {
            $result = $this->builder->get($perPage, $offset)->getResult();
        }

        return [
            'rekap-presensi' => $result,
            'links' => $pager->makeLinks($page, $perPage, $total, 'my_pagination', 0, 'rekap'),
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
        ];
    }

    public function cekPresensiMasuk($id_pegawai, $tanggal_hari_ini, $hitung = false)
    {
        $condition = [
            'id_pegawai' => $id_pegawai,
            'tanggal_masuk' => $tanggal_hari_ini,
        ];

        if ($hitung) {
            return $this->where($condition)->countAllResults();
        } else {
            return $this->getWhere($condition)->getFirstRow();
        }
    }

    public function getDataPresensiHarian($tanggal_dari = false, $tanggal_sampai = false, $print = false, $perPage = 10)
    {
        $pager = service('pager');
        $pager->setPath('laporan-presensi-harian', 'harian');

        $page = (@$_GET['page_harian']) ? $_GET['page_harian'] : 1;
        $offset = ($page - 1) * $perPage;

        $this->builder = $this->db->table('presensi');
        $this->builder->select('presensi.*, pegawai.nomor_induk, pegawai.nama, pegawai.id_lokasi_presensi, lokasi_presensi.nama_lokasi as lokasi_presensi, lokasi_presensi.jam_masuk as jam_masuk_kantor');
        $this->builder->join('pegawai', 'pegawai.id = presensi.id_pegawai');
        $this->builder->join('lokasi_presensi', 'lokasi_presensi.id = pegawai.id_lokasi_presensi');
        $this->builder->orderBy('tanggal_masuk', 'DESC');

        $total = 0;
        $tanggal_sekarang = date('Y-m-d');

        if ($tanggal_dari || $tanggal_sampai) {
            $this->builder->where('presensi.tanggal_masuk BETWEEN ' . "'" . $tanggal_dari . "'" . ' AND ' . "'" . $tanggal_sampai . "'");
        } else {
            $this->builder->where('presensi.tanggal_masuk = ' . "'" . $tanggal_sekarang . "'");
        }

        $countQuery = clone $this->builder;
        $total = $countQuery->countAllResults();

        if ($print) {
            $result = $this->builder->get()->getResult();
        } else {
            $result = $this->builder->get($perPage, $offset)->getResult();
        }

        return [
            'laporan-harian' => $result,
            'links' => $pager->makeLinks($page, $perPage, $total, 'my_pagination', 0, 'harian'),
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
        ];
    }

    public function getDataPresensiBulanan($filter_bulan = false, $filter_tahun = false, $print = false, $perPage = 10)
    {
        $pager = service('pager');
        $pager->setPath('laporan-presensi-bulanan', 'bulanan');

        $page = (@$_GET['page_bulanan']) ? $_GET['page_bulanan'] : 1;
        $offset = ($page - 1) * $perPage;

        $this->builder = $this->db->table('presensi');
        $this->builder->select('presensi.*, pegawai.nomor_induk, pegawai.nama, pegawai.id_lokasi_presensi, lokasi_presensi.nama_lokasi as lokasi_presensi, lokasi_presensi.jam_masuk as jam_masuk_kantor');
        $this->builder->join('pegawai', 'pegawai.id = presensi.id_pegawai');
        $this->builder->join('lokasi_presensi', 'lokasi_presensi.id = pegawai.id_lokasi_presensi');
        $this->builder->orderBy('tanggal_masuk', 'DESC');

        $total = 0;
        $bulan_sekarang = date('Y-m');

        if ($filter_bulan || $filter_tahun) {
            $bulan_filter = $filter_tahun . '-' . $filter_bulan;
            $this->builder->where('DATE_FORMAT(presensi.tanggal_masuk, "%Y-%m") = ' . "'" . $bulan_filter . "'");
        } else {
            $this->builder->where('DATE_FORMAT(presensi.tanggal_masuk, "%Y-%m") = ' . "'" . $bulan_sekarang . "'");
        }

        $countQuery = clone $this->builder;
        $total = $countQuery->countAllResults();

        if ($print) {
            $result = $this->builder->get()->getResult();
        } else {
            $result = $this->builder->get($perPage, $offset)->getResult();
        }

        return [
            'laporan-bulanan' => $result,
            'links' => $pager->makeLinks($page, $perPage, $total, 'my_pagination', 0, 'bulanan'),
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
        ];
    }

    public function getMinYear()
    {
        $builder = $this->db->table('presensi');
        $builder->selectMin('YEAR(tanggal_masuk)', 'min_year');
        $query = $builder->get();

        $result = $query->getRow();

        return $result ? $result->min_year : null;
    }

    public function getMinDate($id_pegawai = false)
    {
        $builder = $this->db->table('presensi');

        if ($id_pegawai) {
            $builder->where('presensi.id_pegawai', $id_pegawai);
        }

        $builder->selectMin('tanggal_masuk', 'min_date');
        $query = $builder->get();

        $result = $query->getRow();

        return $result ? $result->min_date : null;
    }

    public function getDataPresensiHariIni()
    {
        $builder = $this->db->table('presensi');
        $builder->select('presensi.*');
        $query = $builder->where('tanggal_masuk', date('Y-m-d'))->get();
        return $query->getNumRows();
    }

    public function getLaporanHarianLengkap($tanggal, $filter_jabatan = false, $perPage = 10)
    {
        $pager = service('pager');
        $pager->setPath('laporan-presensi-harian', 'harian');
        $page = (@$_GET['page_harian']) ? $_GET['page_harian'] : 1;
        $offset = ($page - 1) * $perPage;
        $this->builder = $this->db->table('pegawai');
        
        // PERBAIKAN: Menambahkan presensi.tanggal_keluar pada select
        $this->builder->select('pegawai.id as id_pegawai_real, pegawai.nomor_induk, pegawai.nama, pegawai.id_lokasi_presensi, pegawai.id_jabatan,
                                lokasi_presensi.nama_lokasi as lokasi_presensi, lokasi_presensi.jam_masuk as jam_masuk_kantor,
                                presensi.id as id_presensi, presensi.tanggal_masuk, presensi.jam_masuk, presensi.tanggal_keluar, presensi.jam_keluar, presensi.foto_masuk, presensi.foto_keluar,
                                ketidakhadiran.tipe_ketidakhadiran,
                                jabatan.jabatan as nama_jabatan');
        
        $this->builder->join('lokasi_presensi', 'lokasi_presensi.id = pegawai.id_lokasi_presensi', 'left');
        $this->builder->join('jabatan', 'jabatan.id = pegawai.id_jabatan', 'left');
        
        // Join Presensi (Hadir)
        $this->builder->join('presensi', 'presensi.id_pegawai = pegawai.id AND presensi.tanggal_masuk = ' . "'" . $tanggal . "'", 'left');
        $this->builder->join('ketidakhadiran', "ketidakhadiran.id_pegawai = pegawai.id AND ketidakhadiran.status_pengajuan = 'APPROVED' AND '$tanggal' BETWEEN ketidakhadiran.tanggal_mulai AND ketidakhadiran.tanggal_berakhir", 'left');
        
        // Filter berdasarkan jabatan jika dipilih
        if ($filter_jabatan) {
            $this->builder->where('pegawai.id_jabatan', $filter_jabatan);
        }
        
        $this->builder->orderBy('jabatan.jabatan', 'ASC');
        $this->builder->orderBy('pegawai.nama', 'ASC');
        
        $total = $this->builder->countAllResults(false);
        $result = $this->builder->get($perPage, $offset)->getResult();
        
        return [
            'laporan-harian' => $result,
            'links' => $pager->makeLinks($page, $perPage, $total, 'my_pagination', 0, 'harian'),
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
        ];
    }

    // Update juga fungsi Excelnya
    public function getLaporanHarianLengkapNoPage($tanggal, $filter_jabatan = false)
    {
        $this->builder = $this->db->table('pegawai');
        $this->builder->select('pegawai.id as id_pegawai_real, pegawai.nomor_induk, pegawai.nama, pegawai.id_jabatan,
                                lokasi_presensi.jam_masuk as jam_masuk_kantor,
                                presensi.tanggal_masuk, presensi.jam_masuk, presensi.tanggal_keluar, presensi.jam_keluar, presensi.foto_masuk, presensi.foto_keluar,
                                ketidakhadiran.tipe_ketidakhadiran,
                                jabatan.jabatan as nama_jabatan');
                                
        $this->builder->join('lokasi_presensi', 'lokasi_presensi.id = pegawai.id_lokasi_presensi', 'left');
        $this->builder->join('jabatan', 'jabatan.id = pegawai.id_jabatan', 'left');
        $this->builder->join('presensi', 'presensi.id_pegawai = pegawai.id AND presensi.tanggal_masuk = ' . "'" . $tanggal . "'", 'left');
        $this->builder->join('ketidakhadiran', "ketidakhadiran.id_pegawai = pegawai.id AND ketidakhadiran.status_pengajuan = 'APPROVED' AND '$tanggal' BETWEEN ketidakhadiran.tanggal_mulai AND ketidakhadiran.tanggal_berakhir", 'left');
        
        if ($filter_jabatan) {
            $this->builder->where('pegawai.id_jabatan', $filter_jabatan);
        }
        
        $this->builder->orderBy('jabatan.jabatan', 'ASC');
        $this->builder->orderBy('pegawai.nama', 'ASC');
        
        return $this->builder->get()->getResult();
    }

    public function getPresensiByMonth($bulan, $tahun)
    {
        $builder = $this->db->table('presensi');

        return $builder
            ->select('presensi.*, pegawai.id as pegawai_id, lokasi_presensi.jam_masuk as jam_masuk_kantor')
            ->join('pegawai', 'pegawai.id = presensi.id_pegawai', 'left')
            ->join('lokasi_presensi', 'lokasi_presensi.id = pegawai.id_lokasi_presensi', 'left')
            ->where('MONTH(presensi.tanggal_masuk)', $bulan)
            ->where('YEAR(presensi.tanggal_masuk)', $tahun)
            ->get()
            ->getResultArray();
    }
}