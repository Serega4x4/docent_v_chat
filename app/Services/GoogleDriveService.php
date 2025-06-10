<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveService
{
    protected Drive $service;
    protected string $folderId;

    public function __construct()
    {
        $client = new Client();
        // $client->setAuthConfig(storage_path(env('GOOGLE_DRIVE_CREDENTIALS')));
        $client->setAuthConfig(env('GOOGLE_DRIVE_CREDENTIALS'));
        $client->addScope(Drive::DRIVE_READONLY);
        $client->setAccessType('offline');

        $this->service = new Drive($client);
        $this->folderId = env('GOOGLE_DRIVE_FOLDER_ID');
    }

    public function listImages(): array
    {
        $params = [
            'q' => "'{$this->folderId}' in parents and mimeType contains 'image/' and trashed = false",
            'fields' => 'files(id, name)',
        ];

        $results = $this->service->files->listFiles($params);

        return collect($results->getFiles())
            ->map(fn ($file) => [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'url' => "https://drive.google.com/uc?export=view&id=" . $file->getId(),
            ])
            ->toArray();
    }
}
