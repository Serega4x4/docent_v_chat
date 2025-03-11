<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;

class GreetingController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    // Загружаем список слов, которые считаем приветствиями
    private function loadGreetings()
    {
        $filePath = storage_path('app/greetings.txt');

        if (!file_exists($filePath)) {
            return ['привет']; // дефолтное слово
        }

        $words = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_map(fn($word) => mb_strtolower(trim($word), 'UTF-8'), $words);
    }

    // Загружаем список возможных ответов на приветствия
    private function loadGreetingResponses()
    {
        $filePath = storage_path('app/greeting_responses.txt');

        if (!file_exists($filePath)) {
            return ['Привет! Чем могу помочь?']; // дефолтный ответ
        }

        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Основной метод обработки приветствия
    public function handle($chat_id, $message_text, $message_id)
    {
        $greetings = $this->loadGreetings();
        $message = mb_strtolower($message_text, 'UTF-8');

        // Ищем, содержит ли сообщение любое из приветственных слов
        foreach ($greetings as $greeting) {
            if (str_contains($message, $greeting)) {

                $responses = $this->loadGreetingResponses();

                // Если нет ответов, используем дефолтный
                if (empty($responses)) {
                    $response = 'Привет!';
                } else {
                    // Выбираем случайный ответ из списка
                    $response = $responses[array_rand($responses)];
                }

                // Отправляем сообщение обратно в Telegram
                $this->telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $response,
                    'reply_to_message_id' => $message_id,
                ]);

                return true;
            }
        }

        return false;
    }
}
