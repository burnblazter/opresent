<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<style>
.page-body {
  background: #f8fafc;
  min-height: 100vh;
  padding: 20px 0;
}

.detail-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 24px;
}

.card-header {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
  padding: 24px;
}

.card-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 30px;
  padding: 30px;
}

.image-section {
  text-align: center;
}

.face-preview {
  width: 100%;
  max-width: 400px;
  border-radius: 12px;
  border: 3px solid #e5e7eb;
  margin-bottom: 16px;
}

.info-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.info-group {
  background: #f9fafb;
  padding: 16px 20px;
  border-radius: 10px;
  border-left: 4px solid #dda518;
}

.info-label {
  font-weight: 600;
  color: #374151;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
}

.info-value {
  color: #1e3a8a;
  font-size: 1.05rem;
  font-weight: 500;
}

.action-buttons {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-lg {
  padding: 14px 28px;
  font-size: 1.05rem;
}

.btn-success {
  background: #10b981;
  color: white;
}

.btn-success:hover {
  background: #059669;
  transform: translateY(-2px);
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
  transform: translateY(-2px);
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.badge {
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
}

.badge-warning {
  background: #fef3c7;
  color: #92400e;
}

@media (max-width: 768px) {
  .detail-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="page-body">
  <div class="container-xl">

    <!-- Back Button -->
    <div class="row mb-3">
      <div class="col">
        <a href="<?= base_url('kelola-face-enrollment') ?>" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Kembali ke Daftar Request
        </a>
      </div>
    </div>

    <!-- Detail Card -->
    <div class="detail-card">
      <div class="card-header">
        <h3 class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 10px;">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          Detail Request - <?= esc($request->nama) ?>
        </h3>
      </div>

      <div class="detail-grid">

        <!-- Image Section -->
        <div class="image-section">
          <?php if ($request->image_path): ?>
          <img src="<?= base_url('kelola-face-enrollment/image/' . $request->id) ?>" alt="Face Preview"
            class="face-preview">
          <p class="text-muted" style="font-size: 0.9rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" style="vertical-align: middle;">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            Verifikasi kecocokan wajah dengan data pegawai
          </p>
          <?php else: ?>
          <div style="padding: 60px; background: #f3f4f6; border-radius: 12px; border: 2px dashed #d1d5db;">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" style="color: #9ca3af; margin: 0 auto 16px; display: block;">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <p class="text-muted">Tidak ada gambar preview</p>
          </div>
          <?php endif; ?>
        </div>

        <!-- Info Section -->
        <div class="info-section">

          <div class="info-group">
            <div class="info-label">Nama Pegawai</div>
            <div class="info-value"><?= esc($request->nama) ?></div>
          </div>

          <div class="info-group">
            <div class="info-label">NIP</div>
            <div class="info-value"><?= esc($request->nomor_induk) ?></div>
          </div>

          <div class="info-group">
            <div class="info-label">Label Foto</div>
            <div class="info-value"><?= esc($request->label) ?></div>
          </div>

          <div class="info-group">
            <div class="info-label">Tanggal Request</div>
            <div class="info-value">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <?= date('d F Y, H:i:s', strtotime($request->created_at)) ?>
            </div>
          </div>

          <?php if ($request->reason): ?>
          <div class="info-group" style="border-left-color: #f59e0b;">
            <div class="info-label">Alasan Request</div>
            <div class="info-value" style="color: #92400e;"><?= esc($request->reason) ?></div>
          </div>
          <?php endif; ?>

          <div class="info-group">
            <div class="info-label">Status</div>
            <div>
              <span class="badge badge-warning">⏳ Pending - Menunggu Approval</span>
            </div>
          </div>

          <div class="info-group" style="border-left-color: #3b82f6;">
            <div class="info-label">Model Version</div>
            <div class="info-value" style="color: #1e40af; font-family: 'Courier New', monospace;">
              <?= esc($request->model_version) ?>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <form action="<?= base_url('kelola-face-enrollment/approve/' . $request->id) ?>" method="POST"
              onsubmit="return confirm('✅ Setujui request ini? Descriptor akan ditambahkan ke database.')"
              style="flex: 1; margin: 0;">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-success btn-lg w-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Approve Request
              </button>
            </form><button type="button" class="btn btn-danger btn-lg" style="flex: 1;"
              onclick="showRejectModal(<?= $request->id ?>, '<?= esc($request->nama) ?>')">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              Reject Request
            </button>
          </div>

        </div>

      </div>
    </div>

  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="rejectForm" method="POST">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tolak Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Anda akan menolak request dari: <strong id="rejectName"></strong></p>
          <div class="mb-3">
            <label class="form-label">Alasan Penolakan (Opsional)</label>
            <textarea class="form-control" name="rejection_reason" rows="3"
              placeholder="Contoh: Wajah tidak jelas, foto tidak sesuai, data tidak valid..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            Tolak Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function showRejectModal(id, name) {
  document.getElementById('rejectName').innerText = name;
  document.getElementById('rejectForm').action = '<?= base_url('kelola-face-enrollment/reject/') ?>' + id;

  const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
  modal.show();
}
</script>

<?= $this->endSection() ?>