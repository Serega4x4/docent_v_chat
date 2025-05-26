<?php

namespace App\Http\Controllers;

use App\Services\Weather\Service;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class WeatherController extends BaseController
{
    protected $telegram;

    // указываем требуемые города для вывода погоды
    private array $cities = [
        'Красноярск' => 'Krasnoyarsk',
        'Омск' => 'Omsk',
        'Высокий' => 'Megion',
        'Ченстохова' => 'Czestochowa',
        'Штутгарт' => 'Stuttgart',
        'Нижний Новгород' => 'Nizhny Novgorod',
    ];

    public function __construct(Service $service, Api $telegram)
    {
        parent::__construct($service);
        $this->telegram = $telegram;
    }

    public function handle($chat_id, $message_text, $message_id)
    {
        $service = $this->service->weather($chat_id, $message_text, $message_id, $this->cities);
    }
}
