<?php
// \app\Views\components\ai_chat_widget.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

// Hanya tampilkan jika user sudah login dan punya role yang valid
if (!function_exists('user_id') || !user_id()) return;
if (!in_groups(['admin', 'head', 'pegawai'])) return;
?>

<div id="si-pintar-widget">

  <button id="sp-fab" title="Si Pintar - AI Assistant" aria-label="Buka Si Pintar">
    <span id="sp-fab-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="10" rx="2" />
        <circle cx="12" cy="5" r="2" />
        <path d="M12 7v4" />
        <line x1="8" y1="16" x2="8" y2="16" />
        <line x1="16" y1="16" x2="16" y2="16" />
      </svg>
    </span>
  </button>

  <div id="sp-panel" role="dialog" aria-label="Si Pintar Chat">

    <div id="sp-header">
      <div class="sp-header-left">
        <div class="sp-avatar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="10" rx="2" />
            <circle cx="12" cy="5" r="2" />
            <path d="M12 7v4" />
            <line x1="8" y1="16" x2="8" y2="16" />
            <line x1="16" y1="16" x2="16" y2="16" />
          </svg>
        </div>
        <div class="sp-header-info">
          <span class="sp-name">Si Pintar</span>
          <span class="sp-status">
            <span class="sp-status-dot"></span> Online
          </span>
        </div>
      </div>
      <div class="sp-header-actions">
        <button id="sp-tts-toggle" title="Text-to-Speech" class="sp-icon-btn" aria-pressed="false">
          <span id="sp-tts-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
              <line x1="23" y1="9" x2="17" y2="15" />
              <line x1="17" y1="9" x2="23" y2="15" />
            </svg>
          </span>
        </button>
        <button id="sp-clear-btn" title="Hapus riwayat" class="sp-icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6" />
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
          </svg>
        </button>
        <button id="sp-close-btn" title="Tutup" class="sp-icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>
    </div>

    <div id="sp-disclaimer">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        style="flex-shrink:0; margin-top:2px; color:var(--sp-secondary);">
        <path d="M9 3H15" />
        <path d="M10 3V8L3 19A2 2 0 0 0 5 22H19A2 2 0 0 0 21 19L14 8V3" />
      </svg>
      <div style="flex: 1;">
        <span>Si Pintar adalah AI dan dapat melakukan kesalahan. Fitur ini masih <strong>Eksperimental</strong>.</span>
        <div
          style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; font-size: 9.5px; margin-top: 3px; opacity: 0.85; line-height: 1.2;">
          <span>*Note: Fitur suara (TTS) menggunakan bawaan device. Bekerja optimal jika ada mesin TTS Bahasa
            Indonesia.</span>
          <button id="sp-help-btn" type="button" aria-label="Bantuan Install TTS"
            style="background: none; border: none; color: #1e3a8a; font-weight: bold; cursor: pointer; text-decoration: underline; padding: 0; flex-shrink: 0;">Cara
            Install?</button>
        </div>
      </div>
    </div>

    <div id="sp-help-modal" role="dialog" aria-label="Panduan Install Suara">
      <div class="sp-help-card">
        <div class="sp-help-card-header">
          <span style="font-weight: 700; color: var(--sp-primary); font-size: 14px;">Panduan Install Suara (TTS)</span>
          <button id="sp-help-close"
            style="background: none; border: none; cursor: pointer; font-size: 18px; line-height: 1;">&times;</button>
        </div>
        <div class="sp-help-card-body">
          <strong style="color: #1e293b;">💻 Di Windows (PC/Laptop):</strong>
          <ol style="margin: 4px 0 12px 20px; padding: 0; color: #475569;">
            <li>Buka <b>Settings</b> > <b>Time & Language</b> > <b>Language & region</b>.</li>
            <li>Klik <b>Add a language</b>, cari "Indonesia", lalu install.</li>
            <li>Kembali ke menu <b>Speech</b>, pastikan suara bahasa Indonesia (seperti "Andika" atau "Gadis") tersedia.
            </li>
          </ol>
          <strong style="color: #1e293b;">📱 Di Android (HP):</strong>
          <ol style="margin: 4px 0 0 20px; padding: 0; color: #475569;">
            <li>Buka <b>Pengaturan</b> > <b>Aksesibilitas</b> > <b>Keluaran Teks-ke-suara</b> (Text-to-speech).</li>
            <li>Pilih <b>Mesin yang diutamakan</b> (biasanya "Layanan Ucapan dari Google").</li>
            <li>Pastikan bahasa disetel ke <b>Bahasa Indonesia</b>. Jika tidak ada suara, klik ikon ⚙️ (Pengaturan) di
              sebelahnya untuk mendownload paket data suara Bahasa Indonesia.</li>
          </ol>
        </div>
      </div>
    </div>

    <div id="sp-messages">
      <div class="sp-msg-row sp-ai">
        <div class="sp-bubble">
          <p>Halo <strong><?= esc($user_profile->nama ?? 'Pengguna') ?></strong>! 👋 Saya <strong>Si Pintar</strong>, AI
            Assistant PresenSI.</p>
          <div class="sp-capabilities">
            <p class="sp-cap-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                style="vertical-align: text-bottom; margin-right: 4px;">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
              </svg>
              Yang bisa saya bantu:
            </p>
            <ul>
              <?php if (in_groups(['admin', 'head'])): ?>
              <li>Rekap & statistik kehadiran</li>
              <li>Daftar alpha, telat, atau izin hari ini</li>
              <li>Analisis per kelas / unit</li>
              <li>Riwayat presensi pengguna</li>
              <?php else: ?>
              <li>Status presensi saya hari ini</li>
              <li>Hitung telat saya minggu/bulan ini</li>
              <li>Riwayat izin & sakit saya</li>
              <li>Info aturan presensi</li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div id="sp-input-area">
      <form id="sp-form">
        <input type="text" id="sp-input" placeholder="Tanya sesuatu..." autocomplete="off" maxlength="500" />
        <button type="submit" id="sp-send-btn" aria-label="Kirim">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13" />
            <polygon points="22 2 15 22 11 13 2 9 22 2" />
          </svg>
        </button>
      </form>
    </div>

  </div>
