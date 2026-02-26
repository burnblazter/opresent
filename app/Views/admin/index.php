<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<style>
.parent_date {
  display: grid;
  grid-template-columns: repeat(8, auto);
  font-size: 20px;
  text-align: center;
  justify-content: center;
}

.parent_clock {
  display: grid;
  grid-template-columns: repeat(5, auto);
  font-size: 60px;
  font-weight: bold;
  text-align: center;
  justify-content: center;
}
</style>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="row mb-3">
      <div class="col-12">
        <div class="card text-blue p-3">
          <div class="card-body">
            <div class="parent_date">
              <div id="hari"></div>
              <div> , </div>
              <div class="ms-1"></div>
              <div id="tanggal"></div>
              <div class="ms-1"></div>
              <div id="bulan"></div>
              <div class="ms-1"></div>
              <div id="tahun"></div>
            </div>
            <div class="parent_clock">
              <div id="jam"></div>
              <div> : </div>
              <div id="menit"></div>
              <div> : </div>
              <div id="detik"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-deck row-cards">
      <div class="col-12">
        <div class="row row-cards">
          <!-- Pengguna Aktif -->
          <div class="col-sm-6 col-lg-3">
            <a href="<?= base_url('/data-pegawai') ?>" class="card card-sm card-link"
              style="text-decoration: none; color: inherit;">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-primary text-white avatar">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      <?= $jumlah_pegawai_aktif ?> orang
                    </div>
                    <div class="text-muted">
                      Pengguna Aktif
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>

          <!-- Pengguna Masuk -->
          <div class="col-sm-6 col-lg-3">
            <a href="<?= base_url('/laporan-presensi-harian') ?>" class="card card-sm card-link"
              style="text-decoration: none; color: inherit;">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-green text-white avatar">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-check" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                        <path d="M15 19l2 2l4 -4" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      <?= $jumlah_pegawai_hadir ?> orang
                    </div>
                    <div class="text-muted">
                      Pengguna Masuk
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>

          <!-- Pengguna Izin/Sakit -->
          <div class="col-sm-6 col-lg-3">
            <a href="<?= base_url('/kelola-ketidakhadiran') ?>" class="card card-sm card-link"
              style="text-decoration: none; color: inherit;">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-yellow text-white avatar">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-minus" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4c.348 0 .686 .045 1.009 .128" />
                        <path d="M16 19h6" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      <?= $jumlah_pegawai_izin ?> orang
                    </div>
                    <div class="text-muted">
                      Pengguna Izin/Sakit
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>

          <!-- Pengguna Alpha -->
          <div class="col-sm-6 col-lg-3">
            <a href="<?= base_url('/laporan-presensi-harian') ?>" class="card card-sm card-link"
              style="text-decoration: none; color: inherit;">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-danger text-white avatar">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-x" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                        <path d="M22 22l-5 -5" />
                        <path d="M17 22l5 -5" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      <?= $jumlah_pegawai_alpha ?> orang
                    </div>
                    <div class="text-muted">
                      Pengguna Alpha
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Server time sebagai patokan (Unix timestamp)
let serverTime = <?= isset($server_time) ? $server_time : time() ?>;

const namaBulan = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];

// Sync waktu client dengan server
const clientTime = Math.floor(Date.now() / 1000);
const timeDiff = serverTime - clientTime;

function updateClock() {
  // Hitung waktu berdasarkan server time + elapsed time
  const currentServerTime = Math.floor(Date.now() / 1000) + timeDiff;
  const now = new Date(currentServerTime * 1000);

  const hari = namaHari[now.getDay()];
  const tanggal = now.getDate();
  const bulan = namaBulan[now.getMonth()];
  const tahun = now.getFullYear();
  const jam = String(now.getHours()).padStart(2, '0');
  const menit = String(now.getMinutes()).padStart(2, '0');
  const detik = String(now.getSeconds()).padStart(2, '0');

  const hariEl = document.getElementById('hari');
  const tanggalEl = document.getElementById('tanggal');
  const bulanEl = document.getElementById('bulan');
  const tahunEl = document.getElementById('tahun');
  const jamEl = document.getElementById('jam');
  const menitEl = document.getElementById('menit');
  const detikEl = document.getElementById('detik');

  if (hariEl && tanggalEl && bulanEl && tahunEl && jamEl && menitEl && detikEl) {
    hariEl.innerHTML = hari;
    tanggalEl.innerHTML = tanggal;
    bulanEl.innerHTML = bulan;
    tahunEl.innerHTML = tahun;
    jamEl.innerHTML = jam;
    menitEl.innerHTML = menit;
    detikEl.innerHTML = detik;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateClock();
  setInterval(updateClock, 1000);
});
</script>
<?= $this->endSection() ?>