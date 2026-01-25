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
            Silakan tambahkan tanggal libur nasional (tanggal merah) atau cuti bersama di halaman ini.
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Daftar Hari Libur Nasional / Cuti Bersama</h3>
        <a href="<?= base_url('hari-libur/tambah') ?>" class="btn btn-primary">
          + Tambah Hari Libur
        </a>
      </div>
      <div class="card-body">
        <?php if (session()->getFlashdata('berhasil')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('berhasil') ?></div>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table table-vcenter card-table table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
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
                <td colspan="4" class="text-center">Belum ada data hari libur.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>