<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/human.js') ?>"></script>

<style>
/* Face Detection Overlay */
.face-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 10;
}

.face-mesh-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.video-container {
  position: relative;
  display: inline-block;
  border-radius: 8px;
  overflow: hidden;
}

/* Head Movement Instructions */
.head-movement-instruction {
  background: linear-gradient(135deg, #dda518 0%, #1e3a8a 100%);
  color: white;
  padding: 15px 20px;
  border-radius: 12px;
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: 600;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  animation: pulse 2s infinite;
  text-align: center;
}

.head-movement-instruction .instruction-icon {
  font-size: 32px;
  display: block;
  margin-bottom: 8px;
  animation: bounce 1s infinite;
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

@keyframes pulse {

  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.02);
  }
}

@keyframes bounce {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-10px);
  }
}

/* Progress Indicator */
.verification-progress {
  margin: 10px 0;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 2px solid #e9ecef;
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

.progress-item-icon.active {
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

/* Map Legend */
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

/* Map Container */
.map-container {
  margin: 15px 0;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #dee2e6;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

#map {
  height: 300px;
  width: 100%;
}

.video-container {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  transform: scaleX(-1);
}

#my_camera {
  width: 100%;
  height: auto;
  display: block;
}

#face-overlay-canvas {
  position: absolute;
  top: 0;
  left: 0;
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
            <!-- Head Movement Instruction -->
            <div id="head-movement-instruction" class="head-movement-instruction" style="display: none;">
              <div class="instruction-icon" id="instruction-icon">👤</div>
              <div class="instruction-text" id="instruction-text">Siapkan Wajah</div>
              <div class="instruction-subtitle">Ikuti instruksi untuk verifikasi</div>
            </div>

            <!-- Verification Progress -->
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
                <video id="my_camera" width="320" height="240" autoplay playsinline
                  style="border: 2px solid #ccc; border-radius: 8px;"></video>
                <canvas id="face-overlay-canvas" class="face-mesh-canvas" width="320" height="240"></canvas>
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
let detectionInterval = null;
let currentAge = 0;
let currentEmotion = 'neutral';
let currentSimilarity = 0;

// Head Movement Verification State
let headMovementState = {
  required: null,
  completed: false,
  detectionCount: 0,
  requiredCount: 15,
  initialRotation: null
};

const human = new Human.Human({
  backend: 'webgl',
  modelBasePath: '<?= base_url('assets/models/') ?>',
  face: {
    enabled: true,
    detector: {
      rotation: true
    },
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
      enabled: false
    },
    antispoof: {
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
});

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

// Fungsi untuk menampilkan instruksi permission dengan detail
function showPermissionInstructions(type, error) {
  let title = '';
  let html = '';

  if (type === 'camera') {
    title = '📷 Akses Kamera Diperlukan';

    // Deteksi jenis error
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
          <hr>
          <p style="font-size: 12px; color: #666;">
            <strong>Browser Anda:</strong> ${navigator.userAgent.includes('Chrome') ? 'Chrome' : navigator.userAgent.includes('Firefox') ? 'Firefox' : navigator.userAgent.includes('Safari') ? 'Safari' : 'Lainnya'}
          </p>
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
          <p><strong>Solusi:</strong></p>
          <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Pastikan kamera terhubung dengan baik</li>
            <li>Tutup aplikasi lain yang menggunakan kamera (Zoom, Teams, dll)</li>
            <li>Restart browser Anda</li>
          </ol>
        </div>
      `;
    } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
      html = `
        <div style="text-align: left;">
          <p><strong>Kamera tidak dapat diakses!</strong></p>
          <p>Kamera mungkin sedang digunakan oleh aplikasi lain.</p>
          <p><strong>Solusi:</strong></p>
          <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Tutup semua aplikasi yang menggunakan kamera</li>
            <li>Restart browser</li>
            <li>Jika masih bermasalah, restart komputer Anda</li>
          </ol>
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
  } else if (type === 'location') {
    title = '📍 Akses Lokasi Diperlukan';
    html = `
      <div style="text-align: left;">
        <p><strong>Anda memblokir akses lokasi.</strong></p>
        <p>Untuk mengaktifkan kembali:</p>
        <ol style="margin: 10px 0; padding-left: 20px;">
          <li>Klik ikon <strong>🔒 gembok</strong> di address bar</li>
          <li>Cari pengaturan <strong>"Lokasi"</strong></li>
          <li>Ubah menjadi <strong>"Izinkan"</strong></li>
          <li>Refresh halaman (F5)</li>
        </ol>
        <p style="margin-top: 10px;"><em>Lokasi diperlukan untuk memverifikasi Anda berada di area sekolah.</em></p>
      </div>
    `;
  }

  Swal.fire({
    icon: 'warning',
    title: title,
    html: html,
    confirmButtonText: 'Saya Mengerti',
    confirmButtonColor: '#1e3a8a',
    width: '600px',
    customClass: {
      popup: 'text-start'
    }
  });
}

// Fungsi untuk menampilkan transparansi AI
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
    title: '🤖 Transparansi AI',
    html: `
      <div style="text-align: left;">
        <h5 style="margin-top: 0; color: #1e3a8a;">📊 Hasil Verifikasi Wajah</h5>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
          <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: ${statusColor}; font-weight: bold;">${status}</span></p>
          <p style="margin: 5px 0;"><strong>Tingkat Kecocokan:</strong> ${similarity}%</p>
          <div style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0;">
            <div style="background: ${statusColor}; height: 100%; width: ${similarity}%; transition: width 0.3s;"></div>
          </div>
        </div>
        
        <h5 style="color: #1e3a8a;">ℹ️ Cara Kerja Sistem</h5>
        <ol style="font-size: 14px; line-height: 1.6;">
          <li><strong>Deteksi Wajah:</strong> AI mendeteksi wajah Anda dari kamera</li>
          <li><strong>Verifikasi Gerakan:</strong> Anda diminta melakukan gerakan kepala untuk memastikan bukan foto/video</li>
          <li><strong>Pencocokan:</strong> Wajah Anda dicocokkan dengan ${registeredFaces} data wajah terdaftar atas nama Anda</li>
          <li><strong>Scoring:</strong> Sistem memberikan skor 0-100% berdasarkan kemiripan fitur wajah</li>
        </ol>
        
        <div style="background: #e7f3ff; padding: 10px; border-left: 4px solid #0d6efd; margin: 15px 0;">
          <p style="margin: 0; font-size: 13px;"><strong>💡 Standar Kecocokan:</strong></p>
          <ul style="margin: 5px 0; font-size: 13px; padding-left: 20px;">
            <li>75-100%: Sangat Cocok (Identitas pasti)</li>
            <li>62-74%: Cocok (Identitas terverifikasi)</li>
            <li>0-61%: Tidak Cocok (Ditolak sistem)</li>
          </ul>
        </div>
        
        <h5 style="color: #1e3a8a;">📸 Data Terdeteksi</h5>
        <ul style="font-size: 14px; line-height: 1.6;">
          <li><strong>Usia Estimasi:</strong> ~${currentAge} tahun</li>
          <li><strong>Emosi:</strong> ${emotionMap[currentEmotion] || 'Netral'}</li>
          <li><strong>Wajah Terdaftar:</strong> ${registeredFaces} data</li>
        </ul>
        
        <hr>
        <p style="font-size: 12px; color: #6c757d; margin: 10px 0;">
          <strong>Privasi:</strong> Data wajah Anda terenkripsi dan hanya digunakan untuk verifikasi presensi.
          Sistem tidak menyimpan foto/video Anda.
        </p>
      </div>
    `,
    confirmButtonText: 'Mengerti',
    confirmButtonColor: '#1e3a8a',
    width: '650px',
    showCloseButton: true,
    customClass: {
      popup: 'text-start'
    }
  });
}

