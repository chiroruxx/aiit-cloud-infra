<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

class InstanceCreationCompletionJob extends BaseJob
{
    public function __construct(private string $containerId)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        logger('Complete instance creation.', get_object_vars($this));
    }
}
