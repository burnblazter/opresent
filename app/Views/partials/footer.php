<?php
// \app\Views\partials\footer.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<footer class="footer footer-transparent d-print-none mt-auto footer-fade"
  style="border-top: 0; background: transparent !important;">
  <div class="container-xl">
    <div class="row text-center align-items-center flex-row-reverse">

      <div class="col-lg-auto ms-lg-auto">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item text-secondary fst-italic">
            "Si Pintar Urusan Presensi"
          </li>
        </ul>
      </div>

      <div class="col-12 col-lg-auto mt-3 mt-lg-0" id="app-credit"></div>

    </div>
  </div>
</footer>

<script src="<?= base_url('assets/js/diamond.js') ?>"></script>

<style>
.footer-fade {
  opacity: 0.3;
  transition: opacity 0.3s ease-in-out;
}

.footer-fade:hover {
  opacity: 1;
}
</style>