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
        $chatId = config('services.telegram.chat_id');


        // Погода
        $weatherController = new WeatherController($this->telegram);
        $weatherController->handle($chatId, 'погода', null);

        sleep(2); // чтобы Telegram не заблокировал из-за флуда

        // Валюта
        $moneyController = new MoneyController($this->telegram);
        $moneyController->handle($chatId, 'валюта', null);
    }
}
