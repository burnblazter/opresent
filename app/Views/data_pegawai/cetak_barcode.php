<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Barcode - PresenSi</title>
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
  <style>
  :root {
    --primary: #1e3a8a;
    --accent: #dda518;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #e8edf4;
    color: #1a1a2e;
  }

  /* TOOLBAR */
  .toolbar {
    background: var(--primary);
    color: #fff;
    padding: 12px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    position: sticky;
    top: 0;
    z-index: 999;
  }

  .toolbar h1 {
    font-size: 1.05rem;
    font-weight: 700;
  }

  .toolbar .subtitle {
    font-size: 0.78rem;
    opacity: 0.75;
    margin-top: 2px;
  }

  .btn-print {
    background: var(--accent);
    color: #fff;
    border: none;
    padding: 10px 22px;
    border-radius: 6px;
    font-size: 0.92rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .btn-print:hover {
    background: #c4921a;
  }

  .btn-back {
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.45);
    padding: 9px 16px;
    border-radius: 6px;
    font-size: 0.88rem;
    cursor: pointer;
    text-decoration: none;
  }

  .perpage-control {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
  }

  .perpage-control label {
    opacity: 0.85;
    white-space: nowrap;
  }

  .perpage-control input {
    width: 56px;
    padding: 5px 8px;
    border-radius: 5px;
    border: none;
    font-size: 0.88rem;
    text-align: center;
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
  }

  .perpage-control input:focus {
    outline: 2px solid var(--accent);
    background: rgba(255, 255, 255, 0.25);
  }

  .btn-apply {
    background: rgba(255, 255, 255, 0.18);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.35);
    padding: 5px 12px;
    border-radius: 5px;
    font-size: 0.82rem;
    cursor: pointer;
  }

  .btn-apply:hover {
    background: rgba(255, 255, 255, 0.3);
  }

  /* WRAPPER */
  .page-wrapper {
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 24px;
  }

  /* A4 */
  .a4-page {
    width: 210mm;
    background: #fff;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.14);
    padding: 10mm 8mm;
    display: flex;
    flex-direction: column;
  }

  /* PAGE HEADER */
  .page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 3.5px solid var(--primary);
    padding-bottom: 7px;
    margin-bottom: 10px;
    flex-shrink: 0;
  }

  .page-header .app-name {
    font-size: 1.35rem;
    font-weight: 900;
    color: var(--primary);
    letter-spacing: 1px;
  }

  .page-header .app-name span {
    color: var(--accent);
  }

  .page-header .header-meta {
    font-size: 0.65rem;
    color: #555;
    text-align: right;
    line-height: 1.6;
  }

  /* GRID */
  .barcode-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6mm;
    flex: 1;
    align-content: start;
  }

  /* ITEM */
  .barcode-item {
    border: 3px solid var(--primary);
    border-radius: 6px;
    padding: 10px 10px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    position: relative;
    outline: 1.5px dashed #b0bec5;
    outline-offset: 3px;
    break-inside: avoid;
  }

  .barcode-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary) 60%, var(--accent) 100%);
    border-radius: 3px 3px 0 0;
  }

  .barcode-item svg {
    width: 100%;
    max-width: 260px;
    height: auto;
    margin-top: 4px;
    display: block;
  }

  .barcode-item .emp-name {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--primary);
    text-align: center;
    margin-top: 6px;
    line-height: 1.3;
    word-break: break-word;
  }

  .barcode-item .emp-unit {
    font-size: 0.72rem;
    color: #fff;
    font-weight: 700;
    text-align: center;
    margin-top: 5px;
    background: var(--accent);
    padding: 2px 10px;
    border-radius: 20px;
  }

  .barcode-item .base-url {
    font-size: 0.58rem;
    color: #94a3b8;
    text-align: center;
    margin-top: 6px;
    word-break: break-all;
  }

  /* PAGE FOOTER */
  .page-footer {
    margin-top: 8mm;
    border-top: 1.5px solid #e2e8f0;
    padding-top: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
  }

  .page-footer .footer-brand {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--primary);
  }

  .page-footer .footer-brand span {
    color: var(--accent);
  }

  .page-footer .page-num {
    font-size: 0.65rem;
    color: #94a3b8;
  }

  /* EMPTY */
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
    width: 210mm;
    background: #fff;
    border-radius: 8px;
  }

  /* PRINT */
  @media print {
    @page {
      size: A4;
      margin: 0;
    }

    body {
      background: #fff;
    }

    .toolbar {
      display: none !important;
    }

    .page-wrapper {
      padding: 0;
      gap: 0;
    }

    .a4-page {
      box-shadow: none;
      width: 210mm;
      height: 297mm;
      max-height: 297mm;
      overflow: hidden;
      padding: 10mm 8mm;
      page-break-after: always;
      page-break-inside: avoid;
    }

    .barcode-item {
      break-inside: avoid;
      outline: 1.5px dashed #90a4ae;
      outline-offset: 3px;
    }

    .barcode-item:last-child:nth-child(odd) {
      grid-column: 1;
    }
  }
  </style>