</div>

<style>
#si-pintar-widget {
  --sp-primary: #1e3a8a;
  --sp-primary-rgb: 30, 58, 138;
  --sp-secondary: #dda518;
  --sp-radius: 16px;
  --sp-panel-w: 380px;
  --sp-panel-h: 560px;
  --sp-font: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif;
  --sp-shadow: 0 20px 60px rgba(30, 58, 138, 0.18), 0 4px 16px rgba(0, 0, 0, 0.1);
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  font-family: var(--sp-font);
}

/* ── FAB ── */
#sp-fab {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--sp-primary), #2563eb);
  border: none;
  color: #fff;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(var(--sp-primary-rgb), 0.25);
  transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 2;
}

#sp-fab:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 16px rgba(var(--sp-primary-rgb), 0.35);
}

#sp-fab.active {
  background: #ef4444;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

#sp-fab-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
}

#sp-fab.active #sp-fab-icon {
  transform: rotate(90deg);
}

#sp-fab::after {
  content: '';
  position: absolute;
  top: 6px;
  right: 6px;
  width: 10px;
  height: 10px;
  background: var(--sp-secondary);
  border-radius: 50%;
  border: 2px solid #fff;
  display: none;
}

#sp-fab.has-notif::after {
  display: block;
}

/* ── PANEL ── */
#sp-panel {
  position: absolute;
  bottom: 70px;
  right: 0;
  width: var(--sp-panel-w);
  height: var(--sp-panel-h);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-radius: var(--sp-radius);
  box-shadow: var(--sp-shadow);
  border: 1px solid rgba(255, 255, 255, 0.4);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transform-origin: bottom right;
  transform: scale(0.9) translateY(10px);
  opacity: 0;
  pointer-events: none;
  transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), opacity 0.25s ease;
}

#sp-panel.open {
  transform: scale(1) translateY(0);
  opacity: 1;
  pointer-events: all;
}

/* ── HEADER ── */
#sp-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background: linear-gradient(135deg, var(--sp-primary), #2563eb);
  color: #fff;
  flex-shrink: 0;
}

.sp-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.sp-avatar {
  width: 36px;
  height: 36px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sp-name {
  font-weight: 700;
  font-size: 15px;
  display: block;
}

.sp-status {
  font-size: 11px;
  opacity: 0.85;
  display: flex;
  align-items: center;
  gap: 4px;
}

.sp-status-dot {
  width: 6px;
  height: 6px;
  background: #4ade80;
  border-radius: 50%;
  display: inline-block;
  animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.4;
  }
}

.sp-header-actions {
  display: flex;
  gap: 4px;
}

