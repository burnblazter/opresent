<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<style>
.parent_date {
  display: grid;
  grid-template-columns: repeat(5, auto);
  font-size: 20px;
  text-align: center;
  justify-content: center;
}

.parent_clock {
  display: grid;
  grid-template-columns: repeat(5, auto);
  font-size: 30px;
  font-weight: bold;
  text-align: center;
  justify-content: center;
}

.ai-joke-box {
  border-top: 1px dashed #e6e7e9;
  margin-top: 1.5rem;
  padding-top: 1rem;
  animation: fadeIn 1s;
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 15px;
}

.ai-prediction-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-top: 10px;
}

.ai-prediction-item {
  background: white;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #e9ecef;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.ai-prediction-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  color: #6c757d;
  font-weight: 600;
  margin-bottom: 4px;
}

.ai-prediction-value {
  font-size: 1.1rem;
  font-weight: bold;
  color: #1e3a8a;
}

.ai-prediction-emoji {
  font-size: 1.5rem;
  margin-bottom: 4px;
}

/* GPS Status Indicator */
.gps-status {
  margin: 15px 0;
  padding: 12px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s ease;
}

.gps-status-loading {
  background-color: #fff3cd;
  border: 1px solid #ffc107;
  color: #856404;
}

.gps-status-success {
  background-color: #d1e7dd;
  border: 1px solid #198754;
  color: #0f5132;
}

.gps-status-error {
  background-color: #f8d7da;
  border: 1px solid #dc3545;
  color: #842029;
}

.gps-status-warning {
  background-color: #fff3cd;
  border: 1px solid #ffc107;
  color: #856404;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #856404;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.btn-presensi:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.accuracy-indicator {
  font-size: 12px;
  margin-top: 5px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.accuracy-good {
  color: #198754;
}

.accuracy-medium {
  color: #ffc107;
}

.accuracy-poor {
  color: #dc3545;
}

/* Map Container */
.map-container {
  margin: 15px 0;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #dee2e6;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

#map-in,
#map-out {
  height: 250px;
  width: 100%;
}

.location-info {
  background-color: #f8f9fa;
  padding: 10px;
  border-radius: 6px;
  margin: 10px 0;
  font-size: 13px;
}

.location-info-item {
  display: flex;
  justify-content: space-between;
  padding: 5px 0;
  border-bottom: 1px dashed #dee2e6;
}

.location-info-item:last-child {
  border-bottom: none;
}

.location-info-label {
  font-weight: 600;
  color: #495057;
}

.location-info-value {
  color: #6c757d;
  font-family: monospace;
}

.distance-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin-top: 5px;
}

.distance-valid {
  background-color: #d1e7dd;
  color: #0f5132;
}

.distance-invalid {
  background-color: #f8d7da;
  color: #842029;
}

.map-legend {
  font-size: 11px;
  color: #6c757d;
  margin-top: 8px;
  padding: 8px;
  background-color: #fff;
  border-radius: 4px;
  border: 1px solid #dee2e6;
}

.legend-item {
  display: inline-flex;
  align-items: center;
  margin-right: 12px;
  gap: 4px;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}

.btn-toggle-map {
  font-size: 12px;
  padding: 4px 12px;
  margin-top: 8px;
}
</style>

