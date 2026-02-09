<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/human.js') ?>"></script>

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

<script language="JavaScript">
let stream = null;
let isModelLoaded = false;
let isVerifying = false;
let faceDatabase = [];
let animationFrameId = null;
let lastDetectionTime = 0;
let currentAge = 0;
let currentEmotion = 'neutral';
let currentSimilarity = 0;
let isFaceRegistered = false;
const FACE_ENROLLMENT_URL = '<?= base_url('face-enrollment') ?>';

const DOM_CACHE = {
  video: null,
  canvas: null,
  button: null,
  btnText: null,
  statusDiv: null,
  messageDiv: null,
  detailsDiv: null,
  progressFill: null,
  faceVerifiedInput: null,
};

let previousValues = {
  statusMessage: '',
  statusType: '',
  progressPercentage: -1,
  statusDetails: '',
  buttonDisabled: null,
  buttonText: ''
};

let headMovementState = {
  required: null,
  completed: false,
  detectionCount: 0,
  requiredCount: 8,
  initialRotation: null,
  lastCheckTime: 0
};

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
      enabled: true,
    },
    emotion: {
      enabled: true,
      minConfidence: 0.1,
    },
    iris: {
      enabled: false
    },
    antispoof: {
      enabled: false
    },
    liveness: {
      enabled: false
    },
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
    equalization: false,
  }
});

const DETECTION_THROTTLE_MS = 200;
const DEBOUNCE_HEAD_MOVEMENT_MS = 150;

const emotionMap = {
  happy: 'Senang',
  sad: 'Sedih',
  neutral: 'Netral',
  angry: 'Marah',
  fearful: 'Takut',
  surprised: 'Terkejut',
  disgusted: 'Jijik'
};

const headMovementInstructions = {
  up: {
    icon: '⬆️',
    text: 'DONGAKKAN KEPALA',
    subtitle: 'Lihat ke atas'
  },
  down: {
    icon: '⬇️',
    text: 'TUNDUKKAN KEPALA',
    subtitle: 'Lihat ke bawah'
  },
  right: {
    icon: '⬅️',
    text: 'TOLEH KE KIRI',
    subtitle: 'Putar kepala ke kiri'
  },
  left: {
    icon: '➡️',
    text: 'TOLEH KE KANAN',
    subtitle: 'Putar kepala ke kanan'
  }
};

function selectRandomMovement() {
  const movements = ['up', 'down', 'left', 'right'];
  headMovementState.required = movements[Math.floor(Math.random() * movements.length)];
  console.log('✅ Gerakan yang diperlukan:', headMovementState.required);
}

function showPermissionInstructions(type, error) {
  let title = '';
  let html = '';

  if (type === 'camera') {
    title = '📷 Akses Kamera Diperlukan';

    if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
      html = `
        <div style="text-align: left;">
          <p><strong>Anda memblokir akses kamera.</strong></p>
          <p>Untuk mengaktifkan kembali:</p>
          <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Klik ikon <strong>🔒 gembok</strong> atau <strong>ℹ️ info</strong> di address bar browser</li>
            <li>Cari pengaturan <strong>"Kamera"</strong></li>
            <li>Ubah dari <strong>"Blokir"</strong> menjadi <strong>"Izinkan"</strong></li>
            <li>Refresh halaman ini (F5)</li>
          </ol>
        </div>
      `;
    } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
      html = `
        <div style="text-align: left;">
          <p><strong>Kamera tidak ditemukan!</strong></p>
          <p>Kemungkinan penyebab:</p>
          <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Kamera tidak terhubung ke perangkat</li>
            <li>Driver kamera belum terinstal</li>
            <li>Kamera sedang digunakan aplikasi lain</li>
          </ul>
        </div>
      `;
    } else {
      html = `
        <div style="text-align: left;">
          <p><strong>Terjadi kesalahan:</strong> ${error.message}</p>
          <p><strong>Solusi umum:</strong></p>
          <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Refresh halaman (F5)</li>
            <li>Pastikan browser mendukung kamera</li>
            <li>Coba gunakan browser Chrome/Firefox terbaru</li>
          </ul>
        </div>
      `;
    }
  }

  Swal.fire({
    icon: 'warning',
    title: title,
    html: html,
    confirmButtonText: 'Saya Mengerti',
    confirmButtonColor: '#1e3a8a',
    width: '600px'
  });
}

