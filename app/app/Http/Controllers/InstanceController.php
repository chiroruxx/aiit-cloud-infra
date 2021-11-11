<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        // TODO: CPU数、メモリサイズをユーザが指定できるようにする
        $cpus = 1;
        $memorySize = '512m';

        $instance = Instance::initialize($request->input('name') ?? '', $cpus, $memorySize);
        logger('Initialize instance.', ['instance' => $instance->hash, 'status' => $instance->status]);

        CreateInstanceRequestJob::dispatch($instance);

        return response()->json($instance, Response::HTTP_ACCEPTED);
    }
}
