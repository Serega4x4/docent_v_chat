<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class SendHello extends Command
{
    // protected $signature = 'telegram:daily-hello {timezone}';  // для отдельных временных зон
    protected $signature = 'telegram:daily-hello'; // название команды
    protected $description = 'Отправить приветствие по часовому поясу в конкретный чат';

    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(): void
    {
        // общее приветствие
        // $chatIds = config('services.telegram.chat_id');

        $chatIds = [
            config('services.telegram.chat_id_friend'), 
            config('services.telegram.chat_id_parents'), 
            config('services.telegram.chat_id_cousins'),
        ];

        $message = 'Приветствую, Родня! Как спалось? Как настроение?'; // если только одно приветствие

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

        ///////////////////////////////////////////////////////////////////////////////////////////////////////

        // для отдельных временных зон
        // $timezone = $this->argument('timezone');
        // $chatId = config('services.telegram.chat_id');

        // $greetings = [
        //     // 'Asia/Krasnoyarsk'     => 'Шуматкечын ямле, @Andriyanich85. Илышым куанен шупшылаш жап эртен. (Доброе утро, Сергей. Пора наслаждаться жизнью)',
        //     // 'Asia/Omsk'            => '@Bessovestnotalantlivi, сәлам! Яңа үрләр яуларга вакыт. (Артур, салям! Пора покарять новые вершины)',
        //     // 'Asia/Yekaterinburg'   => 'Salam aleykum, @KenanHM! Nətərsən? (Привет, Кенан! Как дела?)',
        //     // 'Europe/Warsaw'        => '@Tamagochi4x4, здравствуйте... В Ваше отсутствие всё было в порядке! (Stwórco, cześć... Wszystko było w porządku, gdy Cię nie było.)',
        //     // 'Europe/Berlin'        => 'Guten Morgen, @KAGBest! Zeit, in den neuen Tag zu starten. (Доброе утро, Артем Генадиевич! Пора вставать в новый день)',
        // ];

        // $message = $greetings[$timezone] ?? 'Доброе утро!';

        // $this->telegram->sendMessage([
        //     'chat_id' => $chatId,
        //     'text' => $message,
        // ]);

        // $this->info("Отправлено сообщение для $timezone: $message");
    }
}