function showAITransparency() {
  const similarity = (currentSimilarity * 100).toFixed(1);
  const registeredFaces = faceDatabase.length;

  let status = '';
  let statusColor = '';

  if (currentSimilarity >= 0.75) {
    status = 'Sangat Cocok ✅';
    statusColor = '#28a745';
  } else if (currentSimilarity >= 0.62) {
    status = 'Cocok ✓';
    statusColor = '#ffc107';
  } else {
    status = 'Tidak Cocok ❌';
    statusColor = '#dc3545';
  }

  Swal.fire({
    title: '🤖 Detail AI',
    html: `
      <div style="text-align: left;">
        <h5 style="margin-top: 0; color: #1e3a8a;">📊 Hasil Verifikasi Wajah</h5>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
          <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: ${statusColor}; font-weight: bold;">${status}</span></p>
          <p style="margin: 5px 0;"><strong>Tingkat Kecocokan:</strong> ${similarity}%</p>
          <div style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0;">
            <div style="background: ${statusColor}; height: 100%; width: ${similarity}%;"></div>
          </div>
        </div>
        
        <h5 style="color: #1e3a8a;">📸 Data Terdeteksi</h5>
        <ul style="font-size: 14px; line-height: 1.6;">
          <li><strong>Usia Estimasi:</strong> ~${currentAge} tahun</li>
          <li><strong>Emosi:</strong> ${emotionMap[currentEmotion] || 'Netral'}</li>
          <li><strong>Wajah Terdaftar:</strong> ${registeredFaces} data</li>
        </ul>
      </div>
    `,
    confirmButtonText: 'Mengerti',
    confirmButtonColor: '#1e3a8a',
    width: '650px'
  });
}

async function setupCamera() {
  try {
    updateStatus('Meminta izin kamera...', 'info');
    const video = DOM_CACHE.video;

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      const notSupportedError = new Error('Browser tidak mendukung kamera.');
      showPermissionInstructions('camera', notSupportedError);
      throw notSupportedError;
    }

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
        stream = await navigator.mediaDevices.getUserMedia(constraint);
        break;
      } catch (err) {
        lastError = err;
      }
    }

    if (!stream) {
      showPermissionInstructions('camera', lastError);
      throw lastError;
    }

    video.srcObject = stream;

    await new Promise((resolve, reject) => {
      video.onloadedmetadata = () => video.play().then(resolve).catch(reject);
      setTimeout(() => reject(new Error('Timeout loading video')), 10000);
    });

    console.log('✅ Kamera berhasil diaktifkan');
    return true;
  } catch (error) {
    console.error('❌ Error kamera:', error);
    updateStatus('Gagal mengaktifkan kamera.', 'danger');
    return false;
  }
}

function captureImage() {
  const video = DOM_CACHE.video;
  const canvas = DOM_CACHE.canvas;
  const context = canvas.getContext('2d', {
    alpha: false
  });

  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  const imageData = canvas.toDataURL('image/jpeg', 0.75);
  context.clearRect(0, 0, canvas.width, canvas.height);

  return imageData;
}

