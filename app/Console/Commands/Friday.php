<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Carbon\Carbon;

class Friday extends Command
{
    protected $signature = 'telegram:friday'; // название команды
    protected $description = 'Отправить наставления в пятницу в конкретный чат';

    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(): void
    {
        //  проверка на пятницу
        if (Carbon::now('Asia/Krasnoyarsk')->dayOfWeek !== Carbon::FRIDAY) {
            $this->info('Сегодня не пятница — сообщение не отправлено.');
            return;
        }

        // $chatIds = config('services.telegram.chat_id');

        $chatIds = [
            config('services.telegram.chat_id_friend'), 
            config('services.telegram.chat_id_parents'), 
            config('services.telegram.chat_id_cousins'),
        ];

        $message = 'Ох, ПЯТНИЦА! Хорошо! За это можно и по рюмашечке!';

        foreach ($chatIds as $chatId) {
            try {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);
                $this->info("Сообщение отправлено в чат: $chatId");
            } catch (\Exception $e) {
                $this->error("Ошибка при отправке в $chatId: " . $e->getMessage());
            }
        }

        $this->info("Отправлено сообщение: $message");
    }
}
