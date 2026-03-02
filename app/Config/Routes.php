<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================================
// HOME & DASHBOARD
// ============================================================================
$routes->get('/', 'Home::index', ['filter' => 'role:admin,pegawai,helper']);
$routes->post('/waktu', 'Home::getWaktu');
$routes->get('/waktu', fn() => redirect()->to('/'));

$routes->get('/admin', 'Admin::index', ['filter' => 'role:admin,head,helper']);

// ============================================================================
// JABATAN
// ============================================================================
$routes->get('/jabatan', 'Jabatan::index', ['filter' => 'role:admin,head']);
$routes->post('/jabatan/store', 'Jabatan::store', ['filter' => 'role:admin,head']);
$routes->get('/jabatan/(:segment)', 'Jabatan::edit/$1', ['filter' => 'role:admin,head']);
$routes->post('/jabatan/update', 'Jabatan::update', ['filter' => 'role:admin,head']);
$routes->delete('/jabatan/(:num)', 'Jabatan::delete/$1', ['filter' => 'role:admin,head']);
$routes->get('/cari-jabatan', 'Jabatan::pencarianJabatan', ['filter' => 'role:admin,head']);

// ============================================================================
// LOKASI PRESENSI
// ============================================================================
$routes->get('/lokasi-presensi', 'LokasiPresensi::index', ['filter' => 'role:admin,head']);
$routes->get('/tambah-lokasi-presensi', 'LokasiPresensi::add', ['filter' => 'role:admin,head']);
$routes->post('/lokasi-presensi/store', 'LokasiPresensi::store', ['filter' => 'role:admin,head']);
$routes->post('/lokasi-presensi/update', 'LokasiPresensi::update', ['filter' => 'role:admin,head']);
$routes->delete('/lokasi-presensi/(:num)', 'LokasiPresensi::delete/$1', ['filter' => 'role:admin,head']);
$routes->post('/lokasi-presensi/excel', 'LokasiPresensi::dataLokasiExcel', ['filter' => 'role:admin,head']);
$routes->get('/lokasi-presensi/edit/(:segment)', 'LokasiPresensi::edit/$1', ['filter' => 'role:admin,head']);
$routes->get('/lokasi-presensi/(:any)', 'LokasiPresensi::detail/$1', ['filter' => 'role:admin,head']);
$routes->get('/cari-lokasi', 'LokasiPresensi::pencarianLokasi', ['filter' => 'role:admin,head']);

// ============================================================================
// DATA PEGAWAI
// ============================================================================
$routes->get('/data-pegawai', 'Pegawai::index', ['filter' => 'role:admin,head,helper']);
$routes->get('/tambah-data-pegawai', 'Pegawai::add', ['filter' => 'role:admin,head,helper']);
$routes->post('/data-pegawai/store', 'Pegawai::store', ['filter' => 'role:admin,head,helper']);
$routes->post('/data-pegawai/update', 'Pegawai::update', ['filter' => 'role:admin,head,helper']);
$routes->delete('/data-pegawai/(:num)', 'Pegawai::delete/$1', ['filter' => 'role:admin,head,helper']);
$routes->post('/data-pegawai/bulk-delete', 'Pegawai::bulkDelete', ['filter' => 'role:admin,head,helper']);
$routes->post('data-pegawai/bulk-update-unit', 'Pegawai::bulkUpdateUnit', ['filter' => 'role:admin,head,helper']);
$routes->get('/data-pegawai/edit/(:segment)', 'Pegawai::edit/$1', ['filter' => 'role:admin,head,helper']);
$routes->get('/data-pegawai/download-template', 'Pegawai::downloadTemplate', ['filter' => 'role:admin,head,helper']);
$routes->post('/data-pegawai/import-excel', 'Pegawai::importExcel', ['filter' => 'role:admin,head,helper']);
$routes->post('/data-pegawai/reset-password', 'Pegawai::resetPassword', ['filter' => 'role:admin,head']);
$routes->post('/data-pegawai/excel', 'Pegawai::dataPegawaiExcel', ['filter' => 'role:admin,head,helper']);
$routes->get('/data-pegawai/manage-face-descriptors/(:num)', 'Pegawai::manageFaceDescriptors/$1', ['filter' => 'role:admin,head,helper']);
$routes->post('/data-pegawai/save-face-descriptor', 'Pegawai::saveFaceDescriptor', ['filter' => 'role:admin,head,helper']);
$routes->post('/data-pegawai/update-descriptor-label', 'Pegawai::updateDescriptorLabel', ['filter' => 'role:admin,head,helper']);
$routes->get('/data-pegawai/delete-descriptor/(:num)', 'Pegawai::deleteDescriptor/$1', ['filter' => 'role:admin,head,helper']);
$routes->get('/data-pegawai/(:segment)', 'Pegawai::detail/$1', ['filter' => 'role:admin,head,helper']);