.sp-icon-btn {
  background: rgba(255, 255, 255, 0.15);
  border: none;
  color: #fff;
  width: 30px;
  height: 30px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.sp-icon-btn:hover {
  background: rgba(255, 255, 255, 0.28);
}

.sp-icon-btn.tts-on {
  background: var(--sp-secondary);
  color: #1e3a8a;
}

/* ── DISCLAIMER ── */
#sp-disclaimer {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 8px 14px;
  background: rgba(221, 165, 24, 0.1);
  border-bottom: 1px solid rgba(221, 165, 24, 0.2);
  font-size: 11px;
  color: #92400e;
  flex-shrink: 0;
}

/* ── MESSAGES ── */
#sp-messages {
  flex: 1;
  overflow-y: auto;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: rgb(248, 248, 239);
  scroll-behavior: smooth;
}

#sp-messages::-webkit-scrollbar {
  width: 4px;
}

#sp-messages::-webkit-scrollbar-thumb {
  background: rgba(30, 58, 138, 0.2);
  border-radius: 2px;
}

.sp-msg-row {
  display: flex;
}

.sp-msg-row.sp-user {
  justify-content: flex-end;
}

.sp-msg-row.sp-ai {
  justify-content: flex-start;
}

.sp-bubble {
  max-width: 82%;
  padding: 10px 13px;
  border-radius: 16px;
  font-size: 13.5px;
  line-height: 1.55;
  word-wrap: break-word;
  animation: sp-fadein 0.25s ease-out;
}

@keyframes sp-fadein {
  from {
    opacity: 0;
    transform: translateY(6px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.sp-msg-row.sp-user .sp-bubble {
  background: var(--sp-primary);
  color: #fff;
  border-radius: 16px 16px 4px 16px;
  box-shadow: 0 2px 8px rgba(30, 58, 138, 0.15);
}

.sp-msg-row.sp-ai .sp-bubble {
  background: #fff;
  color: #1e293b;
  border-radius: 16px 16px 16px 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.sp-bubble p {
  margin: 0 0 6px;
}

.sp-bubble p:last-child {
  margin-bottom: 0;
}

.sp-bubble h1,
.sp-bubble h2,
.sp-bubble h3 {
  font-size: 14px;
  font-weight: 700;
  margin: 8px 0 4px;
  color: var(--sp-primary);
}

.sp-bubble ul,
.sp-bubble ol {
  margin: 4px 0 4px 18px;
  padding: 0;
}

.sp-bubble li {
  margin-bottom: 2px;
}

.sp-bubble code {
  background: rgba(30, 58, 138, 0.08);
  padding: 1px 5px;
  border-radius: 4px;
  font-size: 12px;
  font-family: 'Courier New', monospace;
}

.sp-bubble pre {
  background: #0f172a;
  color: #e2e8f0;
  padding: 10px;
  border-radius: 8px;
  overflow-x: auto;
  margin: 6px 0;
  font-size: 12px;
}

.sp-bubble pre code {
  background: none;
  padding: 0;
  color: inherit;
  font-size: inherit;
}

.sp-bubble strong {
  font-weight: 700;
}

.sp-bubble em {
  font-style: italic;
}

.sp-bubble a {
  color: #2563eb;
  text-decoration: underline;
}

.sp-msg-row.sp-user .sp-bubble code {
  background: rgba(255, 255, 255, 0.2);
}

.sp-msg-row.sp-user .sp-bubble a {
  color: #bfdbfe;
}

.sp-capabilities {
  margin-top: 8px;
  padding: 8px 10px;
  background: rgba(30, 58, 138, 0.05);
  border-radius: 10px;
  border-left: 3px solid var(--sp-primary);
}

.sp-cap-title {
  margin: 0 0 5px;
  font-weight: 600;
  font-size: 12px;
  color: var(--sp-primary);
}

.sp-capabilities ul {
  margin: 0;
  padding-left: 16px;
}

.sp-capabilities li {
  font-size: 12px;
  color: #475569;
  margin-bottom: 2px;
}

.sp-typing-bubble {
  background: #fff;
  border-radius: 16px 16px 16px 4px;
  padding: 12px 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.sp-typing {
  display: flex;
  gap: 4px;
  align-items: center;
}

.sp-typing span {
  width: 6px;
  height: 6px;
  background: #94a3b8;
  border-radius: 50%;
  animation: sp-bounce 1.4s infinite ease-in-out;
}

.sp-typing span:nth-child(1) {
  animation-delay: -0.32s;
}

.sp-typing span:nth-child(2) {
  animation-delay: -0.16s;
}

@keyframes sp-bounce {

  0%,
  80%,
  100% {
    transform: scale(0);
    opacity: .4;
  }

  40% {
    transform: scale(1);
    opacity: 1;
  }
}

.sp-tts-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  color: var(--sp-secondary);
  margin-top: 4px;
  opacity: 0.9;
  font-weight: 600;
}

/* ── INPUT AREA ── */
#sp-input-area {
  padding: 12px;
  background: #fff;
  border-top: 1px solid rgba(30, 58, 138, 0.08);
  flex-shrink: 0;
}

#sp-form {
  display: flex;
  gap: 8px;
  align-items: center;
}

#sp-input {
  flex: 1;
  border: 1.5px solid rgba(30, 58, 138, 0.15);
  border-radius: 12px;
  padding: 9px 13px;
  font-size: 13.5px;
  font-family: var(--sp-font);
  outline: none;
  background: rgb(248, 248, 239);
  color: #1e293b;
  transition: border-color 0.2s, background 0.2s;
}

#sp-input:focus {
  border-color: var(--sp-primary);
  background: #fff;
}

