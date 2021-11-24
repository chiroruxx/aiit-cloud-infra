<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class DockerContainerManager
{
    use CommandExecutable;

    private const STATUS_RUNNING = 'running';
    private const STATUS_EXITED = 'exited';

    public function __construct(private string $containerId)
    {
    }

    public static function build(
        int $cpus,
        string $memorySize,
        string $storageSize,
        string $metaDrivePath,
        string $image
    ): self {
        $buildCommand = self::buildCommand([
            'docker',
            'run',
            '-d',
            '--cap-add=SYS_ADMIN',
            "--cpus={$cpus}",
            "--memory={$memorySize}",
            '--storage-opt',
            "size={$storageSize}",
            '-v',
            '/sys/fs/cgroup:/sys/fs/cgroup:ro',
            '-v',
            "{$metaDrivePath}:/metadata",
            $image,
        ]);

        $buildResult = self::execCommand($buildCommand);
        if (!isset($buildResult[0])) {
            throw new RuntimeException('コンテナIDが見つかりませんでした');
        }

        return new self($buildResult[0]);
    }

    public function getId(): string
    {
        return $this->containerId;
    }

    public function connectToDockerNetwork(string $ip): self
    {
        $connectCommand = $this->buildCommand([
            'docker',
            'network',
            'connect',
            "--ip={$ip}",
            'mybridge',
            $this->containerId,
        ]);

        try {
            $this->execCommand($connectCommand);
        } catch (RuntimeException $e) {
            $this->remove();
            throw $e;
        }

        return $this;
    }

    public function stop(): self
    {
        $stopCommand = $this->buildCommand([
            'docker',
            'stop',
            $this->containerId,
        ]);

        try {
            $this->execCommand($stopCommand);
        } catch (RuntimeException $e) {
            $this->remove();
            throw $e;
        }

        if ($this->getStatus() === 'dead') {
            throw new RuntimeException('コンテナの停止に失敗しました');
        }

        return $this;
    }

    public function restart(): self
    {
        $startCommand = $this->buildCommand([
            'docker',
            'start',
            $this->containerId,
        ]);

        try {
            $this->execCommand($startCommand);
        } catch (RuntimeException $e) {
            $this->remove();
            throw $e;
        }

        return $this;
    }

    public function remove(): void
    {
        if ($this->isRunning()) {
            $this->stop()
                ->remove();
            return;
        }

        if (!$this->isExited()) {
            throw new RuntimeException("ステータスが不正です");
        }

        $removeCommand = $this->buildCommand([
            'docker',
            'rm',
            $this->containerId,
        ]);

        $this->execCommand($removeCommand);
    }

    public function isRunning(): bool
    {
        return $this->getStatus() === self::STATUS_RUNNING;
    }

    public function isExited(): bool
    {
        return $this->getStatus() === self::STATUS_EXITED;
    }

    protected function getStatus(): string
    {
        $checkCommand = $this->buildCommand([
            'docker',
            'inspect',
            '--format={{.State.Status}}',
            $this->containerId,
        ]);

        $checkResult = $this->execCommand($checkCommand);
        if (!isset($checkResult[0])) {
            throw new RuntimeException('コンテナのステータスが見つかりませんでした');
        }

        return $checkResult[0];
    }
}