<div class="page-body">
  <div class="container-xl">
    <div class="row align-items-stretch g-3">
      <div class="col-md-2"></div>

      <div class="col-md-4">
        <div class="card text-center" style="height: 100%;">
          <div class="card-header justify-content-center">
            <h3 class="mb-0">Presensi Masuk</h3>
          </div>
          <div class="card-body">

            <?php if ($status_ketidakhadiran === 1) : ?>
            <div class="text-warning text-xxl-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-exclamation-circle" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round" style="height: 96px; width: 96px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M12 9v4" />
                <path d="M12 16v.01" />
              </svg>
            </div>
            <h4 class="my-3">Izin/Sakit diterima. Get Well Soon!</h4>

            <?php elseif ($jumlah_presensi_masuk === 0) : ?>
            <div class="parent_date">
              <div id="tanggal_masuk"></div>
              <div class="ms-2"></div>
              <div id="bulan_masuk"></div>
              <div class="ms-2"></div>
              <div id="tahun_masuk"></div>
            </div>
            <div class="parent_clock mt-3">
              <div id="jam_masuk"></div>
              <div>:</div>
              <div id="menit_masuk"></div>
              <div>:</div>
              <div id="detik_masuk"></div>
            </div>

            <!-- GPS Status Indicator -->
            <div id="gps-status-in" class="gps-status gps-status-loading">
              <div class="spinner"></div>
              <span>Mendeteksi lokasi Anda...</span>
            </div>

            <!-- Location Info -->
            <div id="location-info-in" class="location-info" style="display: none;">
              <div class="location-info-item">
                <span class="location-info-label">📍 Lokasi Anda:</span>
                <span class="location-info-value" id="user-coords-in">-</span>
              </div>
              <div class="location-info-item">
                <span class="location-info-label">🏫 Lokasi Sekolah:</span>
                <span class="location-info-value"><?= esc($user_lokasi_presensi->latitude, 'html') ?>,
                  <?= esc($user_lokasi_presensi->longitude, 'html') ?></span>
              </div>
              <div class="location-info-item">
                <span class="location-info-label">📏 Jarak:</span>
                <span class="location-info-value" id="distance-in">Menghitung...</span>
              </div>
              <div class="location-info-item">
                <span class="location-info-label">⭕ Radius Presensi:</span>
                <span class="location-info-value"><?= esc($user_lokasi_presensi->radius, 'html') ?> meter</span>
              </div>
              <div id="distance-status-in"></div>

              <button class="btn btn-sm btn-outline-secondary btn-toggle-map" onclick="toggleMap('in')">
                <span id="map-toggle-text-in">🗺️ Sembunyikan Peta</span>
              </button>
            </div>

            <!-- Map Container -->
            <div id="map-container-in" class="map-container">
              <div id="map-in"></div>
              <div class="map-legend">
                <div class="legend-item">
                  <span class="legend-color" style="background-color: #dc3545;"></span>
                  <span>Lokasi Anda</span>
                </div>
                <div class="legend-item">
                  <span class="legend-color" style="background-color: #0d6efd;"></span>
                  <span>Lokasi Sekolah</span>
                </div>
                <div class="legend-item">
                  <span class="legend-color" style="background-color: #198754; opacity: 0.3;"></span>
                  <span>Radius Presensi</span>
                </div>
              </div>
            </div>

            <form action="<?= esc(base_url('/presensi-masuk'), 'attr') ?>" method="post" id="form-masuk">
              <?= csrf_field() ?>
              <input type="hidden" name="latitude_kantor" value="<?= esc($user_lokasi_presensi->latitude, 'attr') ?>"
                readonly>
              <input type="hidden" name="longitude_kantor" value="<?= esc($user_lokasi_presensi->longitude, 'attr') ?>"
                readonly>
              <input type="hidden" name="radius" value="<?= esc($user_lokasi_presensi->radius, 'attr') ?>" readonly>
              <input type="hidden" name="zona_waktu" value="<?= esc($user_lokasi_presensi->zona_waktu, 'attr') ?>"
                readonly>
              <input type="hidden" name="latitude_pegawai" id="latitude_pegawai_in" readonly>
              <input type="hidden" name="longitude_pegawai" id="longitude_pegawai_in" readonly>
              <input type="hidden" name="tanggal_masuk" id="tanggal_masuk_hidden" readonly>
              <input type="hidden" name="jam_masuk" id="jam_masuk_hidden" readonly>
              <button type="submit" class="btn btn-primary mt-3 btn-presensi" id="btn-masuk" disabled>
                <span class="btn-text">Menunggu GPS...</span>
              </button>
            </form>

            <?php else : ?>
            <div class="text-success text-xxl-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round" style="height: 96px; width: 96px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M9 12l2 2l4 -4" />
              </svg>
            </div>
            <h4 class="my-3">Presensi Masuk <span class="d-block text-success">Berhasil!</span></h4>

            <div id="ai-joke-container-in" class="ai-joke-box" style="display: none;">
              <div class="text-muted small text-uppercase fw-bold mb-2">🤖 Prediksi AI</div>
              <div id="ai-emoji-in" style="font-size: 2.5rem; line-height: 1;"></div>
              <p id="ai-message-in" class="mt-2 mb-2 text-dark fw-medium"></p>

              <!-- Grid untuk Usia dan Ekspresi -->
              <div class="ai-prediction-grid">
                <div class="ai-prediction-item">
                  <div class="ai-prediction-label">Prediksi Usia</div>
                  <div class="ai-prediction-value"><span id="ai-age-in">0</span> thn</div>
                </div>
                <div class="ai-prediction-item">
                  <div class="ai-prediction-label">Prediksi Ekspresi</div>
                  <div class="ai-prediction-emoji" id="ai-emotion-emoji-in">😐</div>
                  <div class="ai-prediction-value" id="ai-emotion-in" style="font-size: 0.85rem;">Netral</div>
                </div>
              </div>

              <div class="mt-3 border-top pt-2">
                <small class="text-muted fst-italic" style="font-size: 0.65rem; display:block; line-height: 1.3;">
                  ⚠️ <strong>Disclaimer:</strong> Prediksi usia & ekspresi cuma tebakan AI buat fun aja! Akurasi bisa
                  meleset jauh. Jangan baper ya! 😉
                </small>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card text-center" style="height: 100%;">
          <div class="card-header justify-content-center">
            <h3 class="mb-0">Presensi Pulang</h3>
          </div>
          <div class="card-body">

            <?php if ($status_ketidakhadiran != 0) : ?>
            <div class="text-warning text-xxl-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-exclamation-circle" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round" style="height: 96px; width: 96px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M12 9v4" />
                <path d="M12 16v.01" />
              </svg>
            </div>
            <h4 class="my-3">Kamu Izin/Sakit. Istirahat ya!</h4>

            <?php elseif ((strtotime(date('H:i:s')) >= strtotime($jam_pulang)) && ($jumlah_presensi_masuk === 0)) : ?>
            <div class="text-danger text-xxl-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round" style="height: 96px; width: 96px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M10 10l4 4m0 -4l-4 4" />
              </svg>
            </div>
            <h4 class="my-3">Ups! Kamu belum <span class="text-primary">Presensi Masuk</span> pagi ini.</h4>

            <?php elseif (strtotime(date('H:i:s')) < strtotime($jam_pulang)) : ?>
            <div class="text-danger text-xxl-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round" style="height: 96px; width: 96px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M10 10l4 4m0 -4l-4 4" />
              </svg>
            </div>
            <h4 class="my-3">Sabar... Belum waktunya <span class="d-block">Pulang Sekolah</span></h4>

            <?php elseif ($data_presensi_masuk->tanggal_masuk !== '0000-00-00' && $data_presensi_masuk->tanggal_keluar !== '0000-00-00') : ?>
            <div class="text-success text-xxl-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round" style="height: 96px; width: 96px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M9 12l2 2l4 -4" />
              </svg>
            </div>
            <h4 class="my-3">Presensi Pulang <span class="d-block text-success">Berhasil!</span></h4>

            <div id="ai-joke-container-out" class="ai-joke-box" style="display: none;">
              <div class="text-muted small text-uppercase fw-bold mb-2">🤖 Prediksi AI</div>
              <div id="ai-emoji-out" style="font-size: 2.5rem; line-height: 1;"></div>
              <p id="ai-message-out" class="mt-2 mb-2 text-dark fw-medium"></p>

              <!-- Grid untuk Usia dan Ekspresi -->
              <div class="ai-prediction-grid">
                <div class="ai-prediction-item">
                  <div class="ai-prediction-label">Prediksi Usia</div>
                  <div class="ai-prediction-value"><span id="ai-age-out">0</span> thn</div>
                </div>
                <div class="ai-prediction-item">
                  <div class="ai-prediction-label">Prediksi Ekspresi</div>
                  <div class="ai-prediction-emoji" id="ai-emotion-emoji-out">😐</div>
                  <div class="ai-prediction-value" id="ai-emotion-out" style="font-size: 0.85rem;">Netral</div>
                </div>
              </div>

              <div class="mt-3 border-top pt-2">
                <small class="text-muted fst-italic" style="font-size: 0.65rem; display:block; line-height: 1.3;">
                  ⚠️ <strong>Disclaimer:</strong> Prediksi AI ini cuma untuk hiburan! Bisa salah total. Have fun! ✌️
                </small>
              </div>
            </div>
            <?php else : ?>
            <div class="parent_date">
              <div id="tanggal_keluar"></div>
              <div class="ms-2"></div>
              <div id="bulan_keluar"></div>
              <div class="ms-2"></div>
              <div id="tahun_keluar"></div>
            </div>
            <div class="parent_clock mt-3">
              <div id="jam_keluar"></div>
              <div>:</div>
              <div id="menit_keluar"></div>
              <div>:</div>
              <div id="detik_keluar"></div>
            </div>

            <!-- GPS Status Indicator -->
            <div id="gps-status-out" class="gps-status gps-status-loading">
              <div class="spinner"></div>
              <span>Mendeteksi lokasi Anda...</span>
            </div>

            <!-- Location Info -->
            <div id="location-info-out" class="location-info" style="display: none;">
              <div class="location-info-item">
                <span class="location-info-label">📍 Lokasi Anda:</span>
                <span class="location-info-value" id="user-coords-out">-</span>
              </div>
              <div class="location-info-item">
                <span class="location-info-label">🏫 Lokasi Sekolah:</span>
                <span class="location-info-value"><?= esc($user_lokasi_presensi->latitude, 'html') ?>,
                  <?= esc($user_lokasi_presensi->longitude, 'html') ?></span>
              </div>
              <div class="location-info-item">
                <span class="location-info-label">📏 Jarak:</span>
                <span class="location-info-value" id="distance-out">Menghitung...</span>
              </div>
              <div class="location-info-item">
                <span class="location-info-label">⭕ Radius Presensi:</span>
                <span class="location-info-value"><?= esc($user_lokasi_presensi->radius, 'html') ?> meter</span>
              </div>
              <div id="distance-status-out"></div>

              <button class="btn btn-sm btn-outline-secondary btn-toggle-map" onclick="toggleMap('out')">
                <span id="map-toggle-text-out">🗺️ Sembunyikan Peta</span>
              </button>
            </div>

            <!-- Map Container -->
            <div id="map-container-out" class="map-container">
              <div id="map-out"></div>
              <div class="map-legend">
                <div class="legend-item">
                  <span class="legend-color" style="background-color: #dc3545;"></span>
                  <span>Lokasi Anda</span>
                </div>
                <div class="legend-item">
                  <span class="legend-color" style="background-color: #0d6efd;"></span>
                  <span>Lokasi Sekolah</span>
                </div>
                <div class="legend-item">
                  <span class="legend-color" style="background-color: #198754; opacity: 0.3;"></span>
                  <span>Radius Presensi</span>
                </div>
              </div>
            </div>

            <form action="<?= esc(base_url('/presensi-keluar'), 'attr') ?>" method="post" id="form-keluar">
              <?= csrf_field() ?>
              <input type="hidden" name="latitude_kantor" value="<?= esc($user_lokasi_presensi->latitude, 'attr') ?>"
                readonly>
              <input type="hidden" name="longitude_kantor" value="<?= esc($user_lokasi_presensi->longitude, 'attr') ?>"
                readonly>
              <input type="hidden" name="radius" value="<?= esc($user_lokasi_presensi->radius, 'attr') ?>" readonly>
              <input type="hidden" name="zona_waktu" value="<?= esc($user_lokasi_presensi->zona_waktu, 'attr') ?>"
                readonly>
              <input type="hidden" name="latitude_pegawai" id="latitude_pegawai_out" readonly>
              <input type="hidden" name="longitude_pegawai" id="longitude_pegawai_out" readonly>
              <input type="hidden" name="tanggal_keluar" id="tanggal_keluar_hidden" readonly>
              <input type="hidden" name="jam_keluar" id="jam_keluar_hidden" readonly>
              <input type="hidden" name="id_presensi" value="<?= esc($data_presensi_masuk->id, 'attr') ?>" readonly>
              <button class="btn btn-primary mt-3 bg-red btn-presensi" type="submit" id="btn-keluar" disabled>
                <span class="btn-text">Menunggu GPS...</span>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-2"></div>
    </div>
  </div>
