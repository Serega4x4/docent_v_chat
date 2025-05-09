<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class SendHello extends Command
{
    protected $signature = 'telegram:daily-hello {timezone}';
    protected $description = 'Отправить приветствие по часовому поясу в конкретный чат';

    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(): void
    {
        $timezone = $this->argument('timezone');
        $chatId = config('services.telegram.chat_id');

        $greetings = [
            'Asia/Krasnoyarsk'     => 'Шуматкечын ямле, Сергей. Илышым куанен шупшылаш жап эртен.',
            'Asia/Omsk'            => '@Bessovestnotalantlivi, сәлам! Яңа үрләр яуларга вакыт.',
            'Asia/Yekaterinburg'   => 'Salam aleykum, @KenanHM! Nətərsən?',
            'Europe/Warsaw'        => '@Tamagochi4x4, здравствуйте... В Ваше отсутствие всё было в порядке!',
            'Europe/Berlin'        => 'Guten Morgen, @KAGBest! Zeit, in den neuen Tag zu starten.',
        ];

        $message = $greetings[$timezone] ?? 'Доброе утро!';

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        $this->info("Отправлено сообщение для $timezone: $message");
    }
}
