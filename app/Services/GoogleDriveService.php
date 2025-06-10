<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected Drive $service;
    protected string $folderId;

    public function __construct()
    {
        $client = new Client();
        $credentialsPath = env('GOOGLE_DRIVE_CREDENTIALS', '/etc/secrets/credentials.json');
        Log::info("Права доступа к файлу $credentialsPath: " . (file_exists($credentialsPath) ? fileperms($credentialsPath) : 'файл не существует'));

        try {
            if (!file_exists($credentialsPath)) {
                Log::error("Файл учетных данных не найден: $credentialsPath");
                throw new \InvalidArgumentException("Файл учетных данных не найден: $credentialsPath");
            }
            $client->setAuthConfig($credentialsPath);
            Log::info("Учетные данные Google Drive успешно загружены: $credentialsPath");
        } catch (\Exception $e) {
            Log::error('Ошибка загрузки учетных данных Google Drive: ' . $e->getMessage());
            throw $e;
        }

        $client->addScope(Drive::DRIVE_READONLY);
        $client->setAccessType('offline');
        $this->service = new Drive($client);
        $this->folderId = env('GOOGLE_DRIVE_FOLDER_ID');
    }

    public function listImages(): array
    {
        try {
            $params = [
                'q' => "'{$this->folderId}' in parents and mimeType contains 'image/' and trashed = false",
                'fields' => 'files(id, name)',
            ];
            $results = $this->service->files->listFiles($params);
            $files = collect($results->getFiles())
                ->map(
                    fn($file) => [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'url' => 'https://drive.google.com/uc?export=view&id=' . $file->getId(),
                    ],
                )
                ->toArray();
            Log::info('Найдено изображений в Google Drive: ' . count($files));
            return $files;
        } catch (\Exception $e) {
            Log::error('Ошибка получения изображений из Google Drive: ' . $e->getMessage());
            return [];
        }
    }
}
