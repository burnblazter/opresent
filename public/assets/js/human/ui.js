// ==================== FACE UI MODULE ====================
const FaceUI = (function () {
  "use strict"

  const DOM_CACHE = {
    video: null,
    canvas: null,
    button: null,
    btnText: null,
    statusDiv: null,
    messageDiv: null,
    detailsDiv: null,
    progressFill: null,
    faceVerifiedInput: null,
  }

  let previousValues = {
    statusMessage: "",
    statusType: "",
    progressPercentage: -1,
    statusDetails: "",
    buttonDisabled: null,
    buttonText: "",
    webcamFaces: -1,
    webcamAge: "",
    webcamGender: "",
    webcamEmotion: "",
  }

  // ==================== AUTO CAPTURE STATE ====================
  let autoCaptureState = {
    enabled: false,
    startTime: null,
    duration: 3500,
    interval: null,
    countdown: 5,
  }

  // ==================== DOM INITIALIZATION ====================
  function initDOMCache() {
    DOM_CACHE.video =
      document.getElementById("my_camera") ||
      document.getElementById("webcam-video")
    DOM_CACHE.canvas = document.getElementById("canvas")
    DOM_CACHE.button =
      document.getElementById("ambil-foto") ||
      document.getElementById("btn-capture-webcam")
    DOM_CACHE.btnText = document.getElementById("btn-text")
    DOM_CACHE.statusDiv = document.getElementById("face-status")
    DOM_CACHE.messageDiv = document.getElementById("face-message")
    DOM_CACHE.detailsDiv = document.getElementById("face-details")
    DOM_CACHE.progressFill = document.getElementById("movement-progress-fill")
    DOM_CACHE.faceVerifiedInput = document.getElementById("face-verified")
  }

  // ==================== STATUS UPDATES ====================
  function updateStatus(message, type = "info", details = "") {
    if (
      message === previousValues.statusMessage &&
      type === previousValues.statusType &&
      details === previousValues.statusDetails
    ) {
      return
    }

    previousValues.statusMessage = message
    previousValues.statusType = type
    previousValues.statusDetails = details

    if (!DOM_CACHE.statusDiv) return

    DOM_CACHE.statusDiv.className = `alert alert-${type}`
    DOM_CACHE.statusDiv.style.display = "block"
    DOM_CACHE.messageDiv.innerHTML = message

    if (details && DOM_CACHE.detailsDiv) {
      DOM_CACHE.detailsDiv.innerHTML =
        details +
        ' <a href="javascript:void(0)" onclick="showAITransparency()" style="font-size: 12px;">🤖 Detail AI</a>'
      DOM_CACHE.detailsDiv.style.display = "block"
    } else if (DOM_CACHE.detailsDiv) {
      DOM_CACHE.detailsDiv.style.display = "none"
    }
  }

  function updateProgressBar(percentage) {
    if (Math.abs(percentage - previousValues.progressPercentage) < 2) return
    previousValues.progressPercentage = percentage

    if (DOM_CACHE.progressFill) {
      DOM_CACHE.progressFill.style.width = percentage + "%"
    }
  }

  function updateProgressIcon(id, status) {
    const icon = document.getElementById(id)
    if (!icon) return

    icon.className = "progress-item-icon"

    if (status === "completed") {
      icon.textContent = "✅"
      icon.classList.add("completed")
    } else if (status === "active") {
      icon.textContent = "⏳"
      icon.classList.add("active")
    } else {
      icon.textContent = "⏳"
      icon.classList.add("pending")
    }
  }

  function updateWebcamStats(faces, age, gender, emotion) {
    if (previousValues.webcamFaces !== faces) {
      const el = document.getElementById("stat-faces")
      if (el) el.innerText = faces
      previousValues.webcamFaces = faces
    }

    if (previousValues.webcamAge !== age) {
      const el = document.getElementById("stat-age")
      if (el) el.innerText = age
      previousValues.webcamAge = age
    }

    if (previousValues.webcamGender !== gender) {
      const el = document.getElementById("stat-gender")
      if (el) el.innerText = gender
      previousValues.webcamGender = gender
    }

    if (previousValues.webcamEmotion !== emotion) {
      const el = document.getElementById("stat-emotion")
      if (el) el.innerText = emotion
      previousValues.webcamEmotion = emotion
    }
  }

  // ==================== BUTTON STATE ====================
  function checkButtonState(state) {
    if (!DOM_CACHE.button || !DOM_CACHE.btnText) return

    const video = DOM_CACHE.video

    if (
      state.stream &&
      video.readyState === 4 &&
      state.isModelLoaded &&
      state.isFaceRegistered
    ) {
      if (previousValues.buttonDisabled !== false) {
        DOM_CACHE.button.disabled = false
        previousValues.buttonDisabled = false
      }

      const newText = "Ambil Gambar & Verifikasi"
      if (previousValues.buttonText !== newText) {
        DOM_CACHE.btnText.innerText = newText
        previousValues.buttonText = newText
      }

      updateStatus("✅ Sistem siap! Ikuti instruksi verifikasi.", "success")
    } else if (!state.isFaceRegistered && state.isModelLoaded) {
      DOM_CACHE.button.disabled = true
      DOM_CACHE.btnText.innerText = "Wajah Tidak Terdaftar"
    }
  }

  // ==================== AUTO CAPTURE ====================
  function startAutoCapture(state) {
    if (!autoCaptureState.enabled) {
      autoCaptureState.enabled = true
      autoCaptureState.verifiedStartTime = Date.now()
      autoCaptureState.countdown = 3

      startCountdown(state)
    }
  }

  function stopAutoCapture() {
    autoCaptureState.enabled = false
    autoCaptureState.startTime = null
    autoCaptureState.progress = 0

    if (autoCaptureState.interval) {
      clearInterval(autoCaptureState.interval)
      autoCaptureState.interval = null
    }

    if (DOM_CACHE.button) {
      DOM_CACHE.button.style.transition = "all 0.5s ease-out"
      DOM_CACHE.button.style.setProperty("--slide-x", "-100%")
      DOM_CACHE.button.style.borderColor = "#1e3a8a"
      DOM_CACHE.button.style.transform = "none"

      setTimeout(() => {
        if (DOM_CACHE.button) {
          DOM_CACHE.button.style.transition = "all 0.2s ease"
        }
      }, 500)
    }
  }

  function startCountdown(state) {
    if (autoCaptureState.interval) {
      clearInterval(autoCaptureState.interval)
    }

    autoCaptureState.startTime = Date.now()
    autoCaptureState.progress = 0

    if (DOM_CACHE.button) {
      DOM_CACHE.button.style.setProperty("--slide-x", "-100%")

      setTimeout(() => {
        const pseudoStyle = document.createElement("style")
        pseudoStyle.id = "auto-capture-style"
        pseudoStyle.innerHTML = `
        #ambil-foto::before {
          transition: transform 0.05s linear !important;
        }
      `
        document.head.appendChild(pseudoStyle)
      }, 50)
    }

    autoCaptureState.interval = setInterval(() => {
      const elapsedTime = Date.now() - autoCaptureState.startTime
      const percentage = Math.min(
        (elapsedTime / autoCaptureState.duration) * 100,
        100,
      )

      autoCaptureState.progress = percentage

      if (DOM_CACHE.button) {
        const translateX = -100 + percentage
        DOM_CACHE.button.style.setProperty("--slide-x", `${translateX}%`)
        DOM_CACHE.button.style.borderColor = "#dda518"
      }

      if (elapsedTime >= autoCaptureState.duration) {
        clearInterval(autoCaptureState.interval)
        autoCaptureState.interval = null

        const styleEl = document.getElementById("auto-capture-style")
        if (styleEl) styleEl.remove()

        triggerCapture(state)
      }
    }, 50)
  }

  function checkAutoCapture(state) {
    const faceVerified = DOM_CACHE.faceVerifiedInput?.value === "true"

    if (faceVerified && !autoCaptureState.enabled && !state.isVerifying) {
      startAutoCapture(state)
    } else if (!faceVerified && autoCaptureState.enabled) {
      stopAutoCapture()
    }
  }

  function triggerCapture(state) {
    if (!DOM_CACHE.button) return

    DOM_CACHE.button.style.background = "#dda518"
    DOM_CACHE.btnText.innerText = "📸 JEPRET!"

    setTimeout(() => {
      DOM_CACHE.button.click()
    }, 150)
  }

  // ==================== EVENT HANDLERS ====================
  function setupButtonListener(state) {
    if (!DOM_CACHE.button) return

    DOM_CACHE.button.addEventListener("click", function () {
      // Stop auto capture jika manual click
      stopAutoCapture()

      if (!state.isFaceRegistered) {
        Swal.fire({
          icon: "error",
          title: "Akses Ditolak",
          text: "Wajah Anda belum terdaftar di sistem.",
          showCancelButton: true,
          confirmButtonText: "Request Pendaftaran Wajah",
          confirmButtonColor: "#1e3a8a",
          cancelButtonText: "Batal",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = state.config.userData.faceEnrollmentUrl
          }
        })
        return
      }

      const headMovementState = FaceVerification.getHeadMovementState()

      if (
        !headMovementState.completed &&
        state.config.mode.startsWith("presensi")
      ) {
        Swal.fire({
          icon: "warning",
          title: "Verifikasi Belum Selesai",
          text: "Selesaikan verifikasi gerakan kepala terlebih dahulu!",
          confirmButtonColor: "#1e3a8a",
        })
        return
      }

      const faceVerified = DOM_CACHE.faceVerifiedInput?.value

      if (faceVerified !== "true" && state.config.mode.startsWith("presensi")) {
        Swal.fire({
          icon: "error",
          title: "Wajah Belum Terverifikasi",
          html: `
            <p>Wajah belum terverifikasi dengan baik.</p>
            <p><strong>Tips:</strong></p>
            <ul style="text-align: left; padding-left: 20px;">
              <li>Pastikan wajah terlihat jelas</li>
              <li>Pencahayaan cukup</li>
              <li>Posisi tegak menghadap kamera</li>
            </ul>
          `,
          confirmButtonColor: "#1e3a8a",
        })
        return
      }

      state.isVerifying = true
      FaceRecognition.stopDetectionLoop()

      DOM_CACHE.button.disabled = true
      DOM_CACHE.btnText.innerText = "Mengambil foto..."

      try {
        const imageData = FaceRecognition.captureImage()
        document.querySelector(".image-tag").value = imageData
        document.getElementById("my_result").innerHTML =
          '<img src="' +
          imageData +
          '" style="max-width: 100%; border-radius: 8px; border: 2px solid #28a745;"/>'

        const funData = {
          age: state.currentAge,
          emotion: state.currentEmotion,
          similarity: state.currentSimilarity,
          date_recorded: new Date().toISOString().split("T")[0],
          type: state.config.mode === "presensi_masuk" ? "in" : "out",
        }

        localStorage.setItem(
          `daily_ai_mood_${funData.type}`,
          JSON.stringify(funData),
        )

        Swal.fire({
          icon: "success",
          title: "Foto Berhasil Diambil",
          html: `<p>Mengirim data presensi...</p>`,
          timer: 1500,
          showConfirmButton: false,
          didClose: () => {
            document.getElementById("presensi-form").submit()
          },
        })
      } catch (error) {
        console.error("Error capturing image:", error)
        Swal.fire({
          icon: "error",
          title: "Gagal Mengambil Foto",
          text: error.message,
          confirmButtonColor: "#1e3a8a",
        })

        state.isVerifying = false
        DOM_CACHE.button.disabled = false
        DOM_CACHE.btnText.innerText = "Ambil Gambar & Verifikasi"
        FaceRecognition.start()
      }
    })
  }

  // ==================== CLEANUP ====================
  function setupCleanup(state) {
    window.addEventListener("beforeunload", () => {
      stopAutoCapture()
      FaceRecognition.stopDetectionLoop()

      if (state.stream) {
        state.stream.getTracks().forEach((track) => track.stop())
        state.stream = null
      }

      state.faceDatabase = null
      console.log("🧹 Cleanup completed")
    })

    document.addEventListener("visibilitychange", function () {
      if (document.hidden) {
        stopAutoCapture()
        console.log("⏸️ Tab hidden - detection paused")
      } else {
        console.log("▶️ Tab visible - detection resumed")
      }
    })
  }

  // ==================== LEAFLET MAP INITIALIZATION ====================
  function initLeafletMap(config) {
    if (!document.getElementById("map")) return

    const latitude_kantor = config.map?.latitude_kantor || 0
    const longitude_kantor = config.map?.longitude_kantor || 0
    const latitude_pegawai = config.map?.latitude_pegawai || 0
    const longitude_pegawai = config.map?.longitude_pegawai || 0
    const radius = config.map?.radius || 100

    function getTileLayerUrl() {
      const isDark =
        document.documentElement.getAttribute("data-bs-theme") === "dark" ||
        document.documentElement.getAttribute("data-darkreader-scheme") ===
          "dark"

      return isDark
        ? "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
        : "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
    }

    var map = L.map("map").setView([latitude_kantor, longitude_kantor], 15)

    var tileLayer = L.tileLayer(getTileLayerUrl(), {
      maxZoom: 19,
      attribution: "© OpenStreetMap",
    }).addTo(map)

    var userIcon = L.divIcon({
      className: "custom-marker",
      html: '<div style="background-color: #dc3545; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>',
      iconSize: [20, 20],
      iconAnchor: [10, 10],
    })

    var officeIcon = L.divIcon({
      className: "custom-marker",
      html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>',
      iconSize: [20, 20],
      iconAnchor: [10, 10],
    })

    L.marker([latitude_pegawai, longitude_pegawai], { icon: userIcon })
      .addTo(map)
      .bindPopup("<b>📍 Lokasi Anda</b>")

    L.marker([latitude_kantor, longitude_kantor], { icon: officeIcon })
      .addTo(map)
      .bindPopup("<b>🏫 Lokasi Sekolah</b>")

    L.circle([latitude_kantor, longitude_kantor], {
      color: "#198754",
      fillColor: "#198754",
      fillOpacity: 0.15,
      radius: radius,
    }).addTo(map)

    var group = new L.featureGroup([
      L.marker([latitude_pegawai, longitude_pegawai]),
      L.marker([latitude_kantor, longitude_kantor]),
    ])
    map.fitBounds(group.getBounds().pad(0.1))

    // Theme observer
    const themeObserver = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === "attributes") {
          if (
            ["data-bs-theme", "data-darkreader-scheme", "class"].includes(
              mutation.attributeName,
            )
          ) {
            updateMapTheme()
          }
        }
      })
    })

    themeObserver.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ["data-bs-theme", "data-darkreader-scheme", "class"],
    })

    window
      .matchMedia("(prefers-color-scheme: dark)")
      .addEventListener("change", (event) => {
        if (!document.documentElement.getAttribute("data-bs-theme")) {
          updateMapTheme()
        }
      })

    function updateMapTheme() {
      if (tileLayer) {
        map.removeLayer(tileLayer)
      }

      const newUrl = getTileLayerUrl()

      tileLayer = L.tileLayer(newUrl, {
        maxZoom: 19,
        attribution: "© OpenStreetMap",
      }).addTo(map)
    }
  }

  // ==================== PUBLIC API ====================
  return {
    DOM_CACHE,
    initDOMCache,
    updateStatus,
    updateProgressBar,
    updateProgressIcon,
    updateWebcamStats,
    checkButtonState,
    setupButtonListener,
    setupCleanup,
    checkAutoCapture,
    stopAutoCapture,
    initLeafletMap,
  }
})()