</div>

<script>
const serverTime = parseInt(<?= json_encode($server_time, JSON_NUMERIC_CHECK) ?>) || Math.floor(Date.now() / 1000);
const currentDate = <?= json_encode(date('Y-m-d')) ?>;

const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober",
  "November", "Desember"
];

const officeLocation = {
  lat: parseFloat(<?= json_encode($user_lokasi_presensi->latitude) ?>),
  lng: parseFloat(<?= json_encode($user_lokasi_presensi->longitude) ?>),
  radius: parseFloat(<?= json_encode($user_lokasi_presensi->radius) ?>)
};

const gpsState = {
  in: {
    isDetecting: false,
    isReady: false,
    accuracy: null,
    attempts: 0,
    maxAttempts: 3,
    map: null,
    tileLayer: null,
    userMarker: null,
    officeMarker: null,
    radiusCircle: null,
    currentLat: null,
    currentLng: null
  },
  out: {
    isDetecting: false,
    isReady: false,
    accuracy: null,
    attempts: 0,
    maxAttempts: 3,
    map: null,
    tileLayer: null,
    userMarker: null,
    officeMarker: null,
    radiusCircle: null,
    currentLat: null,
    currentLng: null
  }
};

function sanitizeHTML(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function sanitizeText(text) {
  if (typeof text !== 'string') return '';
  return text.replace(/[<>\"']/g, '');
}

function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371000;
  const φ1 = lat1 * Math.PI / 180;
  const φ2 = lat2 * Math.PI / 180;
  const Δφ = (lat2 - lat1) * Math.PI / 180;
  const Δλ = (lon2 - lon1) * Math.PI / 180;

  const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
    Math.cos(φ1) * Math.cos(φ2) *
    Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return R * c;
}

function toggleMap(type) {
  const mapContainer = document.getElementById(`map-container-${type}`);
  const toggleText = document.getElementById(`map-toggle-text-${type}`);

  if (mapContainer.style.display === 'none' || !mapContainer.style.display) {
    mapContainer.style.display = 'block';
    toggleText.textContent = '🗺️ Sembunyikan Peta';

    if (!gpsState[type].map) {
      initializeMap(type);
    } else {
      gpsState[type].map.invalidateSize();
    }
  } else {
    mapContainer.style.display = 'none';
    toggleText.textContent = '📍 Lihat Peta';
  }
}

function getTileLayerUrl() {
  const isDark = document.documentElement.getAttribute('data-darkreader-scheme') === 'dark' ||
    localStorage.getItem('theme-preference') === 'dark';

  return isDark ?
    'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' :
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
}

function initializeMap(type) {
  const mapId = `map-${type}`;
  const mapContainer = document.getElementById(mapId);

  if (!mapContainer) {
    console.error(`Map container dengan ID '${mapId}' tidak ditemukan`);
    return;
  }

  if (mapContainer.offsetParent === null) {
    console.warn(`Map container '${mapId}' tidak visible atau hidden`);
  }

  gpsState[type].map = L.map(mapId).setView([officeLocation.lat, officeLocation.lng], 16);

  const tileUrl = getTileLayerUrl();
  gpsState[type].tileLayer = L.tileLayer(tileUrl, {
    attribution: '© OpenStreetMap contributors &copy; CARTO',
    maxZoom: 19
  });

  gpsState[type].tileLayer.addTo(gpsState[type].map);

  gpsState[type].officeMarker = L.marker([officeLocation.lat, officeLocation.lng], {
    icon: L.divIcon({
      className: 'custom-marker',
      html: '<div style="background-color: #0d6efd; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
      iconSize: [24, 24],
      iconAnchor: [12, 12]
    })
  }).addTo(gpsState[type].map);

  gpsState[type].officeMarker.bindPopup('<b>🏫 Lokasi Sekolah</b><br>Titik presensi').openPopup();

  gpsState[type].radiusCircle = L.circle([officeLocation.lat, officeLocation.lng], {
    color: '#198754',
    fillColor: '#198754',
    fillOpacity: 0.15,
    radius: officeLocation.radius
  }).addTo(gpsState[type].map);

  gpsState[type].radiusCircle.bindPopup(`Radius presensi: ${officeLocation.radius} meter`);

  if (gpsState[type].currentLat && gpsState[type].currentLng) {
    updateUserMarker(type, gpsState[type].currentLat, gpsState[type].currentLng);
  }

  setTimeout(() => {
    gpsState[type].map.invalidateSize();
  }, 100);
}

function updateMapTheme() {
  const newUrl = getTileLayerUrl();
  ['in', 'out'].forEach(type => {
    if (gpsState[type].map && gpsState[type].tileLayer) {
      gpsState[type].map.removeLayer(gpsState[type].tileLayer);

      gpsState[type].tileLayer = L.tileLayer(newUrl, {
        attribution: '© OpenStreetMap contributors &copy; CARTO',
        maxZoom: 19
      });

      gpsState[type].tileLayer.addTo(gpsState[type].map).bringToBack();
    }
  });
}

function updateUserMarker(type, lat, lng) {
  if (!gpsState[type].map) return;

  if (gpsState[type].userMarker) {
    gpsState[type].map.removeLayer(gpsState[type].userMarker);
  }

  gpsState[type].userMarker = L.marker([lat, lng], {
    icon: L.divIcon({
      className: 'custom-marker',
      html: '<div style="background-color: #dc3545; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
      iconSize: [24, 24],
      iconAnchor: [12, 12]
    })
  }).addTo(gpsState[type].map);

  const distance = calculateDistance(lat, lng, officeLocation.lat, officeLocation.lng);
  const distanceText = distance < 1000 ? `${Math.round(distance)} meter` : `${(distance / 1000).toFixed(2)} km`;

  gpsState[type].userMarker.bindPopup(`<b>📍 Lokasi Anda</b><br>Jarak: ${distanceText}`);

  const bounds = L.latLngBounds([
    [lat, lng],
    [officeLocation.lat, officeLocation.lng]
  ]);
  gpsState[type].map.fitBounds(bounds, {
    padding: [50, 50]
  });
}

document.addEventListener('DOMContentLoaded', function() {
  updateClock();
  setInterval(updateClock, 1000);

  initializeGPS('in');
  initializeGPS('out');

  const boxIn = document.getElementById('ai-joke-container-in');
  if (boxIn && typeof checkAiData === 'function') {
    checkAiData('daily_ai_mood', 'in', boxIn, currentDate);
  }

  const boxOut = document.getElementById('ai-joke-container-out');
  if (boxOut && typeof checkAiData === 'function') {
    checkAiData('daily_ai_mood_out', 'out', boxOut, currentDate);
  }

  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'attributes') {
        updateMapTheme();
      }
    });
  });

  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class', 'data-theme', 'data-bs-theme', 'data-darkreader-scheme']
  });

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateMapTheme);
});

