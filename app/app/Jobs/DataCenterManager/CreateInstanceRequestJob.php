<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\ByteSize;
use App\Jobs\Agent\CreateInstanceJob;
use App\Models\Instance;
use App\Services\InstanceManager;

class CreateInstanceRequestJob extends BaseJob
{
    public function __construct(private Instance $instance)
    {
        parent::__construct();
    }

    public function handle(InstanceManager $manager): void
    {
        $instance = $manager->start($this->instance);
        logger('Start instance.', ['instance' => $instance->hash, 'status' => $instance->status]);

        // Agent は DB にアクセスできないので値をすべて Job に渡す
        CreateInstanceJob::dispatch(
            $instance->hash,
            $instance->container->image->docker_image_name,
            $instance->container->publicKey->content,
            $instance->container->ip,
            $instance->container->cpus,
            (new ByteSize($instance->container->memory_size))->getWithUnit(),
            (new ByteSize($instance->container->storage_size))->getWithUnit(),
        )->onQueue($instance->container->machine->queue_name);
    }
}
