<?php
// \app\Views\data_pegawai\face_descriptors.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/human.js') ?>"></script>
<script src="<?= base_url('assets/js/heic2any.min.js') ?>"></script>
<link rel="stylesheet" href="<?= base_url('assets/css/cropper.min.css') ?>" />
<script src="<?= base_url('assets/js/cropper.min.js') ?>"></script>

<style>
/* --- BASE LAYOUT --- */
* {
  box-sizing: border-box;
}

.page-body {
  background: #f8fafc;
  min-height: 100vh;
  padding: 20px 0;
}

/* --- CONTAINER UTAMA --- */
.main-enrollment-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

/* --- TAB STYLING --- */
.nav-tabs {
  border-bottom: 2px solid #e5e7eb;
  background: #f9fafb;
  padding: 0 20px;
  margin: 0;
}

.nav-tabs .nav-item {
  margin-bottom: -2px;
}

.nav-tabs .nav-link {
  border: none;
  color: #6b7280;
  font-weight: 500;
  padding: 16px 24px;
  transition: all 0.2s;
  position: relative;
  background: transparent;
}

.nav-tabs .nav-link:hover {
  color: #1e3a8a;
  background: rgba(30, 58, 138, 0.05);
}

.nav-tabs .nav-link.active {
  color: #1e3a8a;
  background: white;
  border-bottom: 3px solid #dda518;
}

.nav-tabs .nav-link svg {
  vertical-align: middle;
  margin-right: 8px;
}

/* --- TAB CONTENT --- */
.tab-content {
  padding: 30px;
  background: white;
}

/* --- INSTRUCTION BOX --- Simplified (no gradients) */
.instruction-box {
  background: #1e3a8a;
  color: white;
  padding: 24px;
  border-radius: 12px;
  margin-bottom: 24px;
  border: 2px solid #dda518;
}

.instruction-box h4 {
  color: white;
  margin: 0 0 20px 0;
  font-weight: 600;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.instruction-step {
  display: flex;
  align-items: flex-start;
  margin-bottom: 14px;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 8px;
}

.instruction-step:last-child {
  margin-bottom: 0;
}

.instruction-step .step-number {
  background: white;
  color: #1e3a8a;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.95rem;
  margin-right: 14px;
  flex-shrink: 0;
}

.instruction-step .step-text {
  flex: 1;
  line-height: 1.6;
  padding-top: 4px;
}

.instruction-step .step-text strong {
  font-weight: 600;
}

/* --- UPLOAD SECTION --- */
.upload-zone {
  border: 3px dashed #d1d5db;
  border-radius: 12px;
  padding: 32px;
  text-align: center;
  background: #f9fafb;
  transition: all 0.3s;
  cursor: pointer;
  position: relative;
}

.upload-zone:hover {
  border-color: #1e3a8a;
  background: #eff6ff;
}

.upload-zone.active {
  border-color: #10b981;
  background: #ecfdf5;
}

.upload-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 16px;
  color: #9ca3af;
  transition: color 0.3s;
}

.upload-zone:hover .upload-icon {
  color: #1e3a8a;
}

.upload-zone input[type="file"] {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  opacity: 0;
  cursor: pointer;
}

.upload-label-text {
  font-size: 1.1rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.upload-hint {
  color: #6b7280;
  font-size: 0.9rem;
}

/* --- IMAGE EDITOR --- */
.img-container {
  max-height: 560px;
  background: #1e293b;
  overflow: hidden;
  border-radius: 12px;
  border: 2px solid #334155;
  margin-bottom: 24px;
  position: relative;
  transform: translateZ(0);
}

.img-container img {
  max-width: 100%;
  display: block;
}


#upload-detection-canvas {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 999;
}

/* --- ROTATION CONTROL --- */
.rotation-wrapper {
  background: #f8fafc;
  padding: 20px 24px;
  border-radius: 12px;
  border: 2px solid #e2e8f0;
  margin-bottom: 24px;
}

.rotation-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.rotation-label-text {
  font-weight: 600;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 8px;
}

.rotation-value {
  font-weight: 700;
  color: #dda518;
  font-size: 1.1rem;
  min-width: 50px;
  text-align: right;
}

.range-slider-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.range-slider {
  flex: 1;
  height: 8px;
  -webkit-appearance: none;
  appearance: none;
  background: #cbd5e1;
  border-radius: 10px;
  outline: none;
  cursor: pointer;
}

.range-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 24px;
  height: 24px;
  background: #dda518;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s;
}

.range-slider::-webkit-slider-thumb:hover {
  background: #c89316;
  transform: scale(1.1);
}

