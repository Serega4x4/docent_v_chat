<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
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
            Log::info('Нет forward_from_chat в сообщении:', $message);
            return false;
        }

        $forwardChat = $message['forward_from_chat'];
        Log::info('forward_from_chat:', $forwardChat);

        $bannedChannels = array_map('strtolower', $this->loadBannedChannels());

        // Получаем username или id канала
        $channelIdOrUsername = isset($forwardChat['username']) ? '@' . strtolower($forwardChat['username']) : (string) $forwardChat['id'];

        Log::info('Channel identifier: ' . $channelIdOrUsername);
        Log::info('Banned channels: ' . json_encode($bannedChannels));

        if (in_array($channelIdOrUsername, $bannedChannels, true)) {
            $this->telegram->deleteMessage([
                'chat_id' => $chat_id,
                'message_id' => $message['message_id'],
            ]);
            Log::info('Deleted message from banned channel: ' . $channelIdOrUsername);

            return true;
        }

        return false;
    }
}
