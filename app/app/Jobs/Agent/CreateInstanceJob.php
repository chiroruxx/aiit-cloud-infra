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

class CreateInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        exec(escapeshellcmd(implode(' ', $command)), $output, $result_code);

        if ($result_code !== 0 || !isset($output[0])) {
            throw new RuntimeException(
                'コマンド実行に失敗しました。' .
                var_export(compact('output', 'result_code'), true)
            );
        }

        logger('before dispatch');
        InstanceCreationCompletionJob::dispatch($output[0]);
        logger('after dispatch');
    }
}