// ==================== AUTO-INIT ====================
document.addEventListener("DOMContentLoaded", function () {
  FaceUI.initDOMCache()

  const state = FaceRecognition.getState()
  FaceUI.setupButtonListener(state)
  FaceUI.setupCleanup(state)

  FaceRecognition.start()
})

// ==================== GLOBAL HELPERS ====================
function showAITransparency() {
  const state = FaceRecognition.getState()
  const similarity = (state.currentSimilarity * 100).toFixed(1)
  const registeredFaces = state.faceDatabase.length

  let status = ""
  let statusColor = ""

  if (state.currentSimilarity >= 0.75) {
    status = "Sangat Cocok ✅"
    statusColor = "#28a745"
  } else if (state.currentSimilarity >= 0.62) {
    status = "Cocok ✓"
    statusColor = "#ffc107"
  } else {
    status = "Tidak Cocok ❌"
    statusColor = "#dc3545"
  }

  Swal.fire({
    title: "🤖 Detail AI",
    html: `
      <div style="text-align: left;">
        <h5 style="margin-top: 0; color: #1e3a8a;">📊 Hasil Verifikasi Wajah</h5>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
          <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: ${statusColor}; font-weight: bold;">${status}</span></p>
          <p style="margin: 5px 0;"><strong>Tingkat Kecocokan:</strong> ${similarity}%</p>
          <div style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0;">
            <div style="background: ${statusColor}; height: 100%; width: ${similarity}%;"></div>
          </div>
        </div>
        
        <h5 style="color: #1e3a8a;">📸 Data Terdeteksi</h5>
        <ul style="font-size: 14px; line-height: 1.6;">
          <li><strong>Usia Estimasi:</strong> ~${state.currentAge} tahun</li>
          <li><strong>Emosi:</strong> ${FaceRecognition.getEmotionText(state.currentEmotion)}</li>
          <li><strong>Wajah Terdaftar:</strong> ${registeredFaces} data</li>
        </ul>
      </div>
    `,
    confirmButtonText: "Mengerti",
    confirmButtonColor: "#1e3a8a",
    width: "650px",
  })
}
