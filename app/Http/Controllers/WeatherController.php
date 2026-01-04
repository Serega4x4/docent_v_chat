<?php

namespace App\Http\Controllers;

use App\Services\Service;
use Telegram\Bot\Api;

class WeatherController extends BaseController
{
    protected $telegram;

    // указываем требуемые города для вывода погоды
    private array $cities = [
        'Москва' => 'Moscow',
        'Кувай' => 'Kuwait City',
        'Оймякон' => 'Oymyakon',
        'Париж' => 'Paris',
        'Рим' => 'Rome',
        'Лондон' => 'London',
        'Токио' => 'Tokyo',
        'Пекин' => 'Beijing',
    ];

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
        'Сургут' => 'Surgut',
        'Тюмень' => 'Tyumen',
        'Нижние Серги' => 'Nizhniye Sergi',
        'Ченстохова' => 'Czestochowa',
        'Гютерсло' => 'Gutersloh',
    ];

    public function __construct(Service $service, Api $telegram)
    {
        parent::__construct($service);
        $this->telegram = $telegram;
    }

    public function handle($chat_id, $message_text, $message_id)
    {
        $chatIdsFriend = config('services.telegram.chat_id_friend');
        $chatIdsParent = config('services.telegram.chat_id_parents');
        $chatIdsCousin = config('services.telegram.chat_id_cousins');

        $chatCities = [
            $chatIdsFriend => $this->citiesFriends,
            $chatIdsParent => $this->citiesParents,
            $chatIdsCousin => $this->citiesCousisns,
        ];

        $cities = $chatCities[$chat_id] ?? $this->cities;

        return $this->service->weather($chat_id, $message_text, $message_id, $cities, $this->telegram);
    }
}
