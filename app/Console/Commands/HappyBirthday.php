<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Telegram\Bot\Api;

class HappyBirthday extends Command
{
    protected $signature = 'telegram:happy-birthday {timezone}'; // название команды
    protected $description = 'Отправить поздравления с днем рождения в определенный чат по часовому поясу';

    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle()
    {
        $timezone = $this->argument('timezone');
        $chatIds = config('services.telegram.chat_id');

        // Список Дней Рождения
        $birthdays = [
            'Asia/Krasnoyarsk' => '12-21',
            'Asia/Omsk' => '08-05',
            'Asia/Yekaterinburg' => '10-18',
            'Europe/Warsaw' => '07-10',
            'Europe/Berlin' => '04-11',
        ];

        // Получаем текущую дату в нужной таймзоне
        $now = Carbon::now($timezone)->format('m-d');

        if (!isset($birthdays[$timezone])) {
            $this->warn("Неизвестная таймзона: $timezone");
            return self::FAILURE;
        }

        if ($now !== $birthdays[$timezone]) {
            $this->info("Сегодня не день рождения для $timezone ($now). Сообщение не отправлено.");
            return self::SUCCESS;
        }

        $greetings = [
            'Asia/Krasnoyarsk' => 'Серёга! Поздравлям тебя с Днём рождения! Желаем крепкого здоровья, неиссякаемой энергии и успехов во всех начинаниях. Пусть каждый день приносит радость, а рядом всегда будут любящие и близкие люди. Счастья, процветания и ярких моментов! С наилучшими пожеланиями, твои друзья',
            'Asia/Omsk' => 'Артур! От всей души поздравляем с Днём рождения! Желаем крепкого здоровья, счастья и успехов во всём. Пусть жизнь радует яркими моментами, а рядом всегда будут близкие и любящие люди. Оставайся таким же душевным, гордым своим татарским духом и полным энергии! С лучшими пожеланиями, твои друзья',
            'Asia/Yekaterinburg' => 'Кенан! Сердечно поздравляем тебя с Днём рождения! Желаем крепкого здоровья, бесконечного счастья и больших успехов во всех делах. Пусть жизнь будет полна ярких событий, тепла близких и радости. Оставайся таким же гордым, душевным и энергичным, неся свет азербайджанской души! С наилучшими пожеланиями, твои друзья',
            'Europe/Warsaw' => 'Создатель, с днем рождения Вас. ',
            'Europe/Berlin' => 'Тёма! От всей души поздравляем тебя с Днём рождения! Желаем крепкого здоровья, счастья и успехов во всех начинаниях. Пусть жизнь радует яркими моментами, а рядом всегда будут близкие и дорогие люди. Оставайся таким же надёжным, целеустремлённым и полным немецкого духа! С лучшими пожеланиями, твои друзья',
        ];

        $message = $greetings[$timezone] ?? 'С днем рождения, Родня!';

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

        $this->info("Отправлено сообщение для $timezone: $message");
        return self::SUCCESS;
    }
}
