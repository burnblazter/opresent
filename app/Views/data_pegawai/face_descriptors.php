<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/human.js') ?>"></script>

<style>
/* Styling Kartu Wajah */
.face-card {
  border: 1px solid #e2e8f0;
  padding: 15px;
  margin-bottom: 15px;
  border-radius: 8px;
  background: #fff;
  transition: all 0.2s;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.face-card:hover {
  background: #f8f9fa;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

/* Styling Area Kamera agar "Techy" */
#camera-container {
  position: relative;
  width: 100%;
  max-width: 640px;
  /* Lebar maksimal agar tidak pecah di layar lebar */
  margin: 0 auto;
  border-radius: 8px;
  overflow: hidden;
  background-color: #000;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

#webcam-video {
  width: 100%;
  height: auto;
  display: block;
  transform: scaleX(-1);
}

#webcam-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: scaleX(-1);
}

/* Info Dashboard Admin */
.tech-stats {
  display: flex;
  justify-content: space-around;
  background: #1e293b;
  color: #00d2ff;
  padding: 10px;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
  border-radius: 0 0 8px 8px;
  margin-top: -4px;
  /* Nempel ke video */
  position: relative;
  z-index: 10;
}

.tech-stat-item strong {
  color: #fff;
}

.tab-content {
  padding: 20px;
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 8px 8px;
}
</style>

<div class="page-body">
  <div class="container-xl">
    <div class="row mb-3">
      <div class="col">
        <a href="<?= base_url('data-pegawai') ?>" class="btn btn-secondary">
          <i class="ti ti-arrow-left"></i> Kembali
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs" id="enrollTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="tab-webcam-link" data-bs-toggle="tab" href="#webcam-tab">
                  <i class="ti ti-camera"></i> Webcam (Live)
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-upload-link" data-bs-toggle="tab" href="#upload-tab">
                  <i class="ti ti-upload"></i> Upload File
                </a>
              </li>
            </ul>

            <div class="tab-content">
              <div id="webcam-tab" class="tab-pane fade show active">
                <div id="face-status" class="alert alert-info text-center">
                  <div id="face-message">Memuat model AI...</div>
                </div>

                <div class="text-center mb-3">
                  <div id="camera-container">
                    <video id="webcam-video" autoplay playsinline muted></video>
                    <canvas id="webcam-canvas"></canvas>
                  </div>
                  <div class="tech-stats" id="tech-stats" style="max-width: 640px; margin: 0 auto;">
                    <div class="tech-stat-item">Age: <strong id="stat-age">-</strong></div>
                    <div class="tech-stat-item">Gender: <strong id="stat-gender">-</strong></div>
                    <div class="tech-stat-item">Emotion: <strong id="stat-emotion">-</strong></div>
                  </div>
                </div>

                <div class="row justify-content-center">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Label Foto</label>
                      <input type="text" class="form-control" id="webcam-label"
                        placeholder="Contoh: Wajah Depan / Senyum" value="Scan <?= date('d/m H:i') ?>">
                    </div>
                    <button class="btn btn-primary w-100 btn-lg" id="btn-capture-webcam" disabled>
                      <i class="ti ti-camera"></i> Scan & Simpan Wajah
                    </button>
                  </div>
                </div>
              </div>

              <div id="upload-tab" class="tab-pane fade">
                <div id="upload-status" class="alert alert-info" style="display: none;">
                  <div id="upload-message"></div>
                </div>

                <div class="row justify-content-center">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Pilih Gambar</label>
                      <input type="file" class="form-control" id="file-upload" accept="image/*">
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Label Foto</label>
                      <input type="text" class="form-control" id="upload-label" placeholder="Contoh: Foto Dari File"
                        value="Upload <?= date('d/m H:i') ?>">
                    </div>

                    <div id="upload-preview" class="text-center mb-3" style="display: none;">
                      <img id="preview-image" src=""
                        style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>

                    <button class="btn btn-primary w-100" id="btn-upload-save" disabled>
                      <i class="ti ti-check"></i> Proses & Simpan
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Database Wajah Tersimpan (<?= count($descriptors) ?>)</h3>
          </div>
          <div class="card-body">
            <?php if (empty($descriptors)): ?>
            <div class="alert alert-warning">
              Belum ada wajah terdaftar untuk pegawai ini.
            </div>
            <?php else: ?>
            <div class="row" id="descriptors-list">
              <?php foreach ($descriptors as $desc): ?>
              <div class="col-md-4 col-sm-6">
                <div class="face-card">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h4 class="mb-1 text-primary"><?= esc($desc->label) ?></h4>
                      <small class="text-muted d-block">
                        <i class="ti ti-calendar"></i> <?= date('d M Y, H:i', strtotime($desc->created_at)) ?>
                      </small>
                      <span class="badge bg-green-lt mt-2">Vector ID: <?= $desc->id ?></span>
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
// --- KONFIGURASI HUMAN.JS ---
const config = {
  backend: 'webgl',
  modelBasePath: '<?= base_url('assets/models/') ?>',
  filter: {
    enabled: true,
    equalization: false
  },
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
};