.range-slider::-moz-range-thumb {
  width: 24px;
  height: 24px;
  background: #dda518;
  border-radius: 50%;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.range-slider::-moz-range-thumb:hover {
  background: #c89316;
  transform: scale(1.1);
}

.range-marker {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 500;
  min-width: 40px;
  text-align: center;
}

/* --- WEBCAM SECTION --- */
.video-container {
  position: relative;
  display: inline-block;
  border-radius: 8px;
  overflow: hidden;
  transform: scaleX(-1) translateZ(0);
  will-change: transform;
  border: 2px solid #dee2e6 !important;
  box-shadow: none !important;
}

#webcam-video {
  width: 100%;
  height: auto;
  display: block;
  transform: translateZ(0);
}

/* Canvas overlay removed - performance optimization */
/* --- Ganti bagian ini di CSS --- */

#webcam-canvas {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

/* --- TECH STATS --- Simplified */
.tech-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 16px;
  background: #1e293b;
  color: #00d2ff;
  padding: 16px;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
  border-radius: 0 0 12px 12px;
  margin-top: -4px;
  transform: translateZ(0);
}

.tech-stat-item {
  text-align: center;
  padding: 8px;
  background: rgba(0, 210, 255, 0.1);
  border-radius: 6px;
  border: 1px solid rgba(0, 210, 255, 0.3);
}

.tech-stat-item strong {
  color: #fff;
  display: block;
  font-size: 1.1rem;
  margin-top: 4px;
}

/* --- STATUS ALERTS --- */
.status-alert {
  padding: 16px 20px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 12px;
}

.status-alert.alert-info {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.status-alert.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #6ee7b7;
}

.status-alert.alert-warning {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fcd34d;
}

.status-alert.alert-danger {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

/* --- FORM ELEMENTS --- */
.form-label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
  display: block;
}

