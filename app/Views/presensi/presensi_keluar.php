<?php
// \app\Views\presensi\presensi_keluar.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/human.js') ?>"></script>
<script src="<?= base_url('assets/js/human/core.js') ?>"></script>
<script src="<?= base_url('assets/js/human/verification.js') ?>"></script>
<script src="<?= base_url('assets/js/human/ui.js') ?>"></script>

<style>
.video-container {
  position: relative;
  display: inline-block;
  border-radius: 8px;
  overflow: hidden;
  transform: scaleX(-1) translateZ(0);
  will-change: transform;
}

#my_camera {
  width: 100%;
  height: auto;
  display: block;
  transform: translateZ(0);
}

#face-overlay-canvas {
  display: none;
}

.head-movement-instruction {
  background: #1e3a8a;
  color: white;
  padding: 15px 20px;
  border-radius: 12px;
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: 600;
  text-align: center;
  border: 2px solid #dda518;
}

.head-movement-instruction .instruction-icon {
  font-size: 48px;
  display: block;
  margin-bottom: 8px;
}

.head-movement-instruction .instruction-text {
  font-size: 18px;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.head-movement-instruction .instruction-subtitle {
  font-size: 13px;
  opacity: 0.9;
  margin-top: 5px;
}

.verification-progress {
  margin: 10px 0;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 2px solid #e9ecef;
  transform: translateZ(0);
}

.progress-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 5px 0;
  font-size: 14px;
}

.progress-item-icon {
  font-size: 20px;
}

.progress-item-icon.pending {
  opacity: 0.3;
}

.progress-item-icon.completed {
  color: #28a745;
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

[data-bs-theme="dark"] .map-legend,
[data-darkreader-scheme="dark"] .map-legend {
  background-color: #1e293b;
  border-color: #334155;
  color: #94a3b8;
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

.map-container {
  margin: 15px 0;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #dee2e6;
}

#map {
  height: 300px;
  width: 100%;
  transform: translateZ(0);
}

.movement-progress-bar {
  width: 100%;
  height: 8px;
  background: #ffffff;
  border-radius: 4px;
  overflow: hidden;
  margin-top: 10px;
}

.movement-progress-fill {
  height: 100%;
  background: #dda518;
  width: 0%;
  transform: translateZ(0);
}

#ambil-foto {
  position: relative;
  overflow: hidden;
  border: 1px solid #1e3a8a;
  background-color: #1e3a8a;
  color: white;
  transition: all 0.2s ease;
  z-index: 1;
}

#ambil-foto::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #dda518;
  z-index: 1;
  transform: translateX(var(--slide-x, -100%));
  transition: transform 0.5s ease-out;
}

#ambil-foto span {
  position: relative;
  z-index: 2;
  font-weight: 600;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

#ambil-foto:disabled {
  opacity: 1 !important;
  cursor: not-allowed;
}
</style>

<div class="page-body">
  <div class="container-xl">
    <div class="row g-3">
      <div class="col-md-7">
        <div class="card">
          <div class="card-body">
            <div id="map"></div>
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
        </div>
      </div>
      <div class="col-md-5">
        <div class="card text-center">
          <div class="card-body m-auto">
            <div id="head-movement-instruction" class="head-movement-instruction" style="display: none;">
              <div class="instruction-icon" id="instruction-icon">👤</div>
              <div class="instruction-text" id="instruction-text">Siapkan Wajah</div>
              <div class="instruction-subtitle">Ikuti instruksi untuk verifikasi</div>
              <div class="movement-progress-bar">
                <div class="movement-progress-fill" id="movement-progress-fill"></div>
              </div>
            </div>

            <div id="verification-progress" class="verification-progress" style="display: none;">
              <div class="progress-item">
                <span class="progress-item-icon" id="progress-face">⏳</span>
                <span>Deteksi Wajah</span>
              </div>
              <div class="progress-item">
                <span class="progress-item-icon pending" id="progress-movement">⏳</span>
                <span id="progress-movement-text">Gerakan Kepala</span>
              </div>
              <div class="progress-item">
                <span class="progress-item-icon pending" id="progress-match">⏳</span>
                <span>Pencocokan Wajah</span>
              </div>
            </div>

            <div id="face-status" class="alert alert-info" style="display: none;">
              <div id="face-message">Memuat kamera...</div>
              <div id="face-details" class="mt-2 small" style="display: none;"></div>
            </div>

            <div id="my_result"></div>

            <div class="mt-3">
              <div class="video-container">
                <video id="my_camera" autoplay playsinline style="width: 100%; max-width: 640px;"></video>
                <canvas id="face-overlay-canvas"></canvas>
              </div>
              <canvas id="canvas" style="display:none;"></canvas>
            </div>

            <div class="mt-3"><?= date('d F Y', strtotime($tanggal_keluar)) . ' - ' . $jam_keluar ?></div>

            <form action="<?= base_url('/presensi-keluar/simpan') ?>" method="post" id="presensi-form">
              <?= csrf_field() ?>
              <input type="hidden" name="username" value="<?= $user_profile->username ?>">
              <input type="hidden" name="id_presensi" value="<?= $data_presensi_masuk->id ?>">
              <input type="hidden" name="tanggal_keluar" value="<?= $tanggal_keluar ?>">
              <input type="hidden" name="jam_keluar" value="<?= $jam_keluar ?>">
              <input type="hidden" name="latitude_pegawai" value="<?= $latitude_pegawai ?>">
              <input type="hidden" name="longitude_pegawai" value="<?= $longitude_pegawai ?>">
              <input type="hidden" name="image-cam" class="image-tag">
              <input type="hidden" name="face_verified" id="face-verified" value="false">
              <input type="hidden" name="face_similarity" id="face-similarity" value="0">
              <input type="hidden" name="detected_age" id="detected-age" value="0">
              <input type="hidden" name="detected_emotion" id="detected-emotion" value="">
              <button class="btn btn-primary mt-3" type="button" id="ambil-foto" disabled>
                <span id="btn-text">Memuat Kamera...</span>
              </button>
            </form>
            <div class="text-center mt-3">
              <a href="<?= base_url('face-enrollment') ?>" class="text-muted small text-decoration-none">
                <span class="me-1">⚠️</span>Kendala verifikasi? Request Pendaftaran Wajah
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
FaceRecognition.init({
  mode: 'presensi_keluar',
  endpoints: {
    getFaceDescriptors: '<?= base_url('presensi/get-face-descriptors') ?>',
    submitForm: '<?= base_url('/presensi-keluar/simpan') ?>'
  },
  userData: {
    idPegawai: <?= $user_profile->id_pegawai ?>,
    username: '<?= $user_profile->username ?>',
    faceEnrollmentUrl: '<?= base_url('face-enrollment') ?>'
  },
  models: {
    basePath: '<?= base_url('assets/models/') ?>'
  },
  csrf: {
    token: '<?= csrf_token() ?>',
    hash: '<?= csrf_hash() ?>'
  },
  map: {
    latitude_kantor: <?= $latitude_kantor ?>,
    longitude_kantor: <?= $longitude_kantor ?>,
    latitude_pegawai: <?= $latitude_pegawai ?>,
    longitude_pegawai: <?= $longitude_pegawai ?>,
    radius: <?= $radius ?>
  }
});

FaceUI.initLeafletMap(FaceRecognition.getState().config);
</script>

<?= $this->endSection() ?>