const human = new Human.Human(config);

let isModelLoaded = false;
let webcamStream = null;
let requestLoop = null; // Untuk menyimpan ID requestAnimationFrame
const idPegawai = <?= $pegawai->id ?>;
const video = document.getElementById('webcam-video');
const canvas = document.getElementById('webcam-canvas');

// --- INIT ---
async function initHuman() {
  try {
    updateStatus('Memuat model AI...', 'info');
    await human.load();
    await human.warmup();

    isModelLoaded = true;
    updateStatus('Sistem AI Siap. Menunggu Kamera...', 'success');

    // Langsung nyalakan kamera karena tab default adalah webcam
    await startWebcam();
  } catch (error) {
    console.error('Error init Human:', error);
    updateStatus('Gagal memuat model AI. Refresh halaman.', 'danger');
  }
}

// --- LOGIKA WEBCAM & DETEKSI ---
async function startWebcam() {
  if (!isModelLoaded) return;

  try {
    webcamStream = await navigator.mediaDevices.getUserMedia({
      video: {
        width: {
          ideal: 1280
        },
        height: {
          ideal: 720
        },
        facingMode: 'user'
      }
    });

    video.srcObject = webcamStream;

    // Tunggu video play
    await new Promise(resolve => video.onloadeddata = resolve);
    video.play();

    // Sesuaikan ukuran canvas dengan video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    document.getElementById('btn-capture-webcam').disabled = false;
    updateStatus('✅ Kamera Aktif. Deteksi berjalan...', 'success');

    // Mulai Loop Deteksi & Drawing
    drawingLoop();

  } catch (error) {
    console.error('Webcam error:', error);
    updateStatus('Gagal mengakses webcam. Izinkan akses kamera.', 'danger');
  }
}

function stopWebcam() {
  if (webcamStream) {
    webcamStream.getTracks().forEach(track => track.stop());
    webcamStream = null;
  }
  video.srcObject = null;
  cancelAnimationFrame(requestLoop); // Matikan loop processing

  // Bersihkan canvas
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  // Reset stats
  document.getElementById('stat-fps').innerText = "0";
}

// --- LOOP UTAMA (DRAWING & STATS) ---
async function drawingLoop() {
  if (!video.paused && !video.ended && isModelLoaded) {

    // 1. Deteksi
    const result = await human.detect(video);

    // 2. Gambar Overlay (Keren-kerenan Admin)
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Menggambar hasil deteksi (Face Mesh, Box, dll) menggunakan bawaan Human.js
    // drawOptions bisa di-tweak warnanya
    const drawOptions = {
      color: 'rgba(0, 255, 255, 0.4)', // Warna Cyan ala sci-fi
      lineWidth: 1,
      drawLabels: false // Matikan label bawaan human.js, kita pakai custom di bawah
    };
    await human.draw.face(canvas, result.face, drawOptions);

    // 3. Update Debug/Stats Info
    if (result.face && result.face.length > 0) {
      const face = result.face[0];

      // Gender & Umur
      document.getElementById('stat-age').innerText = Math.round(face.age) + " Thn";
      document.getElementById('stat-gender').innerText = face.gender;

      // Emosi
      if (face.emotion && face.emotion.length > 0) {
        const emotion = face.emotion[0].emotion; // Emosi tertinggi
        document.getElementById('stat-emotion').innerText = emotion.toUpperCase();
      }

      document.getElementById('face-message').innerHTML =
        `✅ Wajah Terdeteksi (Score: ${Math.round(face.boxScore * 100)}%)`;
      document.getElementById('face-status').className = 'alert alert-success text-center';
    } else {
      document.getElementById('face-message').innerHTML = `⚠️ Mencari wajah...`;
      document.getElementById('face-status').className = 'alert alert-warning text-center';
      document.getElementById('stat-age').innerText = "-";
      document.getElementById('stat-gender').innerText = "-";
      document.getElementById('stat-emotion').innerText = "-";
    }
  }

  // Request frame selanjutnya
  requestLoop = requestAnimationFrame(drawingLoop);
}