$routes->get('/cari-pegawai', 'Pegawai::pencarianPegawai', ['filter' => 'role:admin,head,helper']);
$routes->post('/hapus-foto/(:segment)', 'Pegawai::hapusFoto/$1', ['filter' => 'role:admin,head,helper']);

// ============================================================================
// PRESENSI
// ============================================================================
$routes->post('/presensi-masuk', 'Presensi::presensiMasuk', ['filter' => 'role:pegawai,helper']);
$routes->post('/presensi-masuk/simpan', 'Presensi::simpanPresensiMasuk', ['filter' => 'role:pegawai,helper']);
$routes->post('/presensi-keluar', 'Presensi::presensiKeluar', ['filter' => 'role:pegawai,helper']);
$routes->post('/presensi-keluar/simpan', 'Presensi::simpanPresensiKeluar', ['filter' => 'role:pegawai,helper']);

$routes->get('/rekap-presensi', 'Presensi::rekapPresensiPegawai', ['filter' => 'role:admin,pegawai,helper']);
$routes->post('/rekap-presensi/excel', 'Presensi::rekapPresensiPegawaiExcel', ['filter' => 'role:admin,pegawai,helper']);

$routes->get('/laporan-presensi-harian', 'Presensi::laporanHarian', ['filter' => 'role:admin,head']);
$routes->post('/laporan-presensi-harian/excel', 'Presensi::laporanHarianExcel', ['filter' => 'role:admin,head']);
$routes->get('/laporan-presensi-bulanan', 'Presensi::laporanBulanan', ['filter' => 'role:admin,head']);
$routes->post('/laporan-presensi-bulanan/excel', 'Presensi::laporanBulananExcel', ['filter' => 'role:admin,head']);

// ============================================================================
// KETIDAKHADIRAN
// ============================================================================
$routes->get('/ketidakhadiran', 'Ketidakhadiran::index', ['filter' => 'role:admin,pegawai']);
$routes->get('/pengajuan-ketidakhadiran', 'Ketidakhadiran::add', ['filter' => 'role:admin,pegawai']);
$routes->post('/pengajuan-ketidakhadiran/store', 'Ketidakhadiran::store', ['filter' => 'role:admin,pegawai']);
$routes->get('/ketidakhadiran/edit/(:num)', 'Ketidakhadiran::edit/$1', ['filter' => 'role:admin,pegawai']);
$routes->post('/ketidakhadiran/update', 'Ketidakhadiran::update', ['filter' => 'role:admin,pegawai']);
$routes->delete('/ketidakhadiran/(:num)', 'Ketidakhadiran::delete/$1', ['filter' => 'role:admin,pegawai']);
$routes->post('/ketidakhadiran/excel', 'Ketidakhadiran::dataKetidakhadiranExcel', ['filter' => 'role:admin,pegawai']);
$routes->get('/cari-ketidakhadiran', 'Ketidakhadiran::pencarianKetidakhadiranPegawai', ['filter' => 'role:admin,pegawai']);

