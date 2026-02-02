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
                <span class="avatar avatar-sm me-2" style="background-image: url('<?= base_url($pathFoto) ?>')"></span>
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
              <span class="badge badge-role 
                <?php
                if ($pegawai->role === 'admin') {
                    echo 'bg-green-lt text-green';
                } else if ($pegawai->role === 'head') {
                    echo 'bg-purple-lt text-purple';
                } else {
                    echo 'bg-blue-lt text-blue';
                }
                ?>">
                <?= ucfirst($pegawai->role) ?>
              </span>
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
            <td class="text-center">
              <div class="action-dropdown">
                <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                      </svg>
                      Detail
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="<?= base_url('data-pegawai/edit/' . $pegawai->id) ?>">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
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
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
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
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
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
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
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