<?= $this->extend('templates/index') ?>
<?= $this->section('pageBody') ?>

<script src="<?= base_url('assets/js/human.js') ?>"></script>
<script src="<?= base_url('assets/js/human/core.js') ?>"></script>

<style>
/* ===== RESET & BASE ===== */
*,
*::before,
*::after {
  box-sizing: border-box;
}

.lab-root {
  color: #e0e6f0;
  font-family: 'Courier New', monospace;
  min-height: 100vh;
  padding: 16px;
  background-image:
    radial-gradient(ellipse at 20% 10%, rgba(0, 255, 65, 0.04) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 90%, rgba(0, 120, 255, 0.04) 0%, transparent 50%);
}

/* ===== HEADER ===== */
.lab-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 12px;
  margin-bottom: 16px;
}

.lab-header-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.lab-header h1 {
  color: #00ff41;
  font-size: 18px;
  margin: 0;
  letter-spacing: 2px;
  text-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
}

.lab-badge {
  background: rgba(0, 255, 65, 0.1);
  border: 1px solid rgba(0, 255, 65, 0.3);
  color: #00ff41;
  font-size: 10px;
  padding: 3px 8px;
  border-radius: 20px;
  letter-spacing: 1px;
}

.lab-status-bar {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 11px;
  color: #666;
}

.lab-status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #333;
  display: inline-block;
  margin-right: 5px;
  transition: all 0.3s;
}

.lab-status-dot.online {
  background: #00ff41;
  box-shadow: 0 0 6px #00ff41;
}

.lab-status-dot.loading {
  background: #ffaa00;
  box-shadow: 0 0 6px #ffaa00;
  animation: pulse 1s infinite;
}

.lab-status-dot.error {
  background: #ff0041;
  box-shadow: 0 0 6px #ff0041;
}

@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.4;
  }
}

@keyframes scanline {
  0% {
    transform: translateY(-100%);
  }

  100% {
    transform: translateY(100vh);
  }
}

/* ===== LAYOUT ===== */
.lab-grid {
  display: grid;
  grid-template-columns: 300px 1fr 320px;
  gap: 12px;
  height: calc(100vh - 100px);
}

.lab-panel {
  background: rgba(15, 20, 40, 0.8);
  border: 1px solid rgba(0, 255, 65, 0.2);
  border-radius: 10px;
  padding: 14px;
  overflow-y: auto;
  overflow-x: hidden;
  backdrop-filter: blur(4px);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.lab-panel::-webkit-scrollbar {
  width: 4px;
}

.lab-panel::-webkit-scrollbar-track {
  background: transparent;
}

.lab-panel::-webkit-scrollbar-thumb {
  background: rgba(0, 255, 65, 0.3);
  border-radius: 2px;
}

.panel-section {
  background: rgba(6, 10, 26, 0.6);
  border: 1px solid rgba(0, 255, 65, 0.12);
  border-radius: 8px;
  padding: 12px;
}

.panel-section-title {
  color: #00ff41;
  font-size: 10px;
  font-weight: bold;
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-bottom: 10px;
  padding-bottom: 6px;
  border-bottom: 1px solid rgba(0, 255, 65, 0.15);
  display: flex;
  align-items: center;
  gap: 6px;
}

/* ===== VIDEO AREA ===== */
.video-lab-container {
  position: relative;
  background: #000;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid rgba(0, 255, 65, 0.2);
}

.video-lab-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, #00ff41, transparent);
  z-index: 10;
  animation: scanH 3s linear infinite;
  opacity: 0.4;
}

@keyframes scanH {
  0% {
    transform: scaleX(0) translateX(-100%);
  }

  100% {
    transform: scaleX(1) translateX(100%);
  }
}

.video-corner {
  position: absolute;
  width: 16px;
  height: 16px;
  z-index: 5;
  opacity: 0.7;
}

.video-corner.tl {
  top: 8px;
  left: 8px;
  border-top: 2px solid #00ff41;
  border-left: 2px solid #00ff41;
}

.video-corner.tr {
  top: 8px;
  right: 8px;
  border-top: 2px solid #00ff41;
  border-right: 2px solid #00ff41;
}

.video-corner.bl {
  bottom: 8px;
  left: 8px;
  border-bottom: 2px solid #00ff41;
  border-left: 2px solid #00ff41;
}

.video-corner.br {
  bottom: 8px;
  right: 8px;
  border-bottom: 2px solid #00ff41;
  border-right: 2px solid #00ff41;
}

#lab-video {
  width: 100%;
  display: block;
}

#lab-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

/* ===== STATS ===== */
.stat-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.stat-box {
  background: rgba(6, 10, 26, 0.8);
  padding: 10px 8px;
  border-radius: 6px;
  text-align: center;
  border: 1px solid rgba(0, 255, 65, 0.15);
  transition: border-color 0.3s;
}

.stat-box:hover {
  border-color: rgba(0, 255, 65, 0.4);
}

.stat-box .stat-value {
  font-size: 22px;
  font-weight: bold;
  color: #00ff41;
  line-height: 1;
  text-shadow: 0 0 8px rgba(0, 255, 65, 0.4);
}

.stat-box .stat-label {
  font-size: 9px;
  color: #556;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-top: 4px;
}

.stat-box.stat-warning .stat-value {
  color: #ffaa00;
  text-shadow: 0 0 8px rgba(255, 170, 0, 0.4);
}

.stat-box.stat-danger .stat-value {
  color: #ff0041;
  text-shadow: 0 0 8px rgba(255, 0, 65, 0.4);
}

/* ===== BUTTONS ===== */
.btn-lab {
  background: rgba(0, 255, 65, 0.1);
  color: #00ff41;
  border: 1px solid rgba(0, 255, 65, 0.4);
  padding: 8px 12px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  font-size: 11px;
  width: 100%;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 1px;
  transition: all 0.2s;
  font-family: 'Courier New', monospace;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.btn-lab:hover {
  background: rgba(0, 255, 65, 0.2);
  border-color: #00ff41;
  box-shadow: 0 0 12px rgba(0, 255, 65, 0.2);
  transform: translateY(-1px);
}

.btn-lab:active {
  transform: translateY(0);
}

.btn-lab:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  transform: none;
}

