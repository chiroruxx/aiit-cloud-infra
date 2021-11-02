<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Jobs\Agent\CreateInstanceJob;

class CreateInstanceRequestJob extends BaseJob
{
    public function handle(): void
    {
        CreateInstanceJob::dispatch()->onQueue('vm1');
    }
}
