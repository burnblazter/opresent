<?php
// \app\Views\lokasi_presensi\index.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="card mb-3">
      <div class="card-body">
        <form action="" method="get">
          <div class="row justify-content-between g-3 flex-column-reverse flex-lg-row">
            <div class="col-lg-7 col-sm-12">
              <div class="row">
                <div class="col">
                  <div class="input-icon">
                    <span class="input-icon-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                        <path d="M21 21l-6 -6" />
                      </svg>
                    </span>
                    <input type="text" value="<?= $filter['keyword'] ?>" class="form-control"
                      placeholder="Temukan lokasi berdasarkan nama atau alamat" id="keyword" name="keyword" autofocus
                      autocomplete="off">
                  </div>
                </div>
                <div class="col-auto">
                  <span>
                    <a href="#" class="btn <?= $isFiltered === true ? 'btn-cyan' : 'btn-outline-cyan' ?>"
                      data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false"
                      title="Filter Pencarian" data-bs-toggle="tooltip">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter-search m-0"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                          d="M11.36 20.213l-2.36 .787v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414" />
                        <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                        <path d="M20.2 20.2l1.8 1.8" />
                      </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow" style="min-width: 300px;">
                      <h3 class="dropdown-header">Filters</h3>
                      <div class="m-3 mt-1">
                        <label for="tipe" class="form-label d-block">Tipe</label>
                        <div class="row g-1 justify-content-evenly w-100">
                          <div class="col-md-12">
                            <select name="tipe" id="tipe" class="form-select">
                              <option value="" <?= ($filter['tipe'] == '') ? 'selected' : '' ?>>Tampilkan Semua</option>
                              <option value="Pusat" <?= ($filter['tipe'] == 'Pusat') ? 'selected' : '' ?>>Pusat</option>
                              <option value="Cabang" <?= ($filter['tipe'] == 'Cabang') ? 'selected' : '' ?>>Cabang
                              </option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="m-3">
                        <label for="waktu" class="form-label d-block">Zona Waktu</label>
                        <div class="row g-1 justify-content-evenly w-100">
                          <div class="col-md-12">
                            <select name="waktu" id="waktu" class="form-select">
                              <option value="" <?= ($filter['waktu'] == '') ? 'selected' : '' ?>>Tampilkan Semua
                              </option>
                              <option value="WIB" <?= ($filter['waktu'] == 'WIB') ? 'selected' : '' ?>>WIB</option>
                              <option value="WITA" <?= ($filter['waktu'] == 'WITA') ? 'selected' : '' ?>>WITA</option>
                              <option value="WIT" <?= ($filter['waktu'] == 'WIT') ? 'selected' : '' ?>>WIT</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="m-3 mt-5">
                        <div class="d-flex">
                          <a href="<?= base_url('lokasi-presensi') ?>" class="btn btn-link">Hapus Filter</a>
                          <button type="submit" class="btn btn-primary ms-auto">Terapkan</button>
                        </div>
                      </div>
                    </div>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-lg-5 col-md-12 text-start text-lg-end">
              <a href="<?= base_url('/tambah-lokasi-presensi') ?>" class="btn btn-primary d-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                  stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M12 5l0 14" />
                  <path d="M5 12l14 0" />
                </svg><span>Tambah Lokasi</span>
              </a>
              <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#exportModal">
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
                <span>Export Excel</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="row row-deck row-cards align-items-start">
      <div class="col-lg-12">
        <div class="card" id="data-lokasi">
          <div class="card-body">
            <h3 class="card-title">Data Lokasi Presensi</h3>
            <div class="table-responsive">
              <table class="table table-bordered">
                <tr class="text-center">
                  <th>No</th>
                  <th style="min-width: 170px;">Nama Lokasi</th>
                  <th style="min-width: 300px;">Alamat Lokasi</th>
                  <th>Zona Waktu</th>
                  <th>Tipe Lokasi</th>
                  <th style="min-width: 180px;">Aksi</th>
                </tr>
                <?php if (!empty($lokasi)) : ?>
                <?php $nomor = 1 + ($perPage * ($currentPage - 1)); ?>
                <?php foreach ($lokasi as $l) : ?>
                <tr>
                  <td class="text-center"><?= $nomor++ ?></td>
                  <td><a href="<?= base_url('/lokasi-presensi/' . $l->slug) ?>" class="d-flex align-items-start g-3"
                      style="gap: 1px;">
                      <?= $l->nama_lokasi ?>
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-up-right"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height:12px;">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M17 7l-10 10" />
                        <path d="M8 7l9 0l0 9" />
                      </svg>
                    </a>
                  </td>
                  <td><?= $l->alamat_lokasi ?></td>
                  <td class="text-center"><?= $l->zona_waktu ?></td>
                  <td class="text-center"><?= $l->tipe_lokasi ?></td>
                  <td class="text-center">
                    <a href="#" class="badge bg-info btn-preview-map" data-lat="<?= $l->latitude ?>"
                      data-lng="<?= $l->longitude ?>" data-name="<?= $l->nama_lokasi ?>" data-radius="<?= $l->radius ?>"
                      data-bs-toggle="modal" data-bs-target="#mapPreviewModal">
                      peta
                    </a>
                    <a href="<?= base_url('/lokasi-presensi/edit/' . $l->slug) ?>" class="badge bg-warning">
                      edit
                    </a>
                    <a href="#" class="badge bg-danger btn-hapus" data-bs-toggle="modal" data-bs-target="#modal-danger"
                      data-id="<?= $l->id ?>" data-name="<?= $l->nama_lokasi ?>">
                      hapus
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php else : ?>
                <tr class="text-center">
                  <td colspan="6">Belum ada data</td>
                </tr>
                <?php endif; ?>
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