.form-control {
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.form-control:focus {
  border-color: #1e3a8a;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  outline: none;
}

/* --- BUTTONS --- */
.btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-primary {
  background: #1e3a8a;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #1e40af;
  transform: translateY(-2px);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-outline-secondary {
  background: transparent;
  border: 2px solid #d1d5db;
  color: #6b7280;
}

.btn-outline-secondary:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.btn-lg {
  padding: 14px 28px;
  font-size: 1.05rem;
}

.btn-icon {
  width: 36px;
  height: 36px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-warning {
  background: #f59e0b;
  color: white;
}

.btn-warning:hover {
  background: #d97706;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

/* --- FACE CARDS --- */
.face-card {
  border: 2px solid #e5e7eb;
  padding: 18px;
  margin-bottom: 20px;
  border-radius: 12px;
  background: white;
  transition: all 0.3s;
}

.face-card:hover {
  background: #f9fafb;
  transform: translateY(-4px);
  border-color: #1e3a8a;
}

.face-card h4 {
  margin: 0 0 8px 0;
  color: #1e3a8a;
  font-size: 1.1rem;
}

.face-card .badge {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}

.badge.bg-green-lt {
  background: #d1fae5;
  color: #065f46;
}

/* --- UTILITIES --- */
.text-center {
  text-align: center;
}

.text-muted {
  color: #6b7280;
}

.text-primary {
  color: #1e3a8a;
}

.mb-3 {
  margin-bottom: 1rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.mt-2 {
  margin-top: 0.5rem;
}

.w-100 {
  width: 100%;
}

.d-flex {
  display: flex;
}

.justify-content-between {
  justify-content: space-between;
}

.justify-content-center {
  justify-content: center;
}

.align-items-center {
  align-items: center;
}

.align-items-start {
  align-items: flex-start;
}

.flex-nowrap {
  flex-wrap: nowrap;
}

.gap-2 {
  gap: 0.5rem;
}

.btn-list {
  display: flex;
  gap: 8px;
}

/* --- ANIMATIONS --- Minimal */
@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.5;
  }
}

.pulse-animation {
  animation: pulse 1.5s ease-in-out infinite;
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

.fade-in {
  animation: fadeIn 0.3s ease-out;
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
  .tab-content {
    padding: 20px 16px;
  }

  .instruction-box {
    padding: 18px;
  }

  .tech-stats {
    grid-template-columns: repeat(2, 1fr);
    font-size: 0.8rem;
  }
}
</style>

<div class="page-body">
  <div class="container-xl">
    <!-- Back Button -->
    <div class="row mb-3">
      <div class="col">
        <a href="<?= base_url('data-pegawai') ?>" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Kembali
        </a>
      </div>
    </div>

    <!-- Main Enrollment Card -->
    <div class="row">
      <div class="col-12 mb-4">
        <div class="card main-enrollment-card">
          <div class="card-body" style="padding: 0;">
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="enrollTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="tab-upload-link" data-bs-toggle="tab" href="#upload-tab">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  Upload File
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-webcam-link" data-bs-toggle="tab" href="#webcam-tab">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                    <circle cx="12" cy="13" r="4"></circle>
                  </svg>
                  Webcam Live
                </a>
              </li>
            </ul>

            <div class="tab-content">
              <!-- TAB UPLOAD -->
              <div id="upload-tab" class="tab-pane fade show active">
                <div class="instruction-box fade-in">
                  <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="16" x2="12" y2="12"></line>
                      <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Panduan Upload & Crop Wajah
                  </h4>
                  <div class="instruction-step">
                    <div class="step-number">1</div>
                    <div class="step-text">Upload foto wajah yang <strong>jelas dan fokus</strong>. Sistem mendukung
                      JPG, PNG, dan HEIC (iPhone).</div>
                  </div>
                  <div class="instruction-step">
                    <div class="step-number">2</div>
                    <div class="step-text">Sesuaikan <strong>posisi crop & rotasi</strong> agar wajah terlihat jelas.
                    </div>
                  </div>
                  <div class="instruction-step">
                    <div class="step-number">3</div>
                    <div class="step-text">Klik tombol <strong>"Potong, Proses & Simpan"</strong> untuk menyimpan face
                      descriptor.</div>
                  </div>
                </div>

                <div id="upload-status" class="status-alert" style="display: none;">
                  <div id="upload-message"></div>
                </div>

                <div class="row justify-content-center">
                  <div class="col-md-10 col-lg-9">

                    <!-- Upload Zone -->
                    <div class="upload-zone" id="upload-zone">
                      <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                      </svg>
                      <div class="upload-label-text">Klik atau Drag File ke Sini</div>
                      <div class="upload-hint">Format: JPG, PNG, HEIC • Maksimal 10MB</div>
                      <input type="file" id="file-upload" accept="image/*, .heic">
                    </div>

                    <!-- Editor Area -->
                    <div id="editor-area" style="display: none;" class="fade-in">
                      <div class="mb-3">
                        <label class="form-label">
                          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="vertical-align: middle; margin-right: 6px;">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                          </svg>
                          Sesuaikan Posisi Wajah
                        </label>

                        <div class="img-container">
                          <img id="image-to-crop" src="">
                          <canvas id="upload-detection-canvas"></canvas>
                        </div>
                      </div>

                      <div class="rotation-wrapper">
                        <div class="rotation-label">
                          <span class="rotation-label-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round">
                              <polyline points="23 4 23 10 17 10"></polyline>
                              <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                            </svg>
                            Koreksi Rotasi
                          </span>
                          <span id="rotation-val-display" class="rotation-value">0°</span>
                        </div>
                        <div class="range-slider-container">
                          <span class="range-marker">-45°</span>
                          <input type="range" class="range-slider" id="rotation-slider" min="-45" max="45" step="1"
                            value="0">
                          <span class="range-marker">+45°</span>
                        </div>
                        <div class="text-center mt-2">
                          <button class="btn btn-outline-secondary" onclick="resetRotation()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round">
                              <polyline points="1 4 1 10 7 10"></polyline>
                              <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                            </svg>
                            Reset Posisi
                          </button>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">
                          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="vertical-align: middle; margin-right: 6px;">
                            <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                            <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                          </svg>
                          Label Foto
                        </label>
                        <input type="text" class="form-control" id="upload-label"
                          placeholder="Contoh: Foto KTP / Selfie" value="Upload <?= date('d/m H:i') ?>">
                      </div>

                      <button class="btn btn-primary w-100 btn-lg" id="btn-upload-save" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                          <circle cx="8.5" cy="8.5" r="1.5"></circle>
                          <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        Potong, Proses & Simpan
                      </button>
                    </div>

                  </div>
                </div>
              </div>

              <!-- TAB WEBCAM -->
              <div id="webcam-tab" class="tab-pane fade">
                <div class="instruction-box fade-in">
                  <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="16" x2="12" y2="12"></line>
                      <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Panduan Scan Wajah Live
                  </h4>
                  <div class="instruction-step">
                    <div class="step-number">1</div>
                    <div class="step-text">Posisikan <strong>satu wajah</strong> dengan jelas di tengah kamera.</div>
                  </div>
                  <div class="instruction-step">
                    <div class="step-number">2</div>
                    <div class="step-text">Pastikan pencahayaan cukup dan wajah tidak terhalang objek.</div>
                  </div>
                  <div class="instruction-step">
                    <div class="step-number">3</div>
                    <div class="step-text">Klik tombol <strong>"Scan & Simpan Wajah"</strong> saat deteksi optimal.
                    </div>
                  </div>
                </div>

                <div id="face-status" class="alert alert-info" style="display: none;">
                  <div id="face-message">Memuat kamera...</div>
                </div>

                <div class="text-center mb-3">
                  <div class="video-container"
                    style="max-width: 640px; margin: 0 auto; border-radius: 8px; overflow: hidden; border: 2px solid #dee2e6;">
                    <video id="webcam-video" autoplay playsinline muted
                      style="width: 100%; height: auto; display: block;"></video>
                    <canvas id="webcam-canvas"></canvas>
                  </div>
                  <div class="tech-stats" id="tech-stats">
                    <div class="tech-stat-item">
                      <div>Wajah</div>
                      <strong id="stat-faces">0</strong>
                    </div>
                    <div class="tech-stat-item">
                      <div>Usia</div>
                      <strong id="stat-age">-</strong>
                    </div>
                    <div class="tech-stat-item">
                      <div>Gender</div>
                      <strong id="stat-gender">-</strong>
                    </div>
                    <div class="tech-stat-item">
                      <div>Emosi</div>
                      <strong id="stat-emotion">-</strong>
                    </div>
                  </div>
                </div>

                <div class="row justify-content-center">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          style="vertical-align: middle; margin-right: 6px;">
                          <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                          <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                        </svg>
                        Label Foto
                      </label>
                      <input type="text" class="form-control" id="webcam-label"
                        placeholder="Contoh: Wajah Depan / Senyum" value="Scan <?= date('d/m H:i') ?>">
                    </div>
                    <button class="btn btn-primary w-100 btn-lg" id="btn-capture-webcam" disabled>
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z">
                        </path>
                        <circle cx="12" cy="13" r="4"></circle>
                      </svg>
                      Scan & Simpan Wajah
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Database Wajah -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header" style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
            <h3 class="card-title" style="color: #1e3a8a; font-weight: 600;">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                style="vertical-align: middle; margin-right: 8px;">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              Database Wajah Tersimpan (<?= count($descriptors) ?>)
            </h3>
          </div>
          <div class="card-body" style="padding: 24px;">
            <?php if (empty($descriptors)): ?>
            <div class="status-alert alert-warning">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                </path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
              Belum ada wajah terdaftar untuk pengguna ini!
            </div>
            <?php else: ?>
            <div class="row" id="descriptors-list">
              <?php foreach ($descriptors as $desc): ?>
              <div class="col-md-4 col-sm-6">
                <div class="face-card">
                  <div class="d-flex justify-content-between align-items-start">
                    <div style="flex: 1;">
                      <h4><?= esc($desc->label) ?></h4>
                      <small class="text-muted d-block" style="margin-bottom: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          style="vertical-align: middle;">
                          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                          <line x1="16" y1="2" x2="16" y2="6"></line>
                          <line x1="8" y1="2" x2="8" y2="6"></line>
                          <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <?= date('d M Y, H:i', strtotime($desc->created_at)) ?>
                      </small>
                      <span class="badge bg-green-lt">Vector ID: <?= $desc->id ?></span>
                    </div>
                    <div class="btn-list flex-nowrap">
                      <button class="btn btn-icon btn-warning btn-sm" title="Edit Label"
                        onclick="editLabel(<?= $desc->id ?>, '<?= esc($desc->label) ?>')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                      </button>
                      <a href="<?= base_url('data-pegawai/delete-descriptor/' . $desc->id) ?>"
                        class="btn btn-icon btn-danger btn-sm" title="Hapus"
                        onclick="return confirm('Hapus data wajah ini?')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="3 6 5 6 21 6"></polyline>
                          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                          </path>
                          <line x1="10" y1="11" x2="10" y2="17"></line>
                          <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// ==================== GLOBAL STATE ====================
let isModelLoaded = false;
let webcamStream = null;
let webcamAnimationFrame = null;
let uploadDetectionTimeout = null;
let cropper = null;
let lastWebcamDetection = 0;
let uploadDetectionInterval = null;

// OPTIMIZATION: DOM Cache
const DOM_CACHE = {
  video: null,
  btnWebcam: null,
  statusDiv: null,
  messageDiv: null,
  // Will be initialized in DOMContentLoaded
};

// Previous values for dirty checking
let previousValues = {
  statusMessage: '',
  statusType: '',
  webcamFaces: -1,
  webcamAge: '',
  webcamGender: '',
  webcamEmotion: ''
};

const idPegawai = <?= $pegawai->id ?>;

// ==================== HUMAN.JS CONFIG ====================
const human = new Human.Human({
  modelBasePath: '<?= base_url('assets/models/') ?>',

  wasm: {
    enabled: true,
    simd: true
  },

  backend: 'wasm',
  deallocate: true,

  face: {
    enabled: true,
    detector: {
      modelPath: 'blazeface.json',
      rotation: true,
      maxDetected: 1,
      skipFrames: 1,
      minConfidence: 0.62
    },
    mesh: {
      enabled: true
    },
    description: {
      enabled: true
    },
    emotion: {
      enabled: true,
      minConfidence: 0.1
    },
    iris: {
      enabled: false
    },
    antispoof: {
      enabled: false
    },
    liveness: {
      enabled: false
    }
  },

  body: {
    enabled: false
  },
  hand: {
    enabled: false
  },
  object: {
    enabled: false
  },
  gesture: {
    enabled: false
  },
  segmentation: {
    enabled: false
  },

  cacheSensitivity: 0.7,

  filter: {
    enabled: true,
    equalization: false
  }
});

// ==================== CONSTANTS ====================
const WEBCAM_DETECTION_INTERVAL = 200; // ms
const UPLOAD_DETECTION_DEBOUNCE = 800; // ms

const emotionMap = {
  happy: 'Senang',
  sad: 'Sedih',
  neutral: 'Netral',
  angry: 'Marah',
  fearful: 'Takut',
  surprised: 'Terkejut',
  disgusted: 'Jijik'
};

// ==================== INIT HUMAN ====================
async function initHuman() {
  try {
    updateStatus('Memuat model AI...', 'info');

    try {
      const support = human.env;

      if (!support.wasm) {
        throw new Error("Browser tidak support WASM");
      }

      await human.load();
      await human.warmup();
      console.log('✅ Backend:', human.tf.getBackend());

    } catch (wasmError) {
      console.warn('⚠️ Fallback ke WebGL...', wasmError);

      human.config.backend = 'webgl';
      await human.load();
      await human.warmup();

      console.log('✅ Berhasil recover menggunakan WebGL');
      updateStatus('Mode Kompatibilitas (WebGL) Aktif', 'warning');
    }

    isModelLoaded = true;
    updateStatus('✅ Sistem AI Siap', 'success');

  } catch (error) {
    console.error('❌ Error init Human:', error);
    updateStatus('❌ Gagal memuat model AI. Silakan refresh halaman.', 'danger');
  }
}

// ==================== WEBCAM SECTION ====================

async function startWebcam() {
  if (!isModelLoaded) {
    await initHuman();
  }

  try {
    updateStatus('⏳ Mengakses kamera...', 'info');

    const constraints = [{
        video: {
          width: {
            ideal: 640,
            max: 640
          },
          height: {
            ideal: 480,
            max: 480
          },
          facingMode: 'user',
          frameRate: {
            ideal: 24,
            max: 30
          }
        },
        audio: false
      },
      {
        video: {
          facingMode: 'user'
        },
        audio: false
      }
    ];

    let lastError = null;
    for (const constraint of constraints) {
      try {
        webcamStream = await navigator.mediaDevices.getUserMedia(constraint);
        break;
      } catch (err) {
        lastError = err;
      }
    }

    if (!webcamStream) {
      throw lastError;
    }

    const video = DOM_CACHE.video;
    video.srcObject = webcamStream;

    await new Promise((resolve, reject) => {
      video.onloadedmetadata = () => video.play().then(resolve).catch(reject);
      setTimeout(() => reject(new Error('Timeout loading video')), 10000);
    });

    DOM_CACHE.btnWebcam.disabled = false;
    updateStatus('✅ Kamera Aktif. Posisikan wajah Anda...', 'success');

    // Start detection loop
    webcamDetectionLoop();

  } catch (error) {
    console.error('Webcam error:', error);
    updateStatus('❌ Gagal mengakses webcam. Periksa izin kamera.', 'danger');
  }
}

function stopWebcam() {
  if (webcamStream) {
    webcamStream.getTracks().forEach(track => track.stop());
    webcamStream = null;
  }

  if (DOM_CACHE.video) {
    DOM_CACHE.video.srcObject = null;
  }

  if (webcamAnimationFrame) {
    cancelAnimationFrame(webcamAnimationFrame);
    webcamAnimationFrame = null;
  }

  // Reset stats
  updateWebcamStats(0, '-', '-', '-');
  DOM_CACHE.btnWebcam.disabled = true;
}



function drawOverlay(result, video) {
  const canvas = document.getElementById('webcam-canvas');
  const ctx = canvas.getContext('2d');

  if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
  }

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  if (result.face && result.face.length > 0) {
    const face = result.face[0];
    const box = face.box; // [x, y, width, height]

    // Config Style
    const x = box[0];
    const y = box[1];
    const w = box[2];
    const h = box[3];
    const lineLen = 20;
    const color = '#D1FAE5';

    ctx.lineWidth = 3;
    ctx.strokeStyle = color;
    ctx.fillStyle = 'rgba(0, 210, 255, 0.1)';

    ctx.beginPath();
    ctx.rect(x, y, w, h);
    ctx.fill();

    ctx.beginPath();

    ctx.moveTo(x, y + lineLen);
    ctx.lineTo(x, y);
    ctx.lineTo(x + lineLen, y);

    ctx.moveTo(x + w - lineLen, y);
    ctx.lineTo(x + w, y);
    ctx.lineTo(x + w, y + lineLen);

    ctx.moveTo(x + w, y + h - lineLen);
    ctx.lineTo(x + w, y + h);
    ctx.lineTo(x + w - lineLen, y + h);

    ctx.moveTo(x + lineLen, y + h);
    ctx.lineTo(x, y + h);
    ctx.lineTo(x, y + h - lineLen);

    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(x, y + (h / 2));
    ctx.lineTo(x + w, y + (h / 2));
    ctx.lineWidth = 1;
    ctx.strokeStyle = '#6EE7B7';
    ctx.stroke();
  }
}

// OPTIMIZATION: RAF-based detection loop with throttling
async function webcamDetectionLoop(currentTime = 0) {
  // Throttle detection
  if (currentTime - lastWebcamDetection < WEBCAM_DETECTION_INTERVAL) {
    webcamAnimationFrame = requestAnimationFrame(webcamDetectionLoop);
    return;
  }

  lastWebcamDetection = currentTime;

  const video = DOM_CACHE.video;

  if (!video.paused && !video.ended && isModelLoaded && webcamStream) {
    try {
      const result = await human.detect(video);
      drawOverlay(result, video);

      const faceCount = result.face ? result.face.length : 0;

      if (faceCount > 0) {
        const face = result.face[0];
        const confidence = Math.round(face.boxScore * 100);
        const age = Math.round(face.age || 0);
        const gender = face.gender || '-';
        const emotion = (face.emotion && face.emotion[0]) ? face.emotion[0].emotion.toUpperCase() : '-';

        updateWebcamStats(faceCount, age + ' Thn', gender, emotion);

        if (faceCount === 1) {
          updateStatus(`✅ Wajah Terdeteksi (${confidence}%)`, 'success');
        } else {
          updateStatus(`⚠️ Terdeteksi ${faceCount} wajah. Pastikan hanya 1 wajah!`, 'warning');
        }
      } else {
        updateWebcamStats(0, '-', '-', '-');
        updateStatus('⚠️ Mencari wajah...', 'warning');
      }

    } catch (error) {
      console.error('Detection error:', error);
    }
  }

  // Continue loop
  if (webcamStream && video.srcObject) {
    webcamAnimationFrame = requestAnimationFrame(webcamDetectionLoop);
  }
}

// OPTIMIZATION: Dirty checking for webcam stats
function updateWebcamStats(faces, age, gender, emotion) {
  if (previousValues.webcamFaces !== faces) {
    document.getElementById('stat-faces').innerText = faces;
    previousValues.webcamFaces = faces;
  }

  if (previousValues.webcamAge !== age) {
    document.getElementById('stat-age').innerText = age;
    previousValues.webcamAge = age;
  }

  if (previousValues.webcamGender !== gender) {
    document.getElementById('stat-gender').innerText = gender;
    previousValues.webcamGender = gender;
  }

  if (previousValues.webcamEmotion !== emotion) {
    document.getElementById('stat-emotion').innerText = emotion;
    previousValues.webcamEmotion = emotion;
  }
}

// Capture dari webcam
document.getElementById('btn-capture-webcam').addEventListener('click', async function() {
  const label = document.getElementById('webcam-label').value.trim();
  if (!label) {
    alert('⚠️ Masukkan label foto terlebih dahulu!');
    return;
  }

  this.disabled = true;
  this.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pulse-animation">
      <circle cx="12" cy="12" r="10"></circle>
    </svg>
    Menganalisa...
  `;

  try {
    const result = await human.detect(DOM_CACHE.video);

    if (!result.face || result.face.length === 0) {
      throw new Error('❌ Wajah tidak terdeteksi! Posisikan wajah dengan jelas.');
    }

    if (result.face.length > 1) {
      throw new Error('❌ Terdeteksi lebih dari 1 wajah! Pastikan hanya ada 1 wajah di frame.');
    }

    await saveFaceDescriptor(Array.from(result.face[0].embedding), label);

  } catch (error) {
    alert(error.message);
    this.disabled = false;
    this.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
        <circle cx="12" cy="13" r="4"></circle>
      </svg>
      Scan & Simpan Wajah
    `;
  }
});

// ==================== UPLOAD SECTION ====================

const imageElement = document.getElementById('image-to-crop');
const rotationSlider = document.getElementById('rotation-slider');
const rotationDisplay = document.getElementById('rotation-val-display');
const uploadZone = document.getElementById('upload-zone');

// File upload handler
document.getElementById('file-upload').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;

  uploadZone.classList.add('active');

  // Check if HEIC
  if (file.type === "image/heic" || file.name.toLowerCase().endsWith('.heic')) {
    updateUploadStatus('⏳ Mengonversi HEIC ke JPG...', 'warning');

    heic2any({
        blob: file,
        toType: "image/jpeg",
        quality: 0.8
      })
      .then(function(conversionResult) {
        const url = URL.createObjectURL(conversionResult);
        setupCropper(url);
        updateUploadStatus('✅ Konversi berhasil. Sesuaikan posisi wajah.', 'info');
      })
      .catch(function(e) {
        console.error(e);
        alert('❌ Gagal convert HEIC: ' + e.message);
        updateUploadStatus('❌ Gagal membaca file HEIC.', 'danger');
        uploadZone.classList.remove('active');
      });
  } else {
    // Regular image
    const reader = new FileReader();
    reader.onload = function(event) {
      setupCropper(event.target.result);
    };
    reader.readAsDataURL(file);
  }
});

