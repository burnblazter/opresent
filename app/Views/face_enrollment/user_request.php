<?php
// \app\Views\face_enrollment\user_request.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/human.js') ?>"></script>

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

/* --- MAIN CARD --- */
.main-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 24px;
}

/* --- INSTRUCTION BOX --- */
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

/* --- WEBCAM SECTION --- */
.video-container {
  position: relative;
  display: inline-block;
  border-radius: 8px;
  overflow: hidden;
  transform: scaleX(-1);
  border: 2px solid #dee2e6;
  max-width: 640px;
  width: 100%;
  margin: 0 auto;
  background: #000;
}

#webcam-video {
  width: 100%;
  height: auto;
  display: block;
}

#webcam-canvas {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

/* --- TECH STATS --- */
.tech-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 10px;
  background: #1e293b;
  color: #00d2ff;
  padding: 12px;
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  border-radius: 0 0 12px 12px;
  margin-top: -4px;
}

.tech-stat-item {
  text-align: center;
  padding: 6px;
  background: rgba(0, 210, 255, 0.1);
  border-radius: 6px;
  border: 1px solid rgba(0, 210, 255, 0.3);
}

.tech-stat-item strong {
  color: #fff;
  display: block;
  font-size: 1rem;
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

/* --- FORM --- */
.form-label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
  display: block;
  font-size: 0.95rem;
}

.form-control {
  width: 100%;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 0.95rem;
  transition: all 0.2s;
  background-color: #fff;
}

.form-control:focus {
  border-color: #1e3a8a;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  outline: none;
}

textarea.form-control {
  resize: vertical;
  min-height: 80px;
}

/* Checkbox Custom */
.form-check {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  background: #f1f5f9;
  padding: 12px;
  border-radius: 8px;
  border: 1px dashed #cbd5e1;
}

.form-check-input {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #1e3a8a;
}

.form-check-label {
  font-size: 0.9rem;
  color: #475569;
  cursor: pointer;
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
  text-decoration: none;
}

.btn-primary {
  background: #1e3a8a;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #1e40af;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: #94a3b8;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-lg {
  padding: 14px 28px;
  font-size: 1.05rem;
  width: 100%;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 0.8rem;
}

/* --- REQUEST CARD --- */
.request-card {
  border: 1px solid #e2e8f0;
  padding: 20px;
  margin-bottom: 16px;
  border-radius: 12px;
  background: white;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.request-card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border-color: #cbd5e1;
}

.badge {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-block;
}

.badge-warning {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fcd34d;
}

.badge-success {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #6ee7b7;
}

.badge-danger {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fca5a5;
}

/* --- UTILITIES --- */
.text-center {
  text-align: center;
}

.text-muted {
  color: #64748b;
}

