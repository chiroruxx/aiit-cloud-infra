<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\ByteSize;
use App\Jobs\Agent\CreateInstanceJob;
use App\Models\Instance;
use App\Models\Machine;
use App\Models\MachineStatistic;

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

        $machine = MachineStatistic::determineMachine(
            $this->instance->container->cpus,
            $this->instance->container->memory_size,
            $this->instance->container->storage_size,
        );

        $instance->container->machine()->associate($machine);
        $instance->container->setIp();

        $instance->container->save();

        // Agent は DB にアクセスできないので値をすべて Job に渡す
        CreateInstanceJob::dispatch(
            $instance->hash,
            $instance->container->image->docker_image_name,
            $instance->container->publicKey->content,
            $instance->container->ip,
            $instance->container->cpus,
            (new ByteSize($instance->container->memory_size))->getWithUnit(),
            (new ByteSize($instance->container->storage_size))->getWithUnit(),
        )->onQueue($machine->queue_name);
    }

    private function determineMachine(): Machine
    {
        $cpus = $this->instance->container->cpus;

        return Machine::where('max_cpu_count', '>=', $cpus)->firstOrFail();
    }
}
