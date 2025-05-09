<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Погода и курс валют
Schedule::command('telegram:daily-report')->dailyAt('20:00')->timezone('Asia/Krasnoyarsk');

// Приветствия в чат
Schedule::command('telegram:daily-hello Asia/Krasnoyarsk')
    ->dailyAt('06:00')
    ->timezone('Asia/Krasnoyarsk');

Schedule::command('telegram:daily-hello Asia/Omsk')
    ->dailyAt('06:00')
    ->timezone('Asia/Omsk');

Schedule::command('telegram:daily-hello Asia/Yekaterinburg')
    ->dailyAt('06:00')
    ->timezone('Asia/Yekaterinburg');

Schedule::command('telegram:daily-hello Europe/Warsaw')
    ->dailyAt('06:00')
    ->timezone('Europe/Warsaw');

Schedule::command('telegram:daily-hello Europe/Berlin')
    ->dailyAt('06:00')
    ->timezone('Europe/Berlin');