.mb-3 {
  margin-bottom: 1rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.d-flex {
  display: flex;
}

.justify-content-between {
  justify-content: space-between;
}

.align-items-center {
  align-items: center;
}

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

/* Responsive fixes */
@media (max-width: 768px) {
  .instruction-step {
    flex-direction: column;
  }

  .instruction-step .step-number {
    margin-bottom: 8px;
  }

  .btn-lg {
    font-size: 1rem;
  }
}
</style>

<div class="page-body">
  <div class="container-xl">

    <div class="row mb-3">
      <div class="col">
        <a href="<?= base_url('/') ?>" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Kembali ke Dashboard
        </a>
      </div>
    </div>

    <?php if ($todayCount >= 3): ?>
    <div class="status-alert alert-danger">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="15" y1="9" x2="9" y2="15"></line>
        <line x1="9" y1="9" x2="15" y2="15"></line>
      </svg>
      <div>
        <strong>Batas Harian Tercapai!</strong><br>
        Anda sudah melakukan 3 request hari ini. Silakan coba lagi besok untuk menjaga kualitas data.
      </div>
    </div>
    <?php elseif ($todayCount > 0): ?>
    <div class="status-alert alert-warning">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
        <line x1="12" y1="9" x2="12" y2="13"></line>
        <line x1="12" y1="17" x2="12.01" y2="17"></line>
      </svg>
      <div>
        Anda sudah melakukan <strong><?= $todayCount ?> request</strong> hari ini. <br>
        Sisa kuota: <strong><?= 3 - $todayCount ?> request</strong>.
      </div>
    </div>
    <?php endif; ?>

    <?php if ($todayCount < 3): ?>
    <div class="row">
      <div class="col-12 mb-4">
        <div class="card main-card">
          <div class="card-body" style="padding: 30px;">

            <div class="instruction-box">
              <h4>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Panduan Pendaftaran Wajah
              </h4>
              <div class="instruction-step">
                <div class="step-number">1</div>
                <div class="step-text">Lepas masker atau aksesoris wajah yang menutupi area utama. Pastikan pencahayaan
                  terang dan merata.</div>
              </div>
              <div class="instruction-step">
                <div class="step-number">2</div>
                <div class="step-text">Posisikan wajah tepat di tengah bingkai kamera. Tunggu hingga indikator berwarna
                  hijau.</div>
              </div>
              <div class="instruction-step">
                <div class="step-number">3</div>
                <div class="step-text">Isi form alasan dengan jujur. Admin akan memverifikasi foto Anda sebelum
                  disetujui.</div>
              </div>
            </div>

            <div id="face-status" class="status-alert alert-info" style="display: none;">
              <div id="face-message">Memuat kamera...</div>
            </div>

            <div class="text-center mb-4">
              <div class="video-container">
                <video id="webcam-video" autoplay playsinline muted></video>
                <canvas id="webcam-canvas"></canvas>
              </div>
              <div class="tech-stats">
                <div class="tech-stat-item">
                  <div>Wajah</div>
                  <strong id="stat-faces">0</strong>
                </div>
                <div class="tech-stat-item">
                  <div>Akurasi</div>
                  <strong id="stat-score">0%</strong>
                </div>
                <div class="tech-stat-item">
                  <div>Jarak</div>
                  <strong id="stat-distance">-</strong>
                </div>
                <div class="tech-stat-item">
                  <div>Emosi</div>
                  <strong id="stat-emotion">-</strong>
                </div>
              </div>
            </div>

            <div class="row justify-content-center">
              <div class="col-md-8 col-lg-6">

                <div class="mb-3">
                  <label class="form-label">
                    Judul Foto / Label <span style="color:red">*</span>
                  </label>
                  <input type="text" class="form-control" id="webcam-label"
                    placeholder="Contoh: Wajah Terbaru Tanpa Kacamata" value="Request <?= date('d/m/Y H:i') ?>">
                </div>

                <div class="mb-3">
                  <label class="form-label">Alasan Request <span style="color:red">*</span></label>
                  <select class="form-control" id="reason-select" onchange="toggleOtherReason()">
                    <option value="" disabled selected>-- Pilih Alasan --</option>

                    <optgroup label="Masalah Teknis">
                      <option value="Wajah sulit terdeteksi saat presensi">Wajah sulit terdeteksi saat presensi</option>
                      <option value="Sistem sering salah mengenali wajah">Sistem sering salah mengenali wajah</option>
                      <option value="Foto lama buram/gelap">Foto lama buram/gelap</option>
                    </optgroup>

                    <optgroup label="Perubahan Fisik">
                      <option value="Perubahan penampilan (Kacamata/Hijab/Jenggot)">Perubahan penampilan
                        (Kacamata/Hijab/Jenggot)</option>
                      <option value="Penurunan berat badan/perubahan struktur wajah">Penurunan berat badan/perubahan
                        struktur wajah</option>
                    </optgroup>

                    <optgroup label="Administrasi">
                      <option value="Pendaftaran Pegawai Baru">Pendaftaran Siswa Baru</option>
                      <option value="Update foto profil berkala">Update foto profil berkala</option>
                      <option value="Lainnya">Lainnya (Jelaskan dibawah)</option>
                    </optgroup>
                  </select>
                </div>

                <div class="mb-3" id="additional-info-group">
                  <label class="form-label">Keterangan Tambahan (Opsional)</label>
                  <textarea class="form-control" id="additional-notes" rows="3"
                    placeholder="Jelaskan lebih detail jika diperlukan (Misal: Saya baru operasi gigi, dll)"></textarea>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="confirm-check">
                  <label class="form-check-label" for="confirm-check">
                    Saya menjamin bahwa ini adalah foto wajah saya sendiri yang terbaru dan asli.
                  </label>
                </div>

                <button class="btn btn-primary btn-lg" id="btn-submit-request" disabled>
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  Kirim Request ke Admin
                </button>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header" style="background: #f8fafc; border-bottom: 2px solid #e5e7eb; padding: 16px 24px;">
            <h3 class="card-title" style="color: #1e3a8a; font-weight: 700; font-size: 1.1rem; margin:0;">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                style="margin-right: 8px;">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
              Riwayat Request Anda (<?= count($requests) ?>)
            </h3>
          </div>
          <div class="card-body" style="padding: 24px;">

            <?php if (empty($requests)): ?>
            <div class="status-alert alert-warning">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              Belum ada riwayat request.
            </div>
            <?php else: ?>

            <?php foreach ($requests as $req): ?>
            <div class="request-card">
              <div class="d-flex justify-content-between align-items-center">
                <div style="flex: 1;">
                  <h4 style="margin: 0 0 6px 0; color: #1e3a8a; font-size: 1.05rem; font-weight: 700;">
                    <?= esc($req->label) ?>
                  </h4>
                  <div class="text-muted" style="font-size: 0.85rem; margin-bottom: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" style="vertical-align: text-top; margin-right:4px;">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <?= date('d M Y, H:i', strtotime($req->created_at)) ?>
                  </div>

                  <div style="margin-bottom: 12px;">
                    <?php if ($req->status === 'pending'): ?>
                    <span class="badge badge-warning">⏳ Menunggu Approval</span>
                    <?php elseif ($req->status === 'approved'): ?>
                    <span class="badge badge-success">✅ Disetujui</span>
                    <?php else: ?>
                    <span class="badge badge-danger">❌ Ditolak</span>
                    <?php endif; ?>
                  </div>

                  <div
                    style="background: #f8fafc; padding: 10px; border-radius: 6px; font-size: 0.9rem; border: 1px solid #f1f5f9;">
                    <div style="margin-bottom: 4px;">
                      <span style="color: #64748b; font-weight: 600;">Alasan:</span>
                      <span style="color: #334155;"><?= esc($req->reason) ?></span>
                    </div>
                    <?php 
                        // Jika ada data tambahan (biasanya di database digabung atau kolom terpisah)
                        // Disini asumsi kita gabung string atau handle di backend.
                        // Jika ingin menampilkan data tambahan manual disini bisa ditambah logic viewnya
                    ?>
                  </div>

                  <?php if ($req->status === 'rejected' && $req->rejection_reason): ?>
                  <div
                    style="margin-top: 10px; padding: 10px; background: #fee2e2; border-left: 3px solid #ef4444; border-radius: 4px;">
                    <strong style="color: #991b1b; font-size: 0.9rem;">Alasan Penolakan:</strong><br>
                    <span style="color: #7f1d1d; font-size: 0.9rem;"><?= esc($req->rejection_reason) ?></span>
                  </div>
                  <?php endif; ?>
                </div>

                <?php if ($req->status === 'pending'): ?>
                <div style="margin-left: 15px;">
                  <a href="<?= base_url('face-enrollment/cancel/' . $req->id) ?>" class="btn btn-danger btn-sm"
                    onclick="return confirm('Apakah Anda yakin ingin membatalkan request ini?')">
                    Batal
                  </a>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>

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
let lastWebcamDetection = 0;
let currentFaceData = null;

const WEBCAM_DETECTION_INTERVAL = 150; // Sedikit dipercepat

// ==================== HUMAN.JS CONFIG ====================
const human = new Human.Human({
  modelBasePath: '<?= base_url('assets/models/') ?>',
  backend: 'wasm',
  face: {
    enabled: true,
    detector: {
      modelPath: 'blazeface.json',
      maxDetected: 1,
      minConfidence: 0.65
    }, // Confidence dinaikkan dikit
    mesh: {
      enabled: true
    },
    description: {
      enabled: true
    },
    emotion: {
      enabled: true
    },
    iris: {
      enabled: true
    } // Tambah deteksi mata
  },
  body: {
    enabled: false
  },
  hand: {
    enabled: false
  },
});

// ==================== INIT HUMAN ====================
async function initHuman() {
  try {
    updateStatus('Sedang memuat model AI...', 'info');
    await human.load();
    await human.warmup();
    isModelLoaded = true;
    updateStatus('✅ AI Siap', 'success');
  } catch (error) {
    console.error('❌ Error init Human:', error);
    updateStatus('❌ Gagal memuat AI. Refresh halaman.', 'danger');
  }
}

// ==================== WEBCAM ====================
async function startWebcam() {
  if (!isModelLoaded) await initHuman();

  try {
    updateStatus('⏳ Membuka kamera...', 'info');

    webcamStream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'user',
        width: {
          ideal: 640
        },
        height: {
          ideal: 480
        }
      }
    });

    const video = document.getElementById('webcam-video');
    video.srcObject = webcamStream;
    await video.play();

    // Enable button logic moved to checkFormValidity
    checkFormValidity();

    updateStatus('✅ Kamera Aktif. Posisikan wajah.', 'success');
    webcamDetectionLoop();
  } catch (error) {
    console.error('Webcam error:', error);
    updateStatus('❌ Gagal akses kamera. Izinkan akses.', 'danger');
  }
}

