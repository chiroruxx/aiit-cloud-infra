<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Container;
use App\Models\Image;
use App\Models\Instance;
use App\Models\MachineStatistic;
use App\Models\PublicKey;
use Illuminate\Support\Facades\DB;

class InstanceManager
{
    public function initialize(
        string $instanceName,
        string $imageName,
        string $publicKeyHash,
        int $cpus,
        int $memorySize,
        int $storageSize
    ): Instance {
        $publicKey = PublicKey::whereHash($publicKeyHash)->firstOrFail(['id']);
        $image = Image::whereName($imageName)->firstOrFail(['id']);

        return DB::transaction(function () use (
            $instanceName,
            $cpus,
            $memorySize,
            $storageSize,
            $image,
            $publicKey
        ): Instance {
            $instance = Instance::toInitializing($instanceName);
            Container::initialize(
                $cpus,
                $memorySize,
                $storageSize,
                $instance,
                $image,
                $publicKey
            );

            return $instance;
        });
    }

    public function start(Instance $instance): Instance
    {
        $instance = $instance->toStarting();

        $machine = MachineStatistic::determineMachine(
            $instance->container->cpus,
            $instance->container->memory_size,
            $instance->container->storage_size,
        );

        $instance->container->setMachineInfo($machine);

        return $instance;
    }

    public function initialized(string $instanceHash, string $containerId): Instance
    {
        $instance = Instance::whereHash($instanceHash)->firstOrFail();

        return DB::transaction(function () use ($instance, $containerId): Instance {
            $instance->toRunning();
            $instance->container->setContainerId($containerId);

            return $instance;
        });
    }

    public function terminate(Instance $instance): Instance
    {
        return $instance->toTerminating();
    }

    public function terminated(string $instanceHash): Instance
    {
        $instance = Instance::whereHash($instanceHash)->firstOrFail();

        return DB::transaction(function () use ($instance): Instance {
            $instance->toTerminated();
            $instance->container->removeDockerContainerInfo();

            return $instance;
        });
    }

    public function halt(Instance $instance): Instance
    {
        return $instance->toHalting();
    }

    public function halted(string $instanceHash): Instance
    {
        $instance = Instance::whereHash($instanceHash)->firstOrFail();

        return $instance->toHalted();
    }

    public function restart(Instance $instance): Instance
    {
        return $instance->toStarting();
    }

    public function restarted(string $instanceHash): Instance
    {
        $instance = Instance::whereHash($instanceHash)->firstOrFail();
        return $instance->toRunning();
    }

    public function update(Instance $instance, ?string $name): Instance
    {
        return $instance->updateName($name);
    }
}