function updateGPSStatus(type, status, message, accuracy = null) {
  const statusEl = document.getElementById(`gps-status-${type}`);
  const btnEl = document.getElementById(`btn-${type === 'in' ? 'masuk' : 'keluar'}`);

  if (!statusEl || !btnEl) return;

  statusEl.className = 'gps-status';

  let icon = '';
  let accuracyHTML = '';

  switch (status) {
    case 'loading':
      statusEl.classList.add('gps-status-loading');
      icon = '<div class="spinner"></div>';
      btnEl.disabled = true;
      btnEl.querySelector('.btn-text').textContent = 'Menunggu GPS...';
      break;

    case 'success':
      statusEl.classList.add('gps-status-success');
      icon = '✓';
      btnEl.disabled = false;
      btnEl.querySelector('.btn-text').textContent = type === 'in' ? 'Presensi Masuk' : 'Pulang Sekolah';

      if (accuracy !== null) {
        let accClass = 'accuracy-good';
        let accText = 'Akurasi Sangat Baik';

        if (accuracy > 20 && accuracy <= 50) {
          accClass = 'accuracy-medium';
          accText = 'Akurasi Cukup';
        } else if (accuracy > 50) {
          accClass = 'accuracy-poor';
          accText = 'Akurasi Rendah';
        }

        accuracyHTML = `<div class="accuracy-indicator ${accClass}">📍 ${accText} (±${Math.round(accuracy)}m)</div>`;
      }
      break;

    case 'error':
      statusEl.classList.add('gps-status-error');
      icon = '✕';
      btnEl.disabled = true;
      btnEl.querySelector('.btn-text').textContent = 'GPS Tidak Tersedia';
      break;

    case 'warning':
      statusEl.classList.add('gps-status-warning');
      icon = '⚠';
      btnEl.disabled = false;
      btnEl.querySelector('.btn-text').textContent = type === 'in' ? 'Presensi Masuk' : 'Pulang Sekolah';
      break;
  }

  statusEl.innerHTML = `${icon}<div><span>${message}</span>${accuracyHTML}</div>`;
}

