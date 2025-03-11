<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $message = mb_strtolower($message, 'UTF-8');

        foreach ($bannedWords as $bannedWord) {
            if (strpos($message, $bannedWord) !== false) {
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

            if (empty($responses)) {
                $response = 'Выражайтесь культурнее!'; // fallback
            } else {
                // Выбираем случайный ответ из массива
                $response = $responses[array_rand($responses)];
            }

            // Отправляем сообщение в чат
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
