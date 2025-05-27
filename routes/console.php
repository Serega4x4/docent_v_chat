<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//////////////////////////////////

// Список временных зон
$timezones = [
    'Asia/Krasnoyarsk',
    'Asia/Omsk',
    'Asia/Yekaterinburg',
    'Europe/Warsaw',
    'Europe/Berlin',
];

// Погода и курс валют
Schedule::command('telegram:daily-report')
    ->dailyAt('07:00')
    ->timezone('Asia/Krasnoyarsk');

// Приветствия в чат без временных зон
Schedule::command('telegram:daily-hello')
        ->dailyAt('06:00')
        ->timezone('Asia/Krasnoyarsk');

// Предложение в пятницу
Schedule::command('telegram:friday')
        ->dailyAt('17:00')
        ->timezone('Asia/Krasnoyarsk');

// в воскресенье о понедельние
Schedule::command('telegram:sunday')
        ->dailyAt('18:00')
        ->timezone('Asia/Krasnoyarsk');

// Регистрация задач для приветствий и поздравлений с днём рождения
foreach ($timezones as $timezone) {
    // Приветствия в чат
    // Schedule::command("telegram:daily-hello {$timezone}")
    //     ->dailyAt('06:00')
    //     ->timezone('Asia/Krasnoyarsk');

    // Поздравления с днём рождения
    Schedule::command("telegram:happy-birthday {$timezone}")
        ->dailyAt('18:55')
        ->timezone($timezone);
}