// Setup Cropper
async function setupCropper(imgSrc) {
  if (cropper) {
    cropper.destroy();
    cropper = null;
  }

  // Stop existing detection
  stopUploadDetection();

  imageElement.src = imgSrc;
  document.getElementById('editor-area').style.display = 'block';
  resetRotationUI();

  cropper = new Cropper(imageElement, {
    aspectRatio: 1,
    viewMode: 1,
    autoCropArea: 0.8,
    responsive: true,
    ready: function() {
      startUploadDetection();
    },
    crop: function() {
      debouncedUploadDetection();
    }
  });

  document.getElementById('btn-upload-save').disabled = false;
}

// OPTIMIZATION: Interval-based detection instead of continuous loop
function startUploadDetection() {
  if (!isModelLoaded || !cropper) return;

  // Initial detection
  detectFaceOnCropper();

  // Periodic detection
  uploadDetectionInterval = setInterval(() => {
    detectFaceOnCropper();
  }, UPLOAD_DETECTION_DEBOUNCE);
}

function stopUploadDetection() {
  if (uploadDetectionInterval) {
    clearInterval(uploadDetectionInterval);
    uploadDetectionInterval = null;
  }

  if (uploadDetectionTimeout) {
    clearTimeout(uploadDetectionTimeout);
    uploadDetectionTimeout = null;
  }
}

