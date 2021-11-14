<?php

namespace App\Jobs\Agent;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected function buildCommand(array $command): string {
        return implode(' ', $command);
    }

    protected function buildGetStatusCommand(string $containerId): string
    {
        return $this->buildCommand([
            'docker',
            'inspect',
            '--format={{.State.Status}}',
            $containerId,
        ]);
    }

    protected function execCommand(string $commandString): array
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