async function setupCamera() {
  try {
    updateStatus('Meminta izin kamera...', 'info');
    const video = document.getElementById('my_camera');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      const notSupportedError = new Error(
        'Browser Anda tidak mendukung akses kamera. Gunakan Chrome, Firefox, atau Edge terbaru.');
      showPermissionInstructions('camera', notSupportedError);
      throw notSupportedError;
    }

    const constraints = [{
        video: {
          width: {
            ideal: 640
          },
          height: {
            ideal: 480
          },
          facingMode: 'user'
        },
        audio: false
      },
      {
        video: {
          facingMode: 'user'
        },
        audio: false
      },
      {
        video: true,
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
      throw lastError || new Error('Tidak dapat mengakses kamera.');
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
    updateStatus('Gagal mengaktifkan kamera. Lihat panduan di atas.', 'danger');
    return false;
  }
}

function captureImage() {
  const video = document.getElementById('my_camera');
  const canvas = document.getElementById('canvas');
  const context = canvas.getContext('2d');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  context.drawImage(video, 0, 0, canvas.width, canvas.height);
  return canvas.toDataURL('image/jpeg', 0.9);
}

async function initHuman() {
  try {
    updateStatus('Memuat model AI...', 'info');
    await human.load();
    await human.warmup();
    updateStatus('Memuat data wajah...', 'info');
    await loadFaceDatabase();
    isModelLoaded = true;

    selectRandomMovement();
    showHeadMovementInstruction();

    checkButtonState();
    startFaceDetection();
  } catch (error) {
    updateStatus('Gagal memuat model AI. Refresh halaman.', 'danger');

    Swal.fire({
      icon: 'error',
      title: 'Model AI Gagal Dimuat',
      html: `
        <p>Terjadi kesalahan saat memuat model AI.</p>
        <p><strong>Solusi:</strong></p>
        <ol style="text-align: left; padding-left: 20px;">
          <li>Refresh halaman ini (tekan F5)</li>
          <li>Periksa koneksi internet Anda</li>
          <li>Kosongkan cache browser</li>
          <li>Gunakan browser Chrome/Firefox terbaru</li>
        </ol>
      `,
      confirmButtonColor: '#1e3a8a'
    });
  }
}

async function loadFaceDatabase() {
  try {
    const response = await fetch('<?= base_url('presensi/get-face-descriptors') ?>', {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      }
    });
    const data = await response.json();
    if (data.error) throw new Error(data.error);
    faceDatabase = data.filter(item => item.id_pegawai == <?= $user_profile->id_pegawai ?>);

    if (faceDatabase.length === 0) {
      updateStatus('Wajah belum terdaftar. Hubungi Admin.', 'warning');
      document.getElementById('ambil-foto').disabled = true;

      Swal.fire({
        icon: 'warning',
        title: 'Wajah Belum Terdaftar',
        html: `
          <p>Data wajah Anda belum tersedia di sistem.</p>
          <p><strong>Langkah selanjutnya:</strong></p>
          <ol style="text-align: left; padding-left: 20px;">
            <li>Hubungi administrator/HRD</li>
            <li>Minta untuk didaftarkan ke sistem face recognition</li>
            <li>Proses pendaftaran biasanya memerlukan foto wajah dari berbagai sudut</li>
          </ol>
          <p style="margin-top: 15px;"><em>Setelah terdaftar, Anda bisa langsung menggunakan presensi wajah.</em></p>
        `,
        confirmButtonColor: '#1e3a8a'
      });

      return;
    }

    faceDatabase = faceDatabase.map(item => ({
      ...item,
      descriptor: new Float32Array(Object.values(item.descriptor))
    }));

    console.log(`✅ Berhasil memuat ${faceDatabase.length} data wajah terdaftar`);
  } catch (error) {
    updateStatus('Gagal memuat database wajah.', 'danger');

    Swal.fire({
      icon: 'error',
      title: 'Database Gagal Dimuat',
      html: `
        <p>Tidak dapat memuat data wajah dari server.</p>
        <p><strong>Kemungkinan penyebab:</strong></p>
        <ul style="text-align: left; padding-left: 20px;">
          <li>Koneksi internet terputus</li>
          <li>Server sedang maintenance</li>
          <li>Session login Anda expired</li>
        </ul>
        <p><strong>Solusi:</strong> Refresh halaman atau login ulang.</p>
      `,
      confirmButtonColor: '#1e3a8a'
    });
  }
}

