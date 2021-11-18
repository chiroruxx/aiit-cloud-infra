<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Services\InstanceManager;

class InstanceTerminationCompleteJob extends BaseJob
{
    public function __construct(private string $instanceHash)
    {
        parent::__construct();
    }

    public function handle(InstanceManager $manager): void
    {
        $instance = $manager->terminated($this->instanceHash);

        logger(
            'Complete instance termination.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
            ]
        );
    }
}
