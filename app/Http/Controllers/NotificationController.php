<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Send a WhatsApp notification via Fonnte API.
     */
    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string',
            'message' => 'required|string',
        ]);

        $token = config('services.fonnte.token');

        if (!$token) {
            Log::warning('Fonnte token tidak dikonfigurasi di .env');
            // Jika token tidak ada, kita tetap return link wa.me sebagai fallback di frontend
            return response()->json([
                'success' => false,
                'message' => 'Fonnte token belum dikonfigurasi. Mengalihkan ke link WhatsApp manual.',
                'fallback_url' => 'https://wa.me/' . preg_replace('/\D/', '', $request->phone) . '?text=' . urlencode($request->message)
            ], 200); // Kita beri 200 agar frontend bisa handle link-nya
        }

        // Normalize phone number: strip non-digits, replace leading 0 with 62
        $phone = preg_replace('/\D/', '', $request->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => [
                    'target'  => $phone,
                    'message' => $request->message,
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $token,
                ],
                CURLOPT_TIMEOUT => 15,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['status']) && $result['status'] === true) {
                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp berhasil dikirim ke ' . $phone,
                ]);
            } else {
                Log::error('Fonnte API error', ['response' => $result, 'http_code' => $httpCode]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim WA otomatis: ' . ($result['reason'] ?? 'Unknown error'),
                    'fallback_url' => 'https://wa.me/' . $phone . '?text=' . urlencode($request->message)
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Fonnte curl exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error koneksi ke Fonnte. Mengalihkan ke link WhatsApp manual.',
                'fallback_url' => 'https://wa.me/' . $phone . '?text=' . urlencode($request->message)
            ], 200);
        }
    }
}
