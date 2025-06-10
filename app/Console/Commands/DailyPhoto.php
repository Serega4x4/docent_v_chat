<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;

class DailyPhoto extends Command
{
    protected $signature = 'telegram:daily-photo';
    protected $description = 'Отправка одного фото в чат каждый день';
    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(): void
    {
        $chatId = config('services.telegram.chat_id_friend');

        $photos = app(\App\Services\GoogleDriveService::class)->listImages();

        $indexFile = storage_path('app/photo_index.txt');

        if (!file_exists($indexFile)) {
            file_put_contents($indexFile, 0);
        }

        $index = (int) file_get_contents($indexFile);

        if (empty($photos)) {
            $this->error('Нет фото для отправки.');
            return;
        }

        $photo = $photos[$index];

        try {
            $this->telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $photo['url'],
                'caption' => 'Ежедневная фотка 😊',
            ]);
            $this->info("Фото отправлено в чат: $chatId");
        } catch (\Exception $e) {
            $this->error("Ошибка при отправке в $chatId: " . $e->getMessage());
        }

        file_put_contents($indexFile, ($index + 1) % count($photos));

        $this->info("Фото #$index успешно отправлено.");
    }
}
