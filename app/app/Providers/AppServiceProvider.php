<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DockerContainerManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DockerContainerManager::class, function (Application $app, array $args): DockerContainerManager {
            return new DockerContainerManager($args['containerId']);
        });
    }

    public function boot(): void
    {
        //
    }
}
