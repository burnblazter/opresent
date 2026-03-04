<?php
// \app\Views\lokasi_presensi\tambah.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<link href="<?= base_url('assets/css/flatpickr.min.css') ?>" rel="stylesheet" />

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <form action="<?= base_url('/lokasi-presensi/store') ?>" method="post">
      <?= csrf_field() ?>
      <div class="row row-deck row-cards align-items-stretch">
        <div class="col-lg-6 col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Nama Lokasi</label>
                <input name="nama_lokasi" type="text"
                  class="form-control <?= validation_show_error('nama_lokasi') ? 'is-invalid' : '' ?>"
                  placeholder="e.g. Outlet Semarang" value="<?= old('nama_lokasi') ?>">
                <?php if (validation_show_error('nama_lokasi')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('nama_lokasi') ?>
                </div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Alamat Lokasi</label>
                <input name="alamat_lokasi" type="text"
                  class="form-control <?= validation_show_error('alamat_lokasi') ? 'is-invalid' : '' ?>"
                  placeholder="e.g. Jalan Semangka Nomor 5" value="<?= old('alamat_lokasi') ?>">
                <?php if (validation_show_error('alamat_lokasi')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('alamat_lokasi') ?>
                </div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Tipe Lokasi</label>
                <select name="tipe_lokasi" type="text"
                  class="form-select <?= validation_show_error('tipe_lokasi') ? 'is-invalid' : '' ?>" id="select-users">
                  <option value="">---Pilih Tipe Lokasi---</option>
                  <option value="Pusat" <?= old('tipe_lokasi') === 'Pusat' ? 'selected' : '' ?>>Pusat</option>
                  <option value="Cabang" <?= old('tipe_lokasi') === 'Cabang' ? 'selected' : '' ?>>Cabang</option>
                </select>
                <?php if (validation_show_error('tipe_lokasi')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('tipe_lokasi') ?>
                </div>
                <?php endif; ?>
              </div>

              <!-- MAP PICKER -->
              <div class="mb-3">
                <label class="form-label d-flex align-items-center">
                  Pilih Lokasi di Peta
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle ms-2" data-bs-toggle="tooltip"
                    data-bs-html="true" title="Klik pada peta untuk memilih lokasi" data-bs-placement="top">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M12 9h.01" />
                    <path d="M11 12h1v4h1" />
                  </svg>
                </label>
                <div class="input-group mb-2">
                  <input type="text" id="map-search" class="form-control"
                    placeholder="Cari lokasi... (e.g. SMA Negeri 1 Balikpapan)" />
                  <button class="btn btn-outline-secondary" type="button" id="btn-search-map">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2">
                      <circle cx="11" cy="11" r="8" />
                      <path d="m21 21-4.35-4.35" />
                    </svg>
                    Cari
                  </button>
                </div>
                <div id="search-results" class="list-group mb-2"
                  style="display:none; max-height: 200px; overflow-y:auto;"></div>
                <div id="map" style="height: 350px; border-radius: 8px;"></div>
                <small class="text-muted">Klik pada peta untuk menentukan koordinat lokasi, atau isi manual di
                  bawah</small>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Latitude</label>
                  <input id="latitude" name="latitude" type="text"
                    class="form-control <?= validation_show_error('latitude') ? 'is-invalid' : '' ?>"
                    placeholder="e.g. -1.2379" value="<?= old('latitude', '-1.2379') ?>">
                  <?php if (validation_show_error('latitude')) : ?>
                  <div class="invalid-feedback">
                    <?= validation_show_error('latitude') ?>
                  </div>
                  <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Longitude</label>
                  <input id="longitude" name="longitude" type="text"
                    class="form-control <?= validation_show_error('longitude') ? 'is-invalid' : '' ?>"
                    placeholder="e.g. 116.8289" value="<?= old('longitude', '116.8289') ?>">
                  <?php if (validation_show_error('longitude')) : ?>
                  <div class="invalid-feedback">
                    <?= validation_show_error('longitude') ?>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Radius (meter)</label>
                <input id="radius" name="radius" type="number"
                  class="form-control <?= validation_show_error('radius') ? 'is-invalid' : '' ?>" placeholder="e.g. 100"
                  value="<?= old('radius', '100') ?>">
                <?php if (validation_show_error('radius')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('radius') ?>
                </div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label d-flex align-items-center">
                  Zona Waktu
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle ms-2" data-bs-toggle="tooltip"
                    data-bs-html="true" title="Jika kota tidak tersedia, pilih zona waktu yang sama."
                    data-bs-placement="top">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M12 9h.01" />
                    <path d="M11 12h1v4h1" />
                  </svg>
                </label>
                <select name="zona_waktu" id="zona_waktu" type="text"
                  class="form-select <?= validation_show_error('zona_waktu') ? 'is-invalid' : '' ?>">
                  <option value="">---Pilih Zona Waktu---</option>
                  <?php foreach (timezone_identifiers_list() as $timezone) { ?>
                  <option value="<?= $timezone; ?>" <?= old('zona_waktu') === $timezone ? 'selected' : '' ?>>
                    <?= $timezone; ?></option>
                  <?php } ?>
                </select>
                <?php if (validation_show_error('zona_waktu')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('zona_waktu') ?>
                </div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label d-flex align-items-center">
                  Jam Masuk
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle ms-2" data-bs-toggle="tooltip"
                    data-bs-html="true"
                    title="Gunakan format 24 jam<br>Contoh: 07:00 untuk jam 7 pagi, 15:00 untuk jam 3 sore"
                    data-bs-placement="top">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M12 9h.01" />
                    <path d="M11 12h1v4h1" />
                  </svg>
                </label>
                <input id="jam_masuk" name="jam_masuk" type="text"
                  class="form-control <?= validation_show_error('jam_masuk') ? 'is-invalid' : '' ?>"
                  value="<?= old('jam_masuk') ?>" placeholder="07:00">
                <small class="form-hint">Format 24 jam. Contoh: 07:00 (pagi) atau 19:00 (malam)</small>
                <?php if (validation_show_error('jam_masuk')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('jam_masuk') ?>
                </div>
                <?php endif; ?>
              </div>

              <div class="mb-3">
                <label class="form-label d-flex align-items-center">
                  Jam Pulang
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle ms-2" data-bs-toggle="tooltip"
                    data-bs-html="true"
                    title="Gunakan format 24 jam<br>Contoh: 15:00 untuk jam 3 sore, 17:00 untuk jam 5 sore"
                    data-bs-placement="top">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M12 9h.01" />
                    <path d="M11 12h1v4h1" />
                  </svg>
                </label>
                <input id="jam_pulang" name="jam_pulang" type="text"
                  class="form-control <?= validation_show_error('jam_pulang') ? 'is-invalid' : '' ?>"
                  value="<?= old('jam_pulang') ?>" placeholder="17:00">
                <small class="form-hint">Format 24 jam. Contoh: 15:00 (jam 3 sore) atau 17:00 (jam 5 sore)</small>
                <?php if (validation_show_error('jam_pulang')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('jam_pulang') ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="card-footer text-end">
              <div class="d-flex">
                <a href="<?= base_url('lokasi-presensi') ?>" class="btn btn-link">Batal</a>
                <button type="submit" class="btn btn-primary ms-auto" style="width: fit-content;">Tambah Lokasi</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Flatpickr JS -->
<script src="<?= base_url('assets/js/flatpickr.min.js') ?>"></script>

<script>
$(document).ready(function() {
  // 1. Init Select2
  $('#zona_waktu').select2({
    placeholder: "---Pilih Zona Waktu---",
    allowClear: false,
    width: '100%',
  });

  // 2. Data Lokasi Awal (nilai default untuk halaman tambah)
  var defaultLat = <?= old('latitude', '-1.2379') ?>;
  var defaultLng = <?= old('longitude', '116.8289') ?>;
  var defaultRadius = <?= old('radius', '100') ?>;

  // 3. Init Map Container
  var map = L.map('map').setView([defaultLat, defaultLng], 15);
  var currentTileLayer = null;

  function updateMapTheme() {
    const isDark = document.documentElement.getAttribute('data-darkreader-scheme') === 'dark' ||
      localStorage.getItem('theme-preference') === 'dark';

    const tileUrl = isDark ?
      'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' :
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

    if (currentTileLayer) {
      map.removeLayer(currentTileLayer);
    }

    currentTileLayer = L.tileLayer(tileUrl, {
      attribution: '&copy; OpenStreetMap &copy; CARTO',
      maxZoom: 19
    }).addTo(map);
  }

  updateMapTheme();
  $(document).on('click', '#enable-dark-mode, #enable-light-mode', function() {
    setTimeout(updateMapTheme, 100);
  });

  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === "data-darkreader-scheme") {
        updateMapTheme();
      }
    });
  });
  observer.observe(document.documentElement, {
    attributes: true
  });

  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: '<?= base_url('assets/img/leaflet/marker-icon-2x.png') ?>',
    iconUrl: '<?= base_url('assets/img/leaflet/marker-icon.png') ?>',
    shadowUrl: '<?= base_url('assets/img/leaflet/marker-shadow.png') ?>',
  });

  // 4. Buat marker dan circle
  var marker = L.marker([defaultLat, defaultLng], {
    draggable: true
  }).addTo(map);

  var circle = L.circle([defaultLat, defaultLng], {
    color: '#206bc4',
    fillColor: '#206bc4',
    fillOpacity: 0.2,
    radius: defaultRadius
  }).addTo(map);

  // 5. Event saat marker di-drag
  marker.on('dragend', function(e) {
    var pos = marker.getLatLng();
    circle.setLatLng(pos);
    updateCoordinates(pos.lat, pos.lng);
  });

  // 6. Event saat klik peta
  map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    circle.setLatLng(e.latlng);
    updateCoordinates(e.latlng.lat, e.latlng.lng);
  });

  // 7. Update input koordinat
  function updateCoordinates(lat, lng) {
    $('#latitude').val(lat.toFixed(7));
    $('#longitude').val(lng.toFixed(7));
  }

  // 8. Update peta saat input koordinat diubah manual
  $('#latitude, #longitude').on('change', function() {
    var lat = parseFloat($('#latitude').val());
    var lng = parseFloat($('#longitude').val());
    if (!isNaN(lat) && !isNaN(lng)) {
      marker.setLatLng([lat, lng]);
      circle.setLatLng([lat, lng]);
      map.setView([lat, lng], 15);
    }
  });

  // 9. Update radius circle saat input radius diubah
  $('#radius').on('input', function() {
    var r = parseFloat($(this).val());
    if (!isNaN(r) && r > 0) {
      circle.setRadius(r);
    }
  });

  // ====================
  // FLATPICKR TIME PICKER
  // ====================

  flatpickr("#jam_masuk", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultHour: 7,
    defaultMinute: 0,
    minuteIncrement: 1,
    allowInput: true
  });

  flatpickr("#jam_pulang", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultHour: 17,
    defaultMinute: 0,
    minuteIncrement: 1,
    allowInput: true
  });

  // ====================
  // MAP SEARCH
  // ====================
  function doMapSearch() {
    var query = $('#map-search').val().trim();
    if (!query) return;

    $('#btn-search-map').prop('disabled', true).text('Mencari...');
    $('#search-results').hide().empty();

    $.getJSON('https://nominatim.openstreetmap.org/search', {
        q: query,
        format: 'json',
        limit: 5,
        addressdetails: 1
      })
      .done(function(results) {
        if (results.length === 0) {
          $('#search-results')
            .append('<div class="list-group-item text-muted">Lokasi tidak ditemukan</div>')
            .show();
          return;
        }

        results.forEach(function(place) {
          var $item = $('<button type="button" class="list-group-item list-group-item-action"></button>')
            .text(place.display_name)
            .on('click', function() {
              var lat = parseFloat(place.lat);
              var lng = parseFloat(place.lon);
              marker.setLatLng([lat, lng]);
              circle.setLatLng([lat, lng]);
              map.setView([lat, lng], 16);
              updateCoordinates(lat, lng);

              $('#search-results').hide().empty();
              $('#map-search').val(place.display_name.split(',').slice(0, 2).join(','));
            });
          $('#search-results').append($item);
        });

        $('#search-results').show();
      })
      .fail(function() {
        alert('Gagal menghubungi layanan pencarian. Coba lagi.');
      })
      .always(function() {
        $('#btn-search-map').prop('disabled', false).html(
          '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg> Cari'
        );
      });
  }

  $('#btn-search-map').on('click', doMapSearch);
  $('#map-search').on('keypress', function(e) {
    if (e.which === 13) {
      e.preventDefault();
      doMapSearch();
    }
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest('#map-search, #search-results, #btn-search-map').length) {
      $('#search-results').hide();
    }
  });
});
</script>
<?= $this->endSection() ?>