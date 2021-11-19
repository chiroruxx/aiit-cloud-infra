<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Services\InstanceManager;

class InstanceCreationCompletionJob extends BaseJob
{
    public function __construct(private string $instanceHash, private string $containerId)
    {
        parent::__construct();
    }

    public function handle(InstanceManager $manager): void
    {
        $instance = $manager->initialized($this->instanceHash, $this->containerId);

        logger(
            'Complete instance creation.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
                'container' => $this->containerId
            ]
        );
    }

    protected function getInstanceHash(): string
    {
        return $this->instanceHash;
    }
}