function drawOverlay(result, video) {
  const canvas = document.getElementById('webcam-canvas');
  const ctx = canvas.getContext('2d');

  if (canvas.width !== video.videoWidth) {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
  }

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  if (result.face && result.face.length > 0) {
    const face = result.face[0];
    const [x, y, w, h] = face.box;

    // Warna box berubah berdasarkan confidence
    ctx.strokeStyle = face.boxScore > 0.8 ? '#10B981' : '#F59E0B'; // Hijau jika bagus, kuning jika ragu
    ctx.lineWidth = 3;
    ctx.strokeRect(x, y, w, h);
  }
}

async function webcamDetectionLoop(currentTime = 0) {
  if (currentTime - lastWebcamDetection < WEBCAM_DETECTION_INTERVAL) {
    webcamAnimationFrame = requestAnimationFrame(webcamDetectionLoop);
    return;
  }

  lastWebcamDetection = currentTime;
  const video = document.getElementById('webcam-video');

  if (!video.paused && isModelLoaded && webcamStream) {
    try {
      const result = await human.detect(video);
      drawOverlay(result, video);

      const faceCount = result.face ? result.face.length : 0;

      if (faceCount > 0) {
        const face = result.face[0];

        // Filter kualitas wajah minimal
        if (face.boxScore < 0.6) {
          updateStatus('⚠️ Wajah kurang jelas / cahaya kurang.', 'warning');
          currentFaceData = null;
        } else {
          currentFaceData = face;

          // Update Stats
          document.getElementById('stat-faces').innerText = faceCount;
          document.getElementById('stat-score').innerText = Math.round(face.boxScore * 100) + '%';

          // Estimasi jarak kasar berdasarkan lebar wajah di frame
          let distanceMsg = "Ideal";
          if (face.box[2] < 150) distanceMsg = "Terlalu Jauh";
          if (face.box[2] > 350) distanceMsg = "Terlalu Dekat";
          document.getElementById('stat-distance').innerText = distanceMsg;

          document.getElementById('stat-emotion').innerText = (face.emotion?. [0]?.emotion || '-').toUpperCase();

          if (faceCount === 1) {
            if (distanceMsg === "Ideal") {
              updateStatus(`✅ Wajah Siap Diambil`, 'success');
            } else {
              updateStatus(`⚠️ ${distanceMsg}`, 'warning');
            }
          } else {
            updateStatus(`⚠️ Terdeteksi ${faceCount} wajah!`, 'warning');
          }
        }
      } else {
        currentFaceData = null;
        document.getElementById('stat-faces').innerText = '0';
        document.getElementById('stat-score').innerText = '0%';
        updateStatus('⚠️ Mencari wajah...', 'warning');
      }

      checkFormValidity(); // Cek terus validitas tombol submit

    } catch (error) {
      console.error('Detection error:', error);
    }
  }

  if (webcamStream) {
    webcamAnimationFrame = requestAnimationFrame(webcamDetectionLoop);
  }
}

