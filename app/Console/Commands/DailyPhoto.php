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
        $chatIds = [
            config('services.telegram.chat_id_friend'), 
            // config('services.telegram.chat_id_parents'), 
            // config('services.telegram.chat_id_cousins'),
        ];

        $data = json_decode(Storage::get('photos.json'), true);
        $index = $data['index'];
        $photos = $data['photos'];

        if (empty($photos)) {
            $this->error('Нет фото для отправки.');
            return;
        }

        $photo = $photos[$index];

        foreach ($chatIds as $chatId) {
            try {
                $this->telegram->sendPhoto([
                    'chat_id' => $chatId,
                    'photo' => $photo,
                    'caption' => 'Ежедневная фотка 😊',
                ]);
                $this->info("Фото отправлено в чат: $chatId");
            } catch (\Exception $e) {
                $this->error("Ошибка при отправке в $chatId: " . $e->getMessage());
            }
        }

        // Увеличиваем индекс или сбрасываем
        $data['index'] = ($index + 1) % count($photos);
        Storage::put('photos.json', json_encode($data, JSON_PRETTY_PRINT));

        $this->info("Фото #$index успешно отправлено.");
    }
}
