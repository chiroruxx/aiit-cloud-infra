<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Models\Instance;

class InstanceHaltCompleteJob extends BaseJob
{
    public function __construct(private string $instanceHash)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $instance = Instance::whereHash($this->instanceHash)->firstOrFail();
        $instance->completeHalt();
        logger(
            'Complete instance halt.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
            ]
        );
    }
}
