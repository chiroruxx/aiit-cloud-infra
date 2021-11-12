<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Jobs\Agent\CreateInstanceJob;
use App\Models\Instance;
use App\Models\Machine;

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

        $machine = $this->determineMachine();

        $instance->container->machine()->associate($machine);
        $instance->container->setIp();

        $instance->container->save();

        CreateInstanceJob::dispatch(
            $instance->hash,
            $this->instance->container->publicKey->content,
            $instance->container->ip,
            $instance->container->cpus,
            $instance->container->memory_size
        )->onQueue($machine->queue_name);
    }

    private function determineMachine(): Machine
    {
        $cpus = $this->instance->container->cpus;

        return Machine::where('max_cpu_count', '>=', $cpus)->firstOrFail();
    }
}
