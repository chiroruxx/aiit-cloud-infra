<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

trait CommandExecutable
{
    protected static function buildCommand(array $command): string
    {
        return implode(' ', $command);
    }

    protected static function execCommand(string $commandString): array
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
