<?php
// app/Controllers/AIJokeController.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Controllers;

class AIJokeController extends BaseController
{
    private array $groqApiKeys = [];
    private string $groqModel  = '';

    public function __construct()
    {
        // Gunakan env() helper CI4, bukan getenv()
        $keysString = env('GROQ_API_KEYS');
        if ($keysString) {
            $this->groqApiKeys = array_filter(array_map('trim', explode(',', $keysString)));
        } else {
            $key = env('GROQ_API_KEY');
            if ($key) $this->groqApiKeys = [trim($key)];
        }

        $this->groqModel = env('GROQ_MODEL', 'llama-3.1-70b-versatile');
    }

    public function generate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (!logged_in()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $emotion = $this->request->getPost('emotion');
        $type    = $this->request->getPost('type');
        $age     = (int) $this->request->getPost('age');

        $validEmotions = ['happy', 'angry', 'sad', 'neutral', 'surprised', 'fear'];
        if (!in_array($emotion, $validEmotions)) $emotion = 'neutral';
        if (!in_array($type, ['in', 'out'])) $type = 'in';
        if ($age < 5 || $age > 100) $age = 17;

        $contextType = $type === 'in'
            ? 'baru saja masuk/tiba di sekolah pagi ini'
            : 'baru saja selesai sekolah dan mau pulang sore ini';

        $emotionLabel = [
            'happy'     => 'terlihat senang dan bahagia',
            'angry'     => 'terlihat marah atau kesal',
            'sad'       => 'terlihat sedih atau murung',
            'neutral'   => 'terlihat biasa saja atau netral',
            'surprised' => 'terlihat terkejut atau kaget',
            'fear'      => 'terlihat takut atau gugup',
        ][$emotion];

          $prompt = <<<EOT
      Kamu adalah asisten presensi sekolah yang fun dan suportif.
      Buat 1 pesan motivasi/humor singkat (MAKSIMAL 12 kata, Bahasa Indonesia gaul/santai) + 1 emoji yang cocok untuk siswa yang {$emotionLabel} saat {$contextType}.
      Sesuaikan tone: sedih/takut → motivasi, senang → apresiasi, marah → menenangkan, netral → semangati.
      Balas HANYA dengan JSON valid, tanpa teks lain:
      {"text":"pesan singkat di sini","emoji":"🎯"}
      EOT;

        $result = $this->callGroq($prompt);

        if (!$result['success']) {
            return $this->response->setJSON(['success' => false, 'debug' => $result['debug'] ?? 'unknown']);
        }

        $raw = trim($result['message']);

        // Bersihkan jika AI membungkus dengan markdown code block
        $raw = preg_replace('/^```json\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);
        $raw = trim($raw);

        // Ekstrak JSON
        if (preg_match('/\{.*\}/s', $raw, $matches)) {
            $parsed = json_decode($matches[0], true);
            if ($parsed && isset($parsed['text'], $parsed['emoji'])) {
                return $this->response->setJSON([
                    'success' => true,
                    'text'    => substr(strip_tags((string) $parsed['text']), 0, 120),
                    'emoji'   => mb_substr((string) $parsed['emoji'], 0, 4),
                    'age'     => $age,
                ]);
            }
        }

        // Parsing gagal
        log_message('warning', '[AIJoke] Parse failed. Raw: ' . $raw);
        return $this->response->setJSON([
            'success' => false,
            'debug'   => 'parse_failed',
        ]);
    }

    private function callGroq(string $prompt): array
    {
        if (empty($this->groqApiKeys)) {
            log_message('error', '[AIJoke] GROQ_API_KEYS kosong atau tidak terbaca dari env');
            return ['success' => false, 'debug' => 'api_keys_empty'];
        }

        $payload = json_encode([
            'model'       => $this->groqModel,
            'messages'    => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.9,
            'max_tokens'  => 80,
        ]);

        foreach ($this->groqApiKeys as $index => $apiKey) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api.groq.com/openai/v1/chat/completions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($curl);
            curl_close($curl);

            if ($curlErr) {
                log_message('error', "[AIJoke] cURL error pada Key #{$index}: {$curlErr}");
                return ['success' => false, 'debug' => 'curl_error'];
            }

            if ($httpCode === 200) {
                $data    = json_decode($response, true);
                $content = $data['choices'][0]['message']['content'] ?? null;
                if ($content) {
                    return ['success' => true, 'message' => $content];
                }
                return ['success' => false, 'debug' => 'empty_content'];
            }

            if ($httpCode === 429) {
                log_message('warning', "[AIJoke] Rate limit pada Key #{$index}, coba key berikutnya...");
                continue;
            }

            log_message('error', "[AIJoke] HTTP {$httpCode} pada Key #{$index}: {$response}");
            return ['success' => false, 'debug' => "http_{$httpCode}"];
        }

        return ['success' => false, 'debug' => 'all_keys_exhausted'];
    }
}