async function initHuman() {
  try {
    const preloadTime = localStorage.getItem('humanjs_preloaded');
    const isCached = preloadTime && (Date.now() - parseInt(preloadTime) < 1800000);

    if (isCached) {
      updateStatus('⚡ Menggunakan model dari cache...', 'info');
      console.log('⚡ Using cached Human.js models (pre-loaded from dashboard)');
    } else {
      updateStatus('Memuat model AI...', 'info');
      console.log('📥 Loading Human.js models (not cached)');
    }

    try {
      await human.load();

      if (!isCached) {
        console.log('🔥 First-time load, performing warmup...');
        await human.warmup();
      } else {
        console.log('⚡ Skipping warmup (already cached)');
      }

      console.log('Backend:', human.tf.getBackend());

    } catch (wasmError) {
      console.warn('⚠️ Gagal memuat WASM, mencoba fallback ke WebGL...', wasmError);
      human.config.backend = 'webgl';
      await human.load();
      await human.warmup();
      console.log('✅ Berhasil recover menggunakan WebGL');
      updateStatus('Mode Kompatibilitas (WebGL) Aktif', 'warning');
    }

    updateStatus('Memuat data wajah...', 'info');
    await loadFaceDatabase();

    isModelLoaded = true;
    selectRandomMovement();
    showHeadMovementInstruction();
    checkButtonState();
    startFaceDetectionRAF();

  } catch (error) {
    console.error('❌ Error init Human:', error);
    updateStatus('Gagal memuat model AI.', 'danger');
    Swal.fire({
      icon: 'error',
      title: 'Model AI Gagal Dimuat',
      html: `<p>Refresh halaman atau periksa koneksi internet.</p>`,
      confirmButtonColor: '#1e3a8a'
    });
  }
}

