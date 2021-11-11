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

        // TODO: VMを残りのリソースを考慮して自動で指定できるようにする
        $instance->container->vm = 'vm2';
        // TODO: 自動でIPを指定できるようにする
        $ip = '10.10.20.10';

        $instance->container->save();

        CreateInstanceJob::dispatch(
            $instance->hash,
            $this->instance->container->publicKey->content,
            $ip,
            $instance->container->cpus,
            $instance->container->memory_size
        )->onQueue($instance->container->vm);
    }
}
