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
            <form action="<?= base_url('/presensi-masuk') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="latitude_kantor" value="<?= $user_lokasi_presensi->latitude ?>">
              <input type="hidden" name="longitude_kantor" value="<?= $user_lokasi_presensi->longitude ?>">
              <input type="hidden" name="radius" value="<?= $user_lokasi_presensi->radius ?>">
              <input type="hidden" name="zona_waktu" value="<?= $user_lokasi_presensi->zona_waktu ?>">
              <input type="hidden" name="latitude_pegawai" id="latitude_pegawai_in">
              <input type="hidden" name="longitude_pegawai" id="longitude_pegawai_in">
              <input type="hidden" name="tanggal_masuk" id="tanggal_masuk_hidden">
              <input type="hidden" name="jam_masuk" id="jam_masuk_hidden">
              <button type="submit" class="btn btn-primary mt-5">Masuk Sekolah</button>
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
            <h4 class="my-3">Absen Masuk <span class="d-block text-primary">Berhasil!</span></h4>

            <div id="ai-joke-container-in" class="ai-joke-box" style="display: none;">
              <div class="text-muted small text-uppercase fw-bold mb-2">Mood Pagi Ini</div>
              <div id="ai-emoji-in" style="font-size: 2.5rem; line-height: 1;"></div>
              <p id="ai-message-in" class="mt-2 mb-1 text-dark fw-medium"></p>
              <div class="text-muted small mt-1" style="font-size: 0.75rem;">(Deteksi Usia: <span
                  id="ai-age-in">0</span> thn)</div>

              <div class="mt-2 border-top pt-1">
                <small class="text-muted fst-italic" style="font-size: 0.65rem; display:block; line-height: 1.2;">
                  *Note: Umur & ekspresi cuma tebak-tebakan AI buat seru-seruan aja ya! Jangan baper, aslinya kamu cakep
                  kok. 😉
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
            <h4 class="my-3">Ups! Kamu belum <span class="text-primary">Absen Masuk</span> pagi ini.</h4>

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
            <h4 class="my-3">Absen Pulang <span class="d-block text-success">Berhasil!</span></h4>

            <div id="ai-joke-container-out" class="ai-joke-box" style="display: none;">
              <div class="text-muted small text-uppercase fw-bold mb-2">Vibes Pulang Sekolah</div>
              <div id="ai-emoji-out" style="font-size: 2.5rem; line-height: 1;"></div>
              <p id="ai-message-out" class="mt-2 mb-1 text-dark fw-medium"></p>
              <div class="text-muted small mt-1" style="font-size: 0.75rem;">(Deteksi Usia: <span
                  id="ai-age-out">0</span> thn)</div>

              <div class="mt-2 border-top pt-1">
                <small class="text-muted fst-italic" style="font-size: 0.65rem; display:block; line-height: 1.2;">
                  *Note: Umur & ekspresi cuma tebak-tebakan AI buat seru-seruan aja ya! Jangan baper, have fun! ✌️
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
            <form action="<?= base_url('/presensi-keluar') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="latitude_kantor" value="<?= $user_lokasi_presensi->latitude ?>">
              <input type="hidden" name="longitude_kantor" value="<?= $user_lokasi_presensi->longitude ?>">
              <input type="hidden" name="radius" value="<?= $user_lokasi_presensi->radius ?>">
              <input type="hidden" name="zona_waktu" value="<?= $user_lokasi_presensi->zona_waktu ?>">
              <input type="hidden" name="latitude_pegawai" id="latitude_pegawai_out">
              <input type="hidden" name="longitude_pegawai" id="longitude_pegawai_out">
              <input type="hidden" name="tanggal_keluar" id="tanggal_keluar_hidden">
              <input type="hidden" name="jam_keluar" id="jam_keluar_hidden">
              <input type="hidden" name="id_presensi" value="<?= $data_presensi_masuk->id ?>">
              <button class="btn btn-primary mt-5 bg-red" type="submit">Pulang Sekolah</button>
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
let serverTime = <?= $server_time ?>;
const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober",
  "November", "Desember"
];

document.addEventListener('DOMContentLoaded', function() {
  updateClock();
  setInterval(updateClock, 1000);

  // Cek untuk Masuk
  const boxIn = document.getElementById('ai-joke-container-in');
  if (boxIn) checkAiData('daily_ai_mood', 'in', boxIn);

  // Cek untuk Keluar
  const boxOut = document.getElementById('ai-joke-container-out');
  if (boxOut) checkAiData('daily_ai_mood_out', 'out', boxOut);
});

