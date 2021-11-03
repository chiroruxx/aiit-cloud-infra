<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Jobs\Agent\CreateInstanceJob;
use App\Models\Instance;

class CreateInstanceRequestJob extends BaseJob
{
    public function __construct(private Instance $instanceHash)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $instance = $this->instanceHash->start();
        logger('Start instance.', ['instance' => $instance->hash, 'status' => $instance->status]);

        $vm = 'vm2';

        CreateInstanceJob::dispatch($instance->hash, $vm)->onQueue($vm);
    }
}
