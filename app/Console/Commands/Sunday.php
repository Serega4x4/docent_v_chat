<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Carbon\Carbon;

class Sunday extends Command
{
    protected $signature = 'telegram:sunday';  // название команды
    protected $description = 'Отправить предупреждение о предстоящем понедельнике в конкретный чат';

    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(): void
    {
        //  проверка на пятницу
        if (Carbon::now('Asia/Krasnoyarsk')->dayOfWeek !== Carbon::SUNDAY) {
            $this->info('Сегодня не пятница — сообщение не отправлено.');
            return;
        }

        // $chatIds = config('services.telegram.chat_id');

        $chatIds = [
            config('services.telegram.chat_id_friend'), 
            config('services.telegram.chat_id_parents'), 
            config('services.telegram.chat_id_cousins'),
        ];
        
        $message = 'Мне очень жаль, но завтра понедельник!';

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