<!-- Modal Box - Map Preview -->
<div class="modal modal-blur fade" id="mapPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapModalTitle">Preview Lokasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="previewMap" style="height: 400px; border-radius: 8px;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Box - Delete -->
<div class="modal modal-blur fade" id="modal-danger" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <div class="modal-body text-center py-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
          viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
          stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <path
            d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z" />
          <path d="M12 9v4" />
          <path d="M12 17h.01" />
        </svg>
        <h3>Hapus?</h3>
        <div class="text-muted">Apakah Anda yakin ingin menghapus lokasi <strong><span id="modal-name"
              class="text-danger">ini</span></strong>? Data lokasi yang sudah dihapus tidak dapat dikembalikan.</div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <div class="row">
            <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">
                Batal
              </a></div>
            <div class="col">
              <form action="" method="post" class="d-inline" id="form-hapus">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-danger w-100">
                  Hapus
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Export Excel Modals -->
<div class="modal" id="exportModal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export Data Lokasi Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('/lokasi-presensi/excel') ?>" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="tipe" class="form-label d-block">Tipe</label>
            <div class="row g-1 justify-content-evenly w-100">
              <div class="col-md-12">
                <select name="tipe" id="tipe" class="form-select">
                  <option value="" <?= ($filter['tipe'] == '') ? 'selected' : '' ?>>Tampilkan Semua</option>
                  <option value="Pusat" <?= ($filter['tipe'] == 'Pusat') ? 'selected' : '' ?>>Pusat</option>
                  <option value="Cabang" <?= ($filter['tipe'] == 'Cabang') ? 'selected' : '' ?>>Cabang</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="waktu" class="form-label d-block">Zona Waktu</label>
            <div class="row g-1 justify-content-evenly w-100">
              <div class="col-md-12">
                <select name="waktu" id="waktu" class="form-select">
                  <option value="" <?= ($filter['waktu'] == '') ? 'selected' : '' ?>>Tampilkan Semua</option>
                  <option value="WIB" <?= ($filter['waktu'] == 'WIB') ? 'selected' : '' ?>>WIB</option>
                  <option value="WITA" <?= ($filter['waktu'] == 'WITA') ? 'selected' : '' ?>>WITA</option>
                  <option value="WIT" <?= ($filter['waktu'] == 'WIT') ? 'selected' : '' ?>>WIT</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// 1. Tambahkan variabel 'previewTileLayer' untuk menampung layer peta
