<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class PikabuController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }



    // Загружаем ответы со стикерами на ключевые слова
    private function loadKeywordResponses()
    {
        $filePath = storage_path('app/response_for_pikabu.txt');

        if (!file_exists($filePath)) {
            return [];
        }

        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Основной метод обработки сообщений
    public function handle($chat_id, $message_text, $message_id)
    {
        if (str_contains(mb_strtolower($message_text), "https://pikabu.ru")) {

            $response = $this->loadKeywordResponses();

            $response = empty($response)
                ? 'Выражайтесь культурнее!'
                : $response[array_rand($response)];

            // Отправляем ответ в чат
             $this->telegram->sendSticker([
                    'chat_id' => $chat_id,
                    'sticker' => $response,
                    'reply_to_message_id' => $message_id,
                ]);

                return true;
        }

        return false;
    }
}