function debouncedUploadDetection() {
  clearTimeout(uploadDetectionTimeout);
  uploadDetectionTimeout = setTimeout(() => {
    detectFaceOnCropper();
  }, 300);
}

// --- Fungsi Baru untuk Menggambar di Upload Area ---

function drawUploadOverlay(result) {
  const canvas = document.getElementById('upload-detection-canvas');
  const ctx = canvas.getContext('2d');
  const container = document.querySelector('.img-container');

  if (canvas.width !== container.offsetWidth || canvas.height !== container.offsetHeight) {
    canvas.width = container.offsetWidth;
    canvas.height = container.offsetHeight;
  }

  ctx.clearRect(0, 0, canvas.width, canvas.height);
  if (!cropper || !result.face || result.face.length === 0) return;
  const face = result.face[0];
  const cropBoxData = cropper.getCropBoxData();
  const scaleX = cropBoxData.width / 512;
  const scaleY = cropBoxData.height / 512;
  const x = cropBoxData.left + (face.box[0] * scaleX);
  const y = cropBoxData.top + (face.box[1] * scaleY);
  const w = face.box[2] * scaleX;
  const h = face.box[3] * scaleY;

  const lineLen = 15;
  const color = '#D1FAE5';
  ctx.lineWidth = 2;
  ctx.strokeStyle = color;
  ctx.fillStyle = 'rgba(0, 210, 255, 0.15)'; //
  ctx.beginPath();
  ctx.rect(x, y, w, h);
  ctx.fill();
  ctx.beginPath();
  ctx.moveTo(x, y + lineLen);
  ctx.lineTo(x, y);
  ctx.lineTo(x + lineLen, y);
  ctx.moveTo(x + w - lineLen, y);
  ctx.lineTo(x + w, y);
  ctx.lineTo(x + w, y + lineLen);
  ctx.moveTo(x + w, y + h - lineLen);
  ctx.lineTo(x + w, y + h);
  ctx.lineTo(x + w - lineLen, y + h);
  ctx.moveTo(x + lineLen, y + h);
  ctx.lineTo(x, y + h);
  ctx.lineTo(x, y + h - lineLen);
  ctx.stroke();
}

