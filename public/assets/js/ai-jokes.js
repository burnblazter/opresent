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
      // HAPPY - Masuk
      {
        e: "happy",
        t: "Senyumnya cerah banget! Semangat belajarnya kelihatan nih.",
        i: "😎",
      },
      {
        e: "happy",
        t: "Ceria amat pagi ini, pasti tidurnya nyenyak ya?",
        i: "😊",
      },
      {
        e: "happy",
        t: "Energy positif detected! Siap produktif hari ini.",
        i: "⚡",
      },
      {
        e: "happy",
        t: "Mantap! Vibes-nya udah siap ngegas belajar.",
        i: "🚀",
      },
      {
        e: "happy",
        t: "Bahagia gini terus dong setiap hari!",
        i: "✨",
      },
      {
        e: "happy",
        t: "Senyum pagi bikin hari makin indah!",
        i: "🌟",
      },
      {
        e: "happy",
        t: "Full semangat! Ayo raih mimpi-mimpimu!",
        i: "🎯",
      },
      {
        e: "happy",
        t: "Positive vibes! Siap jadi yang terbaik hari ini.",
        i: "💪",
      },
      {
        e: "happy",
        t: "Cieee yang ceria, pasti ada kabar baik nih.",
        i: "🎊",
      },
      {
        e: "happy",
        t: "Bahagia itu menular lho, keep smiling!",
        i: "😄",
      },
      {
        e: "happy",
        t: "Mood bagus = Produktivitas tinggi. Gas terus!",
        i: "🔥",
      },
      {
        e: "happy",
        t: "Senyum manis pagi hari, pasti hari ini beruntung!",
        i: "🍀",
      },

      // ANGRY - Masuk
      {
        e: "angry",
        t: "Waduh, take a deep breath dulu yuk. Semua pasti baik-baik aja.",
        i: "🌬️",
      },
      {
        e: "angry",
        t: "Santai bestie, hari ini pasti lebih baik kok.",
        i: "🙏",
      },
      {
        e: "angry",
        t: "Sabar ya, semua masalah ada solusinya.",
        i: "💭",
      },
      {
        e: "angry",
        t: "Keep calm and stay positive!",
        i: "✌️",
      },
      {
        e: "angry",
        t: "Jangan dibawa stres, nanti susah fokus belajarnya.",
        i: "🧘",
      },
      {
        e: "angry",
        t: "Hari yang berat? Tenang, kamu pasti bisa kok!",
        i: "💪",
      },
      {
        e: "angry",
        t: "Tarik napas, hembuskan pelan. Better now?",
        i: "🌈",
      },
      {
        e: "angry",
        t: "Sabar ya, masih pagi kok. Semoga harinya membaik!",
        i: "☀️",
      },

      // SAD - Masuk
      {
        e: "sad",
        t: "Jangan down gitu dong, semangat belajarnya!",
        i: "🥺",
      },
      {
        e: "sad",
        t: "Kamu hebat! Pasti bisa melewati hari ini.",
        i: "💙",
      },
      {
        e: "sad",
        t: "Tenang bestie, everything will be okay.",
        i: "🌈",
      },
      {
        e: "sad",
        t: "Cuaca mendung? Hati jangan ikutan mendung ya!",
        i: "⛅",
      },
      {
        e: "sad",
        t: "Semangat! Setiap hari adalah kesempatan baru.",
        i: "🌅",
      },
      {
        e: "sad",
        t: "Jangan sedih, kamu nggak sendiri kok!",
        i: "🤗",
      },
      {
        e: "sad",
        t: "Tough times don't last, tough people do!",
        i: "💪",
      },
      {
        e: "sad",
        t: "Senyum dulu, nanti juga ketawa sendiri.",
        i: "😊",
      },
      {
        e: "sad",
        t: "Bad day doesn't mean bad life. Keep going!",
        i: "🚶",
      },
      {
        e: "sad",
        t: "Setiap masalah pasti ada jalan keluarnya.",
        i: "🛤️",
      },

      // NEUTRAL - Masuk
      {
        e: "neutral",
        t: "Mode fokus: ON. Mantap!",
        i: "🎯",
      },
      {
        e: "neutral",
        t: "Chill aja ya, santai tapi pasti.",
        i: "😌",
      },
      {
        e: "neutral",
        t: "Standar mode activated. Ayo gas belajarnya!",
        i: "📚",
      },
      {
        e: "neutral",
        t: "Calm and composed. Good energy!",
        i: "🧘",
      },
      {
        e: "neutral",
        t: "Kalem banget, siap konsentrasi penuh nih.",
        i: "🎧",
      },
      {
        e: "neutral",
        t: "Santai tapi serius. Balance is key!",
        i: "⚖️",
      },
      {
        e: "neutral",
        t: "Flat face but big brain. Let's go!",
        i: "🧠",
      },
      {
        e: "neutral",
        t: "Poker face mode. Siap menghadapi apa aja.",
        i: "😐",
      },
      {
        e: "neutral",
        t: "Tenang, dalam, fokus. Itu kuncinya!",
        i: "🔑",
      },

      // SURPRISED - Masuk
      {
        e: "surprised",
        t: "Woah! Ada apa nih?",
        i: "😲",
      },
      {
        e: "surprised",
        t: "Kaget kenapa? Tenang, semua aman kok!",
        i: "🤭",
      },
      {
        e: "surprised",
        t: "Shock therapy pagi hari? Hehe.",
        i: "⚡",
      },
      {
        e: "surprised",
        t: "Plot twist pagi ini ya? Stay calm!",
        i: "🎬",
      },
      {
        e: "surprised",
        t: "Mata melotot gitu, dapat kabar apa nih?",
        i: "👀",
      },
      {
        e: "surprised",
        t: "Surprised but make it positive vibes!",
        i: "✨",
      },

      // FEAR - Masuk
      {
        e: "fear",
        t: "Jangan nervous, kamu pasti bisa!",
        i: "💪",
      },
      {
        e: "fear",
        t: "Take it easy, satu langkah demi satu langkah.",
        i: "👣",
      },
      {
        e: "fear",
        t: "Tegang? Deep breath, you got this!",
        i: "🌬️",
      },
      {
        e: "fear",
        t: "Jangan worry, semua akan baik-baik saja.",
        i: "🙏",
      },
      {
        e: "fear",
        t: "Percaya diri! Kamu lebih hebat dari yang kamu kira.",
        i: "⭐",
      },
      {
        e: "fear",
        t: "Nervous is normal, tapi jangan sampe lupa napas ya!",
        i: "😅",
      },

      // ANY - Masuk (Universal)
      {
        e: "any",
        t: "Selamat pagi! Semoga harimu menyenangkan.",
        i: "🌅",
      },
      {
        e: "any",
        t: "Good morning! Ready to learn something new?",
        i: "📖",
      },
      {
        e: "any",
        t: "Pagi yang indah untuk belajar!",
        i: "☀️",
      },
      {
        e: "any",
        t: "Selamat datang! Ayo mulai hari dengan semangat.",
        i: "🎉",
      },
      {
        e: "any",
        t: "Hi! Jangan lupa senyum hari ini ya.",
        i: "😊",
      },
      {
        e: "any",
        t: "Yuk masuk! Ilmu menanti untuk dipelajari.",
        i: "🎓",
      },
      {
        e: "any",
        t: "Hadir tepat waktu! Good job!",
        i: "⏰",
      },
      {
        e: "any",
        t: "Welcome! Siap jadi versi terbaik hari ini?",
        i: "🌟",
      },
      {
        e: "any",
        t: "Pagi! Jangan lupa sarapan dan minum air ya.",
        i: "🥤",
      },
      {
        e: "any",
        t: "Selamat belajar! Semoga ilmunya berkah.",
        i: "🙏",
      },
      {
        e: "any",
        t: "Ayo masuk! Masa depan cerah menanti.",
        i: "🌈",
      },
      {
        e: "any",
        t: "Semangat! Setiap hari adalah kesempatan baru.",
        i: "💫",
      },
      {
        e: "any",
        t: "Hai! Sudah siap belajar hari ini?",
        i: "✅",
      },
      {
        e: "any",
        t: "Good vibes only! Let's make today count.",
        i: "✨",
      },
      {
        e: "any",
        t: "Selamat pagi! Jangan lupa berdoa sebelum belajar.",
        i: "🤲",
      },
    ],

    out: [
      // HAPPY - Pulang
      {
        e: "happy",
        t: "Akhirnya pulang! Hati-hati di jalan ya.",
        i: "🎉",
      },
      {
        e: "happy",
        t: "Senyum kemenangan! Hari yang produktif!",
        i: "🏆",
      },
      {
        e: "happy",
        t: "Happy ending! Selamat beristirahat.",
        i: "😊",
      },
      {
        e: "happy",
        t: "Bahagia banget, pasti hari ini menyenangkan!",
        i: "💕",
      },
      {
        e: "happy",
        t: "Good job today! Time to relax.",
        i: "🌟",
      },
      {
        e: "happy",
        t: "Pulang dengan senyuman, besok semangat lagi!",
        i: "😄",
      },
      {
        e: "happy",
        t: "Ceria terus! Sampai jumpa besok.",
        i: "👋",
      },
      {
        e: "happy",
        t: "Sukses hari ini! Besok lebih sukses lagi.",
        i: "📈",
      },
      {
        e: "happy",
        t: "Full happiness! Keep that energy!",
        i: "⚡",
      },
      {
        e: "happy",
        t: "Senang banget, pasti dapat nilai bagus ya?",
        i: "💯",
      },

      // ANGRY - Pulang
      {
        e: "angry",
        t: "Hari yang berat ya? Istirahat yang cukup!",
        i: "😌",
      },
      {
        e: "angry",
        t: "Sabar ya, besok pasti lebih baik.",
        i: "🌈",
      },
      {
        e: "angry",
        t: "Take a break, refresh your mind!",
        i: "🧘",
      },
      {
        e: "angry",
        t: "Udah capek? Pulang, istirahat, reset!",
        i: "🔄",
      },
      {
        e: "angry",
        t: "Tomorrow is a new day. Stay strong!",
        i: "💪",
      },
      {
        e: "angry",
        t: "Santai dulu di rumah, besok semangat lagi!",
        i: "🏠",
      },

      // SAD - Pulang
      {
        e: "sad",
        t: "Semoga besok lebih baik ya. Keep fighting!",
        i: "💙",
      },
      {
        e: "sad",
        t: "Istirahat yang cukup, besok pasti lebih baik.",
        i: "😊",
      },
      {
        e: "sad",
        t: "Jangan sedih, kamu sudah hebat hari ini!",
        i: "⭐",
      },
      {
        e: "sad",
        t: "Tough day? Tomorrow will be better!",
        i: "🌅",
      },
      {
        e: "sad",
        t: "Pulang dulu, charge energy, besok gas lagi!",
        i: "🔋",
      },
      {
        e: "sad",
        t: "It's okay to have bad days. Rest well!",
        i: "🤗",
      },
      {
        e: "sad",
        t: "Jangan down, besok kesempatan baru menanti!",
        i: "🌟",
      },
      {
        e: "sad",
        t: "Semangat! Setiap akhir adalah awal yang baru.",
        i: "🎯",
      },

      // NEUTRAL - Pulang
      {
        e: "neutral",
        t: "Hari yang standar. See you tomorrow!",
        i: "👋",
      },
      {
        e: "neutral",
        t: "Pulang dulu, besok semangat lagi!",
        i: "🚶",
      },
      {
        e: "neutral",
        t: "Chill aja. Sampai jumpa besok!",
        i: "😌",
      },
      {
        e: "neutral",
        t: "Another day done. Good job!",
        i: "✅",
      },
      {
        e: "neutral",
        t: "Selesai sudah. Waktunya istirahat.",
        i: "⏰",
      },
      {
        e: "neutral",
        t: "Flat but fine. Rest well!",
        i: "😐",
      },
      {
        e: "neutral",
        t: "Hari biasa, hasil luar biasa. Mantap!",
        i: "👍",
      },

      // SURPRISED - Pulang
      {
        e: "surprised",
        t: "Woah! Apa yang terjadi hari ini?",
        i: "😲",
      },
      {
        e: "surprised",
        t: "Surprise ending! Cerita dong besok!",
        i: "🎬",
      },
      {
        e: "surprised",
        t: "Kaget kenapa? Semoga kabar baik ya!",
        i: "✨",
      },
      {
        e: "surprised",
        t: "Plot twist di akhir hari? Interesting!",
        i: "🤔",
      },

      // FEAR - Pulang
      {
        e: "fear",
        t: "Santai, sudah waktunya pulang kok. Aman!",
        i: "🏠",
      },
      {
        e: "fear",
        t: "Jangan khawatir, besok pasti bisa!",
        i: "💪",
      },
      {
        e: "fear",
        t: "Take it easy, everything will be fine!",
        i: "🌈",
      },
      {
        e: "fear",
        t: "Nervous? Istirahat dulu, besok lebih siap!",
        i: "😌",
      },

      // ANY - Pulang (Universal)
      {
        e: "any",
        t: "Terima kasih sudah belajar hari ini!",
        i: "🙏",
      },
      {
        e: "any",
        t: "Selamat sore! Hati-hati di jalan ya.",
        i: "🛵",
      },
      {
        e: "any",
        t: "Good job today! See you tomorrow.",
        i: "👋",
      },
      {
        e: "any",
        t: "Sampai jumpa! Istirahat yang cukup ya.",
        i: "😊",
      },
      {
        e: "any",
        t: "Pulang dengan selamat! Sampai besok.",
        i: "🏠",
      },
      {
        e: "any",
        t: "Great effort today! Rest well.",
        i: "⭐",
      },
      {
        e: "any",
        t: "Selamat beristirahat! Besok ketemu lagi.",
        i: "💤",
      },
      {
        e: "any",
        t: "Hari yang produktif! Proud of you.",
        i: "🎯",
      },
      {
        e: "any",
        t: "Time to go home! Stay safe.",
        i: "🚸",
      },
      {
        e: "any",
        t: "Jangan lupa review pelajaran hari ini ya!",
        i: "📚",
      },
      {
        e: "any",
        t: "Selamat jalan! Jaga kesehatan.",
        i: "💊",
      },
      {
        e: "any",
        t: "Bye! Semoga mimpi indah nanti malam.",
        i: "🌙",
      },
      {
        e: "any",
        t: "Sudah waktunya pulang. Be safe!",
        i: "✌️",
      },
      {
        e: "any",
        t: "Hari ini selesai, besok lebih baik!",
        i: "🌅",
      },
      {
        e: "any",
        t: "Well done! Recharge and come back stronger.",
        i: "🔋",
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
