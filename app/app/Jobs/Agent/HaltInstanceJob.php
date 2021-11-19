<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Jobs\DataCenterManager\InstanceHaltCompleteJob;
use App\Services\DockerContainerManager;

class HaltInstanceJob extends BaseJob
{
    public function __construct(private string $instanceHash, private string $containerId)
    {
    }

    public function handle()
    {
        /** @var DockerContainerManager $container */
        $container = app(DockerContainerManager::class, ['containerId' => $this->containerId]);
        $container->stop();

        InstanceHaltCompleteJob::dispatch($this->instanceHash);
    }

    protected function getInstanceHash(): string
    {
        return $this->instanceHash;
    }
}
