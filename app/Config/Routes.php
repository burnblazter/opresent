<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================================
// HOME & DASHBOARD
// ============================================================================
$routes->get('/', 'Home::index', ['filter' => 'role:admin,pegawai']);
$routes->post('/waktu', 'Home::getWaktu');
$routes->get('/waktu', function () {
    return redirect()->to('/');
});

$routes->get('/admin', 'Admin::index', ['filter' => 'role:admin,head']);

// ============================================================================
// JABATAN
// ============================================================================
$routes->group('jabatan', ['filter' => 'role:admin,head'], function($routes) {
    $routes->get('/', 'Jabatan::index');
    $routes->post('store', 'Jabatan::store');
    $routes->get('(:segment)', 'Jabatan::edit/$1');
    $routes->post('update', 'Jabatan::update');
    $routes->delete('(:num)', 'Jabatan::delete/$1');
});
$routes->get('/cari-jabatan', 'Jabatan::pencarianJabatan', ['filter' => 'role:admin,head']);

// ============================================================================
// LOKASI PRESENSI
// ============================================================================
$routes->group('lokasi-presensi', ['filter' => 'role:admin,head'], function($routes) {
    $routes->get('/', 'LokasiPresensi::index');
    $routes->get('edit/(:segment)', 'LokasiPresensi::edit/$1');
    $routes->get('(:any)', 'LokasiPresensi::detail/$1');
    $routes->post('store', 'LokasiPresensi::store');
    $routes->post('update', 'LokasiPresensi::update');
    $routes->post('excel', 'LokasiPresensi::dataLokasiExcel');
    $routes->delete('(:num)', 'LokasiPresensi::delete/$1');
});
$routes->get('/tambah-lokasi-presensi', 'LokasiPresensi::add', ['filter' => 'role:admin,head']);
$routes->get('/cari-lokasi', 'LokasiPresensi::pencarianLokasi', ['filter' => 'role:admin,head']);

// ============================================================================
// DATA PEGAWAI
// ============================================================================
$routes->group('data-pegawai', ['filter' => 'role:admin,head'], function($routes) {
    $routes->get('/', 'Pegawai::index');
    $routes->get('download-template', 'Pegawai::downloadTemplate');
    $routes->get('edit/(:segment)', 'Pegawai::edit/$1');
    $routes->get('(:any)', 'Pegawai::detail/$1');
    $routes->post('store', 'Pegawai::store');
    $routes->post('update', 'Pegawai::update');
    $routes->post('excel', 'Pegawai::dataPegawaiExcel');
    $routes->post('import-excel', 'Pegawai::importExcel');
    $routes->post('reset-password', 'Pegawai::resetPassword');
    $routes->delete('(:num)', 'Pegawai::delete/$1');
});
$routes->get('/tambah-data-pegawai', 'Pegawai::add', ['filter' => 'role:admin,head']);
$routes->get('/cari-pegawai', 'Pegawai::pencarianPegawai', ['filter' => 'role:admin,head']);
$routes->post('/hapus-foto/(:segment)', 'Pegawai::hapusFoto/$1', ['filter' => 'role:admin,head']);

// ============================================================================
// PRESENSI
// ============================================================================
$routes->group('presensi-masuk', function($routes) {
    $routes->post('/', 'Presensi::presensiMasuk');
    $routes->post('simpan', 'Presensi::simpanPresensiMasuk');
});

$routes->group('presensi-keluar', function($routes) {
    $routes->post('/', 'Presensi::presensiKeluar');
    $routes->post('simpan', 'Presensi::simpanPresensiKeluar');
});

$routes->get('/rekap-presensi', 'Presensi::rekapPresensiPegawai', ['filter' => 'role:admin,pegawai']);
$routes->post('/rekap-presensi/excel', 'Presensi::rekapPresensiPegawaiExcel', ['filter' => 'role:admin,pegawai']);

$routes->group('laporan-presensi-harian', ['filter' => 'role:admin,head'], function($routes) {
    $routes->get('/', 'Presensi::laporanHarian');
    $routes->post('excel', 'Presensi::laporanHarianExcel');
});

$routes->group('laporan-presensi-bulanan', ['filter' => 'role:admin,head'], function($routes) {
    $routes->get('/', 'Presensi::laporanBulanan');
    $routes->post('excel', 'Presensi::laporanBulananExcel');
});

