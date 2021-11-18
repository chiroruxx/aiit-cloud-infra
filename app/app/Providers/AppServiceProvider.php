<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DockerContainerManager;
use App\Services\InstanceManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InstanceManager::class, fn(): InstanceManager => new InstanceManager());

        $this->app->bind(
            DockerContainerManager::class,
            fn(mixed $_, array $args): DockerContainerManager => new DockerContainerManager($args['containerId'])
        );
    }

    public function boot(): void
    {
        //
    }
}
