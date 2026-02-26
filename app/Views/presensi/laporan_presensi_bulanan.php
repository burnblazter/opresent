<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<div class="page-body">
  <div class="container-xl">
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3 flex-column-reverse flex-lg-row align-items-end">
          <div class="col-lg-8">
            <form method="get">
              <div class="row align-items-end g-1">
                <div class="col">
                  <div class="row g-1">
                    <div class="col">
                      <select name="filter_bulan" class="form-select">
                        <option value="01" <?= $filter_bulan === '01' ? 'selected' : '' ?>>Januari</option>
                        <option value="02" <?= $filter_bulan === '02' ? 'selected' : '' ?>>Februari</option>
                        <option value="03" <?= $filter_bulan === '03' ? 'selected' : '' ?>>Maret</option>
                        <option value="04" <?= $filter_bulan === '04' ? 'selected' : '' ?>>April</option>
                        <option value="05" <?= $filter_bulan === '05' ? 'selected' : '' ?>>Mei</option>
                        <option value="06" <?= $filter_bulan === '06' ? 'selected' : '' ?>>Juni</option>
                        <option value="07" <?= $filter_bulan === '07' ? 'selected' : '' ?>>Juli</option>
                        <option value="08" <?= $filter_bulan === '08' ? 'selected' : '' ?>>Agustus</option>
                        <option value="09" <?= $filter_bulan === '09' ? 'selected' : '' ?>>September</option>
                        <option value="10" <?= $filter_bulan === '10' ? 'selected' : '' ?>>Oktober</option>
                        <option value="11" <?= $filter_bulan === '11' ? 'selected' : '' ?>>November</option>
                        <option value="12" <?= $filter_bulan === '12' ? 'selected' : '' ?>>Desember</option>
                      </select>
                    </div>
                    <div class="col">
                      <select name="filter_tahun" class="form-select filter_tahun" id="filter_tahun">
                      </select>
                    </div>
                    <div class="col">
                      <select name="filter_jabatan" id="filter_jabatan" class="form-select">
                        <option value="">Semua Unit</option>
                        <?php foreach ($data_jabatan as $jabatan): ?>
                        <option value="<?= $jabatan->id ?>" <?= ($filter_jabatan == $jabatan->id) ? 'selected' : '' ?>>
                          <?= esc($jabatan->jabatan) ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-outline-primary">Filter</button>
                </div>
              </div>
            </form>
          </div>
          <div class="col-lg-4 text-start text-lg-end">
            <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#exportModal">
              Export Excel
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row row-deck row-cards align-items-start">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title">Presensi Bulan <strong><?= date('F Y', strtotime($data_bulan)); ?></strong></h3>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="text-center sticky-top">
                  <tr>
                    <th>No</th>
                    <th>Nomor Induk</th>
                    <th>Nama</th>
                    <th>Unit</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Total Jam Kerja</th>
                    <th>Total Keterlambatan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($data_presensi)) : ?>
                  <?php $nomor = 1 + ($perPage * ($currentPage - 1)); ?>
                  <?php foreach ($data_presensi as $data) : ?>
                  <?php
                    // Inisialisasi Default (Kosong)
                    $jam_masuk_tampil = '-';
                    $jam_keluar_tampil = '-';
                    $total_jam_kerja_format = '-';
                    $total_jam_keterlambatan_format = '-';
                    $badge_status = '';

                    // Warna Badge Status
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

                    // Logika Perhitungan HANYA JIKA HADIR
                    if ($data->status_kehadiran == 'Hadir') {
                        $jam_masuk_tampil = $data->jam_masuk;
                        $jam_keluar_tampil = ($data->jam_keluar == '00:00:00') ? 'Belum Pulang' : $data->jam_keluar;

                        // Hitung Jam Kerja
                        if ($data->jam_keluar != '00:00:00') {
                            $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($data->tanggal_masuk . ' ' . $data->jam_masuk));
                            $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($data->tanggal_keluar . ' ' . $data->jam_keluar));
                            $selisih = strtotime($jam_tanggal_keluar) - strtotime($jam_tanggal_masuk);
                            
                            $jam = floor($selisih / 3600);
                            $menit = floor(($selisih % 3600) / 60);
                            $total_jam_kerja_format = sprintf("%d Jam %d Menit", $jam, $menit);
                        }

                        // Hitung Keterlambatan
                        if (isset($data->jam_masuk_kantor) && $data->jam_masuk_kantor) {
                            $jam_masuk_real = strtotime($data->jam_masuk); // Hanya jam (H:i:s)
                            $jam_jadwal = strtotime($data->jam_masuk_kantor); // Asumsi format H:i:s
                            // Jika format tanggal lengkap, ambil jamnya saja
                            $jam_masuk_real = strtotime(date('H:i:s', strtotime($data->jam_masuk)));

                            $terlambat = $jam_masuk_real - $jam_jadwal;
                            if ($terlambat > 0) {
                                $jam_lat = floor($terlambat / 3600);
                                $menit_lat = floor(($terlambat % 3600) / 60);
                                $total_jam_keterlambatan_format = '<span class="text-danger">' . sprintf("%d Jam %d Menit", $jam_lat, $menit_lat) . '</span>';
                            } else {
                                $total_jam_keterlambatan_format = '<span class="badge bg-success">On Time</span>';
                            }
                        }
                    }
                ?>
                  <tr class="align-middle">
                    <td class="text-center"><?= $nomor++ ?></td>
                    <td class="text-center"><?= $data->nomor_induk ?></td>
                    <td><?= $data->nama ?></td>
                    <td class="text-center"><?= esc($data->nama_jabatan ?? '-') ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($data->tanggal_masuk)) ?></td>
                    <td class="text-center"><?= $badge_status ?></td>
                    <td class="text-center"><?= $jam_masuk_tampil ?></td>
                    <td class="text-center"><?= $jam_keluar_tampil ?></td>
                    <td class="text-center"><?= $total_jam_kerja_format ?></td>
                    <td class="text-center"><?= $total_jam_keterlambatan_format ?></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php else : ?>
                  <tr class="text-center">
                    <td colspan="9">Belum ada data presensi.</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between">
            <p class="m-0 text-muted">Showing <span><?= ($perPage * ($currentPage - 1)) + 1 ?></span> to
              <span><?= min($perPage * $currentPage, $total) ?></span> of <span><?= $total ?></span> entries
            </p>
            <?= $pager; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Ambil elemen select
  var selectTahuns = document.getElementsByClassName('filter_tahun');
  for (var i = 0; i < selectTahuns.length; i++) {
    var selectTahun = selectTahuns[i];
    var tahunSekarang = new Date().getFullYear();
    for (var tahun = <?= $tahun_mulai ?>; tahun <= tahunSekarang; tahun++) {
      var option = document.createElement('option');
      option.value = tahun;
      option.text = tahun;
      if (tahun == <?= $filter_tahun ?>) {
        option.selected = true;
      }
      selectTahun.add(option);
    }
  }
});
</script>