#sp-send-btn {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: var(--sp-primary);
  border: none;
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: opacity 0.2s ease, background 0.2s ease;
  flex-shrink: 0;
}

#sp-send-btn:hover {
  background: #2563eb;
}

#sp-send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* ── MOBILE ── */
@media (max-width: 480px) {
  #si-pintar-widget {
    bottom: 16px;
    right: 16px;
  }

  #sp-panel {
    width: calc(100vw - 32px);
    height: calc(100dvh - 100px);
    right: 0;
  }
}

/* ── HELP MODAL ── */
#sp-help-modal {
  position: absolute;
  top: 64px;
  /* Tepat di bawah header */
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease;
}

#sp-help-modal.open {
  opacity: 1;
  pointer-events: auto;
}

.sp-help-card {
  background: #fff;
  border-radius: 12px;
  width: 100%;
  box-shadow: 0 10px 25px rgba(30, 58, 138, 0.15);
  border: 1px solid rgba(30, 58, 138, 0.1);
  transform: translateY(10px);
  transition: transform 0.25s ease;
}

#sp-help-modal.open .sp-help-card {
  transform: translateY(0);
}

.sp-help-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid rgba(30, 58, 138, 0.08);
  background: rgb(248, 248, 239);
  border-radius: 12px 12px 0 0;
}

.sp-help-card-body {
  padding: 16px;
  font-size: 12px;
  line-height: 1.5;
  overflow-y: auto;
  max-height: 300px;
}
</style>

