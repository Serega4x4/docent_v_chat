<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class DeleteController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    // Загружаем список запрещённых слов
    private function loadForbiddenWords(): array
    {
        $filePath = storage_path('app/for_delete.txt');

        if (!file_exists($filePath)) return [];

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_map(fn($line) => mb_strtolower(trim($line), 'UTF-8'), $lines);
    }

    public function handle($chat_id, $message_text, $message_id): bool
    {
        $forbiddenWords = $this->loadForbiddenWords();

        if (empty($forbiddenWords)) return false;

        $text = mb_strtolower($message_text, 'UTF-8');

        foreach ($forbiddenWords as $word) {
            if (str_contains($text, $word)) {
                // Удаляем сообщение
                $this->telegram->deleteMessage([
                    'chat_id' => $chat_id,
                    'message_id' => $message_id,
                ]);

                return true;
            }
        }

        return false;
    }
}
