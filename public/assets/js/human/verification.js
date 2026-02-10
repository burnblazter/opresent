// ==================== FACE VERIFICATION MODULE ====================
const FaceVerification = (function () {
  "use strict"

  const headMovementState = {
    required: null,
    completed: false,
    detectionCount: 0,
    requiredCount: 8,
    initialRotation: null,
    lastCheckTime: 0,
  }

  const CONSTANTS = {
    DEBOUNCE_HEAD_MOVEMENT_MS: 150,
    HEAD_MOVEMENT_INSTRUCTIONS: {
      up: { icon: "⬆️", text: "DONGAKKAN KEPALA", subtitle: "Lihat ke atas" },
      down: {
        icon: "⬇️",
        text: "TUNDUKKAN KEPALA",
        subtitle: "Lihat ke bawah",
      },
      right: {
        icon: "⬅️",
        text: "TOLEH KE KIRI",
        subtitle: "Putar kepala ke kiri",
      },
      left: {
        icon: "➡️",
        text: "TOLEH KE KANAN",
        subtitle: "Putar kepala ke kanan",
      },
    },
  }

  // ==================== CAMERA ====================
  async function setupCamera(state) {
    try {
      FaceUI.updateStatus("Meminta izin kamera...", "info")
      const video = FaceUI.DOM_CACHE.video

      if (!navigator.mediaDevices?.getUserMedia) {
        const err = new Error("Browser tidak mendukung kamera.")
        showPermissionInstructions("camera", err)
        throw err
      }

      const constraints = [
        {
          video: {
            width: { ideal: 640, max: 640 },
            height: { ideal: 480, max: 480 },
            facingMode: "user",
            frameRate: { ideal: 24, max: 30 },
          },
          audio: false,
        },
        { video: { facingMode: "user" }, audio: false },
      ]

      let lastError = null
      for (const constraint of constraints) {
        try {
          state.stream = await navigator.mediaDevices.getUserMedia(constraint)
          break
        } catch (err) {
          lastError = err
        }
      }

      if (!state.stream) {
        showPermissionInstructions("camera", lastError)
        throw lastError
      }

      video.srcObject = state.stream

      await new Promise((resolve, reject) => {
        video.onloadedmetadata = () => video.play().then(resolve).catch(reject)
        setTimeout(() => reject(new Error("Timeout loading video")), 10000)
      })

      console.log("✅ Kamera berhasil diaktifkan")
      return true
    } catch (error) {
      console.error("❌ Error kamera:", error)
      FaceUI.updateStatus("Gagal mengaktifkan kamera.", "danger")
      return false
    }
  }

  function showPermissionInstructions(type, error) {
    let title = "📷 Akses Kamera Diperlukan"
    let html = ""

    if (
      error.name === "NotAllowedError" ||
      error.name === "PermissionDeniedError"
    ) {
      html = `
        <div style="text-align: left;">
          <p><strong>Anda memblokir akses kamera.</strong></p>
          <p>Untuk mengaktifkan kembali:</p>
          <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Klik ikon <strong>🔒 gembok</strong> atau <strong>ℹ️ info</strong> di address bar browser</li>
            <li>Cari pengaturan <strong>"Kamera"</strong></li>
            <li>Ubah dari <strong>"Blokir"</strong> menjadi <strong>"Izinkan"</strong></li>
            <li>Refresh halaman ini (F5)</li>
          </ol>
        </div>
      `
    } else if (
      error.name === "NotFoundError" ||
      error.name === "DevicesNotFoundError"
    ) {
      html = `<div style="text-align: left;">
          <p><strong>Kamera tidak ditemukan!</strong></p>
          <p>Kemungkinan penyebab:</p>
          <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Kamera tidak terhubung ke perangkat</li>
            <li>Driver kamera belum terinstal</li>
            <li>Kamera sedang digunakan aplikasi lain</li>
          </ul>
        </div>`
    } else {
      html = `<div style="text-align: left;">
          <p><strong>Terjadi kesalahan:</strong> ${error.message}</p>
          <p><strong>Solusi umum:</strong></p>
          <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Refresh halaman (F5)</li>
            <li>Pastikan browser mendukung kamera</li>
            <li>Coba gunakan browser Chrome/Firefox terbaru</li>
          </ul>
        </div>`
    }

    Swal.fire({
      icon: "warning",
      title: title,
      html: html,
      confirmButtonText: "Saya Mengerti",
      confirmButtonColor: "#1e3a8a",
      width: "600px",
    })
  }

  // ==================== IMAGE CAPTURE ====================
  function captureImage() {
    const video = FaceUI.DOM_CACHE.video
    const canvas = FaceUI.DOM_CACHE.canvas
    const context = canvas.getContext("2d", { alpha: false })

    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    context.drawImage(video, 0, 0, canvas.width, canvas.height)

    const imageData = canvas.toDataURL("image/jpeg", 0.75)
    context.clearRect(0, 0, canvas.width, canvas.height)

    return imageData
  }

  // ==================== HEAD MOVEMENT ====================
  function selectRandomMovement() {
    const movements = ["up", "down", "left", "right"]
    headMovementState.required =
      movements[Math.floor(Math.random() * movements.length)]
    console.log("✅ Gerakan yang diperlukan:", headMovementState.required)
  }

  function showHeadMovementInstruction() {
    const instructionDiv = document.getElementById("head-movement-instruction")
    if (!instructionDiv) return

    const instruction =
      CONSTANTS.HEAD_MOVEMENT_INSTRUCTIONS[headMovementState.required]

    document.getElementById("instruction-icon").textContent = instruction.icon
    document.getElementById("instruction-text").textContent = instruction.text
    document.querySelector(".instruction-subtitle").textContent =
      instruction.subtitle

    instructionDiv.style.display = "block"
    document.getElementById("verification-progress").style.display = "block"

    FaceUI.updateProgressIcon("progress-face", "active")
  }

  function checkHeadMovement(face, currentTime) {
    if (!face?.rotation) return false

    if (
      currentTime - headMovementState.lastCheckTime <
      CONSTANTS.DEBOUNCE_HEAD_MOVEMENT_MS
    ) {
      return false
    }
    headMovementState.lastCheckTime = currentTime

    const { angle } = face.rotation
    const toDegrees = (rad) => rad * (180 / Math.PI)
    const pitch = toDegrees(angle.pitch || 0)
    const yaw = toDegrees(angle.yaw || 0)

    if (!headMovementState.initialRotation) {
      headMovementState.initialRotation = { pitch, yaw }
      return false
    }

    const pitchDiff = pitch - headMovementState.initialRotation.pitch
    const yawDiff = yaw - headMovementState.initialRotation.yaw
    const threshold = 12

    let isCorrectMovement = false

    switch (headMovementState.required) {
      case "up":
        isCorrectMovement = pitchDiff < -threshold
        break
      case "down":
        isCorrectMovement = pitchDiff > threshold
        break
      case "left":
        isCorrectMovement = yawDiff > threshold
        break
      case "right":
        isCorrectMovement = yawDiff < -threshold
        break
    }

    if (isCorrectMovement) {
      headMovementState.detectionCount++
      const percentage =
        (headMovementState.detectionCount / headMovementState.requiredCount) *
        100
      FaceUI.updateProgressBar(percentage)

      if (headMovementState.detectionCount >= headMovementState.requiredCount) {
        headMovementState.completed = true
        FaceUI.updateProgressIcon("progress-movement", "completed")
        document.getElementById("head-movement-instruction").style.display =
          "none"
        FaceUI.updateStatus("✅ OK! Tahan...", "success")
        return true
      }
    } else {
      headMovementState.detectionCount = Math.max(
        0,
        headMovementState.detectionCount - 1,
      )
      const percentage =
        (headMovementState.detectionCount / headMovementState.requiredCount) *
        100
      FaceUI.updateProgressBar(percentage)
    }

    return false
  }

  // ==================== DETECTION HANDLERS ====================
  function handlePresensiDetection(result, state, currentTime) {
    if (result.face?.length > 0) {
      const face = result.face[0]
      FaceUI.updateProgressIcon("progress-face", "completed")

      if (!headMovementState.completed) {
        FaceUI.updateProgressIcon("progress-movement", "active")
        checkHeadMovement(face, currentTime)
        FaceUI.updateStatus("👤 Ikuti instruksi gerakan kepala", "info")
        FaceUI.DOM_CACHE.faceVerifiedInput.value = "false"
        return
      }

      FaceUI.updateProgressIcon("progress-match", "active")

      if (!face.embedding) {
        FaceUI.updateStatus("⚠️ Embedding tidak tersedia", "warning")
        FaceUI.DOM_CACHE.faceVerifiedInput.value = "false"
        return
      }

      const match = FaceRecognition.findBestMatch(face.embedding)
      state.currentSimilarity = match.score

      if (match.score < 0.62) {
        const helpLink = `<div class="mt-2">Susah terdeteksi? <a href="${state.config.userData.faceEnrollmentUrl}" class="fw-bold text-decoration-none">Request Pendaftaran Wajah</a></div>`
        FaceUI.updateStatus(
          "⚠️ Akurasi rendah, posisikan wajah dengan benar.",
          "warning",
          helpLink,
        )
        FaceUI.DOM_CACHE.faceVerifiedInput.value = "false"
        return
      }

      state.currentAge = Math.round(face.age || 0)
      state.currentEmotion = face.emotion?.[0]?.emotion || "neutral"

      const emotionText = FaceRecognition.getEmotionText(state.currentEmotion)
      const details = `Akurasi: ${(match.score * 100).toFixed(1)}% | Usia: ~${state.currentAge} | Emosi: ${emotionText}`

      FaceUI.updateProgressIcon("progress-match", "completed")
      FaceUI.updateStatus("✅ Wajah terverifikasi!", "success", details)

      FaceUI.DOM_CACHE.faceVerifiedInput.value = "true"
      document.getElementById("face-similarity").value = match.score
      document.getElementById("detected-age").value = state.currentAge
      document.getElementById("detected-emotion").value = state.currentEmotion
      FaceUI.checkAutoCapture(state)
    } else {
      FaceUI.updateStatus("👤 Tidak ada wajah terdeteksi", "info")
      FaceUI.DOM_CACHE.faceVerifiedInput.value = "false"
      state.currentSimilarity = 0
      FaceUI.stopAutoCapture()
    }
  }

  function handleDescriptorDetection(result, state) {
    const faceCount = result.face?.length || 0

    if (faceCount > 0) {
      const face = result.face[0]
      const confidence = Math.round(face.boxScore * 100)
      const age = Math.round(face.age || 0)
      const gender = face.gender || "-"
      const emotion = face.emotion?.[0]?.emotion.toUpperCase() || "-"

      FaceUI.updateWebcamStats(faceCount, age + " Thn", gender, emotion)

      if (faceCount === 1) {
        FaceUI.updateStatus(`✅ Wajah Terdeteksi (${confidence}%)`, "success")
      } else {
        FaceUI.updateStatus(
          `⚠️ Terdeteksi ${faceCount} wajah. Pastikan hanya 1 wajah!`,
          "warning",
        )
      }
    } else {
      FaceUI.updateWebcamStats(0, "-", "-", "-")
      FaceUI.updateStatus("⚠️ Mencari wajah...", "warning")
    }
  }

  // ==================== PUBLIC API ====================
  return {
    setupCamera,
    captureImage,
    selectRandomMovement,
    showHeadMovementInstruction,
    handlePresensiDetection,
    handleDescriptorDetection,
    getHeadMovementState: () => headMovementState,
  }
})()