function showHeadMovementInstruction() {
  const instructionDiv = document.getElementById('head-movement-instruction');
  const iconDiv = document.getElementById('instruction-icon');
  const textDiv = document.getElementById('instruction-text');

  const instruction = headMovementInstructions[headMovementState.required];

  iconDiv.textContent = instruction.icon;
  textDiv.textContent = instruction.text;
  document.querySelector('.instruction-subtitle').textContent = instruction.subtitle;

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

const visualConfig = {
  colorGuide: 'rgba(255, 255, 255, 0.4)',
  colorDot: '#dda518',
  colorSuccess: '#ffffff',
  colorText: '#FFFFFF',
  lineWidth: 3
};

function drawAnimatedInstruction(ctx, w, h, direction) {
  const centerX = w / 2;
  const centerY = h / 2;
  const radius = Math.min(w, h) * 0.25;

  const time = Date.now() / 800;
  const moveAmount = Math.abs(Math.sin(time)) * (radius * 0.6);

  let dotX = 0;
  let dotY = 0;
  let label = "";
  let arrowIcon = "";

  switch (direction) {
    case 'right':
      dotX = -moveAmount;
      label = "TOLEH KIRI";
      arrowIcon = "⬅️";
      break;
    case 'left':
      dotX = moveAmount;
      label = "TOLEH KANAN";
      arrowIcon = "➡️";
      break;
    case 'up':
      dotY = -moveAmount;
      label = "DONGAK KEATAS";
      arrowIcon = "⬆️";
      break;
    case 'down':
      dotY = moveAmount;
      label = "TUNDUK KEBAWAH";
      arrowIcon = "⬇️";
      break;
  }

  ctx.save();
  ctx.translate(centerX, centerY);
  ctx.scale(-1, 1);

  ctx.beginPath();
  ctx.arc(0, 0, radius, 0, Math.PI * 2);
  ctx.strokeStyle = visualConfig.colorGuide;
  ctx.lineWidth = 2;
  ctx.setLineDash([5, 5]);
  ctx.stroke();
  ctx.setLineDash([]);

  ctx.beginPath();
  ctx.arc(dotX, dotY, 8, 0, Math.PI * 2);
  ctx.fillStyle = visualConfig.colorDot;
  ctx.fill();

  ctx.beginPath();
  ctx.moveTo(0, 0);
  ctx.lineTo(dotX, dotY);
  ctx.strokeStyle = 'rgba(255, 213, 0, 0.32)';
  ctx.lineWidth = 4;
  ctx.stroke();

  ctx.textAlign = "center";
  ctx.textBaseline = "middle";
  ctx.fillStyle = visualConfig.colorText;

  ctx.font = "40px Arial";
  let iconX = 0,
    iconY = 0;
  if (direction === 'left') iconX = -(radius + 40);
  if (direction === 'right') iconX = radius + 40;
  if (direction === 'up') iconY = -(radius + 40);
  if (direction === 'down') iconY = radius + 40;

  ctx.fillText(arrowIcon, iconX, iconY);

  ctx.font = "bold 16px sans-serif";
  ctx.fillText(label, 0, radius + 30);

  ctx.restore();
}

function drawFancyOverlay(face) {
  const canvas = document.getElementById('face-overlay-canvas');
  const ctx = canvas.getContext('2d');
  const video = document.getElementById('my_camera');

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  if (!headMovementState.completed && headMovementState.required) {
    drawAnimatedInstruction(ctx, canvas.width, canvas.height, headMovementState.required);
  }

  if (!face) return;

  const scaleX = canvas.width / video.videoWidth;
  const scaleY = canvas.height / video.videoHeight;
  const box = face.box;
  const x = box[0] * scaleX;
  const y = box[1] * scaleY;
  const w = box[2] * scaleX;
  const h = box[3] * scaleY;

  const color = headMovementState.completed ? visualConfig.colorSuccess : 'rgba(255, 255, 255, 0.5)';

  ctx.strokeStyle = color;
  ctx.lineWidth = 2;

  const lineLen = 20;
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
  ctx.moveTo(x, y + h - lineLen);
  ctx.lineTo(x, y + h);
  ctx.lineTo(x + lineLen, y + h);
  ctx.stroke();

  // Tampilkan skor kecocokan real-time jika sudah selesai gerakan
  if (headMovementState.completed && currentSimilarity > 0) {
    ctx.save();
    ctx.scale(-1, 1);
    ctx.font = "bold 14px sans-serif";
    ctx.fillStyle = currentSimilarity >= 0.62 ? "#28a745" : "#dc3545";
    ctx.textAlign = "center";
    ctx.fillText(`${(currentSimilarity * 100).toFixed(1)}%`, -(w / 2 + x), y - 10);
    ctx.restore();
  }
}

function checkHeadMovement(face) {
  drawFancyOverlay(face);

  if (!face || !face.rotation) return false;

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
  const threshold = 15;

  if (!headMovementState.completed) {
    const canvas = document.getElementById('face-overlay-canvas');
    const ctx = canvas.getContext('2d');
    ctx.save();
    ctx.scale(-1, 1);
    ctx.font = "12px sans-serif";
    ctx.fillStyle = "rgba(255, 255, 255, 0.5)";
    ctx.textAlign = "right";
    ctx.fillText(`Y: ${yawDiff.toFixed(0)}° P: ${pitchDiff.toFixed(0)}°`, -10, 20);
    ctx.restore();
  }

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

    const canvas = document.getElementById('face-overlay-canvas');
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(canvas.width, canvas.height) * 0.25;

    ctx.save();
    ctx.translate(centerX, centerY);
    ctx.scale(-1, 1);
    ctx.beginPath();
    ctx.arc(0, 0, radius, 0, (Math.PI * 2) * (headMovementState.detectionCount / headMovementState.requiredCount));
    ctx.strokeStyle = visualConfig.colorSuccess;
    ctx.lineWidth = 4;
    ctx.stroke();
    ctx.restore();

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
  }

  return false;
}