function updateLocationInfo(type, lat, lng, distance) {
  const locationInfoEl = document.getElementById(`location-info-${type}`);
  const userCoordsEl = document.getElementById(`user-coords-${type}`);
  const distanceEl = document.getElementById(`distance-${type}`);
  const distanceStatusEl = document.getElementById(`distance-status-${type}`);

  if (locationInfoEl) locationInfoEl.style.display = 'block';

  if (userCoordsEl) {
    userCoordsEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
  }

  if (distanceEl) {
    const distanceText = distance < 1000 ?
      `${Math.round(distance)} meter` :
      `${(distance / 1000).toFixed(2)} km`;
    distanceEl.textContent = distanceText;
  }

  if (distanceStatusEl) {
    const isValid = distance <= officeLocation.radius;
    const badgeClass = isValid ? 'distance-valid' : 'distance-invalid';
    const badgeText = isValid ? '✓ Anda berada dalam radius presensi' : '✕ Anda di luar radius presensi';
    distanceStatusEl.innerHTML = `<div class="distance-badge ${badgeClass}">${badgeText}</div>`;
  }

  const mapContainer = document.getElementById(`map-container-${type}`);
  const toggleText = document.getElementById(`map-toggle-text-${type}`);

  if (mapContainer) {
    mapContainer.style.display = 'block';
    if (toggleText) toggleText.textContent = '🗺️ Sembunyikan Peta';

    if (!gpsState[type].map) {
      initializeMap(type);
    } else {
      updateUserMarker(type, lat, lng);
      gpsState[type].map.invalidateSize();
    }
  }
}

