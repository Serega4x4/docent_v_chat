<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UserActivityService
{
    protected string $file = 'last_activity.json';

    public function updateActivity(int $userId, string $username): void
    {
        $activities = $this->getAll();
        $activities[$userId] = [
            'username' => $username,
            'last_active' => now()->toDateTimeString(),
        ];
        Storage::disk('local')->put($this->file, json_encode($activities, JSON_PRETTY_PRINT));
    }

    public function getAll(): array
    {
        if (!Storage::disk('local')->exists($this->file)) {
            return [];
        }

        return json_decode(Storage::disk('local')->get($this->file), true) ?? [];
    }

    public function getInactiveUsers(int $hours = 12): array
    {
        $activities = $this->getAll();
        $inactive = [];

        foreach ($activities as $id => $data) {
            $lastActive = \Carbon\Carbon::parse($data['last_active']);
            if ($lastActive->diffInHours(now()) >= $hours) {
                $inactive[$id] = $data;
            }
        }

        return $inactive;
    }
}
