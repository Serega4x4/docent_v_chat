<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class WeatherInCityController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle($chat_id, $message_text, $message_id)
    {
        if (preg_match('/погода в\s+(.+)/iu', $message_text, $matches)) {
            $city = trim($matches[1]);

            $apiKey = env('OPENWEATHER_API_KEY');

            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang' => 'ru',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $temp = $data['main']['temp'] ?? 'неизвестно';
                $description = $data['weather'][0]['description'] ?? 'нет описания';

                $text = "*Погода в {$city}:* {$temp}°C, {$description}";
            } else {
                $text = "Чё то не могу найти градусник в *{$city}*. Походу попутал название";
            }

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
