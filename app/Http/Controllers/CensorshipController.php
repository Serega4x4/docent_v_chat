<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class CensorshipController extends Controller
{
    public $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    // Загружаем список запрещённых слов
    private function loadBannedWords()
    {
        $filePath = storage_path('app/banned_words.txt');

        if (!file_exists($filePath)) {
            return []; // Если файла нет, возвращаем пустой массив
        }

        // Массив слов в нижнем регистре
        $words = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_map(fn($word) => mb_strtolower(trim($word), 'UTF-8'), $words);
    }

    // Загружаем список ответов на нарушение цензуры
    private function loadCensorshipResponses()
    {
        $filePath = storage_path('app/censorship_responses.txt');

        if (!file_exists($filePath)) {
            return ['Выражайтесь культурнее!']; // Ответ по умолчанию
        }

        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Проверяем, есть ли запрещённые слова в сообщении
    private function containsBannedWords($message)
    {
        $bannedWords = $this->loadBannedWords();

        if (empty($bannedWords)) return false;

        $message = mb_strtolower($message, 'UTF-8');

        // Очищаем от пунктуации, оставляем только слова
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', '', $message);

        // Разбиваем на отдельные слова
        $words = preg_split('/\s+/', $cleanMessage, -1, PREG_SPLIT_NO_EMPTY);

        // Сравниваем каждое слово с бан-листом
        foreach ($words as $word) {
            if (in_array($word, $bannedWords, true)) {
                return true;
            }
        }

        return false;
    }

    // Основной метод обработки сообщений
    public function handle($chat_id, $message_text, $message_id)
    {
        if ($this->containsBannedWords($message_text)) {

            $responses = $this->loadCensorshipResponses();

            $response = empty($responses)
                ? 'Выражайтесь культурнее!'
                : $responses[array_rand($responses)];

            // Отправляем ответ в чат
            $this->telegram->sendMessage([
                'chat_id' => $chat_id,
                'text' => $response,
                'reply_to_message_id' => $message_id,
            ]);

            return true;
        }

        return false;
    }
}
