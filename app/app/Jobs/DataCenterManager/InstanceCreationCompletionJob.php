<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Models\Instance;

class InstanceCreationCompletionJob extends BaseJob
{
    public function __construct(private Instance $instance, private string $containerId, private string $vm)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->instance->run($this->containerId, $this->vm);
        logger(
            'Complete instance creation.',
            [
                'instance' => $this->instance->hash,
                'status' => $this->instance->status,
                'container' => $this->containerId
            ]
        );
    }
}