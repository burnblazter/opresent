<?= $this->extend('templates/index') ?>

<?= $this->section('pageBody') ?>
<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <form action="<?= base_url('kelola-ketidakhadiran/store') ?>" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $data_ketidakhadiran->id ?>">
      <div class="row row-deck row-cards align-items-stretch">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <tr>
                    <th>Nama</th>
                    <td><?= $data_ketidakhadiran->nama ?></td>
                  </tr>
                  <tr>
                    <th>Tipe Ketidakhadiran</th>
                    <td><?= $data_ketidakhadiran->tipe_ketidakhadiran ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Mulai</th>
                    <td><?= date('d F Y', strtotime($data_ketidakhadiran->tanggal_mulai)) ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Berakhir</th>
                    <td><?= date('d F Y', strtotime($data_ketidakhadiran->tanggal_berakhir)) ?></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Catatan Admin (Opsional, Internal)</label>
                <textarea class="form-control" name="catatan_admin" rows="4"
                  placeholder="Catatan internal untuk admin..."><?= old('catatan_admin', $data_ketidakhadiran->catatan_admin ?? '') ?></textarea>
                <small class="form-hint">Catatan ini hanya untuk keperluan internal admin dan tidak ditampilkan ke
                  pegawai.</small>
              </div>
            </div>
            <div class="card-footer text-end">
              <div class="d-flex">
                <a href="<?= base_url('/kelola-ketidakhadiran') ?>" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary ms-auto">Update</button>
              </div>
            </div>
          </div>

          <!-- Card untuk update file -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ganti File Surat Keterangan</h3>
            </div>
            <form action="<?= base_url('kelola-ketidakhadiran/update-file') ?>" method="post"
              enctype="multipart/form-data">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $data_ketidakhadiran->id ?>">
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">File Saat Ini</label>
                  <div>
                    <a href="<?= base_url('assets/file/surat_keterangan_ketidakhadiran/' . $data_ketidakhadiran->file) ?>"
                      target="_blank" class="btn btn-sm btn-outline-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 17h6" />
                        <path d="M9 13h6" />
                      </svg>
                      Lihat File
                    </a>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Upload File Baru (PDF)</label>
                  <input type="file" class="form-control <?= validation_show_error('file') ? 'is-invalid' : '' ?>"
                    name="file" accept=".pdf" />
                  <?php if (validation_show_error('file')) : ?>
                  <div class="invalid-feedback">
                    <?= validation_show_error('file') ?>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">Update File</button>
              </div>
            </form>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="mb-3">
              <label for="status_pengajuan" class="form-label">Status Pengajuan</label>
              <select name="status_pengajuan"
                class="form-select <?= validation_show_error('status_pengajuan') ? 'is-invalid' : '' ?>">
                <option value="">---Pilih Status Pengajuan---</option>
                <option value="PENDING"
                  <?= old('status_pengajuan', $data_ketidakhadiran->status_pengajuan) === 'PENDING' ? 'selected' : '' ?>>
                  PENDING</option>
                <option value="APPROVED"
                  <?= old('status_pengajuan', $data_ketidakhadiran->status_pengajuan) === 'APPROVED' ? 'selected' : '' ?>>
                  APPROVED</option>
                <option value="REJECTED"
                  <?= old('status_pengajuan', $data_ketidakhadiran->status_pengajuan) === 'REJECTED' ? 'selected' : '' ?>>
                  REJECTED</option>
              </select>
              <?php if (validation_show_error('status_pengajuan')) : ?>
              <div class="invalid-feedback">
                <?= validation_show_error('status_pengajuan') ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="card-footer text-end">
            <div class="d-flex">
              <a href="<?= base_url('/kelola-ketidakhadiran') ?>" class="btn btn-link">Cancel</a>
              <button type="submit" class="btn btn-primary ms-auto">Update Status Pengajuan</button>
            </div>
          </div>
        </div>
      </div>
  </div>
  </form>
</div>
</div>
<?= $this->endSection() ?>