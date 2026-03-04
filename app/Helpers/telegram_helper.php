<?php
// \app\Helpers\telegram_helper.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

if (!function_exists('send_telegram_notification')) {
    /**
     * Mengirim pesan notifikasi ke Telegram Group
     *
     * @param string $message Pesan yang akan dikirim (support HTML)
     * @return bool True jika berhasil, False jika gagal
     */
    function send_telegram_notification($message)
    {
        $botToken = env('telegram.botToken');
        $chatId   = env('telegram.chatId');

        if (empty($botToken) || empty($chatId)) {
            log_message('error', 'Telegram Config: Token atau Chat ID belum disetting di .env');
            return false;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        $data = [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML', // Menggunakan HTML untuk formatting (Bold, dll)
            'disable_web_page_preview' => true
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification untuk local dev
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Timeout koneksi 5 detik (agar tidak blocking lama)
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout eksekusi 5 detik

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($result === false) {
            log_message('error', "Telegram Notification Failed (Curl Error): $error");
            return false;
        }

        if ($httpCode != 200) {
            log_message('error', "Telegram Notification Failed (HTTP $httpCode): $result");
            return false;
        }

        return true;
    }
}

if (!function_exists('format_tanggal_indo')) {
    /**
     * Format tanggal ke Bahasa Indonesia (Contoh: 21 Januari 2026)
     */
    function format_tanggal_indo($tanggal)
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $tgl = date('d', strtotime($tanggal));
        $bln = $bulan[(int)date('m', strtotime($tanggal))];
        $thn = date('Y', strtotime($tanggal));
        
        return $tgl . ' ' . $bln . ' ' . $thn;
    }
}