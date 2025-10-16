<?php

namespace App\Http\Controllers;

use App\Services\UserActivityService;
use Telegram\Bot\Api;

class UserActivityController extends Controller
{
    public $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handleMessage(array $message)
{
    $userId = $message['from']['id'];
    $username = $message['from']['username'] ?? $message['from']['first_name'];

    // обновляем активность
    app(UserActivityService::class)->updateActivity($userId, $username);
}
}
