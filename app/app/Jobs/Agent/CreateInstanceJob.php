<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceCreationCompletionJob;
use RuntimeException;
use Storage;

class CreateInstanceJob extends BaseJob
{
    public function __construct(
        private string $instance,
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

        $buildCommand = $this->buildCreateCommand($metaDrivePath);
        $buildResult = $this->execCommand($buildCommand);
        if (!isset($buildResult[0])) {
            throw new RuntimeException('コンテナIDが見つかりませんでした');
        }
        $containerId = $buildResult[0];

        $connectCommand = $this->buildConnectCommand($containerId);
        $this->execCommand($connectCommand);

        // 正常に作動しているか確認する
        $checkCommand = $this->buildGetStatusCommand($containerId);
        $checkResult = $this->execCommand($checkCommand);
        if (!isset($checkResult[0])) {
            throw new RuntimeException('コンテナのステータスが見つかりませんでした');
        }
        if ($checkResult[0] !== 'running') {
            throw new RuntimeException('コンテナの起動に失敗しました');
        }

        InstanceCreationCompletionJob::dispatch($this->instance, $containerId);
    }

    private function createMetaDataDrive(): string
    {
        $basePath = "meta/{$this->instance}";

        $path = "{$basePath}/id_rsa.pub";
        Storage::disk('local')->put($path, $this->publicKey);

        return storage_path("app/{$basePath}");
    }

    private function buildCreateCommand(string $metaDrivePath): string
    {
        return $this->buildCommand([
            'docker',
            'run',
            '-d',
            '--cap-add=SYS_ADMIN',
            "--cpuset-cpus {$this->cpus}",
            "--memory={$this->memorySize}",
            '--storage-opt',
            "size={$this->storageSize}",
            '-v',
            '/sys/fs/cgroup:/sys/fs/cgroup:ro',
            '-v',
            "{$metaDrivePath}:/metadata",
            'local/c8-systemd-ssh',
        ]);
    }

    private function buildConnectCommand(string $containerId): string
    {
        return $this->buildCommand([
            'docker',
            'network',
            'connect',
            "--ip={$this->ip}",
            'mybridge',
            $containerId,
        ]);
    }
}