async function startFaceDetection() {
  const video = document.getElementById('my_camera');
  if (!video || !video.srcObject) {
    setTimeout(startFaceDetection, 500);
    return;
  }

  detectionInterval = setInterval(async () => {
    if (isVerifying) return;
    try {
      const result = await human.detect(video);

      if (result.face && result.face.length > 0) {
        const face = result.face[0];
        drawFancyOverlay(face);
        updateProgressIcon('progress-face', 'completed');

        if (!headMovementState.completed) {
          updateProgressIcon('progress-movement', 'active');
          checkHeadMovement(face);
          updateStatus('👤 Ikuti instruksi gerakan kepala', 'info');
          document.getElementById('face-verified').value = 'false';
          return;
        }

        updateProgressIcon('progress-match', 'active');

        const match = findBestMatch(face.embedding);
        currentSimilarity = match.score;

        if (match.score < 0.62) {
          updateStatus('⚠️ Akurasi rendah, posisikan wajah dengan benar.', 'warning');
          document.getElementById('face-verified').value = 'false';
          return;
        }

        currentAge = Math.round(face.age);
        currentEmotion = face.emotion[0] ? face.emotion[0].emotion : 'neutral';

        const emotionText = emotionMap[currentEmotion] || 'Netral';
        const details =
          `Akurasi: ${(match.score * 100).toFixed(1)}% | Usia Deteksi: ~${currentAge} thn | Emosi: ${emotionText}`;

        updateProgressIcon('progress-match', 'completed');
        updateStatus('✅ Wajah terverifikasi!', 'success', details);
        document.getElementById('face-verified').value = 'true';
        document.getElementById('face-similarity').value = match.score;
        document.getElementById('detected-age').value = currentAge;
        document.getElementById('detected-emotion').value = currentEmotion;
      } else {
        updateStatus('👤 Tidak ada wajah terdeteksi', 'info');
        document.getElementById('face-verified').value = 'false';
        currentSimilarity = 0;

        const canvas = document.getElementById('face-overlay-canvas');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!headMovementState.completed && headMovementState.required) {
          drawAnimatedInstruction(ctx, canvas.width, canvas.height, headMovementState.required);
        }
      }
    } catch (error) {
      console.error(error);
    }
  }, 100);
}