// --- EVENT LISTENER TOMBOL SCAN ---
document.getElementById('btn-capture-webcam').addEventListener('click', async function() {
  const label = document.getElementById('webcam-label').value;

  if (!label) {
    alert('Masukkan label foto terlebih dahulu!');
    return;
  }

  // Pause loop sebentar untuk efek "Capture"
  this.disabled = true;
  this.innerHTML = '<i class="ti ti-loader"></i> Menganalisa...';

  try {
    // Lakukan deteksi sekali lagi khusus untuk capture data presisi
    const result = await human.detect(video);

    if (!result.face || result.face.length === 0) {
      alert('Wajah tidak terdeteksi dengan jelas!');
      this.disabled = false;
      this.innerHTML = '<i class="ti ti-camera"></i> Scan & Simpan Wajah';
      return;
    }

    if (result.face.length > 1) {
      alert('Terdeteksi lebih dari 1 wajah! Pastikan hanya admin/pegawai di frame.');
      this.disabled = false;
      this.innerHTML = '<i class="ti ti-camera"></i> Scan & Simpan Wajah';
      return;
    }

    const face = result.face[0];
    const descriptor = Array.from(face.embedding);

    // Validasi
    if (descriptor.length < 100 || descriptor.some(val => isNaN(val))) {
      alert('Hasil scan tidak valid. Coba sesuaikan pencahayaan.');
      this.disabled = false;
      this.innerHTML = '<i class="ti ti-camera"></i> Scan & Simpan Wajah';
      return;
    }

    await saveFaceDescriptor(descriptor, label);

  } catch (error) {
    console.error('Capture error:', error);
    alert('Error: ' + error.message);
    this.disabled = false;
    this.innerHTML = '<i class="ti ti-camera"></i> Scan & Simpan Wajah';
  }
});

// --- MANAJEMEN TAB (ON/OFF KAMERA) ---
// Ketika Tab Webcam diklik -> Nyalakan Kamera
document.getElementById('tab-webcam-link').addEventListener('shown.bs.tab', function(event) {
  console.log('Masuk Tab Webcam');
  startWebcam();
});

// Ketika Tab Upload diklik -> Matikan Kamera
document.getElementById('tab-upload-link').addEventListener('shown.bs.tab', function(event) {
  console.log('Masuk Tab Upload (Kamera dimatikan)');
  stopWebcam();
});


// --- BAGIAN UPLOAD FILE (SAMA SEPERTI SEBELUMNYA) ---
document.getElementById('file-upload').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(event) {
    const img = document.getElementById('preview-image');
    img.src = event.target.result;
    document.getElementById('upload-preview').style.display = 'block';
    document.getElementById('btn-upload-save').disabled = false;
  };
  reader.readAsDataURL(file);
});

document.getElementById('btn-upload-save').addEventListener('click', async function() {
  const file = document.getElementById('file-upload').files[0];
  const label = document.getElementById('upload-label').value;
  if (!file || !label) {
    alert('Lengkapi data!');
    return;
  }

  this.disabled = true;
  updateUploadStatus('Sedang memproses AI...', 'info');

  const img = new Image();
  img.src = document.getElementById('preview-image').src;

  img.onload = async function() {
    try {
      const result = await human.detect(img);
      if (!result.face || result.face.length === 0) {
        alert('Tidak ada wajah di gambar ini!');
        document.getElementById('btn-upload-save').disabled = false;
        return;
      }

      // Ambil wajah dengan confidence tertinggi jika ada banyak
      let bestFace = result.face[0];
      // Opsional: Loop cari confidence tertinggi jika perlu

      await saveFaceDescriptor(Array.from(bestFace.embedding), label);

      // Reset Form
      document.getElementById('file-upload').value = '';
      document.getElementById('upload-preview').style.display = 'none';
    } catch (e) {
      alert('Error: ' + e.message);
      document.getElementById('btn-upload-save').disabled = false;
    }
  }
});

// --- AJAX SAVE FUNCTION ---
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
      alert('✅ Berhasil disimpan!');
      location.reload();
    } else {
      alert('❌ ' + result.message);
      location.reload(); // Reload agar token CSRF refresh jika perlu
    }
  } catch (error) {
    alert('Server Error: ' + error.message);
  }
}

// --- HELPER LAINNYA ---
async function editLabel(id, currentLabel) {
  const newLabel = prompt('Ubah Label:', currentLabel);
  if (!newLabel || newLabel === currentLabel) return;
  // ... (Kode ajax update label sama seperti sebelumnya) ...
  // Saya sarankan pakai fetch simple seperti saveFaceDescriptor di atas
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
    if (res.success) location.reload();
    else alert(res.message);
  } catch (e) {
    alert(e.message);
  }
}

function updateStatus(msg, type) {
  const el = document.getElementById('face-status');
  el.className = `alert alert-${type} text-center`;
  document.getElementById('face-message').innerHTML = msg;
}

function updateUploadStatus(msg, type) {
  const el = document.getElementById('upload-status');
  el.style.display = 'block';
  el.className = `alert alert-${type}`;
  document.getElementById('upload-message').innerText = msg;
}

// Bersihkan memory saat leave page
window.addEventListener('beforeunload', () => stopWebcam());

// Start
initHuman();
</script>

<?= $this->endSection() ?>