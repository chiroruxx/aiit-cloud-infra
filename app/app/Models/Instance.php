<?php

declare(strict_types=1);

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Instance
 *
 * @property int $id
 * @property string $name
 * @property string $hash
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Container|null $container
 * @method static \Database\Factories\InstanceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Instance extends Model
{
    private const STATUS_INITIALIZING = 'initializing';
    private const STATUS_STARTING = 'starting';
    private const STATUS_RUNNING = 'running';

    use HasFactory;
    use Hashable;

    protected $fillable = ['name', 'hash', 'status'];
    protected $hidden = ['id'];

    public function container(): HasOne
    {
        return $this->hasOne(Container::class);
    }

    public static function initialize(string $name, int $cpus, string $memorySize): self
    {
        return DB::transaction(function () use ($name, $cpus, $memorySize): self {
            $instance = self::create([
                'name' => $name,
                'hash' => self::generateActiveHash(),
                'status' => self::STATUS_INITIALIZING
            ]);

            $instance->container()->create([
                'cpus' => $cpus,
                'memory_size' => $memorySize,
            ]);

            return $instance;
        });
    }

    public function start(): self
    {
        $this->status = self::STATUS_STARTING;
        $this->save();

        return $this;
    }

    public function run(string $containerId, string $vm): self
    {
        $this->status = self::STATUS_RUNNING;

        $this->container->fill([
            'container_id' => $containerId,
            'vm' => $vm,
        ]);

        DB::transaction(function (): void {
            $this->container->save();
            $this->save();
        });

        return $this;
    }
}
