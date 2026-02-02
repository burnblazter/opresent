<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<div class="page-body">
  <div class="container-xl">

    <!-- ROW UTAMA -->
    <div class="row row-deck row-cards align-items-start g-3">

      <!-- FOTO -->
      <div class="col-lg-4 col-sm-12">
        <div class="card">
          <div class="card-body text-center border-bottom">
            <img src="<?= base_url('./assets/img/user_profile/' . $data_pegawai->foto) ?>"
              alt="<?= $data_pegawai->nama ?>" class="img-thumbnail object-fit-cover" width="150" height="150">
          </div>
          <div class="d-flex">
            <form class="w-100" action="<?= base_url('/hapus-foto/' . $data_pegawai->username) ?>" method="post">
              <input type="hidden" name="foto_db" value="<?= $data_pegawai->foto ?>">
              <button class="card-btn bg-transparent w-100 border-0" type="submit">
                Hapus Foto
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- FORM EDIT -->
      <div class="col-lg-8 col-sm-12">
        <form action="<?= base_url('/data-pegawai/update') ?>" method="post" class="w-100">
          <?= csrf_field() ?>
          <input type="hidden" name="username_db" value="<?= $data_pegawai->username ?>">
          <input type="hidden" name="id" value="<?= $data_pegawai->id ?>">
          <input type="hidden" name="id_pegawai" value="<?= $data_pegawai->id_pegawai ?>">
          <input type="hidden" name="id_user" value="<?= $data_pegawai->id_user ?>">
          <input type="hidden" name="role_db" value="<?= $data_pegawai->role_id ?>">

          <div class="card w-100">
            <div class="card-body">

              <div class="mb-3">
                <label class="form-label">Nomor Induk (NIS/NIP)</label>
                <input name="nomor_induk" type="text"
                  class="form-control <?= validation_show_error('nomor_induk') ? 'is-invalid' : '' ?>"
                  value="<?= old('nomor_induk', $data_pegawai->nomor_induk) ?>">
                <div class="invalid-feedback"><?= validation_show_error('nomor_induk') ?></div>
                <small class="form-hint">NIS untuk siswa atau NIP untuk pegawai</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Nama</label>
                <input name="nama" type="text"
                  class="form-control <?= validation_show_error('nama') ? 'is-invalid' : '' ?>"
                  value="<?= old('nama', $data_pegawai->nama) ?>">
                <div class="invalid-feedback"><?= validation_show_error('nama') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Unit</label>
                <select name="jabatan"
                  class="form-select select2 <?= validation_show_error('jabatan') ? 'is-invalid' : '' ?>"
                  id="select-jabatan-edit">
                  <option value="">---Pilih Unit---</option>
                  <?php foreach ($jabatan as $option) : ?>
                  <option value="<?= $option->id ?>"
                    <?= old('jabatan', $data_pegawai->id_jabatan) == $option->id ? 'selected' : '' ?>>
                    <?= $option->jabatan ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback"><?= validation_show_error('jabatan') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Username</label>
                <input name="username" type="text"
                  class="form-control <?= validation_show_error('username') ? 'is-invalid' : '' ?>"
                  value="<?= old('username', $data_pegawai->username) ?>">
                <div class="invalid-feedback"><?= validation_show_error('username') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Role Akun</label>
                <select name="role" class="form-select select2 <?= validation_show_error('role') ? 'is-invalid' : '' ?>"
                  id="select-role-edit">
                  <option value="">---Pilih Role---</option>
                  <?php foreach ($role as $role_option) : ?>
                  <option value="<?= $role_option['id'] ?>"
                    <?= old('role', $data_pegawai->role_id) == $role_option['id'] ? 'selected' : '' ?>>
                    <?= $role_option['name'] ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback"><?= validation_show_error('role') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <input name="email" type="text"
                  class="form-control <?= validation_show_error('email') ? 'is-invalid' : '' ?>"
                  value="<?= old('email', $data_pegawai->email) ?>">
                <div class="invalid-feedback"><?= validation_show_error('email') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin"
                  class="form-select select2 <?= validation_show_error('jenis_kelamin') ? 'is-invalid' : '' ?>"
                  id="select-jenis-kelamin-edit">
                  <option value="">---Pilih---</option>
                  <option value="Perempuan"
                    <?= old('jenis_kelamin', $data_pegawai->jenis_kelamin)=='Perempuan'?'selected':'' ?>>Perempuan
                  </option>
                  <option value="Laki-laki"
                    <?= old('jenis_kelamin', $data_pegawai->jenis_kelamin)=='Laki-laki'?'selected':'' ?>>Laki-laki
                  </option>
                </select>
                <div class="invalid-feedback"><?= validation_show_error('jenis_kelamin') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input name="alamat" type="text"
                  class="form-control <?= validation_show_error('alamat') ? 'is-invalid' : '' ?>"
                  value="<?= old('alamat', $data_pegawai->alamat) ?>">
                <div class="invalid-feedback"><?= validation_show_error('alamat') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Nomor Handphone</label>
                <input name="no_handphone" type="text"
                  class="form-control <?= validation_show_error('no_handphone') ? 'is-invalid' : '' ?>"
                  value="<?= old('no_handphone', $data_pegawai->no_handphone) ?>">
                <div class="invalid-feedback"><?= validation_show_error('no_handphone') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Lokasi Presensi</label>
                <select name="lokasi_presensi"
                  class="form-select select2 <?= validation_show_error('lokasi_presensi') ? 'is-invalid' : '' ?>"
                  id="select-lokasi-edit">
                  <option value="">---Pilih Lokasi---</option>
                  <?php foreach ($lokasi as $lokasi_option) : ?>
                  <option value="<?= $lokasi_option['id'] ?>"
                    <?= old('lokasi_presensi', $data_pegawai->id_lokasi_presensi)==$lokasi_option['id']?'selected':'' ?>>
                    <?= $lokasi_option['nama_lokasi'] ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback"><?= validation_show_error('lokasi_presensi') ?></div>
              </div>

              <!-- BAGIAN UBAH PASSWORD -->
              <hr class="my-4">
              <h3 class="card-title mb-3">Ubah Password</h3>

              <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input name="password_baru" type="password"
                  class="form-control <?= validation_show_error('password_baru') ? 'is-invalid' : '' ?>"
                  placeholder="Password tidak diubah">
                <div class="invalid-feedback"><?= validation_show_error('password_baru') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input name="konfirmasi_password" type="password"
                  class="form-control <?= validation_show_error('konfirmasi_password') ? 'is-invalid' : '' ?>"
                  placeholder="Password tidak diubah">
                <div class="invalid-feedback"><?= validation_show_error('konfirmasi_password') ?></div>
                <small class="form-hint">Kosongkan saja jika tidak ingin mengubah password</small>
              </div>

            </div>

            <div class="card-footer text-end">
              <a href="<?= base_url('data-pegawai') ?>" class="btn btn-link">Batal</a>
              <button type="submit" class="btn btn-primary ms-auto">Simpan Perubahan</button>
            </div>
          </div>
        </form>
      </div>

    </div><!-- END ROW -->

  </div>
</div>

<script>
$(document).ready(function() {
  $('.select2').select2({
    placeholder: '--- Pilih Opsi ---',
    allowClear: true,
    width: '100%',
    language: {
      noResults: function() {
        return "Tidak ada hasil";
      }
    }
  });
});
</script>

<?= $this->endSection() ?>