<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class WikiController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle($chat_id, $message_text, $message_id)
    {
        if (preg_match('/что такое\s+([^.,!?;:"\'()\[\]]+)/iu', $message_text, $matches)) {
            $keyword = trim($matches[1]);

            $summary = $this->searchWikipedia($keyword);

            $this->telegram->sendMessage([
                'chat_id' => $chat_id,
                'text' => $summary,
                'reply_to_message_id' => $message_id,
            ]);

            return true;
        }

        return false;
    }

    private function searchWikipedia($keyword)
    {
        try {
            $keyword = trim(preg_replace('/\s+/', ' ', $keyword));
            if ($keyword === '') {
                return 'Чего?';
            }

            $keyword = mb_convert_case(mb_substr($keyword, 0, 1), MB_CASE_TITLE, 'UTF-8') . mb_substr($keyword, 1);
            $formattedKeyword = str_replace(' ', '_', $keyword);

            $response = Http::withHeaders([
                'User-Agent' => 'ChatBotTelegram Docent',
            ])->get('https://ru.wikipedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'titles' => $formattedKeyword,
                'prop' => 'extracts',
                'exintro' => true,
                'explaintext' => true,
                'redirects' => 1,
            ]);

            if (!$response->ok()) {
                return "Ошибка Википедии (код {$response->status()})";
            }

            $data = $response->json();
            $pages = $data['query']['pages'] ?? null;
            if (!$pages) {
                return "Не нашёл ничего по \"$keyword\"...";
            }

            $page = reset($pages);

            if (isset($page['extract']) && !empty(trim($page['extract']))) {
                $extract = mb_substr($page['extract'], 0, 4000);
                return $extract;
            }

            return "Век воли не видать, делов не знаю что такое \"$keyword\"...";
        } catch (\Exception $e) {
            // \Log::error('Wiki search error', ['message' => $e->getMessage()]);
            return 'Что-то я подзабыл совсем, спроси позже, обожди...';
        }
    }
}
