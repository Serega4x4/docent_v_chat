<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class VoiceCounterController extends Controller
{
    protected $telegram;
    protected string $counterFile;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        $this->counterFile = storage_path('app/voice_counter.txt');
    }

    private function getCount(): int
    {
        if (!file_exists($this->counterFile)) {
            return 0;
        }

        return (int) file_get_contents($this->counterFile);
    }

    private function loadVoiceResponses()
    {
        $filePath = storage_path('app/voice_responses.txt');

        if (!file_exists($filePath)) {
            return [];
        }

        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public function handle($chat_id, $message_id): bool
    {
        // Получаем текущий счёт
        $count = $this->getCount();
        $count++;

        $responses = $this->loadVoiceResponses();

        file_put_contents($this->counterFile, $count);

        $response = empty($responses)
            ? 'Сколько уже можно говорить!' 
            : $responses[array_rand($responses)];

        if ($count % 3 === 0) {
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
