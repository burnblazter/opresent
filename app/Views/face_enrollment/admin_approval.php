<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>

<style>
/* --- BASE LAYOUT --- */
.page-body {
  background: #f8fafc;
  min-height: 100vh;
  padding: 20px 0;
}

/* --- CARD STYLING --- */
.main-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  background: #f8fafc;
  border-bottom: 2px solid #e5e7eb;
  padding: 20px 24px;
}

.card-title {
  color: #1e3a8a;
  font-weight: 600;
  font-size: 1.25rem;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

/* --- REQUEST CARD --- */
.request-card {
  border: 2px solid #e5e7eb;
  padding: 20px;
  margin-bottom: 20px;
  border-radius: 12px;
  background: white;
  transition: all 0.3s;
  display: flex;
  gap: 20px;
}

.request-card:hover {
  background: #f9fafb;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.request-image {
  flex-shrink: 0;
  width: 120px;
  height: 120px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #e5e7eb;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
}

.request-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.request-image-placeholder {
  color: #9ca3af;
  text-align: center;
  padding: 10px;
  font-size: 0.85rem;
}

.request-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.request-header h4 {
  margin: 0 0 8px 0;
  color: #1e3a8a;
  font-size: 1.15rem;
  font-weight: 600;
}

.request-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 12px;
  font-size: 0.9rem;
  color: #6b7280;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.request-reason {
  background: #f3f4f6;
  padding: 10px 14px;
  border-radius: 8px;
  border-left: 3px solid #dda518;
  margin-bottom: 12px;
}

.request-reason strong {
  color: #374151;
  display: block;
  margin-bottom: 4px;
  font-size: 0.85rem;
}

.request-reason p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
}

.request-actions {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
  justify-content: center;
}

/* --- BADGES --- */
.badge {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.badge-warning {
  background: #fef3c7;
  color: #92400e;
}

.badge-info {
  background: #dbeafe;
  color: #1e40af;
}

/* --- BUTTONS --- */
.btn {
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-decoration: none;
}

.btn-primary {
  background: #1e3a8a;
  color: white;
}

.btn-primary:hover {
  background: #1e40af;
  transform: translateY(-2px);
}

.btn-success {
  background: #10b981;
  color: white;
}

.btn-success:hover {
  background: #059669;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 0.875rem;
}

/* --- ALERTS --- */
.alert {
  padding: 16px 20px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 12px;
}

.alert-info {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.alert-warning {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fcd34d;
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #6ee7b7;
}

.alert-danger {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

/* --- STATS --- */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  transition: all 0.3s;
}

.stat-card:hover {
  border-color: #1e3a8a;
  transform: translateY(-2px);
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1e3a8a;
  margin-bottom: 8px;
}

.stat-label {
  color: #6b7280;
  font-size: 0.9rem;
  font-weight: 500;
}

/* --- UTILITIES --- */
.text-muted {
  color: #6b7280;
}

.mb-3 {
  margin-bottom: 1rem;
}

.w-100 {
  width: 100%;
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
  .request-card {
    flex-direction: column;
  }

  .request-image {
    width: 100%;
    height: 200px;
  }

  .request-actions {
    flex-direction: row;
  }
}
</style>

<div class="page-body">
  <div class="container-xl">

    <!-- Header -->
    <div class="row mb-3">
      <div class="col">
        <a href="<?= base_url('admin') ?>" class="btn btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Kembali ke Dashboard
        </a>
      </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
      <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="15" y1="9" x2="9" y2="15"></line>
        <line x1="9" y1="9" x2="15" y2="15"></line>
      </svg>
      <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?= count($requests) ?></div>
        <div class="stat-label">Request Pending</div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="main-card">
      <div class="card-header">
        <h3 class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          Request Pendaftaran Wajah
        </h3>
      </div>

      <div class="card-body" style="padding: 24px;">

        <?php if (empty($requests)): ?>
        <div class="alert alert-info">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
          Tidak ada request pending saat ini.
        </div>
        <?php else: ?>

        <?php foreach ($requests as $req): ?>
        <div class="request-card">

          <!-- Image Preview -->
          <div class="request-image">
            <?php if ($req->image_path): ?>
            <img src="<?= base_url('kelola-face-enrollment/image/' . $req->id) ?>" alt="Face Preview" loading="lazy">
            <?php else: ?>
            <div class="request-image-placeholder">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
              </svg>
              <div>No Image</div>
            </div>
            <?php endif; ?>
          </div>

          <!-- Request Info -->
          <div class="request-info">
            <div>
              <div class="request-header">
                <h4><?= esc($req->nama) ?></h4>
                <div class="request-meta">
                  <span class="meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    NIP: <?= esc($req->nomor_induk) ?>
                  </span>
                  <span class="meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <?= date('d M Y, H:i', strtotime($req->created_at)) ?>
                  </span>
                </div>
              </div>

              <div style="margin-bottom: 12px;">
                <span class="badge badge-info">📷 <?= esc($req->label) ?></span>
                <span class="badge badge-warning">⏳ Menunggu Verifikasi</span>
              </div>

              <?php if ($req->reason): ?>
              <div class="request-reason">
                <strong>ALASAN REQUEST:</strong>
                <p><?= esc($req->reason) ?></p>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Actions -->
          <div class="request-actions">
            <a href="<?= base_url('kelola-face-enrollment/detail/' . $req->id) ?>" class="btn btn-primary btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              Detail
            </a>

            <form action="<?= base_url('kelola-face-enrollment/approve/' . $req->id) ?>" method="POST"
              onsubmit="return confirm('✅ Setujui request ini?')" style="margin: 0;">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-success btn-sm w-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Approve
              </button>
            </form>

            <button type="button" class="btn btn-danger btn-sm w-100"
              onclick="showRejectModal(<?= $req->id ?>, '<?= esc($req->nama) ?>')">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              Reject
            </button>
          </div>

        </div>
        <?php endforeach; ?>

        <?php endif; ?>
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
              placeholder="Contoh: Wajah tidak jelas, foto tidak sesuai..."></textarea>
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