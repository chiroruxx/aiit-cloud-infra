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

    public function __construct(private string $instance, private string $publicKey, private string $vm)
    {
    }

    public function handle(): void
    {
        $metaDrivePath = $this->createMetaDataDrive();
        $commandString = $this->buildCommand($metaDrivePath);

        $containerId = $this->execCommand($commandString);

        InstanceCreationCompletionJob::dispatch($this->instance, $containerId, $this->vm);
    }

    private function createMetaDataDrive(): string
    {
        $basePath = "meta/{$this->instance}";

        $path = "{$basePath}/id_rsa.pub";
        Storage::disk('local')->put($path, $this->publicKey);

        return storage_path("app/{$basePath}");
    }

    private function buildCommand(string $metaDrivePath): string
    {
        // TODO: ユーザがCPU数やメモリを指定できるようにする
        $command = [
            'docker',
            'run',
            '-d',
            '--cap-add=SYS_ADMIN',
            '-v',
            '/sys/fs/cgroup:/sys/fs/cgroup:ro',
            '-v',
            "{$metaDrivePath}:/metadata",
            'local/c8-systemd-ssh',
        ];

        return implode(' ', $command);
    }

    private function execCommand(string $commandString): string
    {
        $output = null;
        $result_code = null;

        logger('Start execute command.', ['command' => $commandString]);

        exec(escapeshellcmd($commandString), $output, $result_code);

        $commandResult = compact('output', 'result_code');
        logger('Finish execute command.', ['command' => $commandString, 'result' => $commandResult]);

        if ($result_code !== 0 || !isset($output[0])) {
            throw new RuntimeException('コマンド実行に失敗しました。' . var_export($commandResult, true));
        }

        return $output[0];
    }
}
