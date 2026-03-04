<?php
// \app\Views\file_manager\index.php

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

    <?php if (session()->getFlashdata('berhasil')) : ?>
    <div class="alert alert-success alert-dismissible">
      <?= session()->getFlashdata('berhasil') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('gagal')) : ?>
    <div class="alert alert-danger alert-dismissible">
      <?= session()->getFlashdata('gagal') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row row-cards mb-3">
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">Foto Masuk</div>
            </div>
            <div class="h1 mb-3"><?= $stats['foto_masuk']['count'] ?></div>
            <div class="d-flex mb-2">
              <div>Total: <?= number_to_size($stats['foto_masuk']['size']) ?></div>
            </div>
            <a href="<?= base_url('file-manager/browse?type=foto_masuk') ?>" class="btn btn-primary w-100">
              Kelola
            </a>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">Foto Keluar</div>
            </div>
            <div class="h1 mb-3"><?= $stats['foto_keluar']['count'] ?></div>
            <div class="d-flex mb-2">
              <div>Total: <?= number_to_size($stats['foto_keluar']['size']) ?></div>
            </div>
            <a href="<?= base_url('file-manager/browse?type=foto_keluar') ?>" class="btn btn-primary w-100">
              Kelola
            </a>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">PDF Izin/Sakit</div>
            </div>
            <div class="h1 mb-3"><?= $stats['pdf_izin']['count'] ?></div>
            <div class="d-flex mb-2">
              <div>Total: <?= number_to_size($stats['pdf_izin']['size']) ?></div>
            </div>
            <a href="<?= base_url('file-manager/browse?type=pdf_izin') ?>" class="btn btn-primary w-100">
              Kelola
            </a>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="subheader">Total Semua</div>
            </div>
            <div class="h1 mb-3"><?= $stats['total_files'] ?></div>
            <div class="d-flex mb-2">
              <div>Total: <?= number_to_size($stats['total_size']) ?></div>
            </div>
            <button type="button" class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#summaryModal">
              Ringkasan
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Logo Management -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Ganti Logo Aplikasi</h3>
          </div>
          <div class="card-body">
            <div class="mb-3 text-center">
              <img src="<?= base_url('assets/img/company/logo.png?v=' . time()) ?>" alt="Logo" class="img-thumbnail"
                style="max-width: 200px;">
            </div>
            <form action="<?= base_url('file-manager/upload-logo') ?>" method="post" enctype="multipart/form-data">
              <?= csrf_field() ?>
              <div class="mb-3">
                <label class="form-label">Upload Logo Baru</label>
                <input type="file" class="form-control" name="logo" accept="image/*" required>
                <small class="form-hint">Format: PNG, Max: 2MB</small>
              </div>
              <button type="submit" class="btn btn-primary">Upload Logo</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Auto Delete Settings -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Pengaturan Penghapusan Otomatis</h3>
          </div>
          <div class="card-body">
            <form action="<?= base_url('file-manager/update-settings') ?>" method="post">
              <?= csrf_field() ?>
              <div class="mb-3">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="auto_delete_enabled"
                    <?= $auto_delete_enabled == '1' ? 'checked' : '' ?>>
                  <span class="form-check-label">Aktifkan Penghapusan Otomatis</span>
                </label>
              </div>
              <div class="mb-3">
                <label class="form-label">Hapus file lebih dari (hari)</label>
                <input type="number" class="form-control" name="auto_delete_days" value="<?= $auto_delete_days ?>"
                  min="30" max="365">
                <small class="form-hint">File yang lebih lama dari periode ini akan dihapus otomatis</small>
              </div>
              <?php if ($last_cleanup) : ?>
              <div class="mb-3">
                <small class="text-muted">Cleanup terakhir: <?= date('d F Y H:i', strtotime($last_cleanup)) ?></small>
              </div>
              <?php endif; ?>
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                <a href="<?= base_url('file-manager/run-cleanup') ?>" class="btn btn-warning"
                  onclick="return confirm('Jalankan cleanup sekarang?')">
                  Jalankan Cleanup
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Summary Modal -->
<div class="modal modal-blur fade" id="summaryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ringkasan Storage Management</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Overview Stats -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="alert alert-info" role="alert">
              <div class="d-flex">
                <div>
                  <h4 class="alert-title">Total Storage</h4>
                  <div class="text-muted"><?= number_to_size($stats['total_size']) ?> digunakan untuk
                    <?= $stats['total_files'] ?> file</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- File Type Breakdown -->
        <div class="mb-4">
          <h6 class="mb-3">Breakdown Berdasarkan Tipe</h6>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-truncate">
                <span class="badge bg-primary"></span> Foto Masuk
              </span>
              <span class="text-muted"><?= $stats['foto_masuk']['count'] ?> file
                (<?= number_to_size($stats['foto_masuk']['size']) ?>)</span>
            </div>
            <div class="progress progress-sm">
              <div class="progress-bar bg-primary" role="progressbar"
                style="width: <?= $stats['total_size'] > 0 ? ($stats['foto_masuk']['size'] / $stats['total_size']) * 100 : 0 ?>%"
                aria-valuenow="<?= $stats['total_size'] > 0 ? ($stats['foto_masuk']['size'] / $stats['total_size']) * 100 : 0 ?>"
                aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-truncate">
                <span class="badge bg-warning"></span> Foto Keluar
              </span>
              <span class="text-muted"><?= $stats['foto_keluar']['count'] ?> file
                (<?= number_to_size($stats['foto_keluar']['size']) ?>)</span>
            </div>
            <div class="progress progress-sm">
              <div class="progress-bar bg-warning" role="progressbar"
                style="width: <?= $stats['total_size'] > 0 ? ($stats['foto_keluar']['size'] / $stats['total_size']) * 100 : 0 ?>%"
                aria-valuenow="<?= $stats['total_size'] > 0 ? ($stats['foto_keluar']['size'] / $stats['total_size']) * 100 : 0 ?>"
                aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-truncate">
                <span class="badge bg-danger"></span> PDF Izin/Sakit
              </span>
              <span class="text-muted"><?= $stats['pdf_izin']['count'] ?> file
                (<?= number_to_size($stats['pdf_izin']['size']) ?>)</span>
            </div>
            <div class="progress progress-sm">
              <div class="progress-bar bg-danger" role="progressbar"
                style="width: <?= $stats['total_size'] > 0 ? ($stats['pdf_izin']['size'] / $stats['total_size']) * 100 : 0 ?>%"
                aria-valuenow="<?= $stats['total_size'] > 0 ? ($stats['pdf_izin']['size'] / $stats['total_size']) * 100 : 0 ?>"
                aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <!-- Auto Delete Status -->
        <div class="mb-4">
          <h6 class="mb-3">Status Penghapusan Otomatis</h6>
          <div class="row">
            <div class="col-sm-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <div class="text-muted text-sm mb-2">Status</div>
                  <div class="h5">
                    <?php if ($auto_delete_enabled == '1') : ?>
                    <span class="badge bg-success">Aktif</span>
                    <?php else : ?>
                    <span class="badge bg-secondary">Nonaktif</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <div class="text-muted text-sm mb-2">Hapus file lebih dari</div>
                  <div class="h5"><?= $auto_delete_days ?> hari</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Last Cleanup Info -->
        <?php if ($last_cleanup) : ?>
        <div class="alert alert-secondary mb-3" role="alert">
          <div class="d-flex">
            <div>
              <h4 class="alert-title">Cleanup Terakhir</h4>
              <div class="text-muted"><?= date('d F Y \p\u\k\u\l H:i', strtotime($last_cleanup)) ?></div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Summary Table -->
        <div class="table-responsive">
          <table class="table table-sm table-borderless">
            <thead class="text-muted">
              <tr>
                <th>Statistik</th>
                <th class="text-end">Nilai</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Total File</td>
                <td class="text-end"><strong><?= $stats['total_files'] ?></strong></td>
              </tr>
              <tr>
                <td>Total Ukuran</td>
                <td class="text-end"><strong><?= number_to_size($stats['total_size']) ?></strong></td>
              </tr>
              <tr>
                <td>File Foto Masuk</td>
                <td class="text-end"><?= $stats['foto_masuk']['count'] ?></td>
              </tr>
              <tr>
                <td>File Foto Keluar</td>
                <td class="text-end"><?= $stats['foto_keluar']['count'] ?></td>
              </tr>
              <tr>
                <td>File PDF Izin/Sakit</td>
                <td class="text-end"><?= $stats['pdf_izin']['count'] ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <a href="<?= base_url('file-manager') ?>" class="btn btn-link">Kembali ke Dashboard</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>