.btn-lab.btn-danger {
  background: rgba(255, 0, 65, 0.1);
  color: #ff0041;
  border-color: rgba(255, 0, 65, 0.4);
}

.btn-lab.btn-danger:hover {
  background: rgba(255, 0, 65, 0.2);
  border-color: #ff0041;
  box-shadow: 0 0 12px rgba(255, 0, 65, 0.2);
}

.btn-lab.btn-warning {
  background: rgba(255, 170, 0, 0.1);
  color: #ffaa00;
  border-color: rgba(255, 170, 0, 0.4);
}

.btn-lab.btn-warning:hover {
  background: rgba(255, 170, 0, 0.2);
  border-color: #ffaa00;
  box-shadow: 0 0 12px rgba(255, 170, 0, 0.2);
}

.btn-lab.btn-blue {
  background: rgba(0, 120, 255, 0.1);
  color: #4488ff;
  border-color: rgba(0, 120, 255, 0.4);
}

.btn-lab.btn-blue:hover {
  background: rgba(0, 120, 255, 0.2);
  border-color: #4488ff;
  box-shadow: 0 0 12px rgba(0, 120, 255, 0.2);
}

.btn-group {
  display: flex;
  gap: 6px;
}

.btn-group .btn-lab {
  margin-bottom: 0;
}

/* ===== FORM ELEMENTS ===== */
.lab-select,
.lab-input-text {
  width: 100%;
  background: rgba(6, 10, 26, 0.8);
  border: 1px solid rgba(0, 255, 65, 0.25);
  color: #00ff41;
  padding: 8px 10px;
  border-radius: 6px;
  font-family: 'Courier New', monospace;
  font-size: 12px;
  margin-bottom: 8px;
  transition: border-color 0.2s;
  appearance: none;
}

.lab-select:focus,
.lab-input-text:focus {
  outline: none;
  border-color: #00ff41;
  box-shadow: 0 0 8px rgba(0, 255, 65, 0.2);
}

.lab-select option {
  background: #0a0e27;
}

/* ===== TOGGLE SWITCHES ===== */
.toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}

.toggle-row:last-child {
  border-bottom: none;
}

.toggle-label {
  font-size: 11px;
  color: #aab;
  letter-spacing: 0.5px;
}

.toggle-switch {
  position: relative;
  width: 36px;
  height: 18px;
  flex-shrink: 0;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 18px;
  transition: 0.2s;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.toggle-slider::before {
  content: '';
  position: absolute;
  width: 12px;
  height: 12px;
  left: 2px;
  top: 2px;
  background: #555;
  border-radius: 50%;
  transition: 0.2s;
}

.toggle-switch input:checked+.toggle-slider {
  background: rgba(0, 255, 65, 0.25);
  border-color: rgba(0, 255, 65, 0.5);
}

.toggle-switch input:checked+.toggle-slider::before {
  transform: translateX(18px);
  background: #00ff41;
}

/* ===== RANGE SLIDER ===== */
.range-wrapper {
  position: relative;
}

.lab-range {
  width: 100%;
  -webkit-appearance: none;
  height: 4px;
  border-radius: 2px;
  background: rgba(0, 255, 65, 0.2);
  outline: none;
  margin-top: 8px;
}

.lab-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #00ff41;
  cursor: pointer;
  box-shadow: 0 0 6px rgba(0, 255, 65, 0.6);
}

.range-display {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 4px;
}

.range-value {
  font-size: 16px;
  font-weight: bold;
  color: #00ff41;
  text-shadow: 0 0 6px rgba(0, 255, 65, 0.4);
}

/* ===== MODEL TOGGLES ===== */
.model-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
}