function checkAiData(key, type, container) {
  try {
    const raw = localStorage.getItem(key);
    if (!raw) return;
    const data = JSON.parse(raw);
    const today = '<?= date('Y-m-d') ?>';

    if (data.date_recorded === today) {
      const joke = generateJoke(data.age, data.emotion, type);

      document.getElementById(`ai-emoji-${type}`).innerText = joke.emoji;
      document.getElementById(`ai-message-${type}`).innerText = joke.text;
      document.getElementById(`ai-age-${type}`).innerText = data.age;

      container.style.display = 'block';
    }
  } catch (e) {
    console.log("Error parsing AI Data", e);
  }
}

function generateJoke(age, emotion, type) {
  // DB Joke Khas Anak Sekolah (SMA)
  const jokes = {
    // === MASUK SEKOLAH (IN) ===
    in: [
      // HAPPY
      {
        e: 'happy',
        t: "Senyumnya cerah banget! Pasti PR udah kelar nih.",
        i: "😎"
      },
      {
        e: 'happy',
        t: "Bahagia amat, dapet uang saku tambahan ya?",
        i: "🤑"
      },
      {
        e: 'happy',
        t: "Cieee yang semangat mau ketemu doi di kelas.",
        i: "😍"
      },
      {
        e: 'happy',
        t: "Full senyum! Siap menghadapi pelajaran Matematika?",
        i: "📐"
      },
      {
        e: 'happy',
        t: "Vibes-nya positif banget, jangan lupa piket kelas ya.",
        i: "🧹"
      },

      // ANGRY
      {
        e: 'angry',
        t: "Waduh, pagi-pagi jangan emosi bestie, nanti cepet tua.",
        i: "😤"
      },
      {
        e: 'angry',
        t: "Muka ditekuk aja. Belum sarapan atau lupa ngerjain tugas?",
        i: "🍜"
      },
      {
        e: 'angry',
        t: "Sabar... Macet di jalan emang bikin darting.",
        i: "🚗"
      },
      {
        e: 'angry',
        t: "Jangan galak-galak, nanti ditunjuk guru maju ke depan loh.",
        i: "👩‍🏫"
      },

      // SAD
      {
        e: 'sad',
        t: "Jangan sad boy/sad girl gitu dong. Semangat belajarnya!",
        i: "🥺"
      },
      {
        e: 'sad',
        t: "Ngantuk atau galau? Cuci muka dulu gih biar seger.",
        i: "💧"
      },
      {
        e: 'sad',
        t: "Tenang bestie, badai pasti berlalu (termasuk ulangan harian).",
        i: "🌈"
      },
      {
        e: 'sad',
        t: "Kenapa murung? Topi dasi lengkap kan? Aman kok.",
        i: "tophat"
      },

      // NEUTRAL
      {
        e: 'neutral',
        t: "Mode serius: ON. Fokus banget nih kayaknya.",
        i: "😐"
      },
      {
        e: 'neutral',
        t: "Nyawanya belum kumpul semua ya? Ngopi dulu di kantin.",
        i: "☕"
      },
      {
        e: 'neutral',
        t: "Datar amat mukanya, kayak tanggal tua.",
        i: "📅"
      },
      {
        e: 'neutral',
        t: "Santai bro, hari ini jamkos (semoga).",
        i: "🤞"
      },

      // SURPRISED
      {
        e: 'surprised',
        t: "Kaget kenapa? Lupa bawa buku paket?",
        i: "📚"
      },
      {
        e: 'surprised',
        t: "Melotot gitu liat apa? Ada razia rambut ya?",
        i: "💇"
      },

      // FEAR
      {
        e: 'fear',
        t: "Tegang amat, belum ngerjain PR ya?",
        i: "📝"
      },
      {
        e: 'fear',
        t: "Jangan takut, guru killer hari ini rapat kok (mungkin).",
        i: "🤫"
      },

      // DEFAULT / ANY
      {
        e: 'any',
        t: "Selamat Pagi! Jangan lupa berdoa sebelum belajar.",
        i: "🙏"
      },
      {
        e: 'any',
        t: "Gas masuk kelas! Keburu bel bunyi.",
        i: "🔔"
      }
    ],

    // === PULANG SEKOLAH (OUT) ===
    out: [
      // HAPPY
      {
        e: 'happy',
        t: "Akhirnya bel surga berbunyi! Hati-hati di jalan.",
        i: "🎉"
      },
      {
        e: 'happy',
        t: "Senyum kemenangan setelah seharian belajar.",
        i: "🏆"
      },
      {
        e: 'happy',
        t: "Bahagia banget mau nongkrong atau mau tidur?",
        i: "💤"
      },
      {
        e: 'happy',
        t: "Pulang! Saatnya push rank atau drakoran.",
        i: "🎮"
      },
      {
        e: 'happy',
        t: "Full senyum, pasti nggak ada PR buat besok.",
        i: "✨"
      },

      // ANGRY
      {
        e: 'angry',
        t: "Capek ya? Jangan marah-marah, mending beli seblak.",
        i: "🔥"
      },
      {
        e: 'angry',
        t: "Kusut amat. Motor bensinnya abis?",
        i: "⛽"
      },
      {
        e: 'angry',
        t: "Sabar bestie, besok libur (kalau hari Sabtu).",
        i: "📅"
      },

      // SAD
      {
        e: 'sad',
        t: "Lelah letih lesu? Kasur di rumah sudah memanggil.",
        i: "🛌"
      },
      {
        e: 'sad',
        t: "Jangan sedih, besok ketemu doi lagi kok.",
        i: "👋"
      },
      {
        e: 'sad',
        t: "Tugas numpuk? Nangis bentar, abis itu kerjain.",
        i: "💪"
      },

      // NEUTRAL
      {
        e: 'neutral',
        t: "Muka lelah tapi lega. Bye-bye sekolah!",
        i: "🏫"
      },
      {
        e: 'neutral',
        t: "Otw pulang. Jangan mampir-mampir kalau belum izin ortu.",
        i: "🏠"
      },
      {
        e: 'neutral',
        t: "Flat banget, butuh healing secepatnya.",
        i: "ucl"
      },

      // SURPRISED
      {
        e: 'surprised',
        t: "Baru sadar kalau besok ulangan harian?",
        i: "📖"
      },

      // FEAR
      {
        e: 'fear',
        t: "Buru-buru amat, takut dicariin emak?",
        i: "🏃"
      },

      // DEFAULT / ANY
      {
        e: 'any',
        t: "Terima kasih sudah belajar hari ini! Safe trip home.",
        i: "🛵"
      },
      {
        e: 'any',
        t: "Langsung pulang ya, jangan tawuran!",
        i: "☮️"
      }
    ]
  };

  const list = jokes[type] || jokes['in'];

  // Cari yang match emosi
  let matches = list.filter(j => j.e === emotion);

  // Kalau tidak ada match spesifik, ambil yang 'any' atau 'neutral'
  if (matches.length === 0) {
    matches = list.filter(j => j.e === 'any' || j.e === 'neutral');
  }

  // Pilih secara acak dari yang match
  const selected = matches[Math.floor(Math.random() * matches.length)];

  // Fallback jika benar-benar kosong
  if (!selected) {
    return {
      text: "Semangat!",
      emoji: "✊"
    };
  }

  return {
    text: selected.t,
    emoji: selected.i
  };
}

