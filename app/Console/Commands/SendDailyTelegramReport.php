<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MoneyController;
use App\Service\Service;
use Telegram\Bot\Api;

class SendDailyTelegramReport extends Command
{
    protected $signature = 'telegram:daily-report'; // название команды
    protected $description = 'Отправить ежедневный отчет по погоде и валютам в Telegram';

    private array $citiesFriends = [
        'Высокий' => 'Megion',
        'Омск' => 'Omsk',
        'Красноярск' => 'Krasnoyarsk',
        'Ченстохова' => 'Czestochowa',
        'Штутгарт' => 'Stuttgart',
    ];

    private array $citiesParents = [
        'Высокий' => 'Megion',
        'Нижний Новгород' => 'Nizhny Novgorod',
        'Ченстохова' => 'Czestochowa',
    ];

    private array $citiesCousisns = [
        'Высокий' => 'Megion',
        'Тюмень' => 'Tyumen',
        'Нижние Серги' => 'Nizhniye Sergi',
        'Ченстохова' => 'Czestochowa',
        'Гютерсло' => 'Gutersloh',
    ];

    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(): void
    {
        // $chatIds = config('services.telegram.chat_id');

        $chatIds = [
            config('services.telegram.chat_id_friend'), 
            config('services.telegram.chat_id_parents'), 
            config('services.telegram.chat_id_cousins'),
        ];

        $chatCities = [
            $chatIds[0] => $this->citiesFriends,
            $chatIds[1] => $this->citiesParents,
            $chatIds[2] => $this->citiesCousisns,
        ];

        $weather = app(Service::class);
        $money = app(MoneyController::class);

        foreach ($chatIds as $chatId) {
            // Погода
            $cities = $chatCities[$chatId] ?? $this->citiesFriends;
            $weather->weather($chatId, 'погода', null, $cities, $this->telegram);

            sleep(2);

            // Валюта
            $money->handle($chatId, 'валюта', null);
        }
    }
}
