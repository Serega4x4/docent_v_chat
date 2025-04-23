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
        if (preg_match('/^что такое (.+)/iu', $message_text, $matches)) {
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
            $response = Http::get('https://ru.wikipedia.org/api/rest_v1/page/summary/' . urlencode($keyword));

            if ($response->ok() && isset($response->json()['extract'])) {
                return $response->json()['extract'];
            } else {
                return "Делов не знаю, отвечаю \"$keyword\".";
            }
        } catch (\Exception $e) {
            return "Что то я подзабыл совсем, спроси позже, обожди.";
        }
    }
}
