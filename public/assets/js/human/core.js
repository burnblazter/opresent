// human/core.js
// ==================== FACE RECOGNITION CORE ====================
const FaceRecognition = (function () {
  "use strict"

  // ==================== STATE ====================
  const state = {
    isModelLoaded: false,
    isVerifying: false,
    faceDatabase: [],
    isFaceRegistered: false,
    currentAge: 0,
    currentEmotion: "neutral",
    currentSimilarity: 0,
    stream: null,
    animationFrameId: null,
    lastDetectionTime: 0,
    config: {},
    human: null,
  }

  const CONSTANTS = {
    DETECTION_THROTTLE_MS: 200,
    EMOTION_MAP: {
      happy: "Senang",
      sad: "Sedih",
      neutral: "Netral",
      angry: "Marah",
      fearful: "Takut",
      surprised: "Terkejut",
      disgusted: "Jijik",
    },
  }

  // ==================== HUMAN.JS SETUP ====================
  function createHumanInstance(modelBasePath) {
    return new Human.Human({
      modelBasePath,
      wasm: { enabled: true, simd: true },
      backend: "wasm",
      deallocate: true,
      face: {
        enabled: true,
        detector: {
          modelPath: "blazeface.json",
          rotation: true,
          maxDetected: 1,
          skipFrames: 1,
          minConfidence: 0.62,
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
  }

  async function initHuman() {
    try {
      const preloadTime = localStorage.getItem("humanjs_preloaded")
      const isCached =
        preloadTime && Date.now() - parseInt(preloadTime) < 1800000

      FaceUI.updateStatus(
        isCached ? "⚡ Menggunakan model cache..." : "Memuat model AI...",
        "info",
      )

      state.human = createHumanInstance(state.config.models.basePath)

      try {
        await state.human.load()
        if (!isCached) await state.human.warmup()
        console.log("✅ Backend:", state.human.tf.getBackend())
      } catch (wasmError) {
        console.warn("⚠️ Fallback ke WebGL...", wasmError)
        state.human.config.backend = "webgl"
        await state.human.load()
        await state.human.warmup()
        FaceUI.updateStatus("Mode Kompatibilitas (WebGL) Aktif", "warning")
      }

      FaceUI.updateStatus("Memuat data wajah...", "info")
      await loadFaceDatabase()

      state.isModelLoaded = true

      if (state.config.mode.startsWith("presensi")) {
        FaceVerification.selectRandomMovement()
        FaceVerification.showHeadMovementInstruction()
      }

      FaceUI.checkButtonState(state)
      startDetectionLoop()
    } catch (error) {
      console.error("❌ Error init Human:", error)
      FaceUI.updateStatus("Gagal memuat model AI.", "danger")
      Swal.fire({
        icon: "error",
        title: "Model AI Gagal Dimuat",
        html: "<p>Refresh halaman atau periksa koneksi internet.</p>",
        confirmButtonColor: "#1e3a8a",
      })
    }
  }

  async function loadFaceDatabase() {
    try {
      const response = await fetch(state.config.endpoints.getFaceDescriptors, {
        method: "GET",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      })

      const data = await response.json()
      if (data.error) throw new Error(data.error)

      let userFaces = data.filter(
        (item) => item.id_pegawai == state.config.userData.idPegawai,
      )

      if (userFaces.length === 0 && state.config.mode.startsWith("presensi")) {
        FaceUI.updateStatus("Wajah tidak ditemukan di database.", "danger")
        document.querySelector(".video-container").style.display = "none"
        document
          .getElementById("verification-progress")
          ?.style.setProperty("display", "none")
        document
          .getElementById("head-movement-instruction")
          ?.style.setProperty("display", "none")

        Swal.fire({
          icon: "error",
          title: "Wajah Belum Terdaftar",
          text: "Sistem tidak menemukan data wajah Anda untuk verifikasi.",
          showCancelButton: true,
          confirmButtonText: "Request Pendaftaran Wajah",
          confirmButtonColor: "#1e3a8a",
          cancelButtonText: "Tutup",
          cancelButtonColor: "#6c757d",
          allowOutsideClick: false,
          allowEscapeKey: false,
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = state.config.userData.faceEnrollmentUrl
          }
        })

        return false
      }

      state.faceDatabase = userFaces.map((item) => ({
        id: item.id,
        nama: item.nama,
        descriptor: item.descriptor,
      }))

      state.isFaceRegistered = true
      console.log(`✅ Berhasil memuat ${state.faceDatabase.length} data wajah`)
      return true
    } catch (error) {
      console.error("❌ Error loading face database:", error)
      FaceUI.updateStatus("Gagal memuat database wajah.", "danger")
      Swal.fire({
        icon: "error",
        title: "Gagal Memuat Data",
        text: "Terjadi kesalahan saat mengambil data wajah. Silahkan refresh halaman.",
        confirmButtonColor: "#1e3a8a",
      })
      return false
    }
  }

  // ==================== DETECTION LOOP ====================
  function startDetectionLoop() {
    const video = FaceUI.DOM_CACHE.video
    if (!video || !video.srcObject) {
      setTimeout(startDetectionLoop, 500)
      return
    }

    const detectionLoop = async (currentTime) => {
      if (
        document.hidden ||
        currentTime - state.lastDetectionTime < CONSTANTS.DETECTION_THROTTLE_MS
      ) {
        state.animationFrameId = requestAnimationFrame(detectionLoop)
        return
      }

      state.lastDetectionTime = currentTime

      if (FaceUI.DOM_CACHE.button?.disabled) {
        FaceUI.checkButtonState(state)
      }

      if (state.isVerifying) {
        state.animationFrameId = requestAnimationFrame(detectionLoop)
        return
      }

      try {
        const result = await state.human.detect(video)

        // Mode-specific detection handling
        if (state.config.mode === "face_descriptors") {
          FaceVerification.handleDescriptorDetection(result, state)
        } else if (state.config.mode.startsWith("presensi")) {
          FaceVerification.handlePresensiDetection(result, state, currentTime)
        }
      } catch (error) {
        console.error("Detection error:", error)
      }

      state.animationFrameId = requestAnimationFrame(detectionLoop)
    }

    state.animationFrameId = requestAnimationFrame(detectionLoop)
    console.log("✅ Detection loop started")
  }

  function stopDetectionLoop() {
    if (state.animationFrameId) {
      cancelAnimationFrame(state.animationFrameId)
      state.animationFrameId = null
      console.log("⏸️ Detection loop stopped")
    }
  }

  // ==================== FACE MATCHING ====================
  function findBestMatch(descriptor) {
    if (state.faceDatabase.length === 0) {
      return { name: "Unknown", score: 0 }
    }

    let bestMatch = { name: "Unknown", score: 0 }

    for (let i = 0; i < state.faceDatabase.length; i++) {
      const person = state.faceDatabase[i]
      try {
        const score = state.human.match.similarity(
          descriptor,
          person.descriptor,
        )
        if (score > bestMatch.score) {
          bestMatch = { name: person.nama, score: score }
        }
      } catch (error) {
        console.error("Error matching face:", error)
        continue
      }
    }

    return bestMatch
  }

  // ==================== PUBLIC API ====================
  return {
    init: function (config) {
      state.config = config
      return this
    },

    start: async function () {
      await FaceVerification.setupCamera(state)
      await initHuman()
    },

    captureImage: function () {
      return FaceVerification.captureImage()
    },

    getState: function () {
      return state
    },

    findBestMatch,
    stopDetectionLoop,

    getEmotionText: function (emotion) {
      return CONSTANTS.EMOTION_MAP[emotion] || "Netral"
    },
  }
})()