<div class="modal" id="exportModal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export Laporan Presensi Bulanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('/laporan-presensi-bulanan/excel') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label for="filter_bulan">Bulan</label>
            <select name="filter_bulan" class="form-select">
              <option value="01" <?= $filter_bulan === '01' ? 'selected' : '' ?>>Januari</option>
              <option value="02" <?= $filter_bulan === '02' ? 'selected' : '' ?>>Februari</option>
              <option value="03" <?= $filter_bulan === '03' ? 'selected' : '' ?>>Maret</option>
              <option value="04" <?= $filter_bulan === '04' ? 'selected' : '' ?>>April</option>
              <option value="05" <?= $filter_bulan === '05' ? 'selected' : '' ?>>Mei</option>
              <option value="06" <?= $filter_bulan === '06' ? 'selected' : '' ?>>Juni</option>
              <option value="07" <?= $filter_bulan === '07' ? 'selected' : '' ?>>Juli</option>
              <option value="08" <?= $filter_bulan === '08' ? 'selected' : '' ?>>Agustus</option>
              <option value="09" <?= $filter_bulan === '09' ? 'selected' : '' ?>>September</option>
              <option value="10" <?= $filter_bulan === '10' ? 'selected' : '' ?>>Oktober</option>
              <option value="11" <?= $filter_bulan === '11' ? 'selected' : '' ?>>November</option>
              <option value="12" <?= $filter_bulan === '12' ? 'selected' : '' ?>>Desember</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="filter_tahun">Tahun</label>
            <input type="number" name="filter_tahun" class="form-control" value="<?= $filter_tahun ?>">
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
          <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>