function findBestMatch(descriptor) {
  if (faceDatabase.length === 0) return {
    name: 'Unknown',
    score: 0
  };
  let bestMatch = {
    name: 'Unknown',
    score: 0
  };
  for (const person of faceDatabase) {
    const score = human.match.similarity(descriptor, person.descriptor);
    if (score > bestMatch.score) bestMatch = {
      name: person.nama,
      score: score
    };
  }
  return bestMatch;
}

function checkButtonState() {
  const video = document.getElementById('my_camera');
  if (stream && video.readyState === 4 && isModelLoaded) {
    document.getElementById('ambil-foto').disabled = false;
    document.getElementById('btn-text').innerText = 'Ambil Gambar & Verifikasi';
    updateStatus('✅ Sistem siap! Ikuti instruksi verifikasi.', 'success');
  }
}

document.getElementById('ambil-foto').addEventListener('click', function() {
  const faceVerified = document.getElementById('face-verified').value;

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
          <li>Pastikan wajah Anda terlihat jelas</li>
          <li>Cukup pencahayaan (tidak terlalu gelap/terang)</li>
          <li>Posisi tegak menghadap kamera</li>
          <li>Tidak menggunakan masker/kacamata hitam</li>
        </ul>
        <button onclick="showAITransparency()" class="btn btn-sm btn-outline-primary mt-2">
          🤖 Lihat Detail AI
        </button>
      `,
      confirmButtonColor: '#1e3a8a',
      confirmButtonText: 'Coba Lagi'
    });
    return;
  }

  isVerifying = true;
  if (detectionInterval) clearInterval(detectionInterval);

  const btn = document.getElementById('ambil-foto');
  btn.disabled = true;
  document.getElementById('btn-text').innerText = 'Mengambil foto...';

  try {
    const imageData = captureImage();
    document.querySelector('.image-tag').value = imageData;
    document.getElementById('my_result').innerHTML = '<img src="' + imageData +
      '" style="max-width: 100%; border-radius: 8px; border: 2px solid #28a745;"/>';

    const funData = {
      age: currentAge,
      emotion: currentEmotion,
      similarity: currentSimilarity,
      date_recorded: '<?= date('Y-m-d') ?>',
      timestamp: new Date().getTime(),
      type: 'out'
    };
    localStorage.setItem('daily_ai_mood_out', JSON.stringify(funData));

    Swal.fire({
      icon: 'success',
      title: 'Foto Berhasil Diambil',
      html: `
        <p>Mengirim data presensi keluar...</p>
        <p style="font-size: 13px; color: #6c757d; margin-top: 10px;">
          Kecocokan: ${(currentSimilarity * 100).toFixed(1)}%
        </p>
      `,
      timer: 1500,
      showConfirmButton: false,
      didClose: () => {
        document.getElementById('presensi-form').submit();
      }
    });

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal Mengambil Foto',
      text: error.message,
      confirmButtonColor: '#1e3a8a'
    });

    isVerifying = false;
    btn.disabled = false;
    document.getElementById('btn-text').innerText = 'Ambil Gambar & Verifikasi';
    startFaceDetection();
  }
});

function updateStatus(message, type = 'info', details = '') {
  const statusDiv = document.getElementById('face-status');
  const messageDiv = document.getElementById('face-message');
  const detailsDiv = document.getElementById('face-details');
  statusDiv.className = `alert alert-${type}`;
  statusDiv.style.display = 'block';
  messageDiv.innerHTML = message;
  if (details) {
    detailsDiv.innerHTML = details +
      ' <a href="javascript:void(0)" onclick="showAITransparency()" style="font-size: 12px; margin-left: 10px;">🤖 Detail AI</a>';
    detailsDiv.style.display = 'block';
  } else {
    detailsDiv.style.display = 'none';
  }
}

window.addEventListener('beforeunload', () => {
  if (stream) stream.getTracks().forEach(track => track.stop());
});

// ==================== MAP LOGIC ====================
let latitude_kantor = <?= $user_lokasi_presensi->latitude ?? $latitude_kantor ?>;
let longitude_kantor = <?= $user_lokasi_presensi->longitude ?? $longitude_kantor ?>;
let latitude_pegawai = <?= $latitude_pegawai ?>;
let longitude_pegawai = <?= $longitude_pegawai ?>;
let radius = <?= $user_lokasi_presensi->radius ?? $radius ?>;

function getTileLayerUrl() {
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' ||
    document.documentElement.getAttribute('data-darkreader-scheme') === 'dark' ||
    localStorage.getItem('theme-preference') === 'dark';

  return isDark ?
    'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' :
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
}

var map = L.map('map').setView([latitude_kantor, longitude_kantor], 15);

var tileLayer = L.tileLayer(getTileLayerUrl(), {
  maxZoom: 19,
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

var userIcon = L.divIcon({
  className: 'custom-marker',
  html: '<div style="background-color: #dc3545; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
  iconSize: [20, 20],
  iconAnchor: [10, 10]
});

var officeIcon = L.divIcon({
  className: 'custom-marker',
  html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
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

function updateMapTheme() {
  const newUrl = getTileLayerUrl();
  map.removeLayer(tileLayer);
  tileLayer = L.tileLayer(newUrl, {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
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
  attributeFilter: ['data-bs-theme', 'data-darkreader-scheme', 'class']
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateMapTheme);

(async function() {
  await setupCamera();
  await initHuman();
})();
</script>

<?= $this->endSection() ?>