<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserActivityService;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class CheckInactiveUsers extends Command
{
    protected $signature = 'telegram:check-inactive';
    protected $description = 'Отправить напоминание неактивным пользователям';
    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle(UserActivityService $activityService)
    {
        $inactiveUsers = $activityService->getInactiveUsers(12);

        if (empty($inactiveUsers)) {
            return Command::SUCCESS;
        }

        $chatId = config('services.telegram.chat_id_friend');
        $token = env('TELEGRAM_BOT_TOKEN');

        foreach ($inactiveUsers as $user) {
            $text = "@{$user['username']}, что то ты давно уже молчишь!";
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
        }

        return Command::SUCCESS;
    }
}
