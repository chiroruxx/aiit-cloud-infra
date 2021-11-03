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

        $vm = 'vm2';
        $publicKey = PublicKey::first();

        CreateInstanceJob::dispatch($instance->hash, $publicKey->content, $vm)->onQueue($vm);
    }
}
