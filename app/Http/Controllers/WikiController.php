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
        if (preg_match('/что такое\s+(.+)/iu', $message_text, $matches)) {
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
            $keyword = mb_convert_case(mb_substr($keyword, 0, 1), MB_CASE_TITLE, "UTF-8") . mb_substr($keyword, 1);
            $formattedKeyword = str_replace(' ', '_', $keyword);

            $response = Http::get('https://ru.wikipedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'titles' => $formattedKeyword,
                'prop' => 'extracts',
                'exintro' => true,
                'explaintext' => true,
            ]);

            if ($response->ok()) {
                $data = $response->json();
                $pages = $data['query']['pages'] ?? null;

                $page = reset($pages);

                if ($page && isset($page['extract']) && !isset($page['missing'])) {
                    return $page['extract'];
                } else {
                    return "Век воли не видать, делов не знаю что такое \"$keyword\"...";
                }
            } else {
                return "Какая-то параша в Википедии...";
            }
        } catch (\Exception $e) {
            return "Что-то я подзабыл совсем, спроси позже, обожди...";
        }
    }
}
