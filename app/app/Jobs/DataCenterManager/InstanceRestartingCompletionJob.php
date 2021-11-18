<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Services\InstanceManager;

class InstanceRestartingCompletionJob extends BaseJob
{
    public function __construct(private string $instanceHash)
    {
        parent::__construct();
    }

    public function handle(InstanceManager $manager): void
    {
        $instance = $manager->restarted($this->instanceHash);

        logger(
            'Complete instance restart.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
            ]
        );
    }
}
