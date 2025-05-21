<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MoneyController;
use Telegram\Bot\Api;

class SendDailyTelegramReport extends Command
{
    protected $signature = 'telegram:daily-report';
    protected $description = 'Отправить ежедневный отчет по погоде и валютам в Telegram';

    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(): void
    {
        $chatIds = config('services.telegram.chat_id');

        $weatherController = new WeatherController($this->telegram);
        $moneyController = new MoneyController($this->telegram);

        foreach ($chatIds as $chatId) {
            // Погода
            $weatherController->handle($chatId, 'погода', null);

            sleep(2);

            // Валюта
            $moneyController->handle($chatId, 'валюта', null);
        }
    }
}
