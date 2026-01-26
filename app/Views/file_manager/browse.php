<?= $this->extend('templates/index') ?>
<?= $this->section('pageBody') ?>

<div class="page-body">
  <div class="container-xl">

    <div class="row mb-3">
      <div class="col">
        <a href="<?= base_url('file-manager') ?>" class="btn btn-outline-primary">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <polyline points="5 12 3 12 12 3 21 12 19 12" />
            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
          </svg>
          Kembali ke Dashboard
        </a>
      </div>
    </div>

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

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <?php
                    $titles = [
                        'foto_masuk' => 'Foto Presensi Masuk',
                        'foto_keluar' => 'Foto Presensi Keluar',
                        'pdf_izin' => 'PDF Izin/Sakit',
                    ];
                    echo $titles[$type] ?? 'File Manager';
                    ?>
        </h3>
        <div class="card-actions">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBulkAction">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M12 5l0 14" />
              <path d="M5 12l14 0" />
            </svg>
            Aksi Massal
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <div class="text-muted">Total File</div>
            <div class="h3"><?= $stats['count'] ?></div>
          </div>
          <div class="col-md-4">
            <div class="text-muted">Total Ukuran</div>
            <div class="h3"><?= number_to_size($stats['size']) ?></div>
          </div>
          <div class="col-md-4">
            <div class="text-muted">Halaman</div>
            <div class="h3"><?= $currentPage ?> / <?= ceil($total / 20) ?></div>
          </div>
        </div>

        <?php if (!empty($files)) : ?>
        <div class="table-responsive">
          <table class="table table-vcenter card-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama File</th>
                <th>Ukuran</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                                $no = ($currentPage - 1) * 20 + 1;
                                foreach ($files as $file) : 
                                ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <?php if ($type === 'pdf_izin') : ?>
                    <svg xmlns="http://www.w3.org/2000/svg"
                      class="icon icon-tabler icon-tabler-file-type-pdf text-danger me-2" width="24" height="24"
                      viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                      stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                      <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                      <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                      <path d="M17 18h2" />
                      <path d="M20 15h-3v6" />
                      <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                    </svg>
                    <?php else : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo text-primary me-2"
                      width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                      stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <line x1="15" y1="8" x2="15.01" y2="8" />
                      <rect x="4" y="4" width="16" height="16" rx="3" />
                      <path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5" />
                      <path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2" />
                    </svg>
                    <?php endif; ?>
                    <span class="text-truncate" style="max-width: 300px;"><?= esc($file['name']) ?></span>
                  </div>
                </td>
                <td><?= number_to_size($file['size']) ?></td>
                <td>
                  <span data-bs-toggle="tooltip" title="<?= date('d F Y H:i:s', $file['date']) ?>">
                    <?= date('d M Y', $file['date']) ?>
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-info"
                      onclick="previewFile('<?= urlencode($file['name']) ?>', '<?= $type ?>')">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="2" />
                        <path
                          d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                      </svg>
                    </button>
                    <a href="<?= base_url('file-manager/download?file=' . urlencode($file['name']) . '&type=' . $type) ?>"
                      class="btn btn-sm btn-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <polyline points="7 11 12 16 17 11" />
                        <line x1="12" y1="4" x2="12" y2="16" />
                      </svg>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?= $pager ?>
        </div>
        <?php else : ?>
        <div class="empty">
          <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <circle cx="12" cy="12" r="9" />
              <line x1="9" y1="10" x2="9.01" y2="10" />
              <line x1="15" y1="10" x2="15.01" y2="10" />
              <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
            </svg>
          </div>
          <p class="empty-title">Tidak ada file</p>
          <p class="empty-subtitle text-muted">Belum ada file yang tersimpan di kategori ini.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<!-- Modal Bulk Action -->
<div class="modal modal-blur fade" id="modalBulkAction" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Aksi Massal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-download" type="button"
              role="tab">Download</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-delete" type="button"
              role="tab">Hapus</button>
          </li>
        </ul>

        <div class="tab-content">
          <!-- Download Tab -->
          <div class="tab-pane fade show active" id="tab-download" role="tabpanel">
            <form action="<?= base_url('file-manager/download-bulk') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="type" value="<?= $type ?>">

              <div class="mb-3">
                <label class="form-label">Download file dari</label>
                <select class="form-select" name="days">
                  <option value="0">Semua file</option>
                  <option value="7">7 hari terakhir</option>
                  <option value="30">30 hari terakhir</option>
                  <option value="60">60 hari terakhir</option>
                  <option value="90">90 hari terakhir</option>
                </select>
              </div>

              <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                  stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <circle cx="12" cy="12" r="9" />
                  <line x1="12" y1="8" x2="12.01" y2="8" />
                  <polyline points="11 12 12 12 12 16 13 16" />
                </svg>
                File akan didownload dalam format ZIP.
              </div>

              <button type="submit" class="btn btn-primary w-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                  stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                  <polyline points="7 11 12 16 17 11" />
                  <line x1="12" y1="4" x2="12" y2="16" />
                </svg>
                Download ZIP
              </button>
            </form>
          </div>

          <!-- Delete Tab -->
          <div class="tab-pane fade" id="tab-delete" role="tabpanel">
            <form action="<?= base_url('file-manager/delete-bulk') ?>" method="post"
              onsubmit="return confirm('PERHATIAN: File yang dihapus tidak dapat dikembalikan. Lanjutkan?')">
              <?= csrf_field() ?>
              <input type="hidden" name="type" value="<?= $type ?>">

              <div class="mb-3">
                <label class="form-label">Hapus file lebih dari</label>
                <select class="form-select" name="days" required>
                  <option value="">-- Pilih Periode --</option>
                  <option value="30">30 hari</option>
                  <option value="60">60 hari</option>
                  <option value="90">90 hari</option>
                  <option value="180">180 hari</option>
                  <option value="365">1 tahun</option>
                </select>
              </div>

              <div class="alert alert-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                  stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <circle cx="12" cy="12" r="9" />
                  <line x1="12" y1="8" x2="12" y2="12" />
                  <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <strong>PERINGATAN:</strong> File yang dihapus tidak dapat dikembalikan!
              </div>

              <button type="submit" class="btn btn-danger w-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                  stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <line x1="4" y1="7" x2="20" y2="7" />
                  <line x1="10" y1="11" x2="10" y2="17" />
                  <line x1="14" y1="11" x2="14" y2="17" />
                  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                </svg>
                Hapus File
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Preview -->
<div class="modal modal-blur fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewTitle">Preview File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" id="previewContent">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function previewFile(fileName, type) {
  const modal = new bootstrap.Modal(document.getElementById('modalPreview'));
  const content = document.getElementById('previewContent');
  const title = document.getElementById('previewTitle');

  title.textContent = decodeURIComponent(fileName);
  content.innerHTML =
    '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

  modal.show();

  const previewUrl = '<?= base_url('file-manager/preview') ?>?file=' + fileName + '&type=' + type;

  if (type === 'pdf_izin') {
    content.innerHTML = '<iframe src="' + previewUrl + '" style="width:100%; height:70vh;" frameborder="0"></iframe>';
  } else {
    content.innerHTML = '<img src="' + previewUrl + '" class="img-fluid" alt="Preview">';
  }
}

// Tooltip initialization
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<?= $this->endSection() ?>