// ==================== UI LOGIC ====================

function toggleOtherReason() {
  const select = document.getElementById('reason-select');
  const noteArea = document.getElementById('additional-notes');

  // Jika user pilih "Lainnya", fokus ke textarea
  if (select.value === 'Lainnya') {
    noteArea.focus();
  }
  checkFormValidity();
}

// Fungsi Validasi Tombol Submit
function checkFormValidity() {
  const btn = document.getElementById('btn-submit-request');
  const label = document.getElementById('webcam-label').value.trim();
  const reason = document.getElementById('reason-select').value;
  const additionalInfo = document.getElementById('additional-notes').value.trim();
  const isConfirmed = document.getElementById('confirm-check').checked;

  // Validasi Logika:
  // 1. Wajah harus terdeteksi
  // 2. Label tidak boleh kosong
  // 3. Alasan harus dipilih
  // 4. Jika alasan "Lainnya", keterangan tambahan wajib diisi
  // 5. Checkbox konfirmasi harus dicentang

  let isValid = currentFaceData !== null &&
    label !== '' &&
    reason !== '' &&
    isConfirmed;

  if (reason === 'Lainnya' && additionalInfo === '') {
    isValid = false;
  }

  btn.disabled = !isValid;
}

// Event Listeners untuk validasi realtime
document.getElementById('webcam-label').addEventListener('input', checkFormValidity);
document.getElementById('reason-select').addEventListener('change', toggleOtherReason); // handle change
document.getElementById('additional-notes').addEventListener('input', checkFormValidity);
document.getElementById('confirm-check').addEventListener('change', checkFormValidity);