function initializeGPS(type) {
  if (gpsState[type].isDetecting) return;

  gpsState[type].isDetecting = true;
  updateGPSStatus(type, 'loading', 'Mendeteksi lokasi Anda...');

  if (!navigator.geolocation) {
    updateGPSStatus(type, 'error', 'Browser Anda tidak mendukung GPS. Gunakan browser yang lebih baru.');
    gpsState[type].isDetecting = false;
    return;
  }

  const options = {
    enableHighAccuracy: true,
    timeout: 15000,
    maximumAge: 0
  };

  let watchId = navigator.geolocation.watchPosition(
    function(position) {
      handleGPSSuccess(type, position, watchId);
    },
    function(error) {
      handleGPSError(type, error, watchId);
    },
    options
  );
}

function handleGPSSuccess(type, position, watchId) {
  const lat = parseFloat(position.coords.latitude);
  const lng = parseFloat(position.coords.longitude);
  const accuracy = position.coords.accuracy;

  if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
    if (gpsState[type].attempts < gpsState[type].maxAttempts) {
      gpsState[type].attempts++;
      updateGPSStatus(type, 'loading', `Mencoba ulang... (${gpsState[type].attempts}/${gpsState[type].maxAttempts})`);
      return;
    }

    navigator.geolocation.clearWatch(watchId);
    updateGPSStatus(type, 'error', 'Koordinat tidak valid. Pastikan GPS aktif dan coba lagi.');
    gpsState[type].isDetecting = false;
    return;
  }

  const suffix = type === 'in' ? 'in' : 'out';
  const latEl = document.getElementById(`latitude_pegawai_${suffix}`);
  const lngEl = document.getElementById(`longitude_pegawai_${suffix}`);

  if (latEl) latEl.value = lat.toFixed(8);
  if (lngEl) lngEl.value = lng.toFixed(8);

  gpsState[type].currentLat = lat;
  gpsState[type].currentLng = lng;

  gpsState[type].isReady = true;
  gpsState[type].accuracy = accuracy;

  const distance = calculateDistance(lat, lng, officeLocation.lat, officeLocation.lng);

  updateLocationInfo(type, lat, lng, distance);

  if (gpsState[type].map) {
    updateUserMarker(type, lat, lng);
  }

  navigator.geolocation.clearWatch(watchId);

  if (distance > officeLocation.radius) {
    updateGPSStatus(type, 'error',
      `Anda berada ${Math.round(distance - officeLocation.radius)} meter di luar radius presensi.`, accuracy);
    const btnEl = document.getElementById(`btn-${type === 'in' ? 'masuk' : 'keluar'}`);
    if (btnEl) {
      btnEl.disabled = true;
    }
    gpsState[type].isReady = false;
  } else {
    gpsState[type].isReady = true;

    if (accuracy <= 20) {
      updateGPSStatus(type, 'success', '✓ Lokasi terdeteksi! Anda dalam radius presensi.', accuracy);
    } else if (accuracy <= 50) {
      updateGPSStatus(type, 'success', '✓ Lokasi terdeteksi! Anda dalam radius presensi (sinyal GPS agak lemah).',
        accuracy);
    } else {
      updateGPSStatus(type, 'warning',
        '✓ Anda dalam radius presensi, tapi sinyal GPS lemah. Presensi tetap bisa dilakukan.', accuracy);
    }

    const btnEl = document.getElementById(`btn-${type === 'in' ? 'masuk' : 'keluar'}`);
    if (btnEl) {
      btnEl.disabled = false;
    }
  }

  gpsState[type].isDetecting = false;
}

