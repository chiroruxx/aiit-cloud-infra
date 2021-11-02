<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceCreationCompletionJob;
use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class CreateInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Instance $instance)
    {
    }

    public function handle(): void
    {
        $command = [
            'docker',
            'run',
            '-d',
            '--cap-add=SYS_ADMIN',
            '-v',
            '/sys/fs/cgroup:/sys/fs/cgroup:ro',
            'local/c8-systemd-ssh',
        ];

        $output = null;
        $result_code = null;

        $commandString = escapeshellcmd(implode(' ', $command));
        logger('Start execute command.', ['command' => $commandString]);

        exec($commandString, $output, $result_code);

        $commandResult = compact('output', 'result_code');
        logger('Finish execute command.', ['command' => $commandString, 'result' => $commandResult]);

        if ($result_code !== 0 || !isset($output[0])) {
            throw new RuntimeException('コマンド実行に失敗しました。' . var_export($commandResult, true));
        }

        InstanceCreationCompletionJob::dispatch($this->instance, $output[0]);
    }
}
