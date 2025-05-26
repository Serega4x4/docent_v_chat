<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CensorshipController;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\MoneyController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WeatherInCityController;
use App\Http\Controllers\WikiController;
use App\Http\Controllers\DeleteController;


Route::post('/telegram/webhook', function (Request $request) {
    $censorshipController = app(CensorshipController::class);
    $greetingController = app(GreetingController::class);
    $moneyController = app(MoneyController::class);
    $weatherInCityController = app(WeatherInCityController::class);
    $weatherController = app(WeatherController::class);
    $wikiController = app(WikiController::class);
    $keywordController = app(KeywordController::class);
    $stickerController = app(StickerController::class);
    // $deleteController = app(DeleteController::class);

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

        // Обработка ключевых слов и ответ стикером
        if ($stickerController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'sticker']);
        }

        // Обработка ключевых слов
        if ($keywordController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'key_word']);
        }

        // удаление сообщений с ключевых слов
        // if ($deleteController->handle($chat_id, $message_text, $message_id)) {
        //     return response()->json(['status' => 'deleted']);
        // }
    }

    return response()->json(['status' => 'ok']);
});

Route::get('/run-scheduler', function (Request $request) {
    $token = $request->query('token');

    if ($token !== env('PING_SECRET')) {
        abort(403, 'Access denied');
    }

    Artisan::call('schedule:run');

    return response('Scheduler executed');
});
