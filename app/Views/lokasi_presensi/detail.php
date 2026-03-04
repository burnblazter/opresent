<?php
// \app\Views\lokasi_presensi\detail.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="row row-deck row-cards align-items-start flex-md-row flex-column-reverse">
      <div class="col-lg-5 col-md-12">
        <div class="card">
          <div class="card-body">
            <div id="map" style="height: 350px; border-radius: 8px;"></div>
          </div>
        </div>
      </div>
      <div class="col-lg-7 col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <td>Nama Lokasi</td>
                  <td><?= $lokasi['nama_lokasi']; ?></td>
                </tr>
                <tr>
                  <td style="min-width: 150px">Alamat Lokasi</td>
                  <td><?= $lokasi['alamat_lokasi']; ?></td>
                </tr>
                <tr>
                  <td>Tipe Lokasi</td>
                  <td><?= $lokasi['tipe_lokasi']; ?></td>
                </tr>
                <tr>
                  <td>Latitude</td>
                  <td><?= $lokasi['latitude']; ?></td>
                </tr>
                <tr>
                  <td>Longitude</td>
                  <td><?= $lokasi['longitude']; ?></td>
                </tr>
                <tr>
                  <td>Radius</td>
                  <td><?= $lokasi['radius']; ?> meter</td>
                </tr>
                <tr>
                  <td>Zona Waktu</td>
                  <td><?= $lokasi['zona_waktu']; ?></td>
                </tr>
                <tr>
                  <td>Jam Masuk</td>
                  <td><?= $lokasi['jam_masuk']; ?></td>
                </tr>
                <tr>
                  <td>Jam Pulang</td>
                  <td><?= $lokasi['jam_pulang']; ?></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="card-footer">
            <div class="d-flex">
              <a href="<?= base_url('lokasi-presensi/edit/' . $lokasi['slug']) ?>" class="btn btn-link text-warning">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-location-cog" width="24"
                  height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M12 18l-2 -4l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5l-3.14 8.697" />
                  <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                  <path d="M19.001 15.5v1.5" />
                  <path d="M19.001 21v1.5" />
                  <path d="M22.032 17.25l-1.299 .75" />
                  <path d="M17.27 20l-1.3 .75" />
                  <path d="M15.97 17.25l1.3 .75" />
                  <path d="M20.733 20l1.3 .75" />
                </svg>Edit Info Lokasi
              </a>
              <a href="<?= base_url('lokasi-presensi') ?>" class="btn btn-link ms-auto">Kembali</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  var lat = <?= $lokasi['latitude'] ?>;
  var lng = <?= $lokasi['longitude'] ?>;
  var radius = <?= $lokasi['radius'] ?>;

  var map = L.map('map', {
    zoomControl: true,
  }).setView([lat, lng], 16);
  var currentTileLayer = null;

  function updateMapTheme() {
    const isDark = document.documentElement.getAttribute('data-darkreader-scheme') === 'dark' ||
      localStorage.getItem('theme-preference') === 'dark';

    const tileUrl = isDark ?
      'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' :
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

    if (currentTileLayer) map.removeLayer(currentTileLayer);

    currentTileLayer = L.tileLayer(tileUrl, {
      attribution: '&copy; OpenStreetMap &copy; CARTO',
      maxZoom: 19
    }).addTo(map);
  }

  updateMapTheme();
  $(document).on('click', '#enable-dark-mode, #enable-light-mode', function() {
    setTimeout(updateMapTheme, 100);
  });

  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === 'data-darkreader-scheme') updateMapTheme();
    });
  });
  observer.observe(document.documentElement, {
    attributes: true
  });

  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: '<?= base_url('assets/img/leaflet/marker-icon-2x.png') ?>',
    iconUrl: '<?= base_url('assets/img/leaflet/marker-icon.png') ?>',
    shadowUrl: '<?= base_url('assets/img/leaflet/marker-shadow.png') ?>',
  });

  L.marker([lat, lng]).addTo(map)
    .bindPopup('<strong><?= esc($lokasi['nama_lokasi']) ?></strong><br><?= esc($lokasi['alamat_lokasi']) ?>')
    .openPopup();

  L.circle([lat, lng], {
    color: '#206bc4',
    fillColor: '#206bc4',
    fillOpacity: 0.2,
    radius: radius
  }).addTo(map);
});
</script>

<?= $this->endSection() ?>