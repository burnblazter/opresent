<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
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
                <input name="radius" type="number"
                  class="form-control <?= validation_show_error('radius') ? 'is-invalid' : '' ?>" placeholder="e.g. 100"
                  value="<?= old('radius') ?>">
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
                <label class="form-label">Jam Masuk</label>
                <input name="jam_masuk" type="time"
                  class="form-control <?= validation_show_error('jam_masuk') ? 'is-invalid' : '' ?>"
                  value="<?= old('jam_masuk') ?>">
                <?php if (validation_show_error('jam_masuk')) : ?>
                <div class="invalid-feedback">
                  <?= validation_show_error('jam_masuk') ?>
                </div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Jam Pulang</label>
                <input name="jam_pulang" type="time"
                  class="form-control <?= validation_show_error('jam_pulang') ? 'is-invalid' : '' ?>"
                  value="<?= old('jam_pulang') ?>">
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

<script>
$(document).ready(function() {
  $('#zona_waktu').select2({
    placeholder: "---Pilih Zona Waktu---",
    allowClear: false,
    width: '100%',
  });

  // Initialize Map - Default: Balikpapan, Indonesia
  var defaultLat = <?= old('latitude', '-1.2379') ?>;
  var defaultLng = <?= old('longitude', '116.8289') ?>;

  var map = L.map('map').setView([defaultLat, defaultLng], 13);

  // Add OpenStreetMap tiles
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(map);

  // Add marker
  var marker = L.marker([defaultLat, defaultLng], {
    draggable: true
  }).addTo(map);

  // Update coordinates when marker is dragged
  marker.on('dragend', function(e) {
    var position = marker.getLatLng();
    updateCoordinates(position.lat, position.lng);
  });

  // Update coordinates when map is clicked
  map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    updateCoordinates(e.latlng.lat, e.latlng.lng);
  });

  // Function to update input fields
  function updateCoordinates(lat, lng) {
    $('#latitude').val(lat.toFixed(7));
    $('#longitude').val(lng.toFixed(7));
  }

  // Update map when coordinates are manually entered
  $('#latitude, #longitude').on('change', function() {
    var lat = parseFloat($('#latitude').val());
    var lng = parseFloat($('#longitude').val());

    if (!isNaN(lat) && !isNaN(lng)) {
      marker.setLatLng([lat, lng]);
      map.setView([lat, lng], 13);
    }
  });

  // Get user's current location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var userLat = position.coords.latitude;
      var userLng = position.coords.longitude;

      // Only update if fields are empty or default
      if ($('#latitude').val() == defaultLat && $('#longitude').val() == defaultLng) {
        marker.setLatLng([userLat, userLng]);
        map.setView([userLat, userLng], 15);
        updateCoordinates(userLat, userLng);
      }
    });
  }
});
</script>
<?= $this->endSection() ?>