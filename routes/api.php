<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CensorshipController;
use App\Http\Controllers\DeleteController;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\MoneyController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WeatherInCityController;
use App\Http\Controllers\WikiController;
use Telegram\Bot\Api;

Route::post('/telegram/webhook', function (Request $request) {
    $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

    $censorshipController = app(CensorshipController::class);
    $greetingController = app(GreetingController::class);
    $keywordController = app(KeywordController::class);
    $moneyController = app(MoneyController::class);
    $weatherController = app(WeatherController::class);
    $wikiController = app(WikiController::class);
    $deleteController = app(DeleteController::class);
    $weatherInCityController = app(WeatherInCityController::class);

    $update = $censorshipController->telegram->getWebhookUpdate();

    if (isset($update['message']['text'])) {
        $chat_id = $update['message']['chat']['id'];
        $message_text = $update['message']['text'];
        $message_id = $update['message']['message_id'];

        // Проверка на цензуру
        if ($censorshipController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'censored']);
        }

        // Обработка приветствий
        if ($greetingController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'greeted']);
        }

        // Обработка "валюта"
        if ($moneyController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'money']);
        }

        // Обработка команды "погода в <название города в именительном падеже>"
        if ($weatherInCityController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'weather_in_city']);
        }

        // Обработка команды "погода"
        if ($weatherController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'weather']);
        }

        // Обработка команды "что такое <сам вопрос для википедии>"
        if ($wikiController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'wiki']);
        }

        // удаление сообщений с ключевых слов
        if ($deleteController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'deleted']);
        }

        // Обработка ключевых слов
        $keywordController->handle($chat_id, $message_text, $message_id);
    }

    return response()->json(['status' => 'ok']);
});

Route::get('/ping', function () {
    return response('OK', 200);
});