async function loadFaceDatabase() {
  try {
    const response = await fetch('<?= base_url('presensi/get-face-descriptors') ?>', {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = await response.json();
    if (data.error) throw new Error(data.error);

    let userFaces = data.filter(item => item.id_pegawai == <?= $user_profile->id_pegawai ?>);

    if (userFaces.length === 0) {
      updateStatus('Wajah tidak ditemukan di database.', 'danger');

      document.querySelector('.video-container').style.display = 'none';
      document.getElementById('verification-progress').style.display = 'none';
      document.getElementById('head-movement-instruction').style.display = 'none';

      Swal.fire({
        icon: 'error',
        title: 'Wajah Belum Terdaftar',
        text: 'Sistem tidak menemukan data wajah Anda untuk verifikasi.',
        showCancelButton: true,
        confirmButtonText: 'Request Pendaftaran Wajah',
        confirmButtonColor: '#1e3a8a', // Warna biru utama
        cancelButtonText: 'Tutup',
        cancelButtonColor: '#6c757d',
        allowOutsideClick: false,
        allowEscapeKey: false
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = FACE_ENROLLMENT_URL;
        }
      });

      return false;
    }

    faceDatabase = userFaces.map(item => ({
      id: item.id,
      nama: item.nama,
      descriptor: item.descriptor
    }));

    isFaceRegistered = true;
    console.log(`✅ Berhasil memuat ${faceDatabase.length} data wajah`);
    userFaces = null;
    return true; // Return true jika berhasil

  } catch (error) {
    console.error('❌ Error loading face database:', error);
    updateStatus('Gagal memuat database wajah.', 'danger');

    Swal.fire({
      icon: 'error',
      title: 'Gagal Memuat Data',
      text: 'Terjadi kesalahan saat mengambil data wajah. Silahkan refresh halaman.',
      confirmButtonColor: '#1e3a8a'
    });
    return false;
  }
}

function showHeadMovementInstruction() {
  const instructionDiv = document.getElementById('head-movement-instruction');
  const iconDiv = document.getElementById('instruction-icon');
  const textDiv = document.getElementById('instruction-text');
  const subtitleDiv = document.querySelector('.instruction-subtitle');

  const instruction = headMovementInstructions[headMovementState.required];

  iconDiv.textContent = instruction.icon;
  textDiv.textContent = instruction.text;
  subtitleDiv.textContent = instruction.subtitle;

  instructionDiv.style.display = 'block';
  document.getElementById('verification-progress').style.display = 'block';

  updateProgressIcon('progress-face', 'active');
}

function updateProgressIcon(id, status) {
  const icon = document.getElementById(id);
  if (!icon) return;

  icon.className = 'progress-item-icon';

  if (status === 'completed') {
    icon.textContent = '✅';
    icon.classList.add('completed');
  } else if (status === 'active') {
    icon.textContent = '⏳';
    icon.classList.add('active');
  } else {
    icon.textContent = '⏳';
    icon.classList.add('pending');
  }
}

function updateProgressBar(percentage) {
  if (Math.abs(percentage - previousValues.progressPercentage) < 2) return;

  previousValues.progressPercentage = percentage;

  const progressFill = DOM_CACHE.progressFill;
  if (progressFill) {
    progressFill.style.width = percentage + '%';
  }
}

function updateStatus(message, type = 'info', details = '') {
  if (message === previousValues.statusMessage &&
    type === previousValues.statusType &&
    details === previousValues.statusDetails) {
    return;
  }

  previousValues.statusMessage = message;
  previousValues.statusType = type;
  previousValues.statusDetails = details;

  const statusDiv = DOM_CACHE.statusDiv;
  const messageDiv = DOM_CACHE.messageDiv;
  const detailsDiv = DOM_CACHE.detailsDiv;

  statusDiv.className = `alert alert-${type}`;
  statusDiv.style.display = 'block';
  messageDiv.innerHTML = message;

  if (details) {
    detailsDiv.innerHTML = details +
      ' <a href="javascript:void(0)" onclick="showAITransparency()" style="font-size: 12px;">🤖 Detail AI</a>';
    detailsDiv.style.display = 'block';
  } else {
    detailsDiv.style.display = 'none';
  }
}

function checkButtonState() {
  const video = DOM_CACHE.video;
  const button = DOM_CACHE.button;
  const btnText = DOM_CACHE.btnText;

  if (stream && video.readyState === 4 && isModelLoaded && isFaceRegistered) {
    if (previousValues.buttonDisabled !== false) {
      button.disabled = false;
      previousValues.buttonDisabled = false;
    }

    const newText = 'Ambil Gambar & Verifikasi';
    if (previousValues.buttonText !== newText) {
      btnText.innerText = newText;
      previousValues.buttonText = newText;
    }

    updateStatus('✅ Sistem siap! Ikuti instruksi verifikasi.', 'success');
  } else if (!isFaceRegistered && isModelLoaded) {
    button.disabled = true;
    btnText.innerText = "Wajah Tidak Terdaftar";
  }
}

function checkHeadMovement(face, currentTime) {
  if (!face || !face.rotation) return false;

  if (currentTime - headMovementState.lastCheckTime < DEBOUNCE_HEAD_MOVEMENT_MS) {
    return false;
  }
  headMovementState.lastCheckTime = currentTime;

  const {
    angle
  } = face.rotation;

  const toDegrees = (rad) => rad * (180 / Math.PI);
  const pitch = toDegrees(angle.pitch || 0);
  const yaw = toDegrees(angle.yaw || 0);

  if (!headMovementState.initialRotation) {
    headMovementState.initialRotation = {
      pitch,
      yaw
    };
    return false;
  }

  const pitchDiff = pitch - headMovementState.initialRotation.pitch;
  const yawDiff = yaw - headMovementState.initialRotation.yaw;
  const threshold = 12;

  let isCorrectMovement = false;

  switch (headMovementState.required) {
    case 'up':
      isCorrectMovement = pitchDiff < -threshold;
      break;
    case 'down':
      isCorrectMovement = pitchDiff > threshold;
      break;
    case 'left':
      isCorrectMovement = yawDiff > threshold;
      break;
    case 'right':
      isCorrectMovement = yawDiff < -threshold;
      break;
  }

  if (isCorrectMovement) {
    headMovementState.detectionCount++;

    const percentage = (headMovementState.detectionCount / headMovementState.requiredCount) * 100;
    updateProgressBar(percentage);

    if (headMovementState.detectionCount >= headMovementState.requiredCount) {
      headMovementState.completed = true;
      updateProgressIcon('progress-movement', 'completed');

      const instructionDiv = document.getElementById('head-movement-instruction');
      if (instructionDiv) instructionDiv.style.display = 'none';

      updateStatus('✅ OK! Tahan...', 'success');
      return true;
    }
  } else {
    headMovementState.detectionCount = Math.max(0, headMovementState.detectionCount - 1);
    const percentage = (headMovementState.detectionCount / headMovementState.requiredCount) * 100;
    updateProgressBar(percentage);
  }

  return false;
}

function startFaceDetectionRAF() {
  const video = DOM_CACHE.video;

  if (!video || !video.srcObject) {
    setTimeout(startFaceDetectionRAF, 500);
    return;
  }

  const detectionLoop = async (currentTime) => {
    if (document.hidden) {
      animationFrameId = requestAnimationFrame(detectionLoop);
      return;
    }

    if (currentTime - lastDetectionTime < DETECTION_THROTTLE_MS) {
      animationFrameId = requestAnimationFrame(detectionLoop);
      return;
    }

    lastDetectionTime = currentTime;

    if (DOM_CACHE.button && DOM_CACHE.button.disabled) {
      checkButtonState();
    }

    if (isVerifying) {
      animationFrameId = requestAnimationFrame(detectionLoop);
      return;
    }

    try {
      const result = await human.detect(video);

      if (result.face && result.face.length > 0) {
        const face = result.face[0];
        updateProgressIcon('progress-face', 'completed');

        if (!headMovementState.completed) {
          updateProgressIcon('progress-movement', 'active');
          checkHeadMovement(face, currentTime);
          updateStatus('👤 Ikuti instruksi gerakan kepala', 'info');
          DOM_CACHE.faceVerifiedInput.value = 'false';

          animationFrameId = requestAnimationFrame(detectionLoop);
          return;
        }

        updateProgressIcon('progress-match', 'active');

        if (!face.embedding) {
          updateStatus('⚠️ Embedding tidak tersedia', 'warning');
          DOM_CACHE.faceVerifiedInput.value = 'false';
          animationFrameId = requestAnimationFrame(detectionLoop);
          return;
        }

        const match = findBestMatch(face.embedding);
        currentSimilarity = match.score;

        if (match.score < 0.62) {
          const helpLink =
            `<div class="mt-2">Susah terdeteksi? <a href="${FACE_ENROLLMENT_URL}" class="fw-bold text-decoration-none">Request Pendaftaran Wajah</a></div>`;

          updateStatus(
            '⚠️ Akurasi rendah, posisikan wajah dengan benar.',
            'warning',
            helpLink
          );

          DOM_CACHE.faceVerifiedInput.value = 'false';
          animationFrameId = requestAnimationFrame(detectionLoop);
          return;
        }

        currentAge = Math.round(face.age || 0);
        currentEmotion = (face.emotion && face.emotion[0]) ? face.emotion[0].emotion : 'neutral';

        const emotionText = emotionMap[currentEmotion] || 'Netral';
        const details =
          `Akurasi: ${(match.score * 100).toFixed(1)}% | Usia: ~${currentAge} | Emosi: ${emotionText}`;

        updateProgressIcon('progress-match', 'completed');
        updateStatus('✅ Wajah terverifikasi!', 'success', details);

        DOM_CACHE.faceVerifiedInput.value = 'true';
        document.getElementById('face-similarity').value = match.score;
        document.getElementById('detected-age').value = currentAge;
        document.getElementById('detected-emotion').value = currentEmotion;

      } else {
        updateStatus('👤 Tidak ada wajah terdeteksi', 'info');
        DOM_CACHE.faceVerifiedInput.value = 'false';
        currentSimilarity = 0;
      }

    } catch (error) {
      console.error('Detection error:', error);
    }

    animationFrameId = requestAnimationFrame(detectionLoop);
  };

  animationFrameId = requestAnimationFrame(detectionLoop);
  console.log('✅ Face detection loop started');
}

function stopFaceDetection() {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
    animationFrameId = null;
    console.log('⏸️ Face detection loop stopped');
  }
}