async function detectFaceOnCropper() {
  if (!cropper || !isModelLoaded) return;

  try {
    const croppedCanvas = cropper.getCroppedCanvas({
      width: 512,
      height: 512
    });

    const result = await human.detect(croppedCanvas);
    drawUploadOverlay(result);

    if (result.face && result.face.length > 0) {
      const face = result.face[0];
      const confidence = Math.round(face.boxScore * 100);
      const age = Math.round(face.age);
      const gender = face.gender || '-';
      const emotion = face.emotion?. [0]?.emotion || 'neutral';

      updateUploadStatus(
        `✅ Wajah Terdeteksi! (${confidence}%) • Age: ~${age} • ${gender} • ${emotion}`,
        'success'
      );

      document.getElementById('btn-upload-save').disabled = false;

    } else {
      updateUploadStatus('⚠️ Wajah belum terdeteksi. Sesuaikan posisi crop agar wajah terlihat jelas.', 'warning');
      document.getElementById('btn-upload-save').disabled = true;
    }

  } catch (error) {
    console.error('Detection error:', error);
    updateUploadStatus('⚠️ Sedang menganalisa...', 'info');
  }
}

// Rotation slider
rotationSlider.addEventListener('input', function() {
  const degree = parseInt(this.value);
  rotationDisplay.innerText = degree + '°';
  if (cropper) {
    cropper.rotateTo(degree);
    debouncedUploadDetection();
  }
});

