<?php

declare(strict_types=1);

namespace App\Jobs\DataCenterManager;

use App\Jobs\InstanceOperable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InstanceOperable;

    public function __construct()
    {
        $this->onQueue('data_center_manager');
    }
}