function handleGPSError(type, error, watchId) {
  navigator.geolocation.clearWatch(watchId);
  gpsState[type].isDetecting = false;

  let errorMessage = '';
  let canRetry = true;

  switch (error.code) {
    case error.PERMISSION_DENIED:
      errorMessage = 'Akses GPS ditolak. Klik ikon gembok di address bar, lalu izinkan akses lokasi.';
      canRetry = false;
      break;

    case error.POSITION_UNAVAILABLE:
      errorMessage = 'Lokasi tidak dapat dideteksi. Pastikan GPS aktif dan Anda berada di area terbuka.';
      break;

    case error.TIMEOUT:
      errorMessage = 'Deteksi GPS memakan waktu terlalu lama. Periksa koneksi dan GPS Anda.';
      break;

    default:
      errorMessage = 'Terjadi kesalahan saat mendeteksi lokasi. Silakan coba lagi.';
  }

  if (canRetry && gpsState[type].attempts < gpsState[type].maxAttempts) {
    gpsState[type].attempts++;
    updateGPSStatus(type, 'loading',
      `${errorMessage} Mencoba ulang... (${gpsState[type].attempts}/${gpsState[type].maxAttempts})`);
    setTimeout(() => initializeGPS(type), 2000);
  } else {
    updateGPSStatus(type, 'error', errorMessage + (canRetry ?
      ' <button class="btn btn-sm btn-warning mt-2" onclick="retryGPS(\'' + type + '\')">Coba Lagi</button>' : ''));
  }
}