function resetRotation() {
  resetRotationUI();
  if (cropper) {
    cropper.reset();
    debouncedUploadDetection();
  }
}

function resetRotationUI() {
  rotationSlider.value = 0;
  rotationDisplay.innerText = '0°';
}

// Save upload
document.getElementById('btn-upload-save').addEventListener('click', async function() {
  const label = document.getElementById('upload-label').value.trim();
  if (!cropper || !label) {
    alert('⚠️ Data tidak lengkap!');
    return;
  }

  const btn = this;
  btn.disabled = true;
  btn.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pulse-animation">
      <circle cx="12" cy="12" r="10"></circle>
    </svg>
    Memproses...
  `;
  updateUploadStatus('⏳ Mengekstrak face descriptor...', 'info');

  const croppedCanvas = cropper.getCroppedCanvas({
    width: 512,
    height: 512
  });

  try {
    const result = await human.detect(croppedCanvas);

    if (!result.face || result.face.length === 0) {
      throw new Error('❌ Wajah tidak terdeteksi pada area crop! Pastikan crop pas di wajah.');
    }

    if (result.face.length > 1) {
      throw new Error('❌ Terdeteksi lebih dari 1 wajah! Pastikan hanya ada 1 wajah di area crop.');
    }

    await saveFaceDescriptor(Array.from(result.face[0].embedding), label);

    // Cleanup
    document.getElementById('file-upload').value = '';
    document.getElementById('editor-area').style.display = 'none';
    uploadZone.classList.remove('active');

    if (cropper) {
      cropper.destroy();
      cropper = null;
    }

    stopUploadDetection();

  } catch (e) {
    alert(e.message);
    btn.disabled = false;
    btn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
        <circle cx="8.5" cy="8.5" r="1.5"></circle>
        <polyline points="21 15 16 10 5 21"></polyline>
      </svg>
      Potong, Proses & Simpan
    `;
    updateUploadStatus('❌ ' + e.message, 'danger');
  }
});

