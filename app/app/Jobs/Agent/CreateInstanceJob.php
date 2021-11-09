<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceCreationCompletionJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;
use Storage;

class CreateInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $instance,
        private string $publicKey,
        private string $ip,
        private string $vm,
        private int $cpus,
        private string $memorySize
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

        InstanceCreationCompletionJob::dispatch($this->instance, $containerId, $this->vm);
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
        $command = [
            'docker',
            'run',
            '-d',
            '--cap-add=SYS_ADMIN',
            "--cpuset-cpus {$this->cpus}",
            "--memory={$this->memorySize}",
            '-v',
            '/sys/fs/cgroup:/sys/fs/cgroup:ro',
            '-v',
            "{$metaDrivePath}:/metadata",
            'local/c8-systemd-ssh',
        ];

        return implode(' ', $command);
    }

    private function buildConnectCommand(string $containerId): string
    {
        $command = [
            'docker',
            'network',
            'connect',
            "--ip={$this->ip}",
            'mybridge',
            $containerId,
        ];

        return implode(' ', $command);
    }

    private function execCommand(string $commandString): array
    {
        $output = null;
        $result_code = null;

        logger('Start execute command.', ['command' => $commandString]);

        exec(escapeshellcmd($commandString), $output, $result_code);

        $commandResult = compact('output', 'result_code');
        logger('Finish execute command.', ['command' => $commandString, 'result' => $commandResult]);

        if ($result_code !== 0) {
            $parameters = array_merge(compact('commandString'), $commandResult);
            throw new RuntimeException(
                'コマンド実行に失敗しました。' . var_export($parameters, true)
            );
        }

        return $output;
    }
}
