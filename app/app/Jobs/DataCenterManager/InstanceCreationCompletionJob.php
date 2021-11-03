<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Models\Instance;

class InstanceCreationCompletionJob extends BaseJob
{
    public function __construct(private string $instanceHash, private string $containerId, private string $vm)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $instance = Instance::whereHash($this->instanceHash)->firstOrFail();
        $instance->run($this->containerId, $this->vm);
        logger(
            'Complete instance creation.',
            [
                'instance' => $instance->hash,
                'status' => $instance->status,
                'container' => $this->containerId
            ]
        );
    }
}
