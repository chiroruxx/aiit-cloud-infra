<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceCreationCompletionJob;
use App\Services\DockerContainerManager;
use RuntimeException;
use Storage;

class CreateInstanceJob extends BaseJob
{
    public function __construct(
        private string $instance,
        private string $image,
        private string $publicKey,
        private string $ip,
        private int $cpus,
        private string $memorySize,
        private string $storageSize
    ) {
    }

    public function handle(): void
    {
        $metaDrivePath = $this->createMetaDataDrive();

        $container = DockerContainerManager::build(
            $this->cpus,
            $this->memorySize,
            $this->storageSize,
            $metaDrivePath,
            $this->image,
        )
        ->connectToDockerNetwork($this->ip);

        if (!$container->isRunning()) {
            throw new RuntimeException('コンテナの起動に失敗しました');
        }

        InstanceCreationCompletionJob::dispatch($this->instance, $container->getId());
    }

    private function createMetaDataDrive(): string
    {
        $basePath = "meta/{$this->instance}";

        $path = "{$basePath}/id_rsa.pub";
        Storage::disk('local')->put($path, $this->publicKey);

        return storage_path("app/{$basePath}");
    }
}
