<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Models\Instance;

class InstanceRestartingCompleteJob extends BaseJob
{
    public function __construct(private string $instanceHash)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $instance = Instance::whereHash($this->instanceHash)->firstOrFail();
        $instance->completeRestart();
        logger(
            'Complete instance restart.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
            ]
        );
    }
}
