<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CensorshipController;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\MoneyController;
use Telegram\Bot\Api;

Route::post('/telegram/webhook', function (Request $request) {
    $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

    $censorshipController = app(CensorshipController::class);
    $greetingController = app(GreetingController::class);
    $keywordController = app(KeywordController::class);
    $moneyController = app(MoneyController::class);

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

        // Обработка валюты
        if ($moneyController->handle($chat_id, $message_text, $message_id)) {
            return response()->json(['status' => 'money']);
        }
        
        // Обработка ключевых слов
        $keywordController->handle($chat_id, $message_text, $message_id);
    }

    return response()->json(['status' => 'ok']);
});