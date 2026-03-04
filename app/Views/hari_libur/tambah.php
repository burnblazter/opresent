<?php
// \app\Views\hari_libur\tambah.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<div class="page-body">
  <div class="container-xl">
    <div class="row row-cards justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Tambah Hari Libur</h3>
          </div>
          <div class="card-body">
            <form action="<?= base_url('admin/hari-libur/simpan') ?>" method="post">
              <?= csrf_field() ?>

              <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control <?= ($validation->hasError('tanggal')) ? 'is-invalid' : '' ?>"
                  name="tanggal" value="<?= old('tanggal') ?>">
                <div class="invalid-feedback"><?= $validation->getError('tanggal') ?></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Keterangan Libur</label>
                <input type="text" class="form-control <?= ($validation->hasError('keterangan')) ? 'is-invalid' : '' ?>"
                  name="keterangan" placeholder="Contoh: Tahun Baru Imlek" value="<?= old('keterangan') ?>">
                <div class="invalid-feedback"><?= $validation->getError('keterangan') ?></div>
              </div>

              <div class="form-footer text-end">
                <a href="<?= base_url('admin/hari-libur') ?>" class="btn btn-link link-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>