/**
 * PresenSi — MFA Playground Engine
 */
;(function () {
  ;("use strict")

  // ─────────────────────────────────────────────
  // CONFIG & CONSTANTS
  // ─────────────────────────────────────────────
  const CFG = window.PG_CONFIG || {}
  const THRESHOLD = 0.62
  const THROTTLE_MS = 200
  const SUBMIT_HOLD = 2800 // ms progress bar sebelum submit

  const EMOTION_MAP = {
    happy: "Senang",
    sad: "Sedih",
    neutral: "Netral",
    angry: "Marah",
    fearful: "Takut",
    surprised: "Terkejut",
    disgusted: "Jijik",
  }

  // ─────────────────────────────────────────────
  // STATE
  // ─────────────────────────────────────────────
  const S = {
    human: null,
    stream: null,
    rafId: null,
    lastTick: 0,
    modelReady: false,
    registering: false,

    faceDB: [],
    currentDesc: null,
    currentScore: 0,
    currentAge: 0,
    currentEmotion: "neutral",
    matchedId: null,
    matchedName: null,

    faceOk: false,
    gpsOk: false,
    timeOk: false,

    submitTimer: null,
    submitProgress: 0,
    submitting: false,

    phase: "idle",
    // idle | scanning | face_ok | all_ok | submitting | done
  }

  // ─────────────────────────────────────────────
  // DOM REFS
  // ─────────────────────────────────────────────
  const $ = (id) => document.getElementById(id)
  const D = {
    video: $("pg-video"),
    overlay: $("pg-overlay"),
    canvas: $("pg-canvas"),
    scanline: $("cam-scanline"),
    camInit: $("cam-init"),
    camInitText: $("cam-init-text"),
    camFlash: $("cam-flash"),

    banner: $("state-banner"),
    bannerIcon: $("banner-icon"),
    bannerText: $("banner-text"),

    sFaces: $("s-faces"),
    sAge: $("s-age"),
    sEmotion: $("s-emotion"),
    sScore: $("s-score"),

    badgeFace: $("badge-face"),
    badgeGps: $("badge-gps"),
    badgeTime: $("badge-time"),

    recBox: $("rec-box"),
    recName: $("rec-name"),
    recSub: $("rec-sub"),
    confFill: $("conf-fill"),

    btnSubmit: $("btn-submit"),
    submitText: $("submit-btn-text"),
    submitProg: $("submit-progress"),

    regName: $("reg-name"),
    btnRegister: $("btn-register"),
    btnClear: $("btn-clear"),
    regStatus: $("reg-status"),
    faceList: $("face-list"),
    faceEmpty: $("face-empty"),

    gpsLat: $("gps-lat"),
    gpsLng: $("gps-lng"),
    gpsDot: $("gps-dot"),
    gpsText: $("gps-status-text"),
    btnGpsAuto: $("btn-gps-auto"),

    timeInput: $("time-input"),
    timeLimit: $("time-limit"),
    timeChip: $("time-chip"),
    timeDiff: $("time-diff-text"),
    btnTimeNow: $("btn-time-now"),

    stepFace: $("step-face"),
    stepGps: $("step-gps"),
    stepTime: $("step-time"),
    stepDone: $("step-done"),

    receipt: $("receipt-overlay"),
    rName: $("r-name"),
    rScore: $("r-score"),
    rTime: $("r-time"),
    rStatus: $("r-status"),
    rCoords: $("r-coords"),
    receiptId: $("receipt-id"),
    btnRcClose: $("btn-receipt-close"),

    navClock: $("nav-clock"),
    camLabelTop: $("cam-label-top"),
  }

  // ─────────────────────────────────────────────
  // UTILITIES
  // ─────────────────────────────────────────────
  function esc(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
  }

  function padZ(n) {
    return String(n).padStart(2, "0")
  }

  function nowHHMM() {
    const d = new Date()
    return padZ(d.getHours()) + ":" + padZ(d.getMinutes())
  }

  function clockTick() {
    const d = new Date()
    const str =
      padZ(d.getHours()) +
      ":" +
      padZ(d.getMinutes()) +
      ":" +
      padZ(d.getSeconds())
    D.navClock.textContent = str
    D.navClock.style.display = "inline"
  }

  // ─────────────────────────────────────────────
  // BANNER
  // ─────────────────────────────────────────────
  function setBanner(icon, text, type = "idle") {
    D.banner.className = "state-banner " + type
    D.bannerIcon.textContent = icon
    D.bannerText.textContent = text
  }

  // ─────────────────────────────────────────────
  // MFA STEP INDICATOR
  // ─────────────────────────────────────────────
  const STEPS = ["face", "gps", "time", "done"]

  function setSteps(activeStep) {
    const activeIdx = STEPS.indexOf(activeStep)
    STEPS.forEach((s, i) => {
      const el = $("step-" + s)
      if (!el) return
      if (i < activeIdx) el.className = "mfa-step done"
      else if (i === activeIdx) el.className = "mfa-step active"
      else el.className = "mfa-step"
    })
  }

  // ─────────────────────────────────────────────
  // MFA BADGE
  // ─────────────────────────────────────────────
  function setBadge(name, state) {
    // state: ok | fail | wait
    const el = D["badge" + name.charAt(0).toUpperCase() + name.slice(1)]
    if (!el) return
    el.className = "mfa-badge " + state
  }

  // ─────────────────────────────────────────────
  // FACE MATCH
  // ─────────────────────────────────────────────
  function findBestMatch(desc) {
    if (!desc || S.faceDB.length === 0)
      return { label: "Unknown", score: 0, id: null }
    let best = { label: "Unknown", score: 0, id: null }
    S.faceDB.forEach((f) => {
      try {
        const sc = S.human.match.similarity(desc, f.descriptor)
        if (sc > best.score) best = { label: f.label, score: sc, id: f.id }
      } catch (_) {}
    })
    return best
  }

  // ─────────────────────────────────────────────
  // GPS HELPERS
  // ─────────────────────────────────────────────
  function getGPS() {
    const lat = parseFloat(D.gpsLat.value)
    const lng = parseFloat(D.gpsLng.value)
    return !isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0)
      ? { lat, lng }
      : null
  }

  function validateGPS() {
    const g = getGPS()
    S.gpsOk = !!g
    if (g) {
      D.gpsDot.className = "gps-dot ok"
      D.gpsText.textContent =
        "Koordinat valid: " + g.lat.toFixed(5) + ", " + g.lng.toFixed(5)
      setBadge("gps", "ok")
    } else {
      D.gpsDot.className = "gps-dot"
      D.gpsText.textContent = "Masukkan koordinat atau klik Auto"
      setBadge("gps", "wait")
    }
    checkAllMFA()
  }

  // ─────────────────────────────────────────────
  // TIME HELPERS
  // ─────────────────────────────────────────────
  function validateTime() {
    const tv = D.timeInput.value // "HH:MM"
    const tl = D.timeLimit.value // "HH:MM"
    if (!tv) {
      D.timeChip.className = "time-status-chip"
      D.timeChip.textContent = "⏳ Belum diatur"
      D.timeDiff.textContent = ""
      S.timeOk = false
      setBadge("time", "wait")
      checkAllMFA()
      return
    }

    const [th, tm] = tv.split(":").map(Number)
    const [lh, lm] = tl.split(":").map(Number)
    const tMins = th * 60 + tm
    const lMins = lh * 60 + lm
    const diff = tMins - lMins

    S.timeOk = true // semua jam valid untuk sandbox

    if (diff <= 0) {
      D.timeChip.className = "time-status-chip valid"
      D.timeChip.textContent = "✅ Tepat Waktu"
      D.timeDiff.textContent =
        diff === 0 ? "Tepat di batas" : Math.abs(diff) + " menit lebih awal"
      setBadge("time", "ok")
    } else if (diff <= 30) {
      D.timeChip.className = "time-status-chip late"
      D.timeChip.textContent = "⚠️ Terlambat"
      D.timeDiff.textContent = diff + " menit terlambat"
      setBadge("time", "ok") // tetap OK untuk sandbox
    } else {
      D.timeChip.className = "time-status-chip out"
      D.timeChip.textContent = "❌ Di Luar Jadwal"
      D.timeDiff.textContent = diff + " menit melebihi batas"
      setBadge("time", "ok") // tetap OK untuk sandbox
    }
    checkAllMFA()
  }

  // ─────────────────────────────────────────────
  // GLOBAL MFA CHECK
  // ─────────────────────────────────────────────
  function checkAllMFA() {
    const all = S.faceOk && S.gpsOk && S.timeOk

    if (all && !S.submitting) {
      D.btnSubmit.disabled = false
      setSteps("done")
      setBanner(
        "🎯",
        "Semua faktor MFA terverifikasi! Klik Presensi Masuk untuk menyelesaikan.",
        "success",
      )
      D.scanline.className = "cam-scanline"
    } else if (!S.submitting) {
      D.btnSubmit.disabled = true
      // Determine which step we're on
      if (!S.faceOk) setSteps("face")
      else if (!S.gpsOk) setSteps("gps")
      else if (!S.timeOk) setSteps("time")
    }
  }

  // ─────────────────────────────────────────────
  // DETECTION LOOP
  // ─────────────────────────────────────────────
  async function detectionLoop(ts) {
    S.rafId = requestAnimationFrame(detectionLoop)
    if (document.hidden) return
    if (ts - S.lastTick < THROTTLE_MS) return
    S.lastTick = ts
    if (!S.modelReady || !D.video.srcObject) return

    try {
      const result = await S.human.detect(D.video)
      const faces = result.face || []
      const count = faces.length

      D.sFaces.textContent = count

      if (count === 0) {
        D.sAge.textContent = "—"
        D.sEmotion.textContent = "—"
        D.sScore.textContent = "—"
        S.currentScore = 0
        S.currentDesc = null
        S.matchedId = null
        S.matchedName = null
        S.faceOk = false
        D.recBox.classList.remove("show")
        setBadge("face", "wait")
        if (!S.submitting) {
          setBanner("👤", "Arahkan wajah Anda ke kamera...", "idle")
          setSteps("face")
          D.scanline.className = "cam-scanline on"
        }
        checkAllMFA()
        return
      }

      const face = faces[0]
      S.currentAge = Math.round(face.age || 0)
      S.currentEmotion = face.emotion?.[0]?.emotion || "neutral"
      const emoText = EMOTION_MAP[S.currentEmotion] || "Netral"

      D.sAge.textContent = S.currentAge + " thn"
      D.sEmotion.textContent = emoText

      if (face.embedding && face.embedding.length > 0) {
        S.currentDesc = face.embedding

        if (S.faceDB.length > 0) {
          const match = findBestMatch(S.currentDesc)
          S.currentScore = match.score

          D.sScore.textContent = (S.currentScore * 100).toFixed(1) + "%"
          highlightFace(S.currentScore >= THRESHOLD ? match.id : null)

          if (S.currentScore >= THRESHOLD) {
            S.faceOk = true
            S.matchedId = match.id
            S.matchedName = match.label

            setBadge("face", "ok")
            setSteps("gps")

            D.recBox.classList.add("show")
            D.recName.textContent = match.label
            D.recSub.textContent =
              "Kecocokan: " + (match.score * 100).toFixed(1) + "%"
            D.confFill.style.width = (match.score * 100).toFixed(1) + "%"

            if (!S.submitting) {
              setBanner(
                "✅",
                "Wajah terverifikasi — " +
                  match.label +
                  " (" +
                  (match.score * 100).toFixed(1) +
                  "%). Periksa GPS & Waktu.",
                "success",
              )
              D.scanline.className = "cam-scanline"
            }

            D.camLabelTop.textContent = "✅ " + match.label
          } else {
            S.faceOk = false
            S.matchedId = null
            S.matchedName = null
            setBadge("face", "wait")
            D.recBox.classList.remove("show")
            D.confFill.style.width = (S.currentScore * 100).toFixed(1) + "%"
            if (!S.submitting) {
              setBanner(
                "🔍",
                "Wajah terdeteksi — akurasi " +
                  (S.currentScore * 100).toFixed(1) +
                  "% (butuh ≥ 62%)",
                "warning",
              )
              D.scanline.className = "cam-scanline on"
              setSteps("face")
            }
            D.camLabelTop.textContent = "Face Recognition Engine"
          }
        } else {
          // No registered faces
          S.currentScore = 0
          S.faceOk = false
          D.sScore.textContent = "—"
          D.confFill.style.width = "0%"
          if (!S.submitting) {
            setBanner(
              "👤",
              "Wajah terdeteksi! Daftarkan wajah Anda di panel kanan untuk memulai verifikasi.",
              "info",
            )
            D.scanline.className = "cam-scanline on"
          }
          setBadge("face", "wait")
        }

        D.btnRegister.disabled = !S.modelReady || S.registering
      } else {
        D.sScore.textContent = "—"
      }

      checkAllMFA()
    } catch (err) {
      console.warn("Detection error:", err)
    }
  }

  // ─────────────────────────────────────────────
  // FACE LIST UI
  // ─────────────────────────────────────────────
  function renderFaceList() {
    const empty = S.faceDB.length === 0
    D.faceEmpty.style.display = empty ? "block" : "none"
    D.faceList.style.display = empty ? "none" : "flex"
    D.faceList.style.flexDirection = "column"
    D.faceList.style.gap = "8px"
    D.faceList.innerHTML = ""

    S.faceDB.forEach((f) => {
      const init = (f.label[0] || "?").toUpperCase()
      const div = document.createElement("div")
      div.className = "face-item"
      div.id = "fi-" + f.id
      div.innerHTML = `
        <div class="face-avatar">${init}</div>
        <div class="face-info">
          <div class="face-name">${esc(f.label)}</div>
          <div class="face-meta">Didaftarkan ${f.time}</div>
        </div>
        <div class="face-match-badge">✓ Match</div>
      `
      D.faceList.appendChild(div)
    })
  }

  function highlightFace(id) {
    document
      .querySelectorAll(".face-item")
      .forEach((el) => el.classList.remove("matched"))
    if (id) {
      const el = $("fi-" + id)
      if (el) el.classList.add("matched")
    }
  }

  // ─────────────────────────────────────────────
  // REG STATUS
  // ─────────────────────────────────────────────
  function setRegStatus(text, type) {
    D.regStatus.style.display = text ? "flex" : "none"
    D.regStatus.className = "state-banner " + (type || "idle")
    D.regStatus.textContent = text || ""
  }

  // ─────────────────────────────────────────────
  // REGISTER FACE
  // ─────────────────────────────────────────────
  async function registerFace() {
    if (!S.currentDesc) {
      setRegStatus(
        "⚠️ Pastikan wajah terdeteksi di kamera sebelum mendaftar.",
        "warning",
      )
      return
    }
    const rawName = D.regName.value.trim()
    const name = rawName || "Tamu " + (S.faceDB.length + 1)

    S.registering = true
    D.btnRegister.disabled = true
    setRegStatus("⏳ Menyimpan ke sesi sandbox...", "idle")

    try {
      const fd = new FormData()
      fd.append(CFG.csrfToken, CFG.csrfHash)
      fd.append("descriptor", JSON.stringify(Array.from(S.currentDesc)))
      fd.append("label", name)

      const res = await fetch(CFG.registerUrl, {
        method: "POST",
        body: fd,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      })
      const data = await res.json()

      if (data.success) {
        S.faceDB.push({
          id: data.id,
          label: data.label,
          descriptor: Array.from(S.currentDesc),
          time: new Date().toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
          }),
        })
        renderFaceList()
        setRegStatus(
          '✅ "' +
            data.label +
            '" berhasil didaftarkan! (' +
            data.total +
            "/5)",
          "success",
        )
        D.regName.value = ""

        // Flash effect
        D.camFlash.className = "cam-success-flash flash"
        setTimeout(() => {
          D.camFlash.className = "cam-success-flash"
        }, 700)
      } else {
        setRegStatus("⚠️ " + (data.message || "Gagal mendaftar."), "warning")
      }
    } catch (err) {
      console.error(err)
      setRegStatus("❌ Gagal terhubung ke server.", "danger")
    } finally {
      S.registering = false
      D.btnRegister.disabled = false
    }
  }

  // ─────────────────────────────────────────────
  // CLEAR SESSION
  // ─────────────────────────────────────────────
  async function clearSession() {
    try {
      const fd = new FormData()
      fd.append(CFG.csrfToken, CFG.csrfHash)
      await fetch(CFG.clearUrl, {
        method: "POST",
        body: fd,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      })
    } catch (_) {}
    S.faceDB = []
    S.faceOk = false
    S.matchedId = null
    S.matchedName = null
    renderFaceList()
    setRegStatus("", "idle")
    D.recBox.classList.remove("show")
    D.camLabelTop.textContent = "Face Recognition Engine"
    setBadge("face", "wait")
    checkAllMFA()
    setBanner("🗑️", "Sesi direset. Daftarkan wajah baru untuk memulai.", "idle")
  }

  // ─────────────────────────────────────────────
  // SUBMIT PRESENSI
  // ─────────────────────────────────────────────
  function startSubmit() {
    if (S.submitting) return
    S.submitting = true
    D.btnSubmit.disabled = true

    // Determine time status text
    const tv = D.timeInput.value
    const tl = D.timeLimit.value
    let timeStatus = "Tepat Waktu"
    if (tv && tl) {
      const [th, tm] = tv.split(":").map(Number)
      const [lh, lm] = tl.split(":").map(Number)
      const diff = th * 60 + tm - (lh * 60 + lm)
      if (diff > 30) timeStatus = "Di Luar Jadwal"
      else if (diff > 0) timeStatus = "Terlambat " + diff + " mnt"
      else timeStatus = "Tepat Waktu"
    }

    setBanner("📡", "Mengirim data presensi... harap tunggu.", "info")
    S.submitProgress = 0
    D.submitProg.style.width = "0%"
    D.submitText.textContent = "Memverifikasi MFA..."

    const interval = setInterval(() => {
      S.submitProgress += 100 / (SUBMIT_HOLD / 80)
      D.submitProg.style.width = Math.min(S.submitProgress, 95) + "%"
    }, 80)

    setTimeout(async () => {
      clearInterval(interval)
      D.submitProg.style.width = "100%"

      const gps = getGPS()
      try {
        const fd = new FormData()
        fd.append(CFG.csrfToken, CFG.csrfHash)
        fd.append("name", S.matchedName || "Tamu")
        fd.append("score", S.currentScore)
        fd.append("lat", gps ? gps.lat : 0)
        fd.append("lng", gps ? gps.lng : 0)
        fd.append("time", D.timeInput.value || nowHHMM())
        fd.append("emotion", S.currentEmotion)

        const res = await fetch(CFG.submitUrl, {
          method: "POST",
          body: fd,
          headers: { "X-Requested-With": "XMLHttpRequest" },
        })
        const data = await res.json()

        if (data.success) {
          showReceipt(data, timeStatus)
          D.camFlash.className = "cam-success-flash flash"
          setTimeout(() => {
            D.camFlash.className = "cam-success-flash"
          }, 700)
        } else {
          setBanner("❌", "Gagal submit. Coba lagi.", "danger")
          resetSubmit()
        }
      } catch (err) {
        console.error(err)
        setBanner("❌", "Koneksi gagal. Coba lagi.", "danger")
        resetSubmit()
      }
    }, SUBMIT_HOLD)
  }

  function resetSubmit() {
    S.submitting = false
    S.submitProgress = 0
    D.submitProg.style.width = "0%"
    D.submitText.textContent = "Presensi Masuk (Sandbox)"
    D.btnSubmit.disabled = false
    checkAllMFA()
  }

  // ─────────────────────────────────────────────
  // RECEIPT
  // ─────────────────────────────────────────────
  function showReceipt(data, timeStatus) {
    D.receiptId.textContent = "RECEIPT: " + data.receipt_id
    D.rName.textContent = data.name
    D.rScore.textContent = data.score + "% Cocok"
    D.rTime.textContent = data.time
    D.rStatus.textContent = timeStatus
    D.rCoords.textContent = data.lat.toFixed(5) + ", " + data.lng.toFixed(5)
    D.receipt.classList.add("show")
    setSteps("done")
    setBanner("🎉", "Presensi berhasil dicatat (Sandbox Mode)!", "success")
  }

  // ─────────────────────────────────────────────
  // CAMERA SETUP
  // ─────────────────────────────────────────────
  async function setupCamera() {
    const constraints = [
      {
        video: {
          width: { ideal: 640 },
          height: { ideal: 480 },
          facingMode: "user",
          frameRate: { ideal: 24 },
        },
        audio: false,
      },
      { video: { facingMode: "user" }, audio: false },
    ]
    for (const c of constraints) {
      try {
        S.stream = await navigator.mediaDevices.getUserMedia(c)
        break
      } catch (_) {}
    }
    if (!S.stream) {
      setBanner(
        "❌",
        "Kamera tidak dapat diakses. Periksa izin browser.",
        "danger",
      )
      D.camInitText.innerHTML =
        "❌ Kamera tidak dapat diakses<br><small>Periksa izin browser</small>"
      return false
    }
    D.video.srcObject = S.stream
    await new Promise((res, rej) => {
      D.video.onloadedmetadata = () => D.video.play().then(res).catch(rej)
      setTimeout(() => rej(new Error("Timeout")), 10000)
    })
    D.overlay.width = D.video.videoWidth
    D.overlay.height = D.video.videoHeight
    return true
  }

  // ─────────────────────────────────────────────
  // HUMAN.JS INIT
  // ─────────────────────────────────────────────
  async function initHuman() {
    D.camInitText.innerHTML =
      "Memuat AI engine...<br><small style='opacity:.6'>Human.js/small>"

    S.human = new Human.Human({
      modelBasePath: CFG.modelBasePath,
      backend: "wasm",
      wasm: { enabled: true, simd: true },
      deallocate: true,
      face: {
        enabled: true,
        detector: {
          modelPath: "blazeface.json",
          rotation: true,
          maxDetected: 1,
          skipFrames: 1,
          minConfidence: 0.55,
        },
        mesh: { enabled: true },
        description: { enabled: true },
        emotion: { enabled: true, minConfidence: 0.1 },
        iris: { enabled: false },
        antispoof: { enabled: false },
        liveness: { enabled: false },
      },
      body: { enabled: false },
      hand: { enabled: false },
      object: { enabled: false },
      gesture: { enabled: false },
      segmentation: { enabled: false },
      cacheSensitivity: 0.7,
      filter: { enabled: true, equalization: false },
    })

    try {
      D.camInitText.innerHTML =
        "Mengunduh model...<br><small style='opacity:.6'>Pertama kali ±5-15 detik</small>"
      await S.human.load()
      await S.human.warmup()
    } catch (wasmErr) {
      console.warn("WASM fallback:", wasmErr)
      S.human.config.backend = "webgl"
      await S.human.load()
      await S.human.warmup()
    }

    S.modelReady = true
    D.camInit.classList.add("gone")
    D.scanline.className = "cam-scanline on"
    D.btnRegister.disabled = false
    setBanner("👤", "AI engine siap! Arahkan wajah Anda ke kamera.", "info")
    setSteps("face")
  }

  // ─────────────────────────────────────────────
  // TIME
  // ─────────────────────────────────────────────
  function getTimeString(dateObj) {
    return padZ(dateObj.getHours()) + ":" + padZ(dateObj.getMinutes())
  }

  function formatTime(dateObj) {
    const h = String(dateObj.getHours()).padStart(2, "0")
    const m = String(dateObj.getMinutes()).padStart(2, "0")
    return `${h}:${m}`
  }

  function setTimeAuto() {
    const now = new Date()
    const future = new Date(now.getTime() + 5 * 60000)

    D.timeInput.value = formatTime(now)
    D.timeLimit.value = formatTime(future)
    validateTime()

    const originalText = D.btnTimeNow.innerHTML
    D.btnTimeNow.innerHTML = "✅ Updated!"
    setTimeout(() => (D.btnTimeNow.innerHTML = originalText), 1000)
  }

  // ─────────────────────────────────────────────
  // GET GPS
  // ─────────────────────────────────────────────
  function triggerAutoGPS(retryCount = 0) {
    if (!navigator.geolocation) {
      D.gpsText.textContent = "❌ GPS tidak didukung browser ini."
      return
    }

    D.gpsDot.className = "gps-dot active"
    if (retryCount === 0) {
      D.gpsText.textContent = "📡 Mencari satelit..."
    } else {
      D.gpsText.textContent = "📡 Sinyal lemah, mencoba lagi..."
    }

    const options = {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0,
    }

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        D.gpsLat.value = pos.coords.latitude.toFixed(6)
        D.gpsLng.value = pos.coords.longitude.toFixed(6)
        validateGPS()

        D.gpsText.textContent = `Akurasi: ±${Math.round(pos.coords.accuracy)}m`
      },
      (err) => {
        console.warn(`GPS Error (${err.code}): ${err.message}`)

        if (err.code === 1) {
          D.gpsDot.className = "gps-dot"
          D.gpsText.textContent = "❌ Izin lokasi ditolak user."
          return
        }

        if (err.code === 3 && retryCount < 1) {
          console.log("GPS Timeout, retrying...")
          triggerRobustGPS(retryCount + 1)
          return
        }

        D.gpsLat.value = "-6.175392"
        D.gpsLng.value = "106.827153"
        D.gpsDot.className = "gps-dot"
        D.gpsText.textContent = "⚠️ Gagal lock GPS (Mode Dummy)"
        validateGPS()
      },
      options,
    )
  }

  // ─────────────────────────────────────────────
  // BOOT
  // ─────────────────────────────────────────────
  async function boot() {
    if (
      CFG.savedFaces &&
      Array.isArray(CFG.savedFaces) &&
      CFG.savedFaces.length > 0
    ) {
      S.faceDB = CFG.savedFaces.map((f) => ({
        ...f,
        descriptor: f.descriptor,
        time: f.registered || f.time || formatTime(new Date()),
      }))

      renderFaceList()
      const banner = document.getElementById("reg-status")
      if (banner) {
        banner.style.display = "flex"
        banner.className = "state-banner info"
        banner.textContent = `Memuat ${S.faceDB.length} wajah dari sesi.`
      }
    }
    clockTick()
    setInterval(clockTick, 1000)
    setTimeAuto()
    triggerAutoGPS()

    // ──────────────────────────
    // EVENT LISTENERS
    // ──────────────────────────
    D.btnTimeNow.addEventListener("click", setTimeAuto)

    D.timeInput.addEventListener("input", validateTime)
    D.timeLimit.addEventListener("input", validateTime)

    D.gpsLat.addEventListener("input", validateGPS)
    D.gpsLng.addEventListener("input", validateGPS)
    D.btnGpsAuto.addEventListener("click", () => triggerRobustGPS(0))

    D.btnRegister.addEventListener("click", registerFace)
    D.regName.addEventListener("keydown", (e) => {
      if (e.key === "Enter") registerFace()
    })

    D.btnClear.addEventListener("click", clearSession)
    D.btnSubmit.addEventListener("click", startSubmit)

    D.btnRcClose.addEventListener("click", () => {
      D.receipt.classList.remove("show")
      resetSubmit()
    })

    document.addEventListener("visibilitychange", () => {
      if (!document.hidden && S.modelReady && !S.rafId) {
        S.rafId = requestAnimationFrame(detectionLoop)
      }
    })

    const camOk = await setupCamera()
    if (!camOk) return
    await initHuman()
    S.rafId = requestAnimationFrame(detectionLoop)
  }

  document.addEventListener("DOMContentLoaded", boot)
})()