<script>
(function() {
  'use strict';

  // ── Config ────────────────────────────────────────────────────────────────
  const CHAT_URL = '<?= base_url('ai-chat/chat') ?>';
  const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
  const CSRF_HASH = '<?= csrf_hash() ?>';
  const USER_ID = '<?= user_id() ?>';
  const STORAGE_MSG = `sp_messages_${USER_ID}`;
  const STORAGE_HIST = `sp_history_${USER_ID}`;
  const STORAGE_TTS = `sp_tts_${USER_ID}`;
  const STORAGE_OPEN = `sp_open_${USER_ID}`;
  const MAX_HIST_PAIRS = 10;

  // ── SVG Icons (Manual) ────────────────────────────────────────────────────
  const ICON_ROBOT =
    `<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg>`;
  const ICON_CLOSE =
    `<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
  const ICON_VOL_UP =
    `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>`;
  const ICON_VOL_MUTE =
    `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>`;
  const ICON_VOL_BADGE =
    `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>`;

  // ── Elements ──────────────────────────────────────────────────────────────
  const fab = document.getElementById('sp-fab');
  const fabIcon = document.getElementById('sp-fab-icon');
  const panel = document.getElementById('sp-panel');
  const closeBtn = document.getElementById('sp-close-btn');
  const clearBtn = document.getElementById('sp-clear-btn');
  const ttsBtn = document.getElementById('sp-tts-toggle');
  const ttsIcon = document.getElementById('sp-tts-icon');
  const messages = document.getElementById('sp-messages');
  const form = document.getElementById('sp-form');
  const input = document.getElementById('sp-input');
  const sendBtn = document.getElementById('sp-send-btn');

  // ── State ─────────────────────────────────────────────────────────────────
  let isOpen = false;
  let ttsOn = localStorage.getItem(STORAGE_TTS) === 'true';
  let speaking = false;
  let csrfHash = CSRF_HASH;

  // ── Marked config ─────────────────────────────────────────────────────────
  if (typeof marked !== 'undefined') {
    marked.setOptions({
      breaks: true,
      gfm: true
    });
  }

  function renderMarkdown(text) {
    if (typeof marked === 'undefined' || typeof DOMPurify === 'undefined') {
      return text.replace(/\n/g, '<br>');
    }
    return DOMPurify.sanitize(marked.parse(text));
  }

  // ── NATIVE TTS ────────────────────────────────────────────────────────────
  function applyTtsUi() {
    if (ttsOn) {
      ttsBtn.classList.add('tts-on');
      ttsBtn.title = 'TTS Aktif — klik untuk nonaktifkan';
      ttsIcon.innerHTML = ICON_VOL_UP;
      ttsBtn.setAttribute('aria-pressed', 'true');
    } else {
      ttsBtn.classList.remove('tts-on');
      ttsBtn.title = 'Text-to-Speech (nonaktif)';
      ttsIcon.innerHTML = ICON_VOL_MUTE;
      ttsBtn.setAttribute('aria-pressed', 'false');
      if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
      }
    }
  }

  function speak(text) {
    // Pastikan TTS nyala dan browser mendukung Web Speech API
    if (!ttsOn || !('speechSynthesis' in window)) return;

    // Batalkan antrean ucapan yang sedang berjalan
    window.speechSynthesis.cancel();

    // Bersihkan karakter markdown agar tidak ikut dibaca
    const plain = text.replace(/<[^>]+>/g, '').replace(/[#*`_~]/g, '');

    const utterance = new SpeechSynthesisUtterance(plain);
    utterance.lang = 'id-ID'; // Set bahasa ke Indonesia
    utterance.rate = 1.0;
    utterance.pitch = 1.0;

    utterance.onstart = () => {
      speaking = true;
    };
    utterance.onend = () => {
      speaking = false;
    };
    utterance.onerror = () => {
      speaking = false;
    };

    window.speechSynthesis.speak(utterance);
  }

  ttsBtn.addEventListener('click', () => {
    ttsOn = !ttsOn;
    localStorage.setItem(STORAGE_TTS, ttsOn);
    applyTtsUi();
  });
  applyTtsUi();

  // ── Panel toggle ──────────────────────────────────────────────────────────
  function openPanel() {
    isOpen = true;
    panel.classList.add('open');
    fab.classList.add('active');
    fab.setAttribute('aria-expanded', 'true');
    fabIcon.innerHTML = ICON_CLOSE;
    localStorage.setItem(STORAGE_OPEN, '1');
    scrollBottom();
    setTimeout(() => input.focus(), 300);
  }

  function closePanel() {
    isOpen = false;
    panel.classList.remove('open');
    fab.classList.remove('active');
    fab.setAttribute('aria-expanded', 'false');
    fabIcon.innerHTML = ICON_ROBOT;
    localStorage.setItem(STORAGE_OPEN, '0');
    if ('speechSynthesis' in window) window.speechSynthesis.cancel();
  }

  fab.addEventListener('click', () => isOpen ? closePanel() : openPanel());
  closeBtn.addEventListener('click', closePanel);

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && isOpen) closePanel();
  });

  // ── Scroll ────────────────────────────────────────────────────────────────
  function scrollBottom() {
    messages.scrollTo({
      top: messages.scrollHeight,
      behavior: 'smooth'
    });
  }

  // ── LocalStorage ──────────────────────────────────────────────────────────
  function getSavedMessages() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_MSG) || '[]');
    } catch {
      return [];
    }
  }

  function saveMessageStore(type, text) {
    const arr = getSavedMessages();
    arr.push({
      type,
      text,
      time: Date.now()
    });
    localStorage.setItem(STORAGE_MSG, JSON.stringify(arr.slice(-50)));
  }

  function getHistory() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_HIST) || '[]');
    } catch {
      return [];
    }
  }

  function pushHistory(userMsg, aiMsg) {
    const h = getHistory();
    h.push({
      role: 'user',
      content: userMsg
    });
    h.push({
      role: 'assistant',
      content: aiMsg
    });
    localStorage.setItem(STORAGE_HIST, JSON.stringify(h.slice(-(MAX_HIST_PAIRS * 2))));
  }

  // ── Render bubbles ────────────────────────────────────────────────────────
  function appendUserBubble(text) {
    const row = document.createElement('div');
    row.className = 'sp-msg-row sp-user';
    row.innerHTML = `<div class="sp-bubble">${escHtml(text)}</div>`;
    messages.appendChild(row);
    scrollBottom();
  }

  function appendAIBubble(text, withTTS = false) {
    removeTyping();
    const rendered = renderMarkdown(text);
    const row = document.createElement('div');
    row.className = 'sp-msg-row sp-ai';
    row.innerHTML = `
      <div class="sp-bubble">
        ${rendered}
        ${ttsOn ? `<div class="sp-tts-badge">${ICON_VOL_BADGE} Sedang dibacakan...</div>` : ''}
      </div>`;
    messages.appendChild(row);
    scrollBottom();
    if (withTTS) speak(text);
  }

  function showTyping() {
    const row = document.createElement('div');
    row.className = 'sp-msg-row sp-ai sp-typing-row';
    row.innerHTML =
      `<div class="sp-typing-bubble"><div class="sp-typing"><span></span><span></span><span></span></div></div>`;
    messages.appendChild(row);
    scrollBottom();
  }

  function removeTyping() {
    document.querySelectorAll('.sp-typing-row').forEach(el => el.remove());
  }

  function escHtml(str) {
    return str.replace(/[&<>"']/g, m => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    } [m]));
  }

  // ── Load saved messages ───────────────────────────────────────────────────
  function loadSavedMessages() {
    const saved = getSavedMessages();
    if (!saved.length) return;
    const welcome = messages.querySelector('.sp-msg-row');
    if (welcome) welcome.remove();
    saved.forEach(m => {
      if (m.type === 'user') appendUserBubble(m.text);
      else appendAIBubble(m.text, false);
    });
  }

  // ── Clear ─────────────────────────────────────────────────────────────────
  clearBtn.addEventListener('click', () => {
    if (!confirm('Hapus semua riwayat chat? Si Pintar akan lupa percakapan sebelumnya.')) return;
    localStorage.removeItem(STORAGE_MSG);
    localStorage.removeItem(STORAGE_HIST);
    if ('speechSynthesis' in window) window.speechSynthesis.cancel();

    messages.innerHTML = `
      <div class="sp-msg-row sp-ai">
        <div class="sp-bubble">
          <p>Chat direset. Ada yang bisa saya bantu? 😊</p>
        </div>
      </div>`;
  });

  // ── Send message ──────────────────────────────────────────────────────────
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = input.value.trim();
    if (!msg) return;

    setInputState(true);
    appendUserBubble(msg);
    saveMessageStore('user', msg);
    input.value = '';
    showTyping();

    const history = getHistory();

    try {
      const formData = new FormData();
      formData.append('message', msg);
      formData.append('history', JSON.stringify(history));
      formData.append(CSRF_TOKEN_NAME, csrfHash);

      const res = await fetch(CHAT_URL, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });
      const data = await res.json();

      const newHash = res.headers.get('X-CSRF-Token');
      if (newHash) csrfHash = newHash;

      const aiText = data.message || 'Maaf, terjadi kesalahan.';

      if (data.success) {
        appendAIBubble(aiText, true);
        saveMessageStore('ai', aiText);
        pushHistory(data.history_user || msg, data.history_assistant || aiText);
        fab.classList.remove('has-notif');
      } else {
        appendAIBubble('⚠️ ' + aiText, false);
        saveMessageStore('ai', '⚠️ ' + aiText);
      }

    } catch (err) {
      removeTyping();
      appendAIBubble('❌ Gagal menghubungi server. Periksa koneksi Anda.', false);
    } finally {
      setInputState(false);
      input.focus();
    }
  });

  function setInputState(disabled) {
    input.disabled = disabled;
    sendBtn.disabled = disabled;
  }

  function showFabNotif() {
    if (!isOpen) fab.classList.add('has-notif');
  }

  // ── Modal Help Logic ──────────────────────────────────────────────────────
  const helpBtn = document.getElementById('sp-help-btn');
  const helpModal = document.getElementById('sp-help-modal');
  const helpCloseBtn = document.getElementById('sp-help-close');

  if (helpBtn && helpModal && helpCloseBtn) {
    helpBtn.addEventListener('click', () => {
      helpModal.classList.add('open');
    });

    helpCloseBtn.addEventListener('click', () => {
      helpModal.classList.remove('open');
    });

    // Tutup modal jika user mengklik area abu-abu (backdrop)
    helpModal.addEventListener('click', (e) => {
      if (e.target === helpModal) {
        helpModal.classList.remove('open');
      }
    });
  }

  // ── Init ──────────────────────────────────────────────────────────────────
  loadSavedMessages();

  if (localStorage.getItem(STORAGE_OPEN) === '1') {
    openPanel();
  }

})();
</script>