.model-chip {
  background: rgba(6, 10, 26, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 6px;
  padding: 8px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
  user-select: none;
}

.model-chip:hover {
  border-color: rgba(0, 255, 65, 0.3);
}

.model-chip.active {
  background: rgba(0, 255, 65, 0.1);
  border-color: rgba(0, 255, 65, 0.5);
  box-shadow: 0 0 8px rgba(0, 255, 65, 0.1);
}

.model-chip.loading-model {
  border-color: rgba(255, 170, 0, 0.5);
  background: rgba(255, 170, 0, 0.1);
  animation: pulse 0.8s infinite;
}

.model-chip .model-icon {
  font-size: 18px;
}

.model-chip .model-name {
  font-size: 9px;
  color: #889;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-top: 3px;
}

.model-chip.active .model-name {
  color: #00ff41;
}

/* ===== LOG ===== */
.lab-log {
  background: rgba(0, 0, 0, 0.5);
  padding: 10px;
  border-radius: 6px;
  font-size: 11px;
  max-height: 140px;
  min-height: 80px;
  overflow-y: auto;
  border: 1px solid rgba(0, 255, 65, 0.1);
  flex-shrink: 0;
}

.lab-log::-webkit-scrollbar {
  width: 3px;
}

.lab-log::-webkit-scrollbar-thumb {
  background: rgba(0, 255, 65, 0.2);
}

.log-entry {
  padding: 3px 6px;
  border-left: 2px solid #333;
  margin-bottom: 3px;
  font-size: 10px;
  color: #667;
  line-height: 1.4;
}

.log-entry.info {
  border-left-color: #4488ff;
  color: #88aaff;
}

.log-entry.success {
  border-left-color: #00ff41;
  color: #00cc33;
}

.log-entry.error {
  border-left-color: #ff0041;
  color: #ff4466;
}

.log-entry.warning {
  border-left-color: #ffaa00;
  color: #ffcc44;
}

/* ===== DESCRIPTOR LIST ===== */
.descriptor-list-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.descriptor-item {
  background: rgba(6, 10, 26, 0.6);
  padding: 10px 12px;
  border-radius: 6px;
  border-left: 3px solid rgba(0, 255, 65, 0.4);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  transition: all 0.2s;
  animation: slideIn 0.2s ease;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(10px);
  }

  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.descriptor-item:hover {
  background: rgba(15, 20, 40, 0.8);
  border-left-color: #00ff41;
}

.descriptor-item.type-session {
  border-left-color: rgba(255, 170, 0, 0.5);
}

.descriptor-item.type-session:hover {
  border-left-color: #ffaa00;
}

.descriptor-info {
  flex: 1;
  min-width: 0;
}

.descriptor-label {
  font-size: 12px;
  color: #dde;
  font-weight: bold;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.descriptor-meta {
  font-size: 9px;
  color: #556;
  margin-top: 2px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.desc-badge {
  display: inline-block;
  padding: 1px 5px;
  border-radius: 3px;
  font-size: 8px;
  font-weight: bold;
  letter-spacing: 0.5px;
}

.desc-badge.db {
  background: rgba(0, 255, 65, 0.1);
  color: #00ff41;
  border: 1px solid rgba(0, 255, 65, 0.3);
}

.desc-badge.session {
  background: rgba(255, 170, 0, 0.1);
  color: #ffaa00;
  border: 1px solid rgba(255, 170, 0, 0.3);
}

.desc-actions {
  display: flex;
  gap: 4px;
  flex-shrink: 0;
}

.desc-btn {
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #556;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  transition: all 0.15s;
}

.desc-btn:hover.del {
  background: rgba(255, 0, 65, 0.15);
  border-color: rgba(255, 0, 65, 0.5);
  color: #ff0041;
}

.desc-btn:hover.info-btn {
  background: rgba(0, 120, 255, 0.15);
  border-color: rgba(0, 120, 255, 0.5);
  color: #4488ff;
}

.desc-btn:hover.test-btn {
  background: rgba(0, 255, 65, 0.15);
  border-color: rgba(0, 255, 65, 0.5);
  color: #00ff41;
}

/* ===== DESCRIPTOR DETAIL MODAL ===== */
.desc-detail-popup {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
  animation: fadeIn 0.15s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.desc-detail-box {
  background: #0d1230;
  border: 1px solid rgba(0, 255, 65, 0.3);
  border-radius: 12px;
  padding: 24px;
  width: 400px;
  max-width: 90vw;
  box-shadow: 0 0 40px rgba(0, 255, 65, 0.1);
}

.desc-detail-box h3 {
  color: #00ff41;
  margin: 0 0 16px;
  font-size: 14px;
  letter-spacing: 1px;
}

.desc-detail-row {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  font-size: 12px;
}

.desc-detail-row .key {
  color: #556;
}

.desc-detail-row .val {
  color: #dde;
  font-weight: bold;
}

.desc-vector-preview {
  background: rgba(0, 0, 0, 0.4);
  border-radius: 6px;
  padding: 8px;
  margin-top: 12px;
  font-size: 10px;
  color: #336633;
  line-height: 1.6;
  max-height: 80px;
  overflow-y: auto;
  word-break: break-all;
}

/* ===== EMPTY STATE ===== */
.empty-state {
  text-align: center;
  padding: 30px 20px;
  color: #334;
}

.empty-state .empty-icon {
  font-size: 32px;
  margin-bottom: 8px;
  opacity: 0.4;
}

.empty-state .empty-text {
  font-size: 11px;
  letter-spacing: 1px;
}

/* ===== PROGRESS / NOTIFICATIONS ===== */
.toast-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.toast {
  background: rgba(15, 20, 40, 0.95);
  border: 1px solid rgba(0, 255, 65, 0.3);
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 12px;
  color: #00ff41;
  min-width: 220px;
  animation: toastIn 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}

.toast.toast-error {
  border-color: rgba(255, 0, 65, 0.4);
  color: #ff4466;
}

.toast.toast-warning {
  border-color: rgba(255, 170, 0, 0.4);
  color: #ffcc44;
}

@keyframes toastIn {
  from {
    opacity: 0;
    transform: translateX(20px);
  }

  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes toastOut {
  from {
    opacity: 1;
    transform: translateX(0);
  }

  to {
    opacity: 0;
    transform: translateX(20px);
  }
}

/* ===== MISC ===== */
.separator {
  border: none;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  margin: 4px 0;
}

.inline-value {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 11px;
  color: #556;
  margin-top: 2px;
}

.inline-value span:last-child {
  color: #00ff41;
}

.confidence-bar-wrap {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 3px;
  height: 4px;
  margin-top: 6px;
  overflow: hidden;
}

.confidence-bar {
  height: 100%;
  background: linear-gradient(90deg, #00ff41, #00cc33);
  border-radius: 3px;
  transition: width 0.3s;
  width: 0%;
}

.video-lab-container {
  position: relative;
  background: #000;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid rgba(0, 255, 65, 0.2);
  width: 100%;
  aspect-ratio: 4/3;
}

#lab-video {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}

#lab-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}
</style>

<div class="lab-root">

  <!-- HEADER -->
  <div class="lab-header">
    <div class="lab-header-title">
      <h1>⚗️ LAB FACE RECOGNITION</h1>
      <span class="lab-badge">DEV TOOLS</span>
    </div>
    <div class="lab-status-bar">
      <span><span class="lab-status-dot" id="status-human"></span><span id="status-human-text">Idle</span></span>
      <span><span class="lab-status-dot" id="status-cam"></span><span id="status-cam-text">Camera Off</span></span>
      <span style="color: #444">|</span>
      <span>Admin only</span>
    </div>
  </div>

  <!-- GRID -->
  <div class="lab-grid">

    <!-- ====== LEFT PANEL: CONTROLS ====== -->
    <div class="lab-panel">

      <!-- LOAD DESCRIPTOR -->
      <div class="panel-section">
        <div class="panel-section-title">👤 Load Descriptor</div>
        <select class="lab-select" id="pegawai-select">
          <option value="">-- Pilih Pengguna --</option>
          <?php foreach ($pegawai_list as $p): ?>
          <option value="<?= $p->id ?>"><?= esc($p->nama) ?> (<?= esc($p->nomor_induk) ?>)</option>
          <?php endforeach; ?>
        </select>
        <button class="btn-lab" onclick="LabFace.loadDescriptor()">📂 Load Descriptor</button>
      </div>

      <!-- MODEL MODULES -->
      <div class="panel-section">
        <div class="panel-section-title">🧠 Active Models</div>
        <div class="model-grid" id="model-grid">
          <div class="model-chip active" data-model="face" onclick="LabFace.toggleModel('face', this)">
            <div class="model-icon">👁️</div>
            <div class="model-name">Face</div>
          </div>
          <div class="model-chip" data-model="hand" onclick="LabFace.toggleModel('hand', this)">
            <div class="model-icon">✋</div>
            <div class="model-name">Hand</div>
          </div>
          <div class="model-chip" data-model="body" onclick="LabFace.toggleModel('body', this)">
            <div class="model-icon">🧍</div>
            <div class="model-name">Body/Pose</div>
          </div>
          <div class="model-chip" data-model="object" onclick="LabFace.toggleModel('object', this)">
            <div class="model-icon">📦</div>
            <div class="model-name">Object</div>
          </div>
          <div class="model-chip" data-model="gesture" onclick="LabFace.toggleModel('gesture', this)">
            <div class="model-icon">🤙</div>
            <div class="model-name">Gesture</div>
          </div>
          <div class="model-chip" data-model="segmentation" onclick="LabFace.toggleModel('segmentation', this)">
            <div class="model-icon">✂️</div>
            <div class="model-name">Segment</div>
          </div>
        </div>
        <div style="margin-top: 8px;">
          <button class="btn-lab btn-warning" id="btn-reload-models" onclick="LabFace.reloadModels()">
            🔄 Apply & Reload Models
          </button>
        </div>
      </div>

      <!-- THRESHOLD -->
      <div class="panel-section">
        <div class="panel-section-title">🎯 Threshold</div>
        <div class="range-display">
          <span style="font-size:10px;color:#556;">CONFIDENCE</span>
          <span class="range-value" id="threshold-value">0.62</span>
        </div>
        <input type="range" class="lab-range" id="threshold-slider" min="0.3" max="1.0" step="0.05" value="0.62">
        <div class="confidence-bar-wrap" style="margin-top:6px;">
          <div class="confidence-bar" id="threshold-bar" style="width:48%"></div>
        </div>
        <div class="inline-value" style="margin-top:6px;">
          <span>Current match score</span>
          <span id="live-score">-</span>
        </div>
      </div>

      <!-- DRAW OPTIONS -->
      <div class="panel-section">
        <div class="panel-section-title">🎨 Draw Options</div>
        <div class="toggle-row">
          <span class="toggle-label">Canvas Overlay</span>
          <label class="toggle-switch"><input type="checkbox" id="toggle-canvas" checked><span
              class="toggle-slider"></span></label>
        </div>
        <div class="toggle-row">
          <span class="toggle-label">Bounding Box</span>
          <label class="toggle-switch"><input type="checkbox" id="toggle-box" checked><span
              class="toggle-slider"></span></label>
        </div>
        <div class="toggle-row">
          <span class="toggle-label">Landmarks</span>
          <label class="toggle-switch"><input type="checkbox" id="toggle-landmarks" checked><span
              class="toggle-slider"></span></label>
        </div>
        <div class="toggle-row">
          <span class="toggle-label">Labels & Score</span>
          <label class="toggle-switch"><input type="checkbox" id="toggle-labels" checked><span
              class="toggle-slider"></span></label>
        </div>
        <div class="toggle-row">
          <span class="toggle-label">Emotion</span>
          <label class="toggle-switch"><input type="checkbox" id="toggle-emotion" checked><span
              class="toggle-slider"></span></label>
        </div>
        <div class="toggle-row">
          <span class="toggle-label">Auto Detection</span>
          <label class="toggle-switch"><input type="checkbox" id="toggle-auto" checked><span
              class="toggle-slider"></span></label>
        </div>
      </div>

      <!-- ACTIONS -->
      <div class="panel-section">
        <div class="panel-section-title">⚡ Actions</div>
        <button class="btn-lab" onclick="LabFace.enrollCurrentFace()">📸 Enroll Face (Session)</button>
        <hr class="separator">
        <div class="btn-group">
          <button class="btn-lab btn-danger" onclick="LabFace.clearSession()">🗑️ Clear Session</button>
          <button class="btn-lab btn-danger" onclick="LabFace.clearAll()">💣 Clear All</button>
        </div>
      </div>

    </div>

    <!-- ====== CENTER PANEL: VIDEO ====== -->
    <div class="lab-panel" style="gap: 10px;">

      <!-- STATS -->
      <div class="stat-grid">
        <div class="stat-box">
          <div class="stat-value" id="stat-faces">0</div>
          <div class="stat-label">Faces</div>
        </div>
        <div class="stat-box">
          <div class="stat-value" id="stat-fps">0</div>
          <div class="stat-label">FPS</div>
        </div>
        <div class="stat-box">
          <div class="stat-value" id="stat-confidence">—</div>
          <div class="stat-label">Confidence</div>
        </div>
        <div class="stat-box">
          <div class="stat-value" id="stat-match" style="font-size:13px;line-height:1.4;">—</div>
          <div class="stat-label">Best Match</div>
        </div>
      </div>

      <!-- VIDEO -->
      <div class="video-lab-container" style="flex: 1;">
        <div class="video-corner tl"></div>
        <div class="video-corner tr"></div>
        <div class="video-corner bl"></div>
        <div class="video-corner br"></div>
        <video id="lab-video" autoplay playsinline muted></video>
        <canvas id="lab-canvas"></canvas>
      </div>

      <!-- BODY / HAND EXTRA STATS (hidden by default) -->
      <div id="extra-stats" style="display:none;" class="panel-section" style="padding:8px 12px;">
        <div class="panel-section-title">📊 Extra Detection Stats</div>
        <div id="extra-stats-content" style="font-size:11px; color:#556; line-height:1.8;"></div>
      </div>

      <!-- LOG -->
      <div class="lab-log" id="lab-log">
        <div class="log-entry">System ready...</div>
      </div>

    </div>

    <!-- ====== RIGHT PANEL: DESCRIPTORS ====== -->
    <div class="lab-panel">

      <!-- HEADER & BULK ACTIONS -->
      <div style="display:flex; align-items:center; justify-content:space-between;">
        <div class="panel-section-title" style="margin:0; border:none; padding:0;">
          💾 Loaded Descriptors
          <span id="desc-count-badge"
            style="background:rgba(0,255,65,0.15); color:#00ff41; font-size:9px; padding:2px 6px; border-radius:10px; margin-left:4px;">0</span>
        </div>
        <button class="desc-btn del" title="Delete all" onclick="LabFace.clearAll()"
          style="width:auto; padding:0 8px; height:24px; border-radius:4px;">🗑️ All</button>
      </div>

      <!-- FILTER -->
      <div style="display:flex; gap:6px; margin-bottom:4px;">
        <button class="btn-lab" id="filter-all" onclick="LabFace.filterDescriptors('all')"
          style="margin:0; padding:5px 8px; font-size:9px;">All</button>
        <button class="btn-lab btn-blue" id="filter-db" onclick="LabFace.filterDescriptors('db')"
          style="margin:0; padding:5px 8px; font-size:9px;">DB</button>
        <button class="btn-lab btn-warning" id="filter-session" onclick="LabFace.filterDescriptors('session')"
          style="margin:0; padding:5px 8px; font-size:9px;">Session</button>
      </div>

      <!-- LIST -->
      <div id="descriptor-list" class="descriptor-list-wrapper" style="flex:1; overflow-y:auto;">
        <div class="empty-state">
          <div class="empty-icon">📭</div>
          <div class="empty-text">No descriptors loaded</div>
        </div>
      </div>

    </div>

  </div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toast-container"></div>

<script>
const LabFace = (function() {
  "use strict";

  // ==================== STATE ====================
  const state = {
    human: null,
    stream: null,
    loadedDescriptors: [], // from DB
    sessionDescriptors: [], // from session/enrolled
    animationFrameId: null,
    threshold: 0.62,
    currentFilter: 'all',
    config: {
      drawCanvas: true,
      drawLandmarks: true,
      drawBox: true,
      drawLabels: true,
      drawEmotion: true,
      autoDetection: true,
    },
    models: {
      face: true,
      hand: false,
      body: false,
      object: false,
      gesture: false,
      segmentation: false,
    },
    stats: {
      fpsHistory: [],
      lastFrameTime: 0
    },
    lastResult: null,
  };

  const DOM = {
    video: document.getElementById('lab-video'),
    canvas: document.getElementById('lab-canvas'),
    log: document.getElementById('lab-log'),
    descriptorList: document.getElementById('descriptor-list'),
    pegawaiSelect: document.getElementById('pegawai-select'),
  };

  // ==================== INIT ====================
  async function init() {
    setupControls();
    await setupCamera();
    await initHuman();
    startDetection();
  }

  function setupControls() {
    // Threshold
    document.getElementById('threshold-slider').addEventListener('input', (e) => {
      state.threshold = parseFloat(e.target.value);
      document.getElementById('threshold-value').textContent = state.threshold.toFixed(2);
      const pct = ((state.threshold - 0.3) / 0.7 * 100).toFixed(0);
      document.getElementById('threshold-bar').style.width = pct + '%';
    });

    // Draw toggles
    const toggleMap = {
      'toggle-canvas': 'drawCanvas',
      'toggle-box': 'drawBox',
      'toggle-landmarks': 'drawLandmarks',
      'toggle-labels': 'drawLabels',
      'toggle-emotion': 'drawEmotion',
    };
    Object.entries(toggleMap).forEach(([id, key]) => {
      document.getElementById(id).addEventListener('change', (e) => {
        state.config[key] = e.target.checked;
        if (id === 'toggle-canvas') {
          DOM.canvas.style.display = e.target.checked ? 'block' : 'none';
        }
      });
    });

    // Auto detection toggle
    document.getElementById('toggle-auto').addEventListener('change', (e) => {
      state.config.autoDetection = e.target.checked;
      if (e.target.checked) startDetection();
      else stopDetection();
    });
  }

  // ==================== HUMAN.JS ====================
  function buildHumanConfig() {
    return {
      modelBasePath: '<?= base_url('assets/models/') ?>',
      backend: 'wasm',
      cacheSensitivity: 0,
      debug: false,
      face: {
        enabled: state.models.face,
        detector: {
          modelPath: 'blazeface.json',
          maxDetected: 10,
          minConfidence: 0.4,
          rotation: false
        },
        mesh: {
          enabled: true
        },
        description: {
          enabled: true
        },
        emotion: {
          enabled: true
        },
        iris: {
          enabled: false
        },
        antispoof: {
          enabled: false
        },
        liveness: {
          enabled: false
        },
      },
      body: {
        enabled: state.models.body
      },
      hand: {
        enabled: state.models.hand
      },
      object: {
        enabled: state.models.object
      },
      gesture: {
        enabled: state.models.gesture
      },
      segmentation: {
        enabled: state.models.segmentation
      },
      filter: {
        enabled: true,
        equalization: false,
        flip: false
      },
    };
  }

  async function initHuman() {
    setStatusDot('human', 'loading');
    setStatusText('human', 'Loading...');
    addLog('Loading Human.js models...', 'info');

    try {
      if (state.human) {
        stopDetection();
        state.human = null;
      }

      state.human = new Human.Human(buildHumanConfig());
      await state.human.load();
      await state.human.warmup();

      setStatusDot('human', 'online');
      setStatusText('human', 'Ready');
      addLog('✅ Human.js ready — backend: ' + state.human.tf.getBackend(), 'success');
      showToast('Human.js loaded', 'success');
    } catch (err) {
      setStatusDot('human', 'error');
      setStatusText('human', 'Error');
      addLog('❌ Failed to load Human.js: ' + err.message, 'error');
      showToast('Model load failed', 'error');
    }
  }

  async function setupCamera() {
    try {
      state.stream = await navigator.mediaDevices.getUserMedia({
        video: {
          width: 640,
          height: 480,
          facingMode: 'user'
        }
      });
      DOM.video.srcObject = state.stream;
      await DOM.video.play();

      // Tunggu metadata video siap
      await new Promise(resolve => {
        if (DOM.video.readyState >= 2) return resolve();
        DOM.video.addEventListener('loadeddata', resolve, {
          once: true
        });
      });

      syncCanvasSize();

      // Kalau container di-resize, canvas ikut
      new ResizeObserver(syncCanvasSize).observe(DOM.video);

      setStatusDot('cam', 'online');
      setStatusText('cam', 'Camera On');
      addLog('✅ Camera initialized', 'success');
    } catch (err) {
      setStatusDot('cam', 'error');
      setStatusText('cam', 'Camera Error');
      addLog('❌ Camera: ' + err.message, 'error');
    }
  }

  function syncCanvasSize() {
    // Pakai ukuran display aktual, bukan resolusi stream
    const rect = DOM.video.getBoundingClientRect();
    DOM.canvas.width = rect.width;
    DOM.canvas.height = rect.height;
  }

  // ==================== MODEL TOGGLE ====================
  function toggleModel(model, chip) {
    if (model === 'face') {
      showToast('Face model is required', 'warning');
      return;
    }
    state.models[model] = !state.models[model];
    chip.classList.toggle('active', state.models[model]);
    addLog(`Model "${model}" ${state.models[model] ? 'enabled' : 'disabled'}`, 'info');
    showToast(`${model} ${state.models[model] ? 'ON' : 'OFF'} — click "Apply & Reload" to activate`, 'warning');
  }

  async function reloadModels() {
    const btn = document.getElementById('btn-reload-models');
    btn.disabled = true;
    btn.textContent = '⏳ Reloading...';

    stopDetection();
    await initHuman();

    if (state.config.autoDetection) startDetection();
    btn.disabled = false;
    btn.innerHTML = '🔄 Apply & Reload Models';
  }

  // ==================== DETECTION ====================
  function startDetection() {
    if (!state.human) return;

    const loop = async () => {
      if (!state.config.autoDetection) return;

      const t0 = performance.now();
      try {
        const result = await state.human.detect(DOM.video);
        state.lastResult = result;
        updateFPS(performance.now() - t0);
        processDetection(result);
        if (state.config.drawCanvas) drawResults(result);
      } catch (e) {
        /* silent */
      }

      state.animationFrameId = requestAnimationFrame(loop);
    };

    state.animationFrameId = requestAnimationFrame(loop);
  }

  function stopDetection() {
    if (state.animationFrameId) {
      cancelAnimationFrame(state.animationFrameId);
      state.animationFrameId = null;
    }
  }

  function processDetection(result) {
    const faces = result.face || [];
    document.getElementById('stat-faces').textContent = faces.length;

    if (faces.length > 0) {
      const face = faces[0];
      const conf = ((face.boxScore || 0) * 100).toFixed(1);
      document.getElementById('stat-confidence').textContent = conf + '%';

      const allDesc = getAllDescriptors();
      if (face.embedding && allDesc.length > 0) {
        const match = findBestMatch(face.embedding);
        const matchPct = (match.score * 100).toFixed(1);
        document.getElementById('stat-match').textContent =
          match.score >= state.threshold ? match.name : 'Unknown';
        document.getElementById('live-score').textContent = matchPct + '%';
      } else {
        document.getElementById('stat-match').textContent = '—';
        document.getElementById('live-score').textContent = '—';
      }
    } else {
      document.getElementById('stat-confidence').textContent = '—';
      document.getElementById('stat-match').textContent = '—';
      document.getElementById('live-score').textContent = '—';
    }

    // Extra stats for body/hand/object
    updateExtraStats(result);
  }

  function updateExtraStats(result) {
    const lines = [];
    if (state.models.body && result.body?.length) {
      lines.push(`🧍 Body keypoints: ${result.body[0]?.keypoints?.length ?? 0}`);
    }
    if (state.models.hand && result.hand?.length) {
      lines.push(`✋ Hands detected: ${result.hand.length}`);
      result.hand.forEach((h, i) => {
        lines.push(`&nbsp;&nbsp;Hand ${i+1}: ${h.handedness ?? ''} (${(h.score * 100).toFixed(0)}%)`);
      });
    }
    if (state.models.object && result.object?.length) {
      lines.push(`📦 Objects: ${result.object.length}`);
      result.object.forEach(o => {
        lines.push(`&nbsp;&nbsp;${o.label} (${(o.score * 100).toFixed(0)}%)`);
      });
    }
    if (state.models.gesture && result.gesture?.length) {
      lines.push(`🤙 Gestures: ${result.gesture.map(g => g.gesture).join(', ')}`);
    }

    const extra = document.getElementById('extra-stats');
    const content = document.getElementById('extra-stats-content');
    if (lines.length > 0) {
      extra.style.display = 'block';
      content.innerHTML = lines.join('<br>');
    } else {
      extra.style.display = 'none';
    }
  }

  function findBestMatch(embedding) {
    let best = {
      name: 'Unknown',
      score: 0,
      id: null
    };
    for (const desc of getAllDescriptors()) {
      try {
        const score = state.human.match.similarity(embedding, desc.descriptor);
        if (score > best.score) best = {
          name: desc.label,
          score,
          id: desc.id
        };
      } catch (_) {}
    }
    return best;
  }

  function drawResults(result) {
    const ctx = DOM.canvas.getContext('2d');
    ctx.clearRect(0, 0, DOM.canvas.width, DOM.canvas.height);

    // Hitung scale factor antara resolusi native vs display
    const scaleX = DOM.canvas.width / (DOM.video.videoWidth || 640);
    const scaleY = DOM.canvas.height / (DOM.video.videoHeight || 480);

    // FACE
    (result.face || []).forEach(face => {
      if (state.config.drawBox && face.box) {
        const [bx, by, bw, bh] = face.box;
        const isMatch = face.embedding && (() => {
          const m = findBestMatch(face.embedding);
          return m.score >= state.threshold;
        })();

        ctx.strokeStyle = isMatch ? '#00ff41' : '#ff0041';
        ctx.lineWidth = 2;
        ctx.strokeRect(bx * scaleX, by * scaleY, bw * scaleX, bh * scaleY);
      }

      if (state.config.drawLandmarks && face.mesh) {
        ctx.fillStyle = 'rgba(0,255,65,0.5)';
        face.mesh.forEach(([x, y]) => {
          ctx.beginPath();
          ctx.arc(x * scaleX, y * scaleY, 1.5, 0, Math.PI * 2);
          ctx.fill();
        });
      }

      if (state.config.drawLabels && face.embedding && face.box) {
        const [bx, by, , bh] = face.box;
        const match = findBestMatch(face.embedding);
        const label = match.score >= state.threshold ?
          `${match.name} ${(match.score * 100).toFixed(0)}%` :
          `Unknown ${(match.score * 100).toFixed(0)}%`;

        const px = bx * scaleX;
        const py = by * scaleY;

        ctx.font = 'bold 12px Courier New';
        const tw = ctx.measureText(label).width;
        ctx.fillStyle = match.score >= state.threshold ?
          'rgba(0,255,65,0.85)' : 'rgba(255,0,65,0.85)';
        ctx.fillRect(px, py - 20, tw + 10, 18);
        ctx.fillStyle = '#000';
        ctx.fillText(label, px + 5, py - 6);
      }

      if (state.config.drawEmotion && face.emotion?.length && face.box) {
        const [bx, by, , bh] = face.box;
        const top = face.emotion.reduce((a, b) => a.score > b.score ? a : b);
        ctx.fillStyle = 'rgba(0,170,255,0.9)';
        ctx.font = '11px Courier New';
        ctx.fillText(
          `${top.emotion} ${(top.score * 100).toFixed(0)}%`,
          bx * scaleX,
          (by + bh) * scaleY + 14
        );
      }
    });

    // HAND
    if (state.models.hand) {
      (result.hand || []).forEach(hand => {
        if (hand.box) {
          const [bx, by, bw, bh] = hand.box;
          ctx.strokeStyle = '#ffaa00';
          ctx.lineWidth = 2;
          ctx.strokeRect(bx * scaleX, by * scaleY, bw * scaleX, bh * scaleY);
        }
        if (hand.landmarks) {
          ctx.fillStyle = '#ffaa00';
          hand.landmarks.forEach(([x, y]) => {
            ctx.beginPath();
            ctx.arc(x * scaleX, y * scaleY, 3, 0, Math.PI * 2);
            ctx.fill();
          });
        }
      });
    }

    // BODY
    if (state.models.body) {
      (result.body || []).forEach(body => {
        if (!body.keypoints) return;
        ctx.fillStyle = '#ff44ff';
        body.keypoints.forEach(kp => {
          if (kp.score > 0.3) {
            ctx.beginPath();
            ctx.arc(kp.position[0] * scaleX, kp.position[1] * scaleY, 4, 0, Math.PI * 2);
            ctx.fill();
          }
        });
      });
    }
  }

  function updateFPS(frameMs) {
    state.stats.fpsHistory.push(frameMs);
    if (state.stats.fpsHistory.length > 30) state.stats.fpsHistory.shift();
    const avg = state.stats.fpsHistory.reduce((a, b) => a + b, 0) / state.stats.fpsHistory.length;
    document.getElementById('stat-fps').textContent = Math.round(1000 / avg);
  }

  // ==================== DESCRIPTORS ====================
  function getAllDescriptors() {
    return [
      ...state.loadedDescriptors.map(d => ({
        ...d,
        source: 'db'
      })),
      ...state.sessionDescriptors.map(d => ({
        ...d,
        source: 'session'
      })),
    ];
  }

  async function loadDescriptor() {
    const id = DOM.pegawaiSelect.value;
    if (!id) {
      showToast('Pilih pengguna dulu!', 'error');
      return;
    }

    addLog('Loading descriptors...', 'info');

    try {
      const res = await fetch(`<?= base_url('lab/load-descriptor/') ?>${id}`);
      const data = await res.json();

      if (data.success) {
        // Remove existing DB descriptors from same pegawai, then add new
        state.loadedDescriptors = state.loadedDescriptors.filter(d => d.idPegawai !== id);
        data.descriptors.forEach(d => state.loadedDescriptors.push({
          ...d,
          idPegawai: id
        }));
        updateDescriptorList();
        addLog(`✅ ${data.descriptors.length} descriptors loaded`, 'success');
        showToast(`${data.descriptors.length} descriptors loaded`, 'success');
      }
    } catch (err) {
      addLog('❌ Load error: ' + err.message, 'error');
      showToast('Load failed', 'error');
    }
  }

  async function enrollCurrentFace() {
    if (!state.human) {
      showToast('Human.js not ready', 'error');
      return;
    }

    const result = await state.human.detect(DOM.video);
    const faces = result.face || [];

    if (faces.length === 0) {
      showToast('No face detected', 'error');
      return;
    }
    if (faces.length > 1) {
      showToast('Multiple faces — use 1 face only', 'error');
      return;
    }
    if (!faces[0].embedding) {
      showToast('No embedding', 'error');
      return;
    }

    const label = `Session_${new Date().toLocaleTimeString()}`;

    try {
      const res = await fetch('<?= base_url('lab/save-session-descriptor') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          label,
          descriptor: faces[0].embedding,
          session_id: 'default'
        }),
      });
      const data = await res.json();

      if (data.success) {
        state.sessionDescriptors.push({
          label,
          descriptor: faces[0].embedding,
          id: Date.now()
        });
        updateDescriptorList();
        addLog(`✅ Enrolled: ${label}`, 'success');
        showToast(`Enrolled: ${label}`, 'success');
      }
    } catch (err) {
      addLog('❌ Enroll error: ' + err.message, 'error');
    }
  }

  function deleteDescriptor(id, source) {
    if (source === 'db') {
      state.loadedDescriptors = state.loadedDescriptors.filter(d => String(d.id) !== String(id));
    } else {
      state.sessionDescriptors = state.sessionDescriptors.filter(d => String(d.id) !== String(id));
    }
    updateDescriptorList();
    addLog(`🗑️ Descriptor #${id} deleted`, 'warning');
    showToast('Descriptor deleted', 'warning');
  }

  async function clearSession() {
    state.sessionDescriptors = [];
    try {
      await fetch('<?= base_url('lab/clear-session') ?>?session_id=default', {
        method: 'DELETE'
      });
    } catch (_) {}
    updateDescriptorList();
    addLog('✅ Session descriptors cleared', 'success');
    showToast('Session cleared', 'success');
  }

  function clearAll() {
    state.loadedDescriptors = [];
    state.sessionDescriptors = [];
    clearSession();
    updateDescriptorList();
    addLog('💣 All descriptors cleared', 'warning');
    showToast('All cleared', 'warning');
  }

  function filterDescriptors(filter) {
    state.currentFilter = filter;
    updateDescriptorList();
  }

  function showDescriptorDetail(desc) {
    const existing = document.getElementById('desc-detail-popup');
    if (existing) existing.remove();

    const vectorPreview = Array.isArray(desc.descriptor) ?
      desc.descriptor.slice(0, 16).map(v => v.toFixed(4)).join(', ') + ' ...' :
      'N/A';

    const popup = document.createElement('div');
    popup.id = 'desc-detail-popup';
    popup.className = 'desc-detail-popup';
    popup.innerHTML = `
      <div class="desc-detail-box">
        <h3>📊 Descriptor Info</h3>
        <div class="desc-detail-row"><span class="key">Label</span><span class="val">${desc.label}</span></div>
        <div class="desc-detail-row"><span class="key">Source</span><span class="val">${desc.source === 'db' ? '💾 Database' : '🔥 Session'}</span></div>
        <div class="desc-detail-row"><span class="key">ID</span><span class="val">${desc.id ?? '—'}</span></div>
        <div class="desc-detail-row"><span class="key">Vector length</span><span class="val">${Array.isArray(desc.descriptor) ? desc.descriptor.length : '—'}</span></div>
        <div class="desc-detail-row"><span class="key">Created</span><span class="val">${desc.created_at ?? '—'}</span></div>
        <div class="desc-vector-preview">${vectorPreview}</div>
        <button class="btn-lab" style="margin-top:14px;" onclick="document.getElementById('desc-detail-popup').remove()">✕ Close</button>
      </div>
    `;
    popup.addEventListener('click', (e) => {
      if (e.target === popup) popup.remove();
    });
    document.body.appendChild(popup);
  }

  function updateDescriptorList() {
    const all = [
      ...state.loadedDescriptors.map(d => ({
        ...d,
        source: 'db'
      })),
      ...state.sessionDescriptors.map(d => ({
        ...d,
        source: 'session'
      })),
    ];

    const filtered = state.currentFilter === 'all' ? all :
      all.filter(d => d.source === state.currentFilter);

    document.getElementById('desc-count-badge').textContent = all.length;

    if (filtered.length === 0) {
      DOM.descriptorList.innerHTML = `
        <div class="empty-state">
          <div class="empty-icon">📭</div>
          <div class="empty-text">No descriptors</div>
        </div>`;
      return;
    }

    DOM.descriptorList.innerHTML = filtered.map(d => `
      <div class="descriptor-item type-${d.source}">
        <div class="descriptor-info">
          <div class="descriptor-label">${d.label}</div>
          <div class="descriptor-meta">
            <span class="desc-badge ${d.source}">${d.source === 'db' ? '💾 DB' : '🔥 SESSION'}</span>
            ${d.created_at ? `<span>${d.created_at.slice(0,10)}</span>` : ''}
          </div>
        </div>
        <div class="desc-actions">
          <button class="desc-btn info-btn" title="Info" onclick='LabFace.showDescriptorDetail(${JSON.stringify(d)})'>ℹ</button>
          <button class="desc-btn del" title="Delete" onclick="LabFace.deleteDescriptor('${d.id}', '${d.source}')">✕</button>
        </div>
      </div>
    `).join('');
  }

  // ==================== STATUS / UI HELPERS ====================
  function setStatusDot(id, state_) {
    const el = document.getElementById(`status-${id}`);
    if (el) {
      el.className = 'lab-status-dot ' + state_;
    }
  }

  function setStatusText(id, text) {
    const el = document.getElementById(`status-${id}-text`);
    if (el) el.textContent = text;
  }

  function addLog(msg, type = 'info') {
    const entry = document.createElement('div');
    entry.className = `log-entry ${type}`;
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
    DOM.log.insertBefore(entry, DOM.log.firstChild);
    while (DOM.log.children.length > 60) DOM.log.removeChild(DOM.log.lastChild);
  }

  let toastTimer = null;

  function showToast(msg, type = 'success') {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = `toast ${type === 'error' ? 'toast-error' : type === 'warning' ? 'toast-warning' : ''}`;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => {
      t.style.animation = 'toastOut 0.3s ease forwards';
      setTimeout(() => t.remove(), 300);
    }, 2500);
  }

  // ==================== PUBLIC ====================
  return {
    init,
    loadDescriptor,
    enrollCurrentFace,
    clearSession,
    clearAll,
    deleteDescriptor,
    showDescriptorDetail,
    filterDescriptors,
    toggleModel,
    reloadModels,
  };

})();

document.addEventListener('DOMContentLoaded', () => LabFace.init());
</script>

<?= $this->endSection() ?>