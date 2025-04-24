<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class WeatherController extends Controller
{
    protected $telegram;

    // указываем требуемые города для вывода погоды 
    private array $cities = [
        'Красноярск' => 'Krasnoyarsk',
        'Омск' => 'Omsk',
        'Мегион (Высокоград)' => 'Megion',
        'Ченстохова' => 'Czestochowa',
        'Штутгарт' => 'Stuttgart',
        'Нижний Новгород' => 'Nizhny Novgorod',
    ];

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle($chat_id, $message_text, $message_id)
    {
        if (str_contains(mb_strtolower($message_text), 'погода')) {
            $apiKey = env('OPENWEATHER_API_KEY');

            $weatherInfo = [];

            foreach ($this->cities as $label => $city) {
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'lang' => 'ru',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $temp = $data['main']['temp'] ?? null;
                    $description = $data['weather'][0]['description'] ?? 'Так ты определись';

                    $weatherInfo[] = "*{$label}*: {$temp}°C, {$description}";
                } else {
                    $weatherInfo[] = "*{$label}*: Что то не то.";
                }
            }

            $text = "*Ну чё, по погоде у нас сегодня:*\n\n" . implode("\n", $weatherInfo);

            $this->telegram->sendMessage([
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $message_id,
            ]);

            return true;
        }

        return false;
    }
}
