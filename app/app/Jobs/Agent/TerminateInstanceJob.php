<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceTerminationCompleteJob;
use App\Services\DockerContainerManager;

class TerminateInstanceJob extends BaseJob
{
    public function __construct(private string $instanceHash, private string $containerId)
    {
    }

    public function handle()
    {
        $container = app(DockerContainerManager::class, ['containerId' => $this->containerId]);
        $container->remove();

        InstanceTerminationCompleteJob::dispatch($this->instanceHash);
    }

    protected function getInstanceHash(): string
    {
        return $this->instanceHash;
    }
}