function findBestMatch(descriptor) {
  if (faceDatabase.length === 0) {
    return {
      name: 'Unknown',
      score: 0
    };
  }

  let bestMatch = {
    name: 'Unknown',
    score: 0
  };

  for (let i = 0; i < faceDatabase.length; i++) {
    const person = faceDatabase[i];

    try {
      const score = human.match.similarity(descriptor, person.descriptor);

      if (score > bestMatch.score) {
        bestMatch = {
          name: person.nama,
          score: score
        };
      }
    } catch (error) {
      console.error('Error matching face:', error);
      continue;
    }
  }

  return bestMatch;
}

DOM_CACHE.button = document.getElementById('ambil-foto');

document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    console.log('⏸️ Tab hidden - detection paused');
  } else {
    console.log('▶️ Tab visible - detection resumed');
  }
});

window.addEventListener('beforeunload', () => {
  stopFaceDetection();

  if (stream) {
    stream.getTracks().forEach(track => track.stop());
    stream = null;
  }

  faceDatabase = null;

  console.log('🧹 Cleanup completed');
});

let latitude_kantor = <?= $user_lokasi_presensi->latitude ?? $latitude_kantor ?>;
let longitude_kantor = <?= $user_lokasi_presensi->longitude ?? $longitude_kantor ?>;
let latitude_pegawai = <?= $latitude_pegawai ?>;
let longitude_pegawai = <?= $longitude_pegawai ?>;
let radius = <?= $user_lokasi_presensi->radius ?? $radius ?>;

