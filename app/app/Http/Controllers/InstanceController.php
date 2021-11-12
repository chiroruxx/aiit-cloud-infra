<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ByteSize;
use App\Http\Requests\InstanceRequest;
use App\Jobs\DataCenterManager\CreateInstanceRequestJob;
use App\Models\Instance;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class InstanceController extends Controller
{
    public function index(): JsonResponse
    {
        $instances = Instance::all();

        return response()->json($instances);
    }

    public function store(InstanceRequest $request): JsonResponse
    {
        $memorySize = $request->input('memory') ?? '4m';

        $instance = Instance::initialize(
            $request->input('name') ?? '',
            $request->input('key'),
            $request->input('cpus') ?? 1,
            ByteSize::createWithUnit($memorySize)->getValue()
        );
        logger('Initialize instance.', ['instance' => $instance->hash, 'status' => $instance->status]);

        CreateInstanceRequestJob::dispatch($instance);

        return response()->json($instance, Response::HTTP_ACCEPTED);
    }
}