// ==================== SUBMIT REQUEST ====================
document.getElementById('btn-submit-request').addEventListener('click', async function() {
  if (!currentFaceData) return;

  const label = document.getElementById('webcam-label').value.trim();
  const reasonSelect = document.getElementById('reason-select').value;
  const additionalInfo = document.getElementById('additional-notes').value.trim();

  // Gabungkan Reason dan Additional Info agar tersimpan lengkap di database
  // Format: "Alasan Utama (Info Tambahan)"
  let finalReason = reasonSelect;
  if (additionalInfo) {
    finalReason += ` (${additionalInfo})`;
  }

  this.disabled = true;
  this.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pulse-animation">
      <circle cx="12" cy="12" r="10"></circle>
    </svg>
    Mengirim...
  `;

  try {
    // Capture image
    const video = document.getElementById('webcam-video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');

    // Flip horizontally
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/png');

    const formData = new URLSearchParams();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    formData.append('descriptor', JSON.stringify(Array.from(currentFaceData.embedding)));
    formData.append('label', label);
    formData.append('reason', finalReason); // Mengirim alasan yang sudah digabung
    formData.append('image_data', imageData);

    const response = await fetch('<?= base_url('face-enrollment/submit') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      alert('✅ Berhasil: ' + result.message);
      location.reload();
    } else {
      throw new Error(result.message);
    }
  } catch (error) {
    alert('❌ Gagal: ' + error.message);
    this.disabled = false;
    this.innerHTML = `Kirim Request ke Admin`;
    checkFormValidity();
  }
});

// ==================== UTILS ====================
function updateStatus(msg, type) {
  const statusDiv = document.getElementById('face-status');
  statusDiv.className = `status-alert alert-${type}`;
  statusDiv.style.display = 'block';
  document.getElementById('face-message').innerHTML = msg;
}

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', function() {
  initHuman().then(() => {
    <?php if ($todayCount < 3): ?>
    startWebcam();
    <?php endif; ?>
  });
});

window.addEventListener('beforeunload', () => {
  if (webcamStream) {
    webcamStream.getTracks().forEach(track => track.stop());
  }
});
</script>

<?= $this->endSection() ?>