<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<style>
.avatar-sm {
  width: 32px;
  height: 32px;
  font-size: 0.875rem;
}

.table-compact td {
  padding: 0.5rem 0.75rem;
  vertical-align: middle;
}

.filter-chip {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  margin: 0.25rem;
  background: #e9ecef;
  border-radius: 50px;
  font-size: 0.875rem;
}

.filter-chip .btn-close {
  font-size: 0.75rem;
  margin-left: 0.5rem;
}

.action-dropdown .dropdown-menu {
  min-width: 160px;
}

.quick-filter-btn {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 50px;
}

.bulk-action-bar {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  background: #206bc4;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 50px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  display: none;
  z-index: 1000;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateX(-50%) translateY(100px);
    opacity: 0;
  }

  to {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
  }
}

.search-loading {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  display: none;
}

.badge-role {
  font-weight: 500;
  padding: 0.35em 0.65em;
}

.status-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 0.5rem;
}

.table-hover tbody tr:hover {
  background-color: #f8f9fa;
}

.export-import-group {
  display: flex;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .export-import-group {
    flex-direction: column;
  }

  .export-import-group .btn {
    width: 100%;
  }
}

[data-tooltip] {
  position: relative;
  cursor: pointer;
}

[data-tooltip]:before {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  padding: 6px 10px;
  background-color: #1e293b;
  color: #fff;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s ease;
  pointer-events: none;
  margin-bottom: 8px;
  z-index: 1000;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

[data-tooltip]:after {
  content: '';
  position: absolute;
  bottom: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #1e293b transparent transparent transparent;
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s ease;
  margin-bottom: -2px;
  z-index: 1000;
}

[data-tooltip]:hover:before,
[data-tooltip]:hover:after {
  opacity: 1;
  visibility: visible;
  bottom: calc(100% + 5px);
}
</style>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-warning alert-dismissible" role="alert">
      <div class="d-flex">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 9v4" />
            <path
              d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
            <path d="M12 16h.01" />
          </svg>
        </div>
        <div>
          <h4 class="alert-title">Terdapat Error Saat Import!</h4>
          <div class="text-muted">
            <ul>
              <?php foreach (session()->getFlashdata('errors') as $error): ?>
              <li><?= esc($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
      <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
    <?php endif; ?>

    <!-- Search and Actions Bar -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <!-- Search Bar -->
          <div class="col-lg-6">
            <div class="d-flex gap-2">
              <div style="width: 85px;">
                <select id="limit" name="limit" class="form-select">
                  <option value="10" <?= (isset($limit) && $limit == 10) ? 'selected' : '' ?>>10</option>
                  <option value="25" <?= (isset($limit) && $limit == 25) ? 'selected' : '' ?>>25</option>
                  <option value="50" <?= (isset($limit) && $limit == 50) ? 'selected' : '' ?>>50</option>
                  <option value="100" <?= (isset($limit) && $limit == 100) ? 'selected' : '' ?>>100</option>
                </select>
              </div>

              <div class="position-relative flex-grow-1">
                <div class="input-icon">
                  <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                      stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                      <path d="M21 21l-6 -6" />
                    </svg>
                  </span>
                  <input type="text" id="keyword" name="keyword" value="<?= $filter['keyword'] ?>" class="form-control"
                    placeholder="Cari nama, username, atau Nomor Induk..." autofocus>
                </div>
                <div class="search-loading">
                  <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="col-lg-6 d-flex justify-content-lg-end align-items-center gap-2">
            <!-- Quick Filter Toggle -->
            <button class="btn <?= $isFiltered ? 'btn-cyan' : 'btn-outline-cyan' ?> quick-filter-btn" type="button"
              data-bs-toggle="collapse" data-bs-target="#quickFilters" aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                <path d="M6 4v4" />
                <path d="M6 12v8" />
                <path d="M10 16a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                <path d="M12 4v10" />
                <path d="M12 18v2" />
                <path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                <path d="M18 4v1" />
                <path d="M18 9v11" />
              </svg>
              Filter
            </button>

            <div class="export-import-group">
              <!-- Add Button -->
              <a href="<?= base_url('/tambah-data-pegawai') ?>" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                  stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M12 5l0 14" />
                  <path d="M5 12l14 0" />
                </svg>
                Tambah
              </a>

              <!-- Import/Export Dropdown -->
              <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                  </svg>
                  Excel
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 11v6" />
                        <path d="M9.5 13.5l2.5 -2.5l2.5 2.5" />
                      </svg>
                      Import Excel
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 13v6" />
                        <path d="M9.5 15.5l2.5 2.5l2.5 -2.5" />
                      </svg>
                      Export Excel
                    </a>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <a class="dropdown-item" href="<?= base_url('/data-pegawai/download-template') ?>">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" />
                        <path d="M12 4l0 12" />
                      </svg>
                      Download Template
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Filters Collapse -->
        <div class="collapse mt-3" id="quickFilters">
          <form action="" method="get" id="filterForm">
            <div class="row g-2">
              <div class="col-md-3">
                <select name="jabatan" id="jabatan" class="form-select form-select-sm">
                  <option value="">Semua Unit</option>
                  <?php foreach ($data_jabatan as $opsi) : ?>
                  <option value="<?= $opsi['jabatan'] ?>"
                    <?= ($filter['jabatan'] == $opsi['jabatan']) ? 'selected' : '' ?>>
                    <?= $opsi['jabatan'] ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <select name="role" id="role" class="form-select form-select-sm">
                  <option value="">Semua Role</option>
                  <?php foreach ($data_role as $opsi) : ?>
                  <option value="<?= $opsi['name'] ?>" <?= ($filter['role'] == $opsi['name']) ? 'selected' : '' ?>>
                    <?= $opsi['name'] ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <select name="status" id="status" class="form-select form-select-sm">
                  <option value="">Semua Status</option>
                  <option value="1" <?= ($filter['status'] == "1") ? 'selected' : '' ?>>Aktif</option>
                  <option value="0" <?= ($filter['status'] == "0") ? 'selected' : '' ?>>Belum Aktif</option>
                </select>
              </div>
              <div class="col-md-2">
                <select name="jenis-kelamin" id="jenis-kelamin" class="form-select form-select-sm">
                  <option value="">Semua Gender</option>
                  <option value="Perempuan" <?= ($filter['jenis-kelamin'] == "Perempuan") ? 'selected' : '' ?>>Perempuan
                  </option>
                  <option value="Laki-laki" <?= ($filter['jenis-kelamin'] == "Laki-laki") ? 'selected' : '' ?>>Laki-laki
                  </option>
                </select>
              </div>
              <div class="col-md-3">
                <select name="lokasi-presensi" id="lokasi-presensi" class="form-select form-select-sm">
                  <option value="">Semua Lokasi</option>
                  <?php foreach ($data_lokasi as $opsi) : ?>
                  <option value="<?= $opsi['nama_lokasi'] ?>"
                    <?= ($filter['lokasi-presensi'] == $opsi['nama_lokasi']) ? 'selected' : '' ?>>
                    <?= $opsi['nama_lokasi'] ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-sm btn-primary">Terapkan Filter</button>
              <a href="<?= base_url('data-pegawai') ?>" class="btn btn-sm btn-link">Reset</a>
            </div>
          </form>
        </div>

        <!-- Active Filters Chips -->
        <?php if ($isFiltered): ?>
        <div class="mt-3">
          <div class="d-flex flex-wrap align-items-center">
            <span class="text-muted me-2">Filter aktif:</span>
            <?php if (!empty($filter['jabatan'])): ?>
            <span class="filter-chip">
              Unit: <?= $filter['jabatan'] ?>
              <a href="<?= base_url('data-pegawai?keyword=' . $filter['keyword'] . '&role=' . $filter['role'] . '&status=' . $filter['status'] . '&jenis-kelamin=' . $filter['jenis-kelamin'] . '&lokasi-presensi=' . $filter['lokasi-presensi']) ?>"
                class="btn-close"></a>
            </span>
            <?php endif; ?>
            <?php if (!empty($filter['role'])): ?>
            <span class="filter-chip">
              Role: <?= $filter['role'] ?>
              <a href="<?= base_url('data-pegawai?keyword=' . $filter['keyword'] . '&jabatan=' . $filter['jabatan'] . '&status=' . $filter['status'] . '&jenis-kelamin=' . $filter['jenis-kelamin'] . '&lokasi-presensi=' . $filter['lokasi-presensi']) ?>"
                class="btn-close"></a>
            </span>
            <?php endif; ?>
            <?php if ($filter['status'] !== ''): ?>
            <span class="filter-chip">
              Status: <?= $filter['status'] == '1' ? 'Aktif' : 'Belum Aktif' ?>
              <a href="<?= base_url('data-pegawai?keyword=' . $filter['keyword'] . '&jabatan=' . $filter['jabatan'] . '&role=' . $filter['role'] . '&jenis-kelamin=' . $filter['jenis-kelamin'] . '&lokasi-presensi=' . $filter['lokasi-presensi']) ?>"
                class="btn-close"></a>
            </span>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Data Table -->
    <div class="card" id="data-pegawai">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-compact table-hover card-table table-vcenter">
            <thead>
              <tr>
                <th style="width: 40px">
                  <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th style="width: 50px">No</th>
                <th>Pegawai</th>
                <th>NIP</th>
                <th>Unit</th>
                <th>Role</th>
                <th>Status</th>
                <th style="width: 100px" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data_pegawai)) : ?>
              <?php $nomor = 1 + ($perPage * ($currentPage - 1)); ?>
              <?php foreach ($data_pegawai as $pegawai) : ?>
              <tr>
                <td>
                  <input type="checkbox" class="form-check-input row-checkbox" value="<?= $pegawai->id ?>">
                </td>
                <td><?= $nomor++ ?></td>
                <td>
                  <div class="d-flex align-items-center">

                    <?php 
      $pathFoto = 'assets/img/user_profile/' . $pegawai->foto;
      
      if (!empty($pegawai->foto) && $pegawai->foto != 'default.jpg') : 
    ?>
                    <span class="avatar avatar-sm me-2"
                      style="background-image: url('<?= base_url($pathFoto) ?>')"></span>

                    <?php else : ?>

                    <span class="avatar avatar-sm me-2 bg-blue-lt">
                      <?= strtoupper(substr($pegawai->nama, 0, 2)) ?>
                    </span>

                    <?php endif; ?>

                    <div>
                      <a href="<?= base_url('/data-pegawai/' . $pegawai->username) ?>" class="text-reset fw-bold">
                        <?= $pegawai->nama ?>
                      </a>
                      <div class="text-muted small"><?= $pegawai->username ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="text-muted"><?= $pegawai->nomor_induk ?></span>
                </td>
                <td>
                  <span class="text-muted"><?= $pegawai->jabatan ?></span>
                </td>
                <td>
                  <?= role_badge_html($pegawai->role ?? 'user') ?>
                </td>
                <td>
                  <?php if ($pegawai->active == 0) : ?>
                  <span class="badge bg-danger-lt text-danger">
                    <span class="status-indicator bg-danger"></span>
                    Belum Aktif
                  </span>
                  <?php else : ?>
                  <span class="badge bg-success-lt text-success">
                    <span class="status-indicator bg-success"></span>
                    Aktif
                  </span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <div class="d-flex align-items-center justify-content-end gap-2">

                    <?php if (isset($pegawai->jumlah_wajah) && $pegawai->jumlah_wajah > 0) : ?>
                    <a href="<?= base_url('data-pegawai/manage-face-descriptors/' . $pegawai->id) ?>"
                      class="badge bg-azure-lt" title="Face Recognition" style="text-decoration: none;">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-face-id" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round"
                        style="width: 14px; height: 14px; margin-right: 3px;">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 8v-2a2 2 0 0 1 2 -2h2"></path>
                        <path d="M4 16v2a2 2 0 0 0 2 2h2"></path>
                        <path d="M16 4h2a2 2 0 0 1 2 2v2"></path>
                        <path d="M16 20h2a2 2 0 0 0 2 -2v-2"></path>
                        <path d="M9 10l.01 0"></path>
                        <path d="M15 10l.01 0"></path>
                        <path d="M9.5 15a3.5 3.5 0 0 0 5 0"></path>
                      </svg>
                      <?= $pegawai->jumlah_wajah ?>
                    </a>
                    <?php else : ?>
                    <a href="<?= base_url('data-pegawai/manage-face-descriptors/' . $pegawai->id) ?>"
                      class="badge bg-secondary-lt" title="Belum ada data wajah"
                      style="text-decoration: none; opacity: 0.5;">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-face-id" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 8v-2a2 2 0 0 1 2 -2h2"></path>
                        <path d="M4 16v2a2 2 0 0 0 2 2h2"></path>
                        <path d="M16 4h2a2 2 0 0 1 2 2v2"></path>
                        <path d="M16 20h2a2 2 0 0 0 2 -2v-2"></path>
                        <path d="M9 10l.01 0"></path>
                        <path d="M15 10l.01 0"></path>
                        <path d="M9.5 15a3.5 3.5 0 0 0 5 0"></path>
                      </svg>
                      +
                    </a>
                    <?php endif; ?>

                    <div class="action-dropdown">
                      <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                          stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                          stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                          <path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                          <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        </svg>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                          <a class="dropdown-item" href="<?= base_url('data-pegawai/' . $pegawai->id) ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16"
                              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                              stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                              <path
                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                            Detail
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="<?= base_url('data-pegawai/edit/' . $pegawai->id) ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16"
                              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                              stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                              <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                              <path d="M16 5l3 3" />
                            </svg>
                            Edit
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item"
                            href="<?= base_url('data-pegawai/manage-face-descriptors/' . $pegawai->id) ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16"
                              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                              stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                              <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                              <path d="M16 11h6m-3 -3v6" />
                            </svg>
                            Face Recognition
                          </a>
                        </li>
                        <?php if ($pegawai->active == 0) : ?>
                        <li>
                          <hr class="dropdown-divider">
                        </li>
                        <li>
                          <a class="dropdown-item text-info"
                            href="<?= base_url('resend-activate-account?login=' . $pegawai->email) ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16"
                              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                              stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path
                                d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                              <path d="M3 7l9 6l9 -6" />
                            </svg>
                            Kirim Email Aktivasi
                          </a>
                        </li>
                        <?php endif; ?>
                        <li>
                          <hr class="dropdown-divider">
                        </li>
                        <li>
                          <a class="dropdown-item text-danger btn-hapus" href="#" data-id="<?= $pegawai->id ?>"
                            data-name="<?= $pegawai->nama ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16"
                              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                              stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path d="M4 7l16 0" />
                              <path d="M10 11l0 6" />
                              <path d="M14 11l0 6" />
                              <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                              <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                            Hapus
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php else : ?>
              <tr>
                <td colspan="8" class="text-center py-5">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-2" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                    <path d="M12 12l0 .01" />
                    <path d="M3 13a20 20 0 0 0 18 0" />
                  </svg>
                  <div class="text-muted">Tidak ada data pengguna</div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <?php if (!empty($data_pegawai)) : ?>
      <div class="card-footer d-flex align-items-center justify-content-between">
        <p class="m-0 text-muted">
          Menampilkan <strong><?= ($perPage * ($currentPage - 1)) + 1 ?></strong>
          sampai <strong><?= min($perPage * $currentPage, $total) ?></strong>
          dari <strong><?= $total ?></strong> data
        </p>
        <?= $pager; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Bulk Action Bar -->