function retryGPS(type) {
  gpsState[type].attempts = 0;
  gpsState[type].isDetecting = false;
  initializeGPS(type);
}

document.getElementById('form-masuk')?.addEventListener('submit', function(e) {
  if (!gpsState.in.isReady) {
    e.preventDefault();
    updateGPSStatus('in', 'error', 'Tunggu hingga GPS terdeteksi, atau coba refresh halaman.');
    return false;
  }

  const btnEl = document.getElementById('btn-masuk');
  btnEl.disabled = true;
  btnEl.querySelector('.btn-text').textContent = 'Memproses...';
});

document.getElementById('form-keluar')?.addEventListener('submit', function(e) {
  if (!gpsState.out.isReady) {
    e.preventDefault();
    updateGPSStatus('out', 'error', 'Tunggu hingga GPS terdeteksi, atau coba refresh halaman.');
    return false;
  }

  const jamSekarang = new Date((Math.floor(Date.now() / 1000) + timeDiff) * 1000);
  const jamSekarangStr = String(jamSekarang.getHours()).padStart(2, '0') + ':' +
    String(jamSekarang.getMinutes()).padStart(2, '0') + ':' +
    String(jamSekarang.getSeconds()).padStart(2, '0');

  const jamPulang = <?= json_encode($jam_pulang) ?>;
  if (jamSekarangStr < jamPulang) {
    e.preventDefault();
    const [jamP, menitP] = jamPulang.split(':');
    const [jamS, menitS] = jamSekarangStr.split(':');
    const selisihMenit = Math.ceil((parseInt(jamP) * 60 + parseInt(menitP)) - (parseInt(jamS) * 60 + parseInt(
      menitS)));
    updateGPSStatus('out', 'error', `Belum waktunya pulang. Tunggu ${selisihMenit} menit lagi.`);
    return false;
  }

  const btnEl = document.getElementById('btn-keluar');
  btnEl.disabled = true;
  btnEl.querySelector('.btn-text').textContent = 'Memproses...';
});

const clientTime = Math.floor(Date.now() / 1000);
const timeDiff = serverTime - clientTime;

function updateClock() {
  const now = new Date((Math.floor(Date.now() / 1000) + timeDiff) * 1000);
  const tgl = now.getDate();
  const bln = monthNames[now.getMonth()];
  const thn = now.getFullYear();
  const jam = String(now.getHours()).padStart(2, '0');
  const mnt = String(now.getMinutes()).padStart(2, '0');
  const dtk = String(now.getSeconds()).padStart(2, '0');

  const updateEl = (suffix) => {
    const elements = {
      tanggal: document.getElementById(`tanggal_${suffix}`),
      bulan: document.getElementById(`bulan_${suffix}`),
      tahun: document.getElementById(`tahun_${suffix}`),
      jam: document.getElementById(`jam_${suffix}`),
      menit: document.getElementById(`menit_${suffix}`),
      detik: document.getElementById(`detik_${suffix}`),
      hiddenTgl: document.getElementById(`tanggal_${suffix}_hidden`),
      hiddenJam: document.getElementById(`jam_${suffix}_hidden`)
    };

    if (elements.tanggal) {
      elements.tanggal.textContent = tgl;
      elements.bulan.textContent = bln;
      elements.tahun.textContent = thn;
      elements.jam.textContent = jam;
      elements.menit.textContent = mnt;
      elements.detik.textContent = dtk;

      if (elements.hiddenTgl) {
        elements.hiddenTgl.value =
          `${thn}-${String(now.getMonth()+1).padStart(2,'0')}-${String(tgl).padStart(2,'0')}`;
      }
      if (elements.hiddenJam) {
        elements.hiddenJam.value = `${jam}:${mnt}:${dtk}`;
      }
    }
  };

  updateEl('masuk');
  updateEl('keluar');
}
</script>

<script src="<?= base_url('assets/js/ai-jokes.js') ?>"></script>

<?= $this->endSection() ?>