<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Jobs\Agent\CreateInstanceJob;
use App\Models\Instance;
use App\Models\PublicKey;

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

        // TODO: VMを残りのリソースを考慮して自動で指定できるようにする
        $vm = 'vm2';
        // TODO: PublicKeyをユーザが指定できるようにする
        $publicKey = PublicKey::first();
        // TODO: 自動でIPを指定できるようにする
        $ip = '10.10.20.10';

        CreateInstanceJob::dispatch(
            $instance->hash,
            $publicKey->content,
            $ip,
            $vm,
            $instance->container->cpus,
            $instance->container->memory_size
        )->onQueue($vm);
    }
}
