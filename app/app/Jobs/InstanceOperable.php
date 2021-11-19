<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\DataCenterManager\InstanceOperationFailedJob;
use Throwable;

trait InstanceOperable
{
    public function failed(Throwable $exception)
    {
        logger()->error($exception->getMessage());

        $instanceHash = $this->getInstanceHash();

        if ($instanceHash !== null) {
            InstanceOperationFailedJob::dispatch($instanceHash);
        }
    }

    abstract protected function getInstanceHash(): string;
}
