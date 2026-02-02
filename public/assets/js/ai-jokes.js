/**
 * AI Jokes Generator for Presensi App
 */

function sanitizeText(text) {
  if (typeof text !== "string") return ""
  return text.replace(/[<>\"']/g, "")
}

function generateJoke(age, emotion, type) {
  const jokes = {
    in: [
      {
        e: "happy",
        t: "Senyumnya cerah banget! Pasti PR udah kelar nih.",
        i: "😎",
      },
      {
        e: "happy",
        t: "Bahagia amat, dapet uang saku tambahan ya?",
        i: "🤑",
      },
      {
        e: "happy",
        t: "Cieee yang semangat mau ketemu doi di kelas.",
        i: "😍",
      },
      {
        e: "happy",
        t: "Full senyum! Siap menghadapi pelajaran Matematika?",
        i: "📐",
      },
      {
        e: "happy",
        t: "Vibes-nya positif banget, jangan lupa piket kelas ya.",
        i: "🧹",
      },
      {
        e: "angry",
        t: "Waduh, pagi-pagi jangan emosi bestie, nanti cepet tua.",
        i: "😤",
      },
      {
        e: "angry",
        t: "Muka ditekuk aja. Belum sarapan atau lupa ngerjain tugas?",
        i: "🍜",
      },
      {
        e: "angry",
        t: "Sabar... Macet di jalan emang bikin darting.",
        i: "🚗",
      },
      {
        e: "angry",
        t: "Jangan galak-galak, nanti ditunjuk guru maju ke depan loh.",
        i: "👩‍🏫",
      },
      {
        e: "sad",
        t: "Jangan sad boy/sad girl gitu dong. Semangat belajarnya!",
        i: "🥺",
      },
      {
        e: "sad",
        t: "Ngantuk atau galau? Cuci muka dulu gih biar seger.",
        i: "💧",
      },
      {
        e: "sad",
        t: "Tenang bestie, badai pasti berlalu (termasuk ulangan harian).",
        i: "🌈",
      },
      {
        e: "sad",
        t: "Kenapa murung? Topi dasi lengkap kan? Aman kok.",
        i: "🎩",
      },
      {
        e: "neutral",
        t: "Mode serius: ON. Fokus banget nih kayaknya.",
        i: "😐",
      },
      {
        e: "neutral",
        t: "Nyawanya belum kumpul semua ya? Ngopi dulu di kantin.",
        i: "☕",
      },
      {
        e: "neutral",
        t: "Datar amat mukanya, kayak tanggal tua.",
        i: "📅",
      },
      {
        e: "neutral",
        t: "Santai bro, hari ini jamkos (semoga).",
        i: "🤞",
      },
      {
        e: "surprised",
        t: "Kaget kenapa? Lupa bawa buku paket?",
        i: "📚",
      },
      {
        e: "surprised",
        t: "Melotot gitu liat apa? Ada razia rambut ya?",
        i: "💇",
      },
      {
        e: "fear",
        t: "Tegang amat, belum ngerjain PR ya?",
        i: "📝",
      },
      {
        e: "fear",
        t: "Jangan takut, guru killer hari ini rapat kok (mungkin).",
        i: "🤫",
      },
      {
        e: "any",
        t: "Selamat Pagi! Jangan lupa berdoa sebelum belajar.",
        i: "🙏",
      },
      {
        e: "any",
        t: "Gas masuk kelas! Keburu bel bunyi.",
        i: "🔔",
      },
    ],
    out: [
      {
        e: "happy",
        t: "Akhirnya bel surga berbunyi! Hati-hati di jalan.",
        i: "🎉",
      },
      {
        e: "happy",
        t: "Senyum kemenangan setelah seharian belajar.",
        i: "🏆",
      },
      {
        e: "happy",
        t: "Bahagia banget mau nongkrong atau mau tidur?",
        i: "💤",
      },
      {
        e: "happy",
        t: "Pulang! Saatnya push rank atau drakoran.",
        i: "🎮",
      },
      {
        e: "happy",
        t: "Full senyum, pasti nggak ada PR buat besok.",
        i: "✨",
      },
      {
        e: "angry",
        t: "Capek ya? Jangan marah-marah, mending beli seblak.",
        i: "🔥",
      },
      {
        e: "angry",
        t: "Kusut amat. Motor bensinnya abis?",
        i: "⛽",
      },
      {
        e: "angry",
        t: "Sabar bestie, besok libur (kalau hari Sabtu).",
        i: "📅",
      },
      {
        e: "sad",
        t: "Lelah letih lesu? Kasur di rumah sudah memanggil.",
        i: "🛌",
      },
      {
        e: "sad",
        t: "Jangan sedih, besok ketemu doi lagi kok.",
        i: "👋",
      },
      {
        e: "sad",
        t: "Tugas numpuk? Nangis bentar, abis itu kerjain.",
        i: "💪",
      },
      {
        e: "neutral",
        t: "Muka lelah tapi lega. Bye-bye sekolah!",
        i: "🏫",
      },
      {
        e: "neutral",
        t: "Otw pulang. Jangan mampir-mampir kalau belum izin ortu.",
        i: "🏠",
      },
      {
        e: "neutral",
        t: "Flat banget, butuh healing secepatnya.",
        i: "🌴",
      },
      {
        e: "surprised",
        t: "Baru sadar kalau besok ulangan harian?",
        i: "📖",
      },
      {
        e: "fear",
        t: "Buru-buru amat, takut dicariin emak?",
        i: "🏃",
      },
      {
        e: "any",
        t: "Terima kasih sudah belajar hari ini! Safe trip home.",
        i: "🛵",
      },
      {
        e: "any",
        t: "Langsung pulang ya, jangan tawuran!",
        i: "☮️",
      },
    ],
  }

  const list = jokes[type] || jokes["in"]
  let matches = list.filter((j) => j.e === emotion)

  if (matches.length === 0) {
    matches = list.filter((j) => j.e === "any" || j.e === "neutral")
  }

  const selected = matches[Math.floor(Math.random() * matches.length)]

  if (!selected) {
    return {
      text: "Semangat!",
      emoji: "✊",
    }
  }

  return {
    text: selected.t,
    emoji: selected.i,
  }
}

function checkAiData(key, type, container, currentDate) {
  try {
    const raw = localStorage.getItem(key)
    if (!raw) return

    const data = JSON.parse(raw)

    if (!data || typeof data !== "object") return
    if (!data.date_recorded || !data.age || !data.emotion) return

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
      "any",
    ]
    if (!validEmotions.includes(data.emotion)) return

    const joke = generateJoke(age, data.emotion, type)

    const emojiEl = document.getElementById(`ai-emoji-${type}`)
    const messageEl = document.getElementById(`ai-message-${type}`)
    const ageEl = document.getElementById(`ai-age-${type}`)

    if (emojiEl) emojiEl.textContent = sanitizeText(joke.emoji)
    if (messageEl) messageEl.textContent = sanitizeText(joke.text)
    if (ageEl) ageEl.textContent = age

    if (container) container.style.display = "block"
  } catch (e) {
    console.error("Error parsing AI Data:", e)
  }
}

// Export untuk compatibility
if (typeof module !== "undefined" && module.exports) {
  module.exports = { generateJoke, checkAiData, sanitizeText }
}