// HEAD - Kelola Ketidakhadiran (hanya HEAD yang bisa approve/reject)
$routes->get('/kelola-ketidakhadiran', 'Ketidakhadiran::kelolaKetidakhadiran', ['filter' => 'role:head']);
$routes->get('/kelola-ketidakhadiran/(:num)', 'Ketidakhadiran::kelolaKetidakhadiranAksi/$1', ['filter' => 'role:head']);
$routes->post('/kelola-ketidakhadiran/store', 'Ketidakhadiran::updateStatusKetidakhadiran', ['filter' => 'role:head']);
$routes->post('/kelola-ketidakhadiran/mass-approval', 'Ketidakhadiran::massApproval', ['filter' => 'role:head']);
$routes->post('/kelola-ketidakhadiran/update-file', 'Ketidakhadiran::updateFileKetidakhadiran', ['filter' => 'role:head']);
$routes->post('/kelola-ketidakhadiran/excel', 'Ketidakhadiran::kelolaKetidakhadiranExcel', ['filter' => 'role:head']);
$routes->get('/cari-data-ketidakhadiran', 'Ketidakhadiran::pencarianDataKetidakhadiran', ['filter' => 'role:head']);

// ============================================================================
// HARI LIBUR (hanya ADMIN dan HEAD)
// ============================================================================
$routes->get('/hari-libur', 'HariLibur::index', ['filter' => 'role:admin,head']);
$routes->get('/hari-libur/tambah', 'HariLibur::tambah', ['filter' => 'role:admin,head']);
$routes->get('/hari-libur/sync-api', 'HariLibur::syncFromAPI', ['filter' => 'role:admin,head']);
$routes->post('/hari-libur/simpan', 'HariLibur::simpan', ['filter' => 'role:admin,head']);
$routes->post('/hari-libur/approve/(:num)', 'HariLibur::approve/$1', ['filter' => 'role:head']);
$routes->post('/hari-libur/approve-all', 'HariLibur::approveAll', ['filter' => 'role:head']);
$routes->post('/hari-libur/reject-all', 'HariLibur::rejectAll', ['filter' => 'role:head']);
$routes->delete('/hari-libur/hapus/(:num)', 'HariLibur::hapus/$1', ['filter' => 'role:head']);

// ============================================================================
// USER PROFILE & ACCOUNT
// ============================================================================
$routes->get('/profile', 'UserProfile::index');
$routes->get('/profile/edit', 'UserProfile::editProfile');
$routes->post('/profile/hapus-foto', 'UserProfile::hapusFoto');
$routes->post('/profile/update', 'UserProfile::update');

$routes->post('/send-password-token', 'UserProfile::passwordToken');
$routes->post('/send-email-token', 'UserProfile::emailToken');

$routes->get('/change-email', 'UserProfile::changeEmail');
$routes->post('/update-email', 'UserProfile::attemptChangeEmail');

$routes->get('/activate-account', 'Activation::activateAccount', ['as' => 'activate-account']);
$routes->post('/activate-account', 'Activation::attemptActivate');

// ============================================================================
// FILE MANAGER (hanya ADMIN dan HEAD)
// ============================================================================
$routes->get('/file-manager', 'FileManager::index', ['filter' => 'role:admin,head']);
$routes->get('/file-manager/browse', 'FileManager::browse', ['filter' => 'role:admin,head']);
$routes->get('/file-manager/download', 'FileManager::download', ['filter' => 'role:admin,head']);
$routes->get('/file-manager/preview', 'FileManager::preview', ['filter' => 'role:admin,head']);
$routes->get('/file-manager/run-cleanup', 'FileManager::runCleanup', ['filter' => 'role:head']);
$routes->post('/file-manager/download-bulk', 'FileManager::downloadBulk', ['filter' => 'role:admin,head']);
$routes->post('/file-manager/delete-bulk', 'FileManager::deleteBulk', ['filter' => 'role:head']);
$routes->post('/file-manager/update-settings', 'FileManager::updateSettings', ['filter' => 'role:head']);
$routes->post('/file-manager/upload-logo', 'FileManager::uploadLogo', ['filter' => 'role:admin,head']);