var previewMap;
var previewMarker;
var previewCircle;
var previewTileLayer;

$(document).ready(function() {
  // --- Search Handler (Tetap) ---
  $('#keyword').on('keyup', function() {
    $.get('cari-lokasi?keyword=' + $('#keyword').val() + '&tipe=' + $('#tipe').val() + '&waktu=' + $('#waktu')
      .val(),
      function(data) {
        $('#data-lokasi').html(data);
      })
  })

  // --- Delete Handler (Tetap) ---
  $('body').on('click', '.btn-hapus', function(e) {
    e.preventDefault();
    var nama = $(this).data('name');
    var id = $(this).data('id');
    $('#modal-name').html(nama);
    $('#modal-danger').modal('show');
    $('#form-hapus').attr('action', '/lokasi-presensi/' + id);
  });

  // --- PREVIEW MAP HANDLER (DIPATCH) ---
  $('body').on('click', '.btn-preview-map', function(e) {
    e.preventDefault();
    var lat = parseFloat($(this).data('lat'));
    var lng = parseFloat($(this).data('lng'));
    var name = $(this).data('name');
    var radius = parseFloat($(this).data('radius'));

    $('#mapModalTitle').text('Preview Lokasi: ' + name);

    // 2. Cek Tema SAAT TOMBOL DIKLIK (Real-time check)
    var isDark = document.documentElement.getAttribute('data-darkreader-scheme') === 'dark' ||
      localStorage.getItem('theme-preference') === 'dark';

    var tileUrl = isDark ?
      'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' :
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

    // Initialize map on first open
    setTimeout(function() {
      if (!previewMap) {
        // --- A. INISIALISASI PERTAMA KALI ---
        previewMap = L.map('previewMap').setView([lat, lng], 15);

        // Pasang Tile Layer (Simpan ke variabel)
        previewTileLayer = L.tileLayer(tileUrl, {
          attribution: '© OpenStreetMap contributors &copy; CARTO',
          maxZoom: 19
        }).addTo(previewMap);

        // Icon Config
        delete L.Icon.Default.prototype._getIconUrl;
        L.Icon.Default.mergeOptions({
          iconRetinaUrl: '<?= base_url('assets/img/leaflet/marker-icon-2x.png') ?>',
          iconUrl: '<?= base_url('assets/img/leaflet/marker-icon.png') ?>',
          shadowUrl: '<?= base_url('assets/img/leaflet/marker-shadow.png') ?>',
        });

        previewMarker = L.marker([lat, lng]).addTo(previewMap);
        previewCircle = L.circle([lat, lng], {
          color: '#206bc4',
          fillColor: '#206bc4',
          fillOpacity: 0.2,
          radius: radius
        }).addTo(previewMap);

      } else {
        // --- B. UPDATE JIKA MAP SUDAH ADA ---
        previewMap.setView([lat, lng], 15);
        previewMarker.setLatLng([lat, lng]);
        previewCircle.setLatLng([lat, lng]);
        previewCircle.setRadius(radius);

        // Logic Tukar Layer (Penting agar tidak perlu refresh)
        // Hapus layer lama, pasang layer baru sesuai tema saat ini
        if (previewTileLayer) {
          previewMap.removeLayer(previewTileLayer);
        }
        previewTileLayer = L.tileLayer(tileUrl, {
          attribution: '© OpenStreetMap contributors &copy; CARTO',
          maxZoom: 19
        }).addTo(previewMap);
      }

      // Fix map display issue
      setTimeout(function() {
        previewMap.invalidateSize();
      }, 100);
    }, 200);
  });

  // Fix map on modal shown
  $('#mapPreviewModal').on('shown.bs.modal', function() {
    if (previewMap) {
      previewMap.invalidateSize();
    }
  });
})
</script>
<?= $this->endSection() ?>