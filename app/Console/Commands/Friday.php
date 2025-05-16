<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Carbon\Carbon;

class Friday extends Command
{
    protected $signature = 'telegram:friday'; // общее приветствие
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

        $chatId = config('services.telegram.chat_id');

        $message = 'Ох, ПЯТНИЦА! Хорошо! За это можно и по рюмашечке!';

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        $this->info("Отправлено сообщение: $message");
    }
}