</head>

<body>

  <?php
  $jabatanLabel = !empty($filter['jabatan']) ? $filter['jabatan'] : 'Semua Unit';
  $total        = count($data_pegawai ?? []);
  $perPageVal   = $per_page; // dari controller

  $pages        = (!empty($data_pegawai)) ? array_chunk($data_pegawai, $perPageVal) : [];
  $totalPage    = count($pages);
?>

  <!-- TOOLBAR -->
  <div class="toolbar">
    <div>
      <h1>PresenSi &mdash; Cetak Barcode</h1>
      <div class="subtitle">
        Unit: <strong><?= esc($jabatanLabel) ?></strong>
        &bull; Total: <strong><?= $total ?></strong> pegawai
      </div>
    </div>

    <div class="perpage-control">
      <label for="pp_input">Per halaman:</label>
      <input type="number" id="pp_input" min="1" max="30" value="<?= $perPageVal ?>">
      <button class="btn-apply" onclick="applyPerPage()">Terapkan</button>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a href="<?= base_url('/data-pegawai') ?>" class="btn-back">← Kembali</a>
      <button class="btn-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 9V2h12v7" />
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
          <rect x="6" y="14" width="12" height="8" />
        </svg>
        Cetak / Print
      </button>
    </div>
  </div>

  <!-- HIDDEN RESUBMIT FORM — inilah kuncinya, filter dikirim ulang via POST -->
  <form id="resubmitForm" action="<?= base_url('/data-pegawai/barcode') ?>" method="POST" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="jabatan" value="<?= esc($filter['jabatan'] ?? '') ?>">
    <input type="hidden" name="status" value="<?= esc($filter['status'] ?? '') ?>">
    <input type="hidden" name="jenis-kelamin" value="<?= esc($filter['jenis-kelamin'] ?? '') ?>">
    <input type="hidden" name="lokasi-presensi" value="<?= esc($filter['lokasi-presensi'] ?? '') ?>">
    <input type="hidden" name="keyword" value="<?= esc($filter['keyword'] ?? '') ?>">
    <input type="hidden" name="per_page" id="resubmit_pp" value="<?= $perPageVal ?>">
  </form>

  <div class="page-wrapper">

    <?php if (empty($data_pegawai)): ?>
    <div class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1"
        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2" />
        <path d="M3 9h18M9 21V9" />
      </svg>
      <p style="margin-top:16px; font-size:1rem; font-weight:600;">
        Tidak ada data pegawai dengan role <strong>pegawai</strong> pada filter yang dipilih.
      </p>
      <a href="<?= base_url('/data-pegawai') ?>"
        style="color:var(--primary); font-weight:700; margin-top:12px; display:inline-block;">
        ← Kembali &amp; ubah filter
      </a>
    </div>

    <?php else:
  foreach ($pages as $pageIdx => $items):
    if (empty($items)) continue; // skip halaman kosong
    $pageNum = $pageIdx + 1;
?>

    <div class="a4-page">
      <div class="page-header">
        <div class="app-name">Presen<span>Si</span></div>
        <div class="header-meta">
          Unit: <strong><?= esc($jabatanLabel) ?></strong><br>
          Dicetak: <?= date('d/m/Y H:i') ?><br>
          <span style="color:var(--primary); font-size:0.6rem;"><?= esc($base_url) ?></span>
        </div>
      </div>

      <div class="barcode-grid">
        <?php foreach ($items as $idx => $pegawai): ?>
        <div class="barcode-item">
          <svg data-value="<?= esc($pegawai->nomor_induk) ?>"></svg>
          <div class="emp-name"><?= esc($pegawai->nama) ?></div>
          <div class="emp-unit"><?= esc($pegawai->jabatan) ?></div>
          <div class="base-url"><?= esc($base_url) ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="page-footer">
        <div class="footer-brand">Presen<span>Si</span></div>
        <div class="page-num">Halaman <?= $pageNum ?> / <?= $totalPage ?></div>
      </div>
    </div>

    <?php endforeach; ?>
    <?php endif; ?>

  </div><!-- /page-wrapper -->

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.barcode-item svg[data-value]').forEach(function(svg) {
      var val = svg.getAttribute('data-value');
      if (!val) return;
      try {
        JsBarcode(svg, val, {
          format: 'CODE128',
          lineColor: '#000000',
          width: 2.4,
          height: 72,
          displayValue: true,
          fontSize: 13,
          font: 'monospace',
          fontOptions: 'bold',
          textMargin: 4,
          margin: 6,
        });
      } catch (e) {
        console.warn('Barcode error:', val, e);
      }
    });
  });

  function applyPerPage() {
    var val = parseInt(document.getElementById('pp_input').value);
    if (isNaN(val) || val < 1) {
      alert('Minimal 1');
      return;
    }
    if (val > 30) {
      alert('Maksimal 30');
      return;
    }
    document.getElementById('resubmit_pp').value = val;
    document.getElementById('resubmitForm').submit();
  }

  document.getElementById('pp_input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') applyPerPage();
  });
  </script>
</body>

</html>