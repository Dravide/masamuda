<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class OneApiService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('oneapi.base_url');
        $this->apiKey = config('oneapi.api_key');
    }

    /**
     * Send a message via OneAPI using native Curl.
     *
     * @param string $recipient
     * @param string $message
     * @return array
     */
    public function sendMessage($recipient, $message)
    {
        $curl = curl_init();

        // Ensure base URL doesn't have trailing slash
        $url = rtrim($this->baseUrl, '/');

        // If the URL already ends with /messages, don't append it again
        if (!str_ends_with($url, '/messages')) {
            $url .= '/messages';
        }

        $payload = [
            "recipient_type" => "individual",
            "to" => $recipient,
            "type" => "text",
            "text" => [
                "body" => $message
            ]
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ),
        ));

        // Log request for debugging
        Log::info("OneAPI Curl Request to: $url", [
            'recipient' => $recipient,
            'payload_json' => json_encode($payload)
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            Log::error('OneAPI Curl Connection Error: ' . $error);
            return [
                'success' => false,
                'error' => "Curl Error: " . $error,
            ];
        }

        Log::info("OneAPI Response [{$httpCode}]: " . $response);

        // API checks
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'data' => json_decode($response, true),
            ];
        }

        return [
            'success' => false,
            'error' => "HTTP {$httpCode}: " . $response,
            'status' => $httpCode,
        ];
    }
}
