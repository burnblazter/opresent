/**
 * AI Jokes Generator for Presensi App
 */

const AI_JOKE_CACHE_MINUTES = 6

const FALLBACK_JOKES = {
  in: {
    happy: { text: "Semangat belajarnya kelihatan!", emoji: "😎" },
    angry: { text: "Tarik napas, hari ini pasti lebih baik!", emoji: "🌬️" },
    sad: { text: "Kamu hebat! Pasti bisa melewati hari ini.", emoji: "💙" },
    neutral: { text: "Mode fokus: ON. Gas belajarnya!", emoji: "🎯" },
    surprised: { text: "Woah! Ada apa nih pagi-pagi?", emoji: "😲" },
    fear: { text: "Jangan nervous, kamu pasti bisa!", emoji: "💪" },
    any: { text: "Selamat pagi! Semoga harimu menyenangkan.", emoji: "🌅" },
  },
  out: {
    happy: { text: "Senyum kemenangan! Hari yang produktif!", emoji: "🏆" },
    angry: { text: "Hari berat ya? Istirahat yang cukup!", emoji: "😌" },
    sad: { text: "Jangan sedih, kamu sudah hebat hari ini!", emoji: "⭐" },
    neutral: { text: "Another day done. Good job!", emoji: "✅" },
    surprised: { text: "Surprise ending! Cerita dong besok!", emoji: "🎬" },
    fear: { text: "Santai, sudah waktunya pulang. Aman!", emoji: "🏠" },
    any: { text: "Terima kasih sudah belajar hari ini!", emoji: "🙏" },
  },
}

function sanitizeText(text) {
  if (typeof text !== "string") return ""
  return text.replace(/[<>"']/g, "")
}

function getFallback(emotion, type) {
  const pool = FALLBACK_JOKES[type] || FALLBACK_JOKES.in
  return pool[emotion] || pool.any
}

function getCacheKey(emotion, type, date) {
  return `ai_joke_v2_${type}_${emotion}_${date}`
}

function getFromCache(emotion, type, date) {
  try {
    const key = getCacheKey(emotion, type, date)
    const raw = localStorage.getItem(key)
    if (!raw) return null

    const data = JSON.parse(raw)
    const now = Date.now()

    if (now - data.timestamp > AI_JOKE_CACHE_MINUTES * 60 * 1000) {
      localStorage.removeItem(key)
      return null
    }

    return data.joke
  } catch {
    return null
  }
}

function saveToCache(emotion, type, date, joke) {
  try {
    const key = getCacheKey(emotion, type, date)
    localStorage.setItem(
      key,
      JSON.stringify({
        timestamp: Date.now(),
        joke,
      }),
    )
  } catch {}
}

function renderJoke(joke, type, container, age = null) {
  const emojiEl = document.getElementById(`ai-emoji-${type}`)
  const messageEl = document.getElementById(`ai-message-${type}`)
  const ageEl = document.getElementById(`ai-age-${type}`)

  if (emojiEl) emojiEl.textContent = sanitizeText(joke.emoji)
  if (messageEl) messageEl.textContent = sanitizeText(joke.text)
  if (ageEl && age !== null) ageEl.textContent = age
  if (container) container.style.display = "block"
}

function getCsrfToken() {
  const match = document.cookie.match(/(^|;\s*)csrf_cookie_name=([^;]+)/)
  return match ? decodeURIComponent(match[2]) : null
}

async function fetchJokeFromAI(age, emotion, type) {
  const formData = new FormData()
  formData.append("emotion", emotion)
  formData.append("type", type)
  formData.append("age", age)

  const csrf = getCsrfToken()
  if (csrf) formData.append("csrf_test_name", csrf)

  const response = await fetch("/ai-joke/generate", {
    method: "POST",
    headers: { "X-Requested-With": "XMLHttpRequest" },
    body: formData,
  })

  if (!response.ok) throw new Error(`HTTP ${response.status}`)

  const data = await response.json()

  console.log("[AI Joke] Response:", data)

  if (!data.success) throw new Error("AI returned failure")

  return { text: data.text, emoji: data.emoji }
}

async function checkAiData(key, type, container, currentDate) {
  try {
    const raw = localStorage.getItem(key)
    if (!raw) return

    const data = JSON.parse(raw)
    if (!data?.date_recorded || !data?.age || !data?.emotion) return
    if (data.date_recorded !== currentDate) return

    const age = parseInt(data.age)
    if (isNaN(age) || age < 5 || age > 100) return

    const validEmotions = [
      "happy",
      "angry",
      "sad",
      "neutral",
      "surprised",
      "fear",
    ]
    const emotion = validEmotions.includes(data.emotion)
      ? data.emotion
      : "neutral"

    const fallback = getFallback(emotion, type)
    renderJoke(fallback, type, container, age)

    const cached = getFromCache(emotion, type, currentDate)
    if (cached) {
      renderJoke(cached, type, container, age)
      return
    }

    fetchJokeFromAI(age, emotion, type)
      .then((joke) => {
        saveToCache(emotion, type, currentDate, joke)
        renderJoke(joke, type, container, age)
      })
      .catch((err) => {
        console.warn("[AI Joke] Fallback digunakan:", err.message)
      })
  } catch (e) {
    console.error("[AI Joke] Error:", e)
  }
}

// Export untuk compatibility
if (typeof module !== "undefined" && module.exports) {
  module.exports = { checkAiData, sanitizeText }
}
