<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/cropper.min.css') ?>">
<script src="<?= base_url('assets/js/heic2any.min.js') ?>"></script>
<script src="<?= base_url('assets/js/cropper.min.js') ?>"></script>

<style>
.photo-upload-card {
  position: relative;
  cursor: pointer;
  border: 2px dashed transparent;
  transition: border-color 0.2s;
}

.photo-upload-card:hover {
  border-color: #206bc4;
}

.photo-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: transparent;
  transition: all 0.25s ease;
  border-radius: calc(0.375rem - 2px);
  gap: 6px;
  font-size: 0.85rem;
  font-weight: 500;
}

.photo-upload-card:hover .photo-overlay {
  background: rgba(0, 0, 0, 0.48);
  color: #fff;
}

#cropper-container {
  max-height: 400px;
  background: #000;
}

#cropper-container img {
  max-width: 100%;
  display: block;
}

.cropper-action-bar {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 12px;
}

.cropper-action-bar .btn {
  flex: 1;
  min-width: 80px;
}
</style>

<div class="page-body">
  <div class="container-xl">
    <div class="alert alert-warning">
      Jangan lupa <strong>simpan perubahan</strong> setelah memodifikasi data.
    </div>
    <div class="row g-3">

      <!-- ── Foto Card ── -->
      <div class="col-lg-4 col-sm-12">
        <div class="card photo-upload-card" id="photo-card" title="Klik untuk ganti foto">
          <div class="card-body text-center border-bottom p-4">
            <img src="<?= base_url('./assets/img/user_profile/' . $user_profile->foto) ?>"
              alt="<?= $user_profile->nama ?>" class="rounded img-fluid" id="img-preview"
              style="width:150px;height:150px;object-fit:cover;">
            <!-- Overlay hover -->
            <div class="photo-overlay">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.8"
                stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                  d="M5 7h1a2 2 0 0 0 2-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2" />
                <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0-6 0" />
              </svg>
              <span>Ganti Foto</span>
            </div>
          </div>
          <div class="d-flex">
            <form class="w-100" action="<?= base_url('/profile/hapus-foto') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="foto_db" value="<?= $user_profile->foto ?>">
              <button class="card-btn w-100 border-0 bg-transparent text-danger" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24"
                  stroke-width="2" stroke="currentColor" fill="none">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M4 7l16 0" />
                  <path d="M10 11l0 6" />
                  <path d="M14 11l0 6" />
                  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12" />
                  <path d="M9 7v-3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                </svg>
                Hapus Foto
              </button>
            </form>
          </div>
        </div>
        <!-- Hidden file input -->
        <input type="file" id="input-foto-trigger" accept="image/*,.heic,.heif" class="d-none">
      </div>

      <!-- ── Form Card ── -->
      <div class="col-md-8">

        <div class="card">
          <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data"
            id="profile-form">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $user_profile->id_pegawai ?>">
            <input type="hidden" name="foto_lama" value="<?= $user_profile->foto ?>">
            <!-- Hidden: hasil crop dikirim sebagai file -->
            <input type="file" name="foto" id="foto-cropped-input" class="d-none">

            <div class="card-body">
              <!-- Foto info row -->
              <div class="mb-3 d-flex align-items-center gap-3 p-3 bg-light rounded">
                <img src="<?= base_url('./assets/img/user_profile/' . $user_profile->foto) ?>" id="foto-thumb-small"
                  class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                <div class="flex-grow-1">
                  <div class="fw-medium">Foto Profil</div>
                  <div class="text-muted small">Klik foto atau tombol "Pilih Foto" untuk mengubah</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-choose-photo">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path
                      d="M5 7h1a2 2 0 0 0 2-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2" />
                    <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0-6 0" />
                  </svg>
                  Pilih Foto
                </button>
              </div>
              <?php if (validation_show_error('foto')) : ?>
              <div class="alert alert-danger"><?= validation_show_error('foto') ?></div>
              <?php endif; ?>

              <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control <?= validation_show_error('nama') ? 'is-invalid' : '' ?>"
                  name="nama" id="nama" value="<?= old('nama', $user_profile->nama) ?>">
                <?php if (validation_show_error('nama')) : ?>
                <div class="invalid-feedback"><?= validation_show_error('nama') ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control <?= validation_show_error('username') ? 'is-invalid' : '' ?>"
                  name="username" id="username" value="<?= old('username', $user_profile->username) ?>">
                <?php if (validation_show_error('username')) : ?>
                <div class="invalid-feedback"><?= validation_show_error('username') ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin"
                  class="form-select <?= validation_show_error('jenis_kelamin') ? 'is-invalid' : '' ?>">
                  <option value="">---Pilih Jenis Kelamin---</option>
                  <option value="Perempuan"
                    <?= old('jenis_kelamin', $user_profile->jenis_kelamin) === 'Perempuan' ? 'selected' : '' ?>>
                    Perempuan</option>
                  <option value="Laki-laki"
                    <?= old('jenis_kelamin', $user_profile->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' ?>>
                    Laki-laki</option>
                </select>
                <?php if (validation_show_error('jenis_kelamin')) : ?>
                <div class="invalid-feedback"><?= validation_show_error('jenis_kelamin') ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input name="alamat" type="text"
                  class="form-control <?= validation_show_error('alamat') ? 'is-invalid' : '' ?>"
                  placeholder="e.g. Jalan Daun Hijau Nomor 2" value="<?= old('alamat', $user_profile->alamat) ?>">
                <?php if (validation_show_error('alamat')) : ?>
                <div class="invalid-feedback"><?= validation_show_error('alamat') ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Nomor Handphone</label>
                <input name="no_handphone" type="text"
                  class="form-control <?= validation_show_error('no_handphone') ? 'is-invalid' : '' ?>"
                  placeholder="e.g. 087890901010" value="<?= old('no_handphone', $user_profile->no_handphone) ?>">
                <?php if (validation_show_error('no_handphone')) : ?>
                <div class="invalid-feedback"><?= validation_show_error('no_handphone') ?></div>
                <?php endif; ?>
              </div>
            </div>
            <div class="card-footer text-end">
              <div class="d-flex">
                <a href="<?= base_url('profile') ?>" class="btn btn-link">Batal</a>
                <button type="submit" class="btn btn-primary ms-auto">Simpan Perubahan</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── Cropper Modal ── -->
<div class="modal modal-blur fade" id="cropper-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M8 5v10a1 1 0 0 0 1 1h10" />
            <path d="M5 8h10a1 1 0 0 1 1 1v10" />
          </svg>
          Sesuaikan Foto Profil
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="cropper-container">
          <img id="cropper-image" src="" alt="Crop preview">
        </div>
        <!-- Toolbar -->
        <div class="cropper-action-bar mt-3">
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-zoom-in" title="Zoom In">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0-14 0" />
              <path d="M7 10l6 0" />
              <path d="M10 7l0 6" />
              <path d="M21 21l-6-6" />
            </svg> Zoom +
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-zoom-out" title="Zoom Out">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0-14 0" />
              <path d="M7 10l6 0" />
              <path d="M21 21l-6-6" />
            </svg> Zoom −
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-rotate-left">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M9 4.55a8 8 0 0 1 6 14.9m0-11.5l3 3l-3 3" />
              <path d="M5.63 7.16l0 .01" />
              <path d="M4.06 11l0 .01" />
              <path d="M4.63 15.1l0 .01" />
              <path d="M7.16 18.37l0 .01" />
              <path d="M11 19.94l0 .01" />
            </svg> Putar Kiri
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-rotate-right">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M15 4.55a8 8 0 0 0-6 14.9m0-11.5l-3 3l3 3" />
              <path d="M18.37 7.16l0 .01" />
              <path d="M19.94 11l0 .01" />
              <path d="M19.37 15.1l0 .01" />
              <path d="M16.84 18.37l0 .01" />
              <path d="M13 19.94l0 .01" />
            </svg> Putar Kanan
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-flip-h">↔ Flip H</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-flip-v">↕ Flip V</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-reset">↺ Reset</button>
          <!-- Aspect ratio -->
          <div class="ms-auto d-flex gap-2 align-items-center">
            <small class="text-muted">Rasio:</small>
            <button type="button" class="btn btn-xs btn-outline-primary ratio-btn active" data-ratio="1">1:1</button>
            <button type="button" class="btn btn-xs btn-outline-primary ratio-btn" data-ratio="NaN">Bebas</button>
            <button type="button" class="btn btn-xs btn-outline-primary ratio-btn" data-ratio="1.3333">4:3</button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btn-crop-apply">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M5 12l5 5l10-10" />
          </svg>
          Terapkan Foto
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Logout Modal -->
<div class="modal modal-blur fade" id="logout-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="modal-title">Anda yakin ingin Logout?</div>
        <div>Silahkan kembali lagi kapanpun yang Anda inginkan.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
        <a href="<?= base_url('logout') ?>" class="btn btn-danger">Ya, logout</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let cropperInstance = null;
  let currentFlipX = 1,
    currentFlipY = 1;

  const fileInput = document.getElementById('input-foto-trigger');
  const cropperImage = document.getElementById('cropper-image');
  const imgPreview = document.getElementById('img-preview');
  const fotoThumb = document.getElementById('foto-thumb-small');
  const hiddenInput = document.getElementById('foto-cropped-input');
  const modalEl = document.getElementById('cropper-modal');

  // Init modal SETELAH DOM ready, Bootstrap pasti sudah ada
  const cropperModal = new bootstrap.Modal(modalEl);

  // ── Trigger file picker ──
  function openFilePicker() {
    fileInput.click();
  }
  document.getElementById('photo-card').addEventListener('click', openFilePicker);
  document.getElementById('btn-choose-photo').addEventListener('click', function(e) {
    e.stopPropagation();
    openFilePicker();
  });

  // ── Init cropper saat modal sudah tampil ──
  modalEl.addEventListener('shown.bs.modal', function() {
    if (cropperInstance) {
      cropperInstance.destroy();
      cropperInstance = null;
    }
    currentFlipX = 1;
    currentFlipY = 1;

    cropperInstance = new Cropper(cropperImage, {
      aspectRatio: 1,
      viewMode: 1,
      dragMode: 'move',
      autoCropArea: 0.85,
      restore: false,
      guides: true,
      center: true,
      highlight: false,
      cropBoxMovable: true,
      cropBoxResizable: true,
      toggleDragModeOnDblclick: false,
      background: true,
    });
  });

  // ── Cleanup saat modal ditutup ──
  modalEl.addEventListener('hidden.bs.modal', function() {
    if (cropperInstance) {
      cropperInstance.destroy();
      cropperInstance = null;
    }
    fileInput.value = '';
  });

  // ── File selected ──
  fileInput.addEventListener('change', async function() {
    if (!this.files || !this.files[0]) return;
    let file = this.files[0];

    // HEIC/HEIF → JPEG
    if (/heic|heif/i.test(file.type) || /\.(heic|heif)$/i.test(file.name)) {
      try {
        const blob = await heic2any({
          blob: file,
          toType: 'image/jpeg',
          quality: 0.92
        });
        file = new File([blob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), {
          type: 'image/jpeg'
        });
      } catch (e) {
        alert('Gagal mengkonversi file HEIC. Coba format lain.');
        return;
      }
    }

    const reader = new FileReader();
    reader.onload = function(e) {
      cropperImage.src = e.target.result;
      cropperModal.show(); // Bootstrap sudah pasti ada karena DOMContentLoaded
    };
    reader.readAsDataURL(file);
  });

  // ── Toolbar ──
  document.getElementById('btn-zoom-in').addEventListener('click', () => cropperInstance?.zoom(0.1));
  document.getElementById('btn-zoom-out').addEventListener('click', () => cropperInstance?.zoom(-0.1));
  document.getElementById('btn-rotate-left').addEventListener('click', () => cropperInstance?.rotate(-90));
  document.getElementById('btn-rotate-right').addEventListener('click', () => cropperInstance?.rotate(90));
  document.getElementById('btn-reset').addEventListener('click', () => {
    currentFlipX = 1;
    currentFlipY = 1;
    cropperInstance?.reset();
  });
  document.getElementById('btn-flip-h').addEventListener('click', function() {
    currentFlipX *= -1;
    cropperInstance?.scaleX(currentFlipX);
  });
  document.getElementById('btn-flip-v').addEventListener('click', function() {
    currentFlipY *= -1;
    cropperInstance?.scaleY(currentFlipY);
  });

  // ── Aspect ratio ──
  document.querySelectorAll('.ratio-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const ratio = parseFloat(this.dataset.ratio);
      cropperInstance?.setAspectRatio(isNaN(ratio) ? NaN : ratio);
    });
  });

  // ── Apply crop ──
  document.getElementById('btn-crop-apply').addEventListener('click', function() {
    if (!cropperInstance) return;

    const canvas = cropperInstance.getCroppedCanvas({
      width: 400,
      height: 400,
      imageSmoothingQuality: 'high',
    });

    canvas.toBlob(function(blob) {
      const objectUrl = URL.createObjectURL(blob);
      imgPreview.src = objectUrl;
      fotoThumb.src = objectUrl;

      const dt = new DataTransfer();
      dt.items.add(new File([blob], 'profile-photo.jpg', {
        type: 'image/jpeg'
      }));
      hiddenInput.files = dt.files;

      cropperModal.hide();
    }, 'image/jpeg', 0.92);
  });
});
</script>
<?= $this->endSection() ?>