// ============================================================================
// CRON
// ============================================================================
$routes->cli('cron/auto-cleanup', 'Cron::autoCleanup');

// ============================================================================
// PRESENSI PEGAWAI
// ============================================================================
$routes->get('presensi/get-face-descriptors', 'Presensi::getFaceDescriptors', ['filter' => 'role:pegawai,helper']);
$routes->post('presensi/verify-face', 'Presensi::verifyFace', ['filter' => 'role:pegawai,helper']);

// ============================================================================
// FACE ENROLLMENT REQUEST (USER)
// ============================================================================
$routes->get('/face-enrollment', 'FaceEnrollmentRequest::index', ['filter' => 'role:pegawai']);
$routes->post('/face-enrollment/submit', 'FaceEnrollmentRequest::submitRequest', ['filter' => 'role:pegawai']);
$routes->get('/face-enrollment/cancel/(:num)', 'FaceEnrollmentRequest::cancelRequest/$1', ['filter' => 'role:pegawai']);

// ============================================================================
// FACE ENROLLMENT ADMIN (ADMIN/HEAD)
// ============================================================================
$routes->get('/kelola-face-enrollment', 'FaceEnrollmentAdmin::index', ['filter' => 'role:admin,head']);
$routes->get('/kelola-face-enrollment/detail/(:num)', 'FaceEnrollmentAdmin::detail/$1', ['filter' => 'role:admin,head']);
$routes->post('/kelola-face-enrollment/approve/(:num)', 'FaceEnrollmentAdmin::approve/$1', ['filter' => 'role:admin,head']);
$routes->post('/kelola-face-enrollment/reject/(:num)', 'FaceEnrollmentAdmin::reject/$1', ['filter' => 'role:admin,head']);
$routes->get('/kelola-face-enrollment/image/(:num)', 'FaceEnrollmentAdmin::viewImage/$1', ['filter' => 'role:admin,head']);

// ============================================================================
// PUBLIC PLAYGROUND
// ============================================================================
$routes->get('playground',                'PlaygroundController::index');
$routes->post('playground/register-face', 'PlaygroundController::registerFace');
$routes->post('playground/clear',         'PlaygroundController::clearSession');
$routes->post('playground/submit',        'PlaygroundController::submitPresensi');

// ============================================================================
// QUOTE
// ============================================================================
$routes->get('quote/random', 'Quote::random');

// ============================================================================
// LAB
// ============================================================================\
$routes->get('lab', 'Lab::index', ['filter' => 'role:admin,head,helper']);
$routes->get('lab/load-descriptor/(:num)', 'Lab::loadDescriptorByPegawai/$1', ['filter' => 'role:admin,head,helper']);
$routes->post('lab/save-session-descriptor', 'Lab::saveSessionDescriptor', ['filter' => 'role:admin,head,helper']);
$routes->delete('lab/clear-session', 'Lab::clearSessionDescriptors', ['filter' => 'role:admin,head,helper']);

// ============================================================================
// KIOSK MODE
// ============================================================================
$routes->get('/kiosk', 'Kiosk::index', ['filter' => 'role:kiosk']);
$routes->post('/kiosk/cari-pegawai', 'Kiosk::cariPegawai', ['filter' => 'role:kiosk']);
$routes->post('/kiosk/simpan-presensi', 'Kiosk::simpanPresensi', ['filter' => 'role:kiosk']);

// ============================================================================
// AI CHAT
// ============================================================================
$routes->post('ai-chat/chat', 'AIChatController::chat', ['filter' => 'role:admin,head,pegawai']);