<div class="bulk-action-bar" id="bulkActionBar">
  <div class="d-flex align-items-center gap-3">
    <span id="selectedCount">0</span> data terpilih
    <button class="btn btn-sm btn-white" onclick="bulkDelete()">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M4 7l16 0" />
        <path d="M10 11l0 6" />
        <path d="M14 11l0 6" />
        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
      </svg>
      Hapus Terpilih
    </button>
    <button class="btn btn-sm btn-ghost-light" onclick="deselectAll()">Batal</button>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal modal-blur fade" id="modal-danger" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <div class="modal-body text-center py-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
          viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
          stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <path
            d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z" />
          <path d="M12 9v4" />
          <path d="M12 17h.01" />
        </svg>
        <h3>Konfirmasi Hapus</h3>
        <div class="text-muted">
          Apakah Anda yakin ingin menghapus data <strong><span id="modal-name" class="text-danger">ini</span></strong>?
          <br>Data yang sudah dihapus tidak dapat dikembalikan.
        </div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <div class="row">
            <div class="col">
              <a href="#" class="btn w-100" data-bs-dismiss="modal">Batal</a>
            </div>
            <div class="col">
              <form action="" method="post" class="d-inline" id="form-hapus">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-danger w-100">Hapus</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Export Modal -->
<div class="modal modal-blur fade" id="exportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export Data Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('/data-pegawai/excel') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Unit</label>
              <select name="jabatan" class="form-select">
                <option value="">Semua Unit</option>
                <?php foreach ($data_jabatan as $opsi) : ?>
                <option value="<?= $opsi['jabatan'] ?>"><?= $opsi['jabatan'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <select name="role" class="form-select">
                <option value="">Semua Role</option>
                <?php foreach ($data_role as $opsi) : ?>
                <option value="<?= $opsi['name'] ?>"><?= $opsi['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="1">Sudah Aktivasi</option>
                <option value="0">Belum Aktivasi</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Jenis Kelamin</label>
              <select name="jenis-kelamin" class="form-select">
                <option value="">Semua</option>
                <option value="Perempuan">Perempuan</option>
                <option value="Laki-laki">Laki-laki</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Lokasi Presensi</label>
              <select name="lokasi-presensi" class="form-select">
                <option value="">Semua Lokasi</option>
                <?php foreach ($data_lokasi as $opsi) : ?>
                <option value="<?= $opsi['nama_lokasi'] ?>"><?= $opsi['nama_lokasi'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
              <path d="M7 11l5 5l5 -5" />
              <path d="M12 4l0 12" />
            </svg>
            Export Excel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Import Modal -->
<div class="modal modal-blur fade" id="importModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Data Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('/data-pegawai/import-excel') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">File Excel</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
            <small class="form-hint">Format yang didukung: .xlsx atau .xls (Maksimal 2MB)</small>
          </div>

          <div class="alert alert-info">
            <h4 class="alert-title">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 9v4" />
                <path
                  d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                <path d="M12 16h.01" />
              </svg>
              Panduan Import
            </h4>
            <ul class="mb-0 small">
              <li>Download template Excel terlebih dahulu</li>
              <li>Isi data sesuai format yang tersedia</li>
              <li><strong>JENIS KELAMIN:</strong> "Laki-laki" atau "Perempuan"</li>
              <li><strong>UNIT:</strong> Nama unit yang sudah terdaftar</li>
              <li><strong>LOKASI PRESENSI:</strong> Nama lokasi yang sudah terdaftar</li>
              <li><strong>ROLE AKUN:</strong> "pegawai", "head", atau "admin"</li>
              <li><strong>AKTIVASI SEKARANG:</strong> "YA" atau "TIDAK"</li>
              <li><strong>PASSWORD:</strong> Minimal 8 karakter</li>
              <li><strong>NOMOR INDUK:</strong> NIS/NIP (harus unik)</li>
            </ul>
          </div>
        </div>
        <div class="modal-footer">
          <a href="<?= base_url('/data-pegawai/download-template') ?>" class="btn btn-link me-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
              <path d="M7 11l5 5l5 -5" />
              <path d="M12 4l0 12" />
            </svg>
            Download Template
          </a>
          <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
              stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M12 11v6" />
              <path d="M9.5 13.5l2.5 -2.5l2.5 2.5" />
            </svg>
            Import Data
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  let searchTimeout;

  // Auto-search dengan debounce
  $('#keyword').on('keyup', function() {
    clearTimeout(searchTimeout);
    $('.search-loading').show();
    searchTimeout = setTimeout(function() {
      performSearch();
    }, 400);
  });

  // Tambahkan trigger saat limit berubah
  $('#limit').on('change', function() {
    $('.search-loading').show();
    performSearch();
  });

  // Filter change triggers search
  $('#jabatan, #role, #status, #jenis-kelamin, #lokasi-presensi').on('change', function() {
    performSearch();
  });

  function performSearch() {
    // Ambil semua value filter
    var keyword = $('#keyword').val();
    var jabatan = $('#jabatan').val();
    var role = $('#role').val();
    var status = $('#status').val();
    var lokasi = $('#lokasi-presensi').val();
    var gender = $('#jenis-kelamin').val();
    var limit = $('#limit').val(); // Ambil value limit

    $.ajax({
      url: 'cari-pegawai', // Pastikan route ini mengarah ke method pencarianPegawai
      type: 'GET',
      data: {
        keyword: keyword,
        jabatan: jabatan,
        role: role,
        status: status,
        'lokasi-presensi': lokasi,
        'jenis-kelamin': gender,
        limit: limit // Kirim limit ke server
      },
      success: function(data) {
        $('#data-pegawai').html(data);
        $('.search-loading').hide();

        // Update URL browser tanpa reload agar jika di-refresh limit tetap tersimpan
        var newUrl = window.location.pathname + '?keyword=' + keyword +
          '&jabatan=' + jabatan +
          '&role=' + role +
          '&status=' + status +
          '&lokasi-presensi=' + lokasi +
          '&jenis-kelamin=' + gender +
          '&limit=' + limit;
        window.history.pushState({
          path: newUrl
        }, '', newUrl);
      },
      error: function() {
        $('.search-loading').hide();
      }
    });
  }

  // Delete button handler
  $('body').on('click', '.btn-hapus', function(e) {
    e.preventDefault();
    var nama = $(this).data('name');
    var id = $(this).data('id');
    $('#modal-name').html(nama);
    $('#modal-danger').modal('show');
    $('#form-hapus').attr('action', '/data-pegawai/' + id);
  });

  // Checkbox selection
  $('#selectAll').on('change', function() {
    $('.row-checkbox').prop('checked', $(this).prop('checked'));
    updateBulkActionBar();
  });

  $('body').on('change', '.row-checkbox', function() {
    updateBulkActionBar();
    const totalCheckboxes = $('.row-checkbox').length;
    const checkedCheckboxes = $('.row-checkbox:checked').length;
    $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
  });

  function updateBulkActionBar() {
    selectedIds = [];
    $('.row-checkbox:checked').each(function() {
      selectedIds.push($(this).val());
    });

    if (selectedIds.length > 0) {
      $('#bulkActionBar').fadeIn();
      $('#selectedCount').text(selectedIds.length);
    } else {
      $('#bulkActionBar').fadeOut();
    }
  }
});

function bulkDelete() {
  if (selectedIds.length === 0) return;

  if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' data terpilih?')) {
    $.ajax({
      url: '/data-pegawai/bulk-delete',
      type: 'POST',
      data: {
        ids: selectedIds
      },
      success: function(response) {
        if (response.success) {
          alert(response.message);
          location.reload();
        } else {
          alert('Gagal menghapus data.');
        }
      },
      error: function() {
        alert('Terjadi kesalahan pada server.');
      }
    });
  }
}

function deselectAll() {
  $('.row-checkbox, #selectAll').prop('checked', false);
  $('#bulkActionBar').fadeOut();
}
</script>

<?= $this->endSection() ?>