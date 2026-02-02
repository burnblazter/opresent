<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/human.js') ?>"></script>

<div class="page-body">
  <div class="container-xl">
    <div class="row g-3">
      <div class="col-md-7">
        <div class="card">
          <div class="card-body">
            <div id="map"></div>
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="card text-center">
          <div class="card-body m-auto">
            <div id="face-status" class="alert alert-info" style="display: none;">
              <div id="face-message">Memuat kamera...</div>
              <div id="face-details" class="mt-2 small" style="display: none;"></div>
            </div>
            <div id="my_result"></div>
            <div class="mt-3">
              <video id="my_camera" width="320" height="240" autoplay playsinline
                style="border: 2px solid #ccc; border-radius: 8px;"></video>
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

const human = new Human.Human({
  backend: 'webgl',
  modelBasePath: '<?= base_url('assets/models/') ?>',
  face: {
    enabled: true,
    detector: {
      rotation: false
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
      enabled: true
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

async function setupCamera() {
  try {
    updateStatus('Meminta izin kamera...', 'info');
    const video = document.getElementById('my_camera');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      throw new Error('Browser Anda tidak mendukung akses kamera.');
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

    if (!stream) throw lastError || new Error('Tidak dapat mengakses kamera.');

    video.srcObject = stream;

    await new Promise((resolve, reject) => {
      video.onloadedmetadata = () => video.play().then(resolve).catch(reject);
      setTimeout(() => reject(new Error('Timeout loading video')), 10000);
    });

    console.log('✅ Kamera berhasil diaktifkan');
    return true;
  } catch (error) {
    console.error('❌ Error kamera:', error);
    updateStatus('Gagal mengaktifkan kamera: ' + error.message, 'danger');
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
    checkButtonState();
    startFaceDetection();
  } catch (error) {
    updateStatus('Gagal memuat model AI. Refresh halaman.', 'danger');
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
      return;
    }

    faceDatabase = faceDatabase.map(item => ({
      ...item,
      descriptor: new Float32Array(Object.values(item.descriptor))
    }));
  } catch (error) {
    updateStatus('Gagal memuat database wajah.', 'danger');
  }
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
        const antispoofScore = face.real || 0;

        if (antispoofScore < 0.62) {
          updateStatus('⚠️ Deteksi spoof! Gunakan wajah asli.', 'warning');
          document.getElementById('face-verified').value = 'false';
          return;
        }

        const match = findBestMatch(face.embedding);
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

        updateStatus('✅ Wajah terverifikasi!', 'success', details);
        document.getElementById('face-verified').value = 'true';
        document.getElementById('face-similarity').value = match.score;
        document.getElementById('detected-age').value = currentAge;
        document.getElementById('detected-emotion').value = currentEmotion;
      } else {
        updateStatus('👤 Tidak ada wajah terdeteksi', 'info');
        document.getElementById('face-verified').value = 'false';
      }
    } catch (error) {
      console.error(error);
    }
  }, 1000);
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
    updateStatus('✅ Sistem siap! Arahkan wajah ke kamera.', 'success');
  }
}

document.getElementById('ambil-foto').addEventListener('click', function() {
  const faceVerified = document.getElementById('face-verified').value;
  if (faceVerified !== 'true') {
    alert('❌ Wajah belum terverifikasi dengan baik!');
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

    // Simpan data untuk presensi keluar
    const funData = {
      age: currentAge,
      emotion: currentEmotion,
      date_recorded: '<?= date('Y-m-d') ?>',
      timestamp: new Date().getTime(),
      type: 'out'
    };
    localStorage.setItem('daily_ai_mood_out', JSON.stringify(funData));

    setTimeout(() => {
      document.getElementById('presensi-form').submit();
    }, 500);

  } catch (error) {
    alert('Gagal mengambil foto: ' + error.message);
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
    detailsDiv.innerHTML = details;
    detailsDiv.style.display = 'block';
  } else {
    detailsDiv.style.display = 'none';
  }
}

window.addEventListener('beforeunload', () => {
  if (stream) stream.getTracks().forEach(track => track.stop());
});

// Map Logic
let latitude_kantor = <?= $user_lokasi_presensi->latitude ?? $latitude_kantor ?>;
let longitude_kantor = <?= $user_lokasi_presensi->longitude ?? $longitude_kantor ?>;
let latitude_pegawai = <?= $latitude_pegawai ?>;
let longitude_pegawai = <?= $longitude_pegawai ?>;
let radius = <?= $user_lokasi_presensi->radius ?? $radius ?>;

var map = L.map('map').setView([latitude_kantor, longitude_kantor], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap'
}).addTo(map);

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: '<?= base_url('assets/img/leaflet/marker-icon-2x.png') ?>',
  iconUrl: '<?= base_url('assets/img/leaflet/marker-icon.png') ?>',
  shadowUrl: '<?= base_url('assets/img/leaflet/marker-shadow.png') ?>',
});

L.marker([latitude_pegawai, longitude_pegawai]).addTo(map).bindPopup("Posisi Anda");
L.circle([latitude_kantor, longitude_kantor], {
  color: 'yellow',
  fillColor: '#dda518',
  fillOpacity: 0.5,
  radius: radius
}).addTo(map);

(async function() {
  await setupCamera();
  await initHuman();
})();
</script>
<?= $this->endSection() ?>