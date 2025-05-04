<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class ChanelDeleteController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    // Загружаем список ID запрещённых каналов (username или ID)
    private function loadBannedChannels(): array
    {
        $filePath = storage_path('app/banned_channels.txt');

        if (!file_exists($filePath)) {
            return [];
        }

        $channels = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_map('trim', $channels);
    }

    // Основной метод обработки пересланных сообщений
    public function handle($chat_id, $message): bool
    {
        if (!isset($message['forward_from_chat'])) {
            return false;
        }

        $forwardChat = $message['forward_from_chat'];
        $bannedChannels = $this->loadBannedChannels();

        $channelIdOrUsername = isset($forwardChat['username']) ? '@' . strtolower($forwardChat['username']) : (string) $forwardChat['id'];

        $bannedChannels = array_map('strtolower', $this->loadBannedChannels());

        if (in_array($channelIdOrUsername, $bannedChannels, true)) {
            $this->telegram->deleteMessage([
                'chat_id' => $chat_id,
                'message_id' => $message['message_id'],
            ]);

            return true;
        }

        return false;
    }
}
