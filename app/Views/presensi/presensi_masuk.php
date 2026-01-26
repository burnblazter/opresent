<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<!-- Leaflet Library -->
<script src="<?= base_url('assets/js/leaflet.js') ?>"></script>

<!-- Human.js Library -->
<script src="<?= base_url('assets/js/human.js') ?>"></script>

<!-- Page body -->
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
            <div class="mt-3"><?= date('d F Y', strtotime($tanggal_masuk)) . ' - ' . $jam_masuk ?></div>
            <form action="<?= base_url('/presensi-masuk/simpan') ?>" method="post" id="presensi-form">
              <?= csrf_field() ?>
              <input type="hidden" name="username" value="<?= $user_profile->username ?>">
              <input type="hidden" name="id_pegawai" value="<?= $user_profile->id_pegawai ?>">
              <input type="hidden" name="tanggal_masuk" value="<?= $tanggal_masuk ?>">
              <input type="hidden" name="jam_masuk" value="<?= $jam_masuk ?>">
              <input type="hidden" name="image-cam" class="image-tag">
              <input type="hidden" name="face_verified" id="face-verified" value="false">
              <input type="hidden" name="face_similarity" id="face-similarity" value="0">
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

    stream = await navigator.mediaDevices.getUserMedia({
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
    });

    video.srcObject = stream;
    await new Promise((resolve) => {
      video.onloadedmetadata = () => {
        video.play();
        resolve();
      };
    });

    console.log('✅ Kamera berhasil diaktifkan');
    return true;
  } catch (error) {
    console.error('❌ Error kamera:', error);
    updateStatus('Gagal mengaktifkan kamera. Periksa izin browser.', 'danger');
    alert('Tidak dapat mengakses kamera!\n\n' + error.message);
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

    updateStatus('Memuat data wajah terdaftar...', 'info');
    await loadFaceDatabase();

    isModelLoaded = true;
    console.log('✅ Model AI berhasil dimuat');
    checkButtonState();
    startFaceDetection();
  } catch (error) {
    console.error('❌ Error init Human:', error);
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
      },
      credentials: 'same-origin'
    });

    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const data = await response.json();
    if (data.error) throw new Error(data.error);

    faceDatabase = data.filter(item => item.id_pegawai == <?= $user_profile->id_pegawai ?>);

    if (faceDatabase.length === 0) {
      updateStatus('Wajah Anda belum terdaftar. Hubungi Admin.', 'warning');
      document.getElementById('ambil-foto').disabled = true;
      return;
    }

    faceDatabase = faceDatabase.map(item => ({
      ...item,
      descriptor: new Float32Array(Object.values(item.descriptor))
    }));

    console.log(`✅ Database loaded: ${faceDatabase.length} face(s)`);
  } catch (error) {
    console.error('❌ Error loading database:', error);
    updateStatus('Gagal memuat data wajah. Hubungi Admin.', 'danger');
  }
}

async function startFaceDetection() {
  const video = document.getElementById('my_camera');

  if (!video || !video.srcObject) {
    console.warn('Video tidak siap, retry...');
    setTimeout(startFaceDetection, 500);
    return;
  }

  detectionInterval = setInterval(async () => {
    if (isVerifying) return;

    try {
      const result = await human.detect(video);

      if (result.face && result.face.length > 0) {
        const face = result.face[0];
        const match = findBestMatch(face.embedding);

        if (match.score >= 0.5) {
          const age = Math.round(face.age);
          const emotion = face.emotion[0] ? emotionMap[face.emotion[0].emotion] : 'Netral';
          const accuracy = (match.score * 100).toFixed(1);
          const details = `Akurasi: ${accuracy}% | Usia: ~${age} thn | Emosi: ${emotion}`;

          updateStatus('✅ Wajah terverifikasi!', 'success', details);
          document.getElementById('face-verified').value = 'true';
          document.getElementById('face-similarity').value = match.score;
        } else {
          updateStatus('⚠️ Wajah tidak dikenali', 'warning', 'Pastikan wajah Anda jelas terlihat');
          document.getElementById('face-verified').value = 'false';
        }
      } else {
        updateStatus('👤 Tidak ada wajah terdeteksi', 'info');
        document.getElementById('face-verified').value = 'false';
      }
    } catch (error) {
      console.error('Detection error:', error);
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
    if (score > bestMatch.score) {
      bestMatch = {
        name: person.nama,
        score: score
      };
    }
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
  console.log('🔵 Tombol diklik');

  const faceVerified = document.getElementById('face-verified').value;
  if (faceVerified !== 'true') {
    alert('❌ Wajah belum terverifikasi!\n\nPastikan wajah Anda terlihat jelas dan sesuai data terdaftar.');
    return;
  }

  isVerifying = true;
  if (detectionInterval) clearInterval(detectionInterval);

  const btn = document.getElementById('ambil-foto');
  btn.disabled = true;
  document.getElementById('btn-text').innerText = 'Mengambil foto...';

  try {
    const imageData = captureImage();
    console.log('✅ Foto berhasil diambil');

    document.querySelector('.image-tag').value = imageData;
    document.getElementById('my_result').innerHTML =
      '<img src="' + imageData + '" style="max-width: 100%; border-radius: 8px; border: 2px solid #28a745;"/>';

    console.log('📤 Mengirim form...');
    setTimeout(() => {
      document.getElementById('presensi-form').submit();
    }, 500);

  } catch (error) {
    console.error('❌ Error:', error);
    alert('Gagal mengambil foto!\n\n' + error.message);

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

let latitude_kantor = <?= $latitude_kantor ?>;
let longitude_kantor = <?= $longitude_kantor ?>;
let latitude_pegawai = <?= $latitude_pegawai ?>;
let longitude_pegawai = <?= $longitude_pegawai ?>;
let radius = <?= $radius ?>;

var map = L.map('map').setView([latitude_kantor, longitude_kantor], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: '<?= base_url('assets/img/leaflet/marker-icon-2x.png') ?>',
  iconUrl: '<?= base_url('assets/img/leaflet/marker-icon.png') ?>',
  shadowUrl: '<?= base_url('assets/img/leaflet/marker-shadow.png') ?>',
});

var marker = L.marker([latitude_pegawai, longitude_pegawai]).addTo(map).bindPopup("Posisi Anda saat ini.");
var circle = L.circle([latitude_kantor, longitude_kantor], {
  color: 'yellow',
  fillColor: '#dda518',
  fillOpacity: 0.5,
  radius: radius
}).addTo(map).bindPopup("Radius Presensi");

(async function() {
  try {
    console.log('🚀 Memulai inisialisasi...');
    const cameraOk = await setupCamera();
    if (!cameraOk) return;
    await initHuman();
    console.log('✅ Sistem siap digunakan');
  } catch (error) {
    console.error('❌ Init error:', error);
    updateStatus('Gagal inisialisasi: ' + error.message, 'danger');
  }
})();
</script>
<?= $this->endSection() ?>