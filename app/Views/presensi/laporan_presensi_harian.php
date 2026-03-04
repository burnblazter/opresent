<?php
// \app\Views\presensi\laporan_presensi_harian.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<div class="page-body">
  <div class="container-xl">
    <div class="card mb-3">
      <div class="card-body">
        <div class="row justify-content-between g-3 flex-column-reverse flex-lg-row align-items-end">
          <div class="col-lg-6 col-md-12">
            <form action="" method="get">
              <div class="row align-items-end g-1">
                <div class="col">
                  <label for="tanggal_filter" class="form-label">Pilih Tanggal</label>
                  <input type="date" name="tanggal_filter" id="tanggal_filter" class="form-control"
                    value="<?= $tanggal_filter ?>">
                </div>
                <div class="col">
                  <label for="filter_jabatan" class="form-label">Unit</label>
                  <select name="filter_jabatan" id="filter_jabatan" class="form-select">
                    <option value="">Semua Unit</option>
                    <?php foreach ($data_jabatan as $jabatan): ?>
                    <option value="<?= $jabatan->id ?>" <?= ($filter_jabatan == $jabatan->id) ? 'selected' : '' ?>>
                      <?= esc($jabatan->jabatan) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
              </div>
            </form>
          </div>
          <div class="col-lg-6 col-md-12 text-start text-lg-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                <path d="M8 11h8v7h-8z" />
                <path d="M8 15h8" />
                <path d="M11 11v7" />
              </svg>
              Export Excel
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-deck row-cards align-items-start">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Laporan Harian: <strong><?= $data_tanggal; ?></strong></h3>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="text-center sticky-top">
                  <tr>
                    <th>No</th>
                    <th>Nomor Induk</th>
                    <th>Nama Pegawai</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Foto Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Foto Pulang</th>
                    <th>Total Jam Kerja</th>
                    <th>Keterlambatan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($data_presensi)) : ?>
                  <?php $nomor = 1 + ($perPage * ($currentPage - 1)); ?>
                  <?php foreach ($data_presensi as $data) : ?>
                  <?php
                                            // Inisialisasi variabel default agar tidak error jika Alpha
                                            $total_jam_kerja_format = '-';
                                            $total_jam_keterlambatan_format = '-';
                                            $jam_masuk_tampil = '-';
                                            $jam_keluar_tampil = '-';
                                            $foto_masuk_tampil = '-';
                                            $foto_keluar_tampil = '-';
                                            
                                            // Badge Status
                                            if ($data->status_kehadiran == 'Hadir') {
                                                $badge_status = '<span class="badge bg-success">Hadir</span>';
                                            } elseif ($data->status_kehadiran == 'Sakit') {
                                                $badge_status = '<span class="badge bg-warning">Sakit</span>';
                                            } elseif ($data->status_kehadiran == 'Izin') {
                                                $badge_status = '<span class="badge bg-info">Izin</span>';
                                            } elseif ($data->status_kehadiran == 'Libur') {
                                                $badge_status = '<span class="badge bg-secondary">Libur</span>';
                                            } else {
                                                $badge_status = '<span class="badge bg-danger">Alpha</span>';
                                            }

                                            // LOGIKA HITUNG HANYA JIKA HADIR (Jam Masuk Ada)
                                            if ($data->jam_masuk) {
                                                $jam_masuk_tampil = $data->jam_masuk;
                                                $jam_keluar_tampil = $data->jam_keluar;
                                                
                                                // Foto
                                                $foto_masuk_tampil = '<a href="' . base_url('assets/img/foto_presensi/masuk/' . $data->foto_masuk) . '" target="_blank" class="text-primary">Foto</a>';
                                                
                                                if($data->jam_keluar != '00:00:00' && $data->foto_keluar != '-') {
                                                     $foto_keluar_tampil = '<a href="' . base_url('assets/img/foto_presensi/keluar/' . $data->foto_keluar) . '" target="_blank" class="text-primary">Foto</a>';
                                                }

                                                // Hitung Jam Kerja
                                                if ($data->jam_keluar != '00:00:00') {
                                                    $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($data->tanggal_masuk . ' ' . $data->jam_masuk));
                                                    $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($data->tanggal_keluar . ' ' . $data->jam_keluar));
                                                    
                                                    $timestamp_masuk = strtotime($jam_tanggal_masuk);
                                                    $timestamp_keluar = strtotime($jam_tanggal_keluar);
                                                    $selisih = $timestamp_keluar - $timestamp_masuk;
                                                    
                                                    $total_jam_kerja = floor($selisih / 3600);
                                                    $selisih_menit_kerja = floor(($selisih % 3600) / 60);
                                                    $total_jam_kerja_format = sprintf("%d Jam %d Menit", $total_jam_kerja, $selisih_menit_kerja);
                                                } else {
                                                    $total_jam_kerja_format = '<span class="text-muted">Belum Pulang</span>';
                                                }

                                                // Hitung Keterlambatan
                                                $timestamp_jam_masuk_real = strtotime(date('H:i:s', strtotime($data->jam_masuk)));
                                                $timestamp_jam_masuk_kantor = strtotime($data->jam_masuk_kantor);
                                                $terlambat = $timestamp_jam_masuk_real - $timestamp_jam_masuk_kantor;
                                                
                                                if ($terlambat > 0) {
                                                    $total_jam_keterlambatan = floor($terlambat / 3600);
                                                    $selisih_menit_keterlambatan = floor(($terlambat % 3600) / 60);
                                                    $total_jam_keterlambatan_format = '<span class="text-danger">' . sprintf("%d Jam %d Menit", $total_jam_keterlambatan, $selisih_menit_keterlambatan) . '</span>';
                                                } else {
                                                    $total_jam_keterlambatan_format = '<span class="badge bg-success">On Time</span>';
                                                }
                                            }
                                            ?>
                  <tr class="align-middle">
                    <td class="text-center"><?= $nomor++ ?></td>
                    <td class="text-center"><?= $data->nomor_induk ?></td>
                    <td><?= esc($data->nama) ?></td>
                    <td class="text-center"><?= esc($data->nama_jabatan ?? '-') ?></td>
                    <td class="text-center"><?= $badge_status ?></td>
                    <td class="text-center"><?= $jam_masuk_tampil ?></td>
                    <td class="text-center"><?= $foto_masuk_tampil ?></td>
                    <td class="text-center"><?= $jam_keluar_tampil ?></td>
                    <td class="text-center"><?= $foto_keluar_tampil ?></td>
                    <td class="text-center"><?= $total_jam_kerja_format ?></td>
                    <td class="text-center"><?= $total_jam_keterlambatan_format ?></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php else : ?>
                  <tr class="text-center">
                    <td colspan="10">Data tidak ditemukan.</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between">
            <p class="m-0 text-muted">Total Data: <span><?= $total ?></span></p>
            <?= $pager; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="exportModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export Laporan Harian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('laporan-presensi-harian/excel') ?>" method="POST"> <?= csrf_field() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Pilih Tanggal</label>
            <input type="date" class="form-control" name="tanggal_filter" value="<?= $tanggal_filter ?>" required>
          </div>
          <div class="mb-3">
            <label for="filter_jabatan_export" class="form-label">Filter Unit</label>
            <select name="filter_jabatan" id="filter_jabatan_export" class="form-select">
              <option value="">Semua Unit</option>
              <?php foreach ($data_jabatan as $jabatan): ?>
              <option value="<?= $jabatan->id ?>" <?= ($filter_jabatan == $jabatan->id) ? 'selected' : '' ?>>
                <?= esc($jabatan->jabatan) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="text-muted small">
            *Laporan akan berisi seluruh pengguna (Hadir, Alpha, & Libur).<br>
            *Data akan dikelompokkan per unit dalam sheet terpisah di Excel.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success ms-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M14 3v4a1 1 0 0 0 1 1h4" />
              <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
              <path d="M8 11h8v7h-8z" />
              <path d="M8 15h8" />
              <path d="M11 11v7" />
            </svg>
            Download Excel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>