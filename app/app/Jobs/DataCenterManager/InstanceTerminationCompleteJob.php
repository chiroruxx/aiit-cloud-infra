<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Models\Instance;

class InstanceTerminationCompleteJob extends BaseJob
{
    public function __construct(private string $instanceHash)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $instance = Instance::whereHash($this->instanceHash)->firstOrFail();
        $instance->completeTerminate();
        logger(
            'Complete instance termination.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
            ]
        );
    }
}
