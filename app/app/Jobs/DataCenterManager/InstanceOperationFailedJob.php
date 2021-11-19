<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Services\InstanceManager;
use Throwable;

class InstanceOperationFailedJob extends BaseJob
{
    public function __construct(private string $instanceHash)
    {
        parent::__construct();
    }

    public function handle(InstanceManager $manager)
    {
        $manager->failed($this->instanceHash);
    }

    public function failed(Throwable $exception)
    {
        // do nothing.
    }

    protected function getInstanceHash(): string
    {
        return $this->instanceHash;
    }
}