function getTileLayerUrl() {
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' ||
    document.documentElement.getAttribute('data-darkreader-scheme') === 'dark';

  return isDark ?
    'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' :
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
}

var map = L.map('map').setView([latitude_kantor, longitude_kantor], 15);

var tileLayer = L.tileLayer(getTileLayerUrl(), {
  maxZoom: 19,
  attribution: '© OpenStreetMap'
}).addTo(map);

var userIcon = L.divIcon({
  className: 'custom-marker',
  html: '<div style="background-color: #dc3545; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>',
  iconSize: [20, 20],
  iconAnchor: [10, 10]
});

var officeIcon = L.divIcon({
  className: 'custom-marker',
  html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>',
  iconSize: [20, 20],
  iconAnchor: [10, 10]
});

L.marker([latitude_pegawai, longitude_pegawai], {
    icon: userIcon
  })
  .addTo(map)
  .bindPopup("<b>📍 Lokasi Anda</b>");

L.marker([latitude_kantor, longitude_kantor], {
    icon: officeIcon
  })
  .addTo(map)
  .bindPopup("<b>🏫 Lokasi Sekolah</b>");

L.circle([latitude_kantor, longitude_kantor], {
  color: '#198754',
  fillColor: '#198754',
  fillOpacity: 0.15,
  radius: radius
}).addTo(map);

var group = new L.featureGroup([
  L.marker([latitude_pegawai, longitude_pegawai]),
  L.marker([latitude_kantor, longitude_kantor])
]);
map.fitBounds(group.getBounds().pad(0.1));

const themeObserver = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    if (mutation.type === 'attributes') {
      if (['data-bs-theme', 'data-darkreader-scheme', 'class'].includes(mutation.attributeName)) {
        updateMapTheme();
      }
    }
  });
});

themeObserver.observe(document.documentElement, {
  attributes: true,
  attributeFilter: ['data-bs-theme', 'data-darkreader-scheme', 'class']
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
  if (!document.documentElement.getAttribute('data-bs-theme')) {
    updateMapTheme();
  }
});

