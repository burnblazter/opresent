<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?= $title ?> | PresenSi</title>
    <link rel="preload" as="image" href="<?= base_url('assets/img/company/logo.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/company/logo.png') ?>">
    <link href="<?= base_url('assets/css/tabler.min.css?1684106062') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/tabler-flags.min.css?1684106062') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/tabler-payments.min.css?1684106062') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/tabler-vendors.min.css?1684106062') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/custom.css?1684106062') ?>" rel="stylesheet"/>

    <link href="<?= base_url('assets/css/leaflet.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />

    <style>
        :root {
            --tblr-font-sans-serif: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
            transition: background-color 0.5s ease;
        }
        #map { height: 350px; }
    </style>

    <script src="<?= base_url('assets/js/darkreader.min.js') ?>"></script>

    <script>
        const drOptions = { brightness: 100, contrast: 100, sepia: 5 };
        const savedTheme = localStorage.getItem('theme-preference');
        const sysDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && sysDark)) {
            DarkReader.enable(drOptions);
        } else {
            DarkReader.disable();
        }
        
        document.addEventListener("DOMContentLoaded", function () {
            const btnDark = document.getElementById('enable-dark-mode');
            const btnLight = document.getElementById('enable-light-mode');

            function updateUI(isDark) {
                if(isDark) {
                    if(btnDark) btnDark.classList.add('d-none');
                    if(btnLight) btnLight.classList.remove('d-none');
                } else {
                    if(btnLight) btnLight.classList.add('d-none');
                    if(btnDark) btnDark.classList.remove('d-none');
                }
            }

            updateUI(DarkReader.isEnabled());

            if(btnDark) {
                btnDark.addEventListener('click', (e) => {
                    e.preventDefault();
                    DarkReader.enable(drOptions);
                    localStorage.setItem('theme-preference', 'dark');
                    updateUI(true);
                });
            }
            if(btnLight) {
                btnLight.addEventListener('click', (e) => {
                    e.preventDefault();
                    DarkReader.disable();
                    localStorage.setItem('theme-preference', 'light');
                    updateUI(false);
                });
            }
        });
    </script>
    
    <script src="<?= base_url('js/code.jquery.com_jquery-3.7.0.min.js') ?>"></script>
</head>

<body>
    <div class="page d-flex flex-column min-vh-100">
        <?= $this->include('partials/header') ?>

        <?= $this->include('partials/navbar') ?>

        <div class="page-wrapper">
            <?= $this->include('partials/page-header') ?>

            <?= $this->renderSection('pageBody'); ?>

            <?= $this->include('partials/footer') ?>
        </div>
    </div>

    <script src="<?= base_url('assets/js/tabler.min.js?1684106062') ?>" defer></script>
    <script src="<?= base_url('assets/js/demo.min.js?1684106062') ?>" defer></script>
    <script src="<?= base_url('assets/js/leaflet.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/marked.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/purify.min.js') ?>"></script>

    <?php if (session()->getFlashdata('berhasil')) : ?>
        <script>Swal.fire({ title: "Berhasil", text: "<?= session()->getFlashdata('berhasil') ?>", icon: "success" });</script>
    <?php endif; ?>

    <?php
$user_profile = $user_profile ?? (function_exists('user_id') && user_id()
    ? (new \App\Models\UsersModel())->getUserInfo(user_id())
    : null);

if ($user_profile) {
    echo view('components/ai_chat_widget', ['user_profile' => $user_profile]);
}
?>
</body>
</html>