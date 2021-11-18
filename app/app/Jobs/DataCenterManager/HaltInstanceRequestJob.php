<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Jobs\Agent\HaltInstanceJob;
use App\Jobs\Agent\TerminateInstanceJob;
use App\Models\Instance;

class HaltInstanceRequestJob extends BaseJob
{
    public function __construct(private Instance $instance)
    {
        parent::__construct();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        logger(
            'Halt instance.',
            ['instance' => $this->instance->hash, 'status' => $this->instance->status]
        );

        HaltInstanceJob::dispatch($this->instance->hash, $this->instance->container->container_id)
            ->onQueue($this->instance->container->machine->queue_name);
    }
}
