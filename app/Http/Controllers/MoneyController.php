<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class MoneyController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle($chat_id, $message_text, $message_id)
    {
        if (trim(mb_strtolower($message_text)) === 'валюта') {
            $response = Http::get('https://www.cbr-xml-daily.ru/daily_json.js');

            if ($response->successful()) {
                $data = $response->json();

                $usd = $data['Valute']['USD']['Value'] ?? null;
                $eur = $data['Valute']['EUR']['Value'] ?? null;
                $pln = $data['Valute']['PLN']['Value'] ?? null;

                if ($usd && $eur && $pln) {
                    $text = "📈 *По баблу у нас сегодня:*\n\n";
                    $text .= "🇺🇸 *Доллар США:* {$usd} руб.\n";
                    $text .= "🇪🇺 *Евро:* {$eur} руб.\n";
                    $text .= "🇵🇱 *Польский злотый:* {$pln} руб.";

                    $this->telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => $text,
                        'parse_mode' => 'Markdown',
                        'reply_to_message_id' => $message_id,
                    ]);

                    return true;
                } else {
                    $this->telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => 'Пока мне одна утка не донесла по капусте что к чему.',
                        'reply_to_message_id' => $message_id,
                    ]);
                    return true;
                }
            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => 'Облава! Менты спалили что мы баблом ворочаем.',
                    'reply_to_message_id' => $message_id,
                ]);
                return true;
            }
        }

        return false;
    }
}
