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
        $timezoneArg = $this->argument('timezone');

        $birthdays = config('birthdays');

        foreach ($birthdays as $birthday) {
            if ($birthday['timezone'] !== $timezoneArg) {
                continue;
            }

            $now = Carbon::now($birthday['timezone'])->format('m-d');

            if ($now === $birthday['date']) {
                try {
                    $this->telegram->sendMessage([
                        'chat_id' => $birthday['chat_id'],
                        'text' => $birthday['message'],
                    ]);
                    $this->info("Поздравление отправлено: {$birthday['name']} в чат {$birthday['chat_id']}");
                } catch (\Exception $e) {
                    $this->error("Ошибка отправки в чат {$birthday['chat_id']}: {$e->getMessage()}");
                }
            } else {
                $this->line("Сегодня не день рождения у {$birthday['name']} ({$birthday['date']}) в {$birthday['timezone']} (текущая дата: $now)");
            }
        }

        return self::SUCCESS;
    }
}
