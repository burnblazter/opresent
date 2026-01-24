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
            <h4 class="my-3">
              Pengajuan ketidakhadiran diterima. <span class="d-block">Anda tidak perlu melakukan Presensi Masuk</span>
            </h4>
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
              <div> : </div>
              <div id="menit_masuk"></div>
              <div> : </div>
              <div id="detik_masuk"></div>
            </div>
            <form action="<?= base_url('/presensi-masuk') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="latitude_kantor" value="<?= $user_lokasi_presensi->latitude ?>">
              <input type="hidden" name="longitude_kantor" value="<?= $user_lokasi_presensi->longitude ?>">
              <input type="hidden" name="radius" value="<?= $user_lokasi_presensi->radius ?>">
              <input type="hidden" name="zona_waktu" value="<?= $user_lokasi_presensi->zona_waktu ?>">
              <input type="hidden" name="latitude_pegawai" id="latitude_pegawai">
              <input type="hidden" name="longitude_pegawai" id="longitude_pegawai">
              <input type="hidden" name="tanggal_masuk" id="tanggal_masuk_hidden">
              <input type="hidden" name="jam_masuk" id="jam_masuk_hidden">
              <button type="submit" class="btn btn-primary mt-5">Masuk</button>
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
            <h4 class="my-3">
              Anda telah melakukan <span class="d-block">Presensi Masuk</span>
            </h4>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center" style="height: 100%;">
          <div class="card-header justify-content-center">
            <h3 class="mb-0">Presensi Keluar</h3>
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
            <h4 class="my-3">
              Pengajuan ketidakhadiran diterima. <span class="d-block">Anda tidak perlu melakukan Presensi Keluar</span>
            </h4>
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
            <h4 class="my-3">
              Silahkan Melakukan <span class="text-primary">Presensi Masuk</span><span class="d-block">terlebih
                dahulu</span>
            </h4>
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
            <h4 class="my-3">
              Belum waktunya melakukan <span class="d-block">Presensi Keluar</span>
            </h4>
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
            <h4 class="my-3">
              Anda telah melakukan <span class="d-block">Presensi Keluar</span>
            </h4>
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
              <div> : </div>
              <div id="menit_keluar"></div>
              <div> : </div>
              <div id="detik_keluar"></div>
            </div>
            <form action="<?= base_url('/presensi-keluar') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="latitude_kantor" value="<?= $user_lokasi_presensi->latitude ?>">
              <input type="hidden" name="longitude_kantor" value="<?= $user_lokasi_presensi->longitude ?>">
              <input type="hidden" name="radius" value="<?= $user_lokasi_presensi->radius ?>">
              <input type="hidden" name="zona_waktu" value="<?= $user_lokasi_presensi->zona_waktu ?>">
              <input type="hidden" name="latitude_pegawai" id="latitude_pegawai">
              <input type="hidden" name="longitude_pegawai" id="longitude_pegawai">
              <input type="hidden" name="tanggal_keluar" id="tanggal_keluar_hidden">
              <input type="hidden" name="jam_keluar" id="jam_keluar_hidden">
              <input type="hidden" name="id_presensi" value="<?= $data_presensi_masuk->id ?>">
              <button class="btn btn-primary mt-5 bg-red" type="submit">Keluar</button>
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
// Timezone dari server
const timezone = '<?= $user_lokasi_presensi->zona_waktu ?>';

const monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];

function updateClock() {
  // Buat date object dengan timezone yang sesuai
  const now = new Date(new Date().toLocaleString("en-US", {
    timeZone: timezone
  }));

  const tanggal = now.getDate();
  const bulan = monthNames[now.getMonth()];
  const tahun = now.getFullYear();
  const jam = String(now.getHours()).padStart(2, '0');
  const menit = String(now.getMinutes()).padStart(2, '0');
  const detik = String(now.getSeconds()).padStart(2, '0');

  // Update tampilan untuk Presensi Masuk
  const tanggalMasuk = document.getElementById('tanggal_masuk');
  const bulanMasuk = document.getElementById('bulan_masuk');
  const tahunMasuk = document.getElementById('tahun_masuk');
  const jamMasuk = document.getElementById('jam_masuk');
  const menitMasuk = document.getElementById('menit_masuk');
  const detikMasuk = document.getElementById('detik_masuk');

  if (tanggalMasuk && bulanMasuk && tahunMasuk && jamMasuk && menitMasuk && detikMasuk) {
    tanggalMasuk.innerHTML = tanggal;
    bulanMasuk.innerHTML = bulan;
    tahunMasuk.innerHTML = tahun;
    jamMasuk.innerHTML = jam;
    menitMasuk.innerHTML = menit;
    detikMasuk.innerHTML = detik;

    // Update hidden inputs untuk form submit
    document.getElementById('tanggal_masuk_hidden').value =
      `${tahun}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(tanggal).padStart(2, '0')}`;
    document.getElementById('jam_masuk_hidden').value = `${jam}:${menit}:${detik}`;
  }

  // Update tampilan untuk Presensi Keluar
  const tanggalKeluar = document.getElementById('tanggal_keluar');
  const bulanKeluar = document.getElementById('bulan_keluar');
  const tahunKeluar = document.getElementById('tahun_keluar');
  const jamKeluar = document.getElementById('jam_keluar');
  const menitKeluar = document.getElementById('menit_keluar');
  const detikKeluar = document.getElementById('detik_keluar');

  if (tanggalKeluar && bulanKeluar && tahunKeluar && jamKeluar && menitKeluar && detikKeluar) {
    tanggalKeluar.innerHTML = tanggal;
    bulanKeluar.innerHTML = bulan;
    tahunKeluar.innerHTML = tahun;
    jamKeluar.innerHTML = jam;
    menitKeluar.innerHTML = menit;
    detikKeluar.innerHTML = detik;

    // Update hidden inputs untuk form submit
    document.getElementById('tanggal_keluar_hidden').value =
      `${tahun}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(tanggal).padStart(2, '0')}`;
    document.getElementById('jam_keluar_hidden').value = `${jam}:${menit}:${detik}`;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateClock();
  // Update setiap detik, tapi client-side aja
  setInterval(updateClock, 1000);
});

getLocation();

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition,
      function(error) {
        alert("Error code: " + error.code + " :" + error.message);
      }, {
        timeout: 30000,
        maximumAge: 0,
        enableHighAccuracy: true,
      }
    );
  } else {
    alert('Browser Anda tidak mendukung');
  }
}

function showPosition(position) {
  document.getElementById('latitude_pegawai').value = position.coords.latitude;
  document.getElementById('longitude_pegawai').value = position.coords.longitude;
}
</script>
<?= $this->endSection() ?>