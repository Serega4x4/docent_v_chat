<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class KeywordController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    // Загружаем ключевые слова
    private function loadKeywords()
    {
        $filePath = storage_path('app/keywords.txt');

        if (!file_exists($filePath)) {
            return [];
        }

        $words = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_map(fn($word) => mb_strtolower(trim($word), 'UTF-8'), $words);
    }

    // Загружаем ответы на ключевые слова
    private function loadKeywordResponses()
    {
        $filePath = storage_path('app/keyword_responses.txt');

        if (!file_exists($filePath)) {
            return [];
        }

        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Основной метод обработки сообщений
    public function handle($chat_id, $message_text, $message_id)
    {
        $keywords = $this->loadKeywords();
        $responses = $this->loadKeywordResponses();

        $message = mb_strtolower($message_text, 'UTF-8');

        // Проходим по ключевым словам и проверяем, есть ли совпадение
        foreach ($keywords as $index => $keyword) {
            if (str_contains($message, $keyword)) {

                // Получаем ответ по индексу ключевого слова
                $response = $responses[$index] ?? 'Интересное слово!';

                // Отправляем ответ
                $this->telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => str_replace('{keyword}', $keyword, $response),
                    'reply_to_message_id' => $message_id,
                ]);

                return true;
            }
        }

        return false;
    }
}
