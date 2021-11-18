<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceRestartingCompletionJob;
use App\Services\DockerContainerManager;

class RestartInstanceJob extends BaseJob
{
    public function __construct(private string $instanceHash, private string $containerId)
    {
    }

    public function handle()
    {
        /** @var DockerContainerManager $container */
        $container = app(DockerContainerManager::class, ['containerId' => $this->containerId]);
        $container->restart();

        InstanceRestartingCompletionJob::dispatch($this->instanceHash);
    }
}
