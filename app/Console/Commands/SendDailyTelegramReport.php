<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MoneyController;
use Telegram\Bot\Api;

class SendDailyTelegramReport extends Command
{
    protected $signature = 'telegram:daily-report';  // название команды
    protected $description = 'Отправить ежедневный отчет по погоде и валютам в Telegram';

    private array $cities = [
        'Красноярск' => 'Krasnoyarsk',
        'Омск' => 'Omsk',
        'Высокий' => 'Megion',
        'Ченстохова' => 'Czestochowa',
        'Штутгарт' => 'Stuttgart',
        'Нижний Новгород' => 'Nizhny Novgorod',
    ];

    private array $citiesParents = [
        'Высокий' => 'Megion',
        'Ченстохова' => 'Czestochowa',
        'Нижний Новгород' => 'Nizhny Novgorod',
    ];

    private array $citiesCusisns = [
        'Высокий' => 'Megion',
        'Тюмень' => 'Czestochowa',
        'Гютерсло' => 'Stuttgart',
        'Нижние Серги' => 'Nizhny Novgorod',
    ];

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