function updateMapTheme() {
  if (tileLayer) {
    map.removeLayer(tileLayer);
  }

  const newUrl = getTileLayerUrl();

  tileLayer = L.tileLayer(newUrl, {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(map);
}

// --- BAGIAN INI MENGGANTIKAN BAGIAN PALING BAWAH SCRIPT ANDA ---

document.addEventListener('DOMContentLoaded', function() {
  // 1. Inisialisasi Cache Element
  DOM_CACHE.video = document.getElementById('my_camera');
  DOM_CACHE.canvas = document.getElementById('canvas');
  DOM_CACHE.button = document.getElementById('ambil-foto');
  DOM_CACHE.btnText = document.getElementById('btn-text');
  DOM_CACHE.statusDiv = document.getElementById('face-status');
  DOM_CACHE.messageDiv = document.getElementById('face-message');
  DOM_CACHE.detailsDiv = document.getElementById('face-details');
  DOM_CACHE.progressFill = document.getElementById('movement-progress-fill');
  DOM_CACHE.faceVerifiedInput = document.getElementById('face-verified');

  // 2. Setup Event Listener Tombol (DIPINDAHKAN KE SINI)
  setupButtonListener();

  // 3. Jalankan Kamera & AI
  (async function() {
    await setupCamera();
    await initHuman();
  })();
});

// Fungsi baru untuk menangani klik tombol (Pemisahan logika agar rapi)
function setupButtonListener() {
  DOM_CACHE.button.addEventListener('click', function() {
    if (!isFaceRegistered) {
      Swal.fire({
        icon: 'error',
        title: 'Akses Ditolak',
        text: 'Wajah Anda belum terdaftar di sistem.',
        showCancelButton: true,
        confirmButtonText: 'Request Pendaftaran Wajah',
        confirmButtonColor: '#1e3a8a',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = FACE_ENROLLMENT_URL;
        }
      });
      return;
    }

    const faceVerified = DOM_CACHE.faceVerifiedInput.value;

    if (!headMovementState.completed) {
      Swal.fire({
        icon: 'warning',
        title: 'Verifikasi Belum Selesai',
        text: 'Selesaikan verifikasi gerakan kepala terlebih dahulu!',
        confirmButtonColor: '#1e3a8a'
      });
      return;
    }

    if (faceVerified !== 'true') {
      Swal.fire({
        icon: 'error',
        title: 'Wajah Belum Terverifikasi',
        html: `
            <p>Wajah belum terverifikasi dengan baik.</p>
            <p><strong>Tips:</strong></p>
            <ul style="text-align: left; padding-left: 20px;">
              <li>Pastikan wajah terlihat jelas</li>
              <li>Pencahayaan cukup</li>
              <li>Posisi tegak menghadap kamera</li>
            </ul>
          `,
        confirmButtonColor: '#1e3a8a'
      });
      return;
    }

    isVerifying = true;
    stopFaceDetection();

    const btn = DOM_CACHE.button;
    const btnText = DOM_CACHE.btnText;

    btn.disabled = true;
    btnText.innerText = 'Mengambil foto...';

    try {
      const imageData = captureImage();
      document.querySelector('.image-tag').value = imageData;
      document.getElementById('my_result').innerHTML =
        '<img src="' + imageData + '" style="max-width: 100%; border-radius: 8px; border: 2px solid #28a745;"/>';

      const funData = {
        age: currentAge,
        emotion: currentEmotion,
        similarity: currentSimilarity,
        date_recorded: '<?= date('Y-m-d') ?>',
        type: 'out'
      };
      localStorage.setItem('daily_ai_mood_out', JSON.stringify(funData));

      Swal.fire({
        icon: 'success',
        title: 'Foto Berhasil Diambil',
        html: `<p>Mengirim data presensi keluar...</p>`,
        timer: 1500,
        showConfirmButton: false,
        didClose: () => {
          document.getElementById('presensi-form').submit();
        }
      });

    } catch (error) {
      console.error('Error capturing image:', error);
      Swal.fire({
        icon: 'error',
        title: 'Gagal Mengambil Foto',
        text: error.message,
        confirmButtonColor: '#1e3a8a'
      });

      isVerifying = false;
      btn.disabled = false;
      btnText.innerText = 'Ambil Gambar & Verifikasi';
      startFaceDetectionRAF();
    }
  });
}
</script>

<?= $this->endSection() ?>