// ============================================================================
// KETIDAKHADIRAN
// ============================================================================
$routes->group('ketidakhadiran', ['filter' => 'role:admin,pegawai'], function($routes) {
    $routes->get('/', 'Ketidakhadiran::index');
    $routes->get('edit/(:num)', 'Ketidakhadiran::edit/$1');
    $routes->post('store', 'Ketidakhadiran::store');
    $routes->post('update', 'Ketidakhadiran::update');
    $routes->post('excel', 'Ketidakhadiran::dataKetidakhadiranExcel');
    $routes->delete('(:num)', 'Ketidakhadiran::delete/$1');
});
$routes->get('/pengajuan-ketidakhadiran', 'Ketidakhadiran::add', ['filter' => 'role:admin,pegawai']);
$routes->get('/cari-ketidakhadiran', 'Ketidakhadiran::pencarianKetidakhadiranPegawai', ['filter' => 'role:admin,pegawai']);

// ============================================================================
// KELOLA KETIDAKHADIRAN (HEAD)
// ============================================================================
$routes->group('kelola-ketidakhadiran', ['filter' => 'role:head,admin'], function($routes) {
    $routes->get('/', 'Ketidakhadiran::kelolaKetidakhadiran');
    $routes->get('(:num)', 'Ketidakhadiran::kelolaKetidakhadiranAksi/$1');
    $routes->post('store', 'Ketidakhadiran::updateStatusKetidakhadiran');
    $routes->post('update-file', 'Ketidakhadiran::updateFileKetidakhadiran');
    $routes->post('excel', 'Ketidakhadiran::kelolaKetidakhadiranExcel');
});
$routes->get('/cari-data-ketidakhadiran', 'Ketidakhadiran::pencarianDataKetidakhadiran', ['filter' => 'role:head']);

// ============================================================================
// HARI LIBUR
// ============================================================================
$routes->group('hari-libur', ['filter' => 'role:admin,head'], function($routes) {
    $routes->get('/', 'HariLibur::index');
    $routes->get('tambah', 'HariLibur::tambah');
    $routes->get('sync-api', 'HariLibur::syncFromAPI');
    $routes->post('simpan', 'HariLibur::simpan');
    $routes->post('approve/(:num)', 'HariLibur::approve/$1');
    $routes->post('approve-all', 'HariLibur::approveAll');
    $routes->post('reject-all', 'HariLibur::rejectAll');
    $routes->delete('hapus/(:num)', 'HariLibur::hapus/$1');
});

// ============================================================================
// USER PROFILE
// ============================================================================
$routes->group('profile', function($routes) {
    $routes->get('/', 'UserProfile::index');
    $routes->get('edit', 'UserProfile::editProfile');
    $routes->post('hapus-foto', 'UserProfile::hapusFoto');
    $routes->post('update', 'UserProfile::update');
});

$routes->post('/send-password-token', 'UserProfile::passwordToken');
$routes->post('/send-email-token', 'UserProfile::emailToken');

$routes->get('/change-email', 'UserProfile::changeEmail');
$routes->post('/update-email', 'UserProfile::attemptChangeEmail');

// ============================================================================
// ACCOUNT ACTIVATION
// ============================================================================
$routes->group('activate-account', function($routes) {
    $routes->get('/', 'Activation::activateAccount');
    $routes->post('/', 'Activation::attemptActivate');
});

// ============================================================================
// FILE MANAGER
// ============================================================================
$routes->group('file-manager', ['filter' => 'role:admin,head'], function($routes) {
    $routes->get('/', 'FileManager::index');
    $routes->get('browse', 'FileManager::browse');
    $routes->get('download', 'FileManager::download');
    $routes->get('preview', 'FileManager::preview');
    $routes->get('run-cleanup', 'FileManager::runCleanup');
    $routes->post('download-bulk', 'FileManager::downloadBulk');
    $routes->post('delete-bulk', 'FileManager::deleteBulk');
    $routes->post('update-settings', 'FileManager::updateSettings');
    $routes->post('upload-logo', 'FileManager::uploadLogo');
});

// ============================================================================
// CRON
// ============================================================================
$routes->cli('cron/auto-cleanup', 'Cron::autoCleanup');