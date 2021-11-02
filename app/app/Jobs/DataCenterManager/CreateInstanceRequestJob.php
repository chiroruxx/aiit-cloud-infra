<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Jobs\Agent\CreateInstanceJob;
use App\Models\Instance;

class CreateInstanceRequestJob extends BaseJob
{
    public function __construct(private Instance $instance)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $instance = $this->instance->start();
        logger('Start instance.', ['instance' => $instance->hash, 'status' => $instance->status]);

        CreateInstanceJob::dispatch($instance)->onQueue('vm1');
    }
}
