<?php
// \app\Views\hari_libur\index.php

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
    <div class="alert alert-info" role="alert">
      <div class="d-flex">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <circle cx="12" cy="12" r="9" />
            <line x1="12" y1="8" x2="12.01" y2="8" />
            <polyline points="11 12 12 12 12 16 13 16" />
          </svg>
        </div>
        <div>
          <h4 class="alert-title">Informasi Hari Libur</h4>
          <div class="text-muted">
            Hari <strong>Sabtu</strong> dan <strong>Minggu</strong> secara otomatis dianggap sebagai hari libur oleh
            sistem.
            Anda dapat menarik data hari libur dari API atau menambahkan secara manual.
          </div>
        </div>
      </div>
    </div>

    <?php if (session()->getFlashdata('berhasil')) : ?>
    <div class="alert alert-success alert-dismissible" role="alert">
      <div class="d-flex">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M5 12l5 5l10 -10" />
          </svg>
        </div>
        <div><?= session()->getFlashdata('berhasil') ?></div>
      </div>
      <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('gagal')) : ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <div class="d-flex">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <circle cx="12" cy="12" r="9" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
        </div>
        <div><?= session()->getFlashdata('gagal') ?></div>
      </div>
      <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
    <?php endif; ?>

    <!-- Pending Approval Section -->
    <?php if (!empty($pending_libur)) : ?>
    <div class="card mb-3">
      <div class="card-header bg-warning-lt d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock-pause me-2" width="24"
            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M20.942 13.018a9 9 0 1 0 -7.909 7.922" />
            <path d="M12 7v5l2 2" />
            <path d="M17 17v5" />
            <path d="M21 17v5" />
          </svg>
          Menunggu Persetujuan (<?= count($pending_libur) ?>)
        </h3>
        <div class="btn-group" role="group">
          <form action="<?= base_url('hari-libur/approve-all') ?>" method="post" class="d-inline"
            onsubmit="return confirm('Setujui semua hari libur yang pending?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M5 12l5 5l10 -10" />
              </svg>
              Setujui Semua
            </button>
          </form>
          <form action="<?= base_url('hari-libur/reject-all') ?>" method="post" class="d-inline"
            onsubmit="return confirm('Yakin ingin menolak dan menghapus SEMUA hari libur yang pending?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
              Tolak Semua
            </button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-vcenter card-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Sumber</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pending_libur as $index => $libur) : ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><?= date('d F Y', strtotime($libur['tanggal'])) ?></td>
                <td><?= $libur['keterangan'] ?></td>
                <td>
                  <span class="badge bg-azure">
                    <?= $libur['source'] === 'api_libur_deno' ? 'API' : 'Manual' ?>
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <form action="<?= base_url('hari-libur/approve/' . $libur['id']) ?>" method="post" class="d-inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-success btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                          stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                          stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Setujui
                      </button>
                    </form>
                    <form action="<?= base_url('hari-libur/hapus/' . $libur['id']) ?>" method="post" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menolak dan menghapus?')">
                      <?= csrf_field() ?>
                      <input type="hidden" name="_method" value="DELETE">
                      <button type="submit" class="btn btn-danger btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                          stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                          stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <line x1="18" y1="6" x2="6" y2="18" />
                          <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                        Tolak
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Approved Holidays Section -->
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Daftar Hari Libur Nasional / Cuti Bersama</h3>
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-cyan" data-bs-toggle="modal" data-bs-target="#modalSyncAPI">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
              <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
            </svg>
            Sync dari API
          </button>
          <a href="<?= base_url('hari-libur/tambah') ?>" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <line x1="12" y1="5" x2="12" y2="19" />
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Manual
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Sumber</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data_libur)) : ?>
              <?php foreach ($data_libur as $index => $libur) : ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><?= date('d F Y', strtotime($libur['tanggal'])) ?></td>
                <td><?= $libur['keterangan'] ?></td>
                <td>
                  <span class="badge bg-<?= $libur['source'] === 'api_libur_deno' ? 'azure' : 'purple' ?>">
                    <?= $libur['source'] === 'api_libur_deno' ? 'API' : 'Manual' ?>
                  </span>
                </td>
                <td>
                  <span class="badge bg-success">Disetujui</span>
                </td>
                <td>
                  <form action="<?= base_url('hari-libur/hapus/' . $libur['id']) ?>" method="post" class="d-inline"
                    onsubmit="return confirm('Yakin ingin menghapus?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php else : ?>
              <tr>
                <td colspan="6" class="text-center">Belum ada data hari libur yang disetujui.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Sync API -->
<div class="modal modal-blur fade" id="modalSyncAPI" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sync Data dari API</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('hari-libur/sync-api') ?>" method="get">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Pilih Tahun</label>
            <select name="tahun" class="form-select">
              <?php for ($i = date('Y') - 1; $i <= date('Y') + 2; $i++) : ?>
              <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <circle cx="12" cy="12" r="9" />
              <line x1="12" y1="8" x2="12.01" y2="8" />
              <polyline points="11 12 12 12 12 16 13 16" />
            </svg>
            Data yang diambil akan masuk ke daftar pending dan perlu diverifikasi terlebih dahulu.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tarik Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>