// Logic Jam
const clientTime = Math.floor(Date.now() / 1000);
const timeDiff = serverTime - clientTime;

function updateClock() {
  const now = new Date((Math.floor(Date.now() / 1000) + timeDiff) * 1000);
  const tgl = now.getDate(),
    bln = monthNames[now.getMonth()],
    thn = now.getFullYear();
  const jam = String(now.getHours()).padStart(2, '0'),
    mnt = String(now.getMinutes()).padStart(2, '0'),
    dtk = String(now.getSeconds()).padStart(2, '0');

  const updateEl = (suffix) => {
    if (document.getElementById(`tanggal_${suffix}`)) {
      document.getElementById(`tanggal_${suffix}`).innerHTML = tgl;
      document.getElementById(`bulan_${suffix}`).innerHTML = bln;
      document.getElementById(`tahun_${suffix}`).innerHTML = thn;
      document.getElementById(`jam_${suffix}`).innerHTML = jam;
      document.getElementById(`menit_${suffix}`).innerHTML = mnt;
      document.getElementById(`detik_${suffix}`).innerHTML = dtk;

      const hiddenTgl = document.getElementById(`tanggal_${suffix}_hidden`);
      const hiddenJam = document.getElementById(`jam_${suffix}_hidden`);
      if (hiddenTgl) hiddenTgl.value =
        `${thn}-${String(now.getMonth()+1).padStart(2,'0')}-${String(tgl).padStart(2,'0')}`;
      if (hiddenJam) hiddenJam.value = `${jam}:${mnt}:${dtk}`;
    }
  };

  updateEl('masuk');
  updateEl('keluar');
}

getLocation();

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((pos) => {
      const lat = pos.coords.latitude;
      const long = pos.coords.longitude;
      if (document.getElementById('latitude_pegawai_in')) document.getElementById('latitude_pegawai_in').value =
        lat;
      if (document.getElementById('longitude_pegawai_in')) document.getElementById('longitude_pegawai_in').value =
        long;
      if (document.getElementById('latitude_pegawai_out')) document.getElementById('latitude_pegawai_out').value =
        lat;
      if (document.getElementById('longitude_pegawai_out')) document.getElementById('longitude_pegawai_out').value =
        long;
    });
  }
}
</script>
<?= $this->endSection() ?>