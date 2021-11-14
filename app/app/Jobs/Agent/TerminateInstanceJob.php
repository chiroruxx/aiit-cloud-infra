<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceTerminationCompleteJob;
use RuntimeException;

class TerminateInstanceJob extends BaseJob
{

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private string $instanceHash, private string $containerId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $statusCommand = $this->buildGetStatusCommand($this->containerId);
        $statusResult = $this->execCommand($statusCommand);
        if (!isset($statusResult[0])) {
            throw new RuntimeException('コンテナのステータスが見つかりませんでした');
        }

        switch ($statusResult[0]) {
            case 'running':
                $stopCommand = $this->buildStopCommand();
                $this->execCommand($stopCommand);

                $statusResult = $this->execCommand($statusCommand);
                if (!isset($statusResult[0])) {
                    throw new RuntimeException('コンテナのステータスが見つかりませんでした');
                }
                if ($statusResult[0] === 'dead') {
                    throw new RuntimeException('コンテナの停止に失敗しました');
                }
                // break しない
            case 'exited':
                $removeCommand = $this->buildRemoveCommand();
                $this->execCommand($removeCommand);
                break;
            default:
                throw new RuntimeException("ステータス {$statusResult[0]} は不正です");
        }

        InstanceTerminationCompleteJob::dispatch($this->instanceHash);
    }

    public function buildStopCommand(): string
    {
        return $this->buildCommand([
            'docker',
            'stop',
            $this->containerId,
        ]);
    }

    public function buildRemoveCommand(): string
    {
        return $this->buildCommand([
            'docker',
            'rm',
            $this->containerId,
        ]);
    }
}
