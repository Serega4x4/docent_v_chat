<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class Service
{
    public function weather($chat_id, $message_text, $message_id, $cities, $telegram)
    {
        if (str_contains(mb_strtolower($message_text), 'погода')) {
            $apiKey = config('services.telegram.open_weather');

            $weatherInfo = [];

            foreach ($cities as $label => $city) {
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

            $telegram->sendMessage([
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
