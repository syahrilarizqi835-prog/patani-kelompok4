<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\Sawah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    const BATAS_GRATIS = 5;
    const GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';

    public function index()
    {
        $user        = Auth::user();
        $isPremium   = $user->isPremium();
        $chatHariIni = $user->chatHariIni();
        $sisaPesan   = max(0, self::BATAS_GRATIS - $chatHariIni);
        $riwayat     = ChatbotConversation::where('user_id', $user->id)
                        ->latest()->take(20)->get()->reverse();
        $sawahList   = Sawah::where('user_id', $user->id)->get();

        return view('dashboard.chatbot', compact(
            'isPremium', 'chatHariIni', 'sisaPesan', 'riwayat', 'sawahList'
        ));
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        // Cek limit gratis
        if (!$user->isPremium() && $user->chatHariIni() >= self::BATAS_GRATIS) {
            return response()->json([
                'error'   => 'limit',
                'message' => 'Anda telah mencapai batas 5 pesan hari ini. Upgrade ke Premium untuk chat unlimited.',
            ], 403);
        }

        $request->validate(['message' => 'required|string|max:1000']);
        $message = $request->input('message');

        // Konteks sawah petani
        $sawahList    = Sawah::where('user_id', $user->id)->get();
        $konteksSawah = '';
        if ($sawahList->count() > 0) {
            $konteksSawah = "\n\nData sawah petani ini:\n";
            foreach ($sawahList as $s) {
                $konteksSawah .= "- {$s->nama_sawah}: luas {$s->luas} Ha, kondisi tanah {$s->kondisi_tanah}, kondisi air {$s->kondisi_air}, fase tanam {$s->fase_tanam}, jenis padi {$s->jenis_padi}\n";
            }
        }

        // System prompt
        $systemPrompt = "Kamu adalah PATANI Assistant, asisten AI pertanian khusus untuk petani padi di wilayah Indramayu, Jawa Barat, Indonesia.

Tugas kamu:
- Memberikan saran pertanian yang praktis, spesifik, dan berbasis ilmu agronomi
- Menjawab pertanyaan tentang budidaya padi, hama, penyakit, pemupukan, irigasi, dan panen
- Menyesuaikan saran dengan kondisi lahan petani yang sudah tersedia di sistem
- Menggunakan bahasa Indonesia yang mudah dipahami petani
- Memberikan rekomendasi dosis, waktu, dan cara yang konkret

Batasan:
- Fokus hanya pada pertanian padi dan pertanian umum
- Jika ditanya di luar topik pertanian, arahkan kembali ke topik pertanian
- Selalu pertimbangkan kondisi cuaca Indramayu yang tropis lembab
{$konteksSawah}

Jawab dengan singkat, jelas, dan actionable (bisa langsung dipraktikkan). Gunakan bahasa Indonesia.";

        // Ambil riwayat percakapan (konteks 5 terakhir)
        $riwayat = ChatbotConversation::where('user_id', $user->id)
            ->latest()->take(5)->get()->reverse();

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($riwayat as $r) {
            $messages[] = ['role' => 'user',      'content' => $r->message];
            $messages[] = ['role' => 'assistant', 'content' => $r->response];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        // Call Groq API
        try {
            $apiKey = config('services.groq.api_key');
            if (empty($apiKey)) {
                \Log::error('GROQ_API_KEY is not set in config/services.php or .env');
                throw new \Exception('API key tidak dikonfigurasi');
            }

            $apiResp = Http::timeout(30)
                ->withToken($apiKey)
                ->post(self::GROQ_API_URL, [
                    'model'       => config('services.groq.model', 'llama-3.1-8b-instant'),
                    'messages'    => $messages,
                    'temperature' => 0.7,
                    'max_tokens'  => 1024,
                ]);

            if (!$apiResp->successful()) {
                \Log::error('Groq API Error: ' . $apiResp->body());
                throw new \Exception('Groq API error: ' . $apiResp->status());
            }

            $data       = $apiResp->json();
            $aiResponse = $data['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respons dari AI.';
            $tokens     = $data['usage']['total_tokens'] ?? 0;

        } catch (\Exception $e) {
            \Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'api_error',
                'message' => 'Gagal menghubungi AI: ' . $e->getMessage(),
            ], 500);
        }

        // Simpan ke database
        ChatbotConversation::create([
            'user_id'     => $user->id,
            'message'     => $message,
            'response'    => $aiResponse,
            'context'     => 'pertanian',
            'tipe'        => 'teks',
            'tokens_used' => $tokens,
        ]);

        return response()->json([
            'response'  => $aiResponse,
            'isPremium' => $user->isPremium(),
            'sisaPesan' => $user->isPremium() ? null : max(0, self::BATAS_GRATIS - $user->chatHariIni()),
        ]);
    }

    public function upgradePremium(Request $request, $userId)
    {
        $target = \App\Models\User::findOrFail($userId);
        $bulan  = $request->input('bulan', 1);
        $target->update([
            'is_premium'    => true,
            'premium_until' => now()->addMonths($bulan),
        ]);
        return response()->json(['message' => "Premium aktif {$bulan} bulan untuk {$target->name}"]);
    }
}