// ==================== COMMON FUNCTIONS ====================

async function saveFaceDescriptor(descriptor, label) {
  try {
    const response = await fetch('<?= base_url('data-pegawai/save-face-descriptor') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
        'id_pegawai': idPegawai,
        'descriptor': JSON.stringify(descriptor),
        'label': label
      })
    });

    const result = await response.json();

    if (result.success) {
      alert('✅ Berhasil menyimpan face descriptor!');
      location.reload();
    } else {
      alert('❌ ' + result.message);
      location.reload();
    }
  } catch (error) {
    alert('❌ Server Error: ' + error.message);
  }
}

async function editLabel(id, currentLabel) {
  const newLabel = prompt('Ubah Label:', currentLabel);
  if (!newLabel || newLabel === currentLabel) return;

  try {
    const response = await fetch('<?= base_url('data-pegawai/update-descriptor-label') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
        'id': id,
        'label': newLabel
      })
    });

    const res = await response.json();
    if (res.success) {
      location.reload();
    } else {
      alert('❌ ' + res.message);
    }
  } catch (e) {
    alert('❌ ' + e.message);
  }
}

// OPTIMIZATION: Dirty checking for status updates
function updateStatus(msg, type) {
  if (msg === previousValues.statusMessage && type === previousValues.statusType) {
    return;
  }

  previousValues.statusMessage = msg;
  previousValues.statusType = type;

  DOM_CACHE.statusDiv.className = `status-alert alert-${type} text-center`;
  DOM_CACHE.statusDiv.style.display = 'block';
  DOM_CACHE.messageDiv.innerHTML = msg;
}

function updateUploadStatus(msg, type) {
  const el = document.getElementById('upload-status');
  el.style.display = 'block';
  el.className = `status-alert alert-${type}`;
  document.getElementById('upload-message').innerHTML = msg;
}

// ==================== TAB HANDLERS ====================

document.getElementById('tab-webcam-link').addEventListener('shown.bs.tab', () => {
  stopUploadDetection();
  startWebcam();
});

document.getElementById('tab-upload-link').addEventListener('shown.bs.tab', () => {
  stopWebcam();

  // Resume upload detection if cropper active
  if (cropper && !uploadDetectionInterval) {
    startUploadDetection();
  }
});

// ==================== CLEANUP ====================

window.addEventListener('beforeunload', () => {
  stopWebcam();
  stopUploadDetection();

  if (cropper) {
    cropper.destroy();
    cropper = null;
  }
});

// ==================== PAGE VISIBILITY API ====================
document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    console.log('⏸️ Tab hidden - detection paused');
  } else {
    console.log('▶️ Tab visible - detection resumed');
  }
});


// ==================== DOM INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function() {
  // Cache DOM elements
  DOM_CACHE.video = document.getElementById('webcam-video');
  DOM_CACHE.btnWebcam = document.getElementById('btn-capture-webcam');
  DOM_CACHE.statusDiv = document.getElementById('face-status');
  DOM_CACHE.messageDiv = document.getElementById('face-message');

  // Initialize Human.js
  initHuman();
});
</script>

<?= $this->endSection() ?>