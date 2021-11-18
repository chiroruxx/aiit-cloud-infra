<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Services\InstanceManager;

class InstanceHaltCompleteJob extends BaseJob
{
    public function __construct(private string $instanceHash)
    {
        parent::__construct();
    }

    public function handle(InstanceManager $manager): void
    {
        $instance = $manager->halted($this->instanceHash);

        logger(
            'Complete instance halt.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
            ]
        );
    }
}
