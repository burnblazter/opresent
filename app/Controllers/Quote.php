<?php

namespace App\Controllers;

class Quote extends BaseController
{
    public function random()
    {
        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get('https://quotes.liupurnomo.com/api/quotes/random', [
                'timeout' => 3,
                'http_errors' => false
            ]);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['data'])) {
                    return $this->response->setJSON($data['data']);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Quote API Error: ' . $e->getMessage());
        }
        
        // Fallback quote
        return $this->response->setJSON([
            'text' => 'Keberhasilan adalah hasil dari persiapan, kerja keras, dan belajar dari kegagalan.',
            'author' => 'Colin Powell',
            'category' => 'motivasi'
        ]);
    }
}