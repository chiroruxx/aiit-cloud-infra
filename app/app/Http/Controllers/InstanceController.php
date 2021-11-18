<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ByteSize;
use App\Http\Requests\Instance\StoreRequest;
use App\Http\Requests\Instance\UpdateRequest;
use App\Jobs\DataCenterManager\CreateInstanceRequestJob;
use App\Jobs\DataCenterManager\HaltInstanceRequestJob;
use App\Jobs\DataCenterManager\RestartInstanceRequestJob;
use App\Jobs\DataCenterManager\TerminateInstanceRequestJob;
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

    public function store(StoreRequest $request): JsonResponse
    {
        $memorySize = $request->input('memory') ?? '4m';
        $storageSize = $request->input('storage') ?? '100g';

        $instance = Instance::initialize(
            $request->input('name') ?? '',
            $request->input('image'),
            $request->input('key'),
            $request->input('cpus') ?? 1,
            ByteSize::createWithUnit($memorySize)->getValue(),
            ByteSize::createWithUnit($storageSize)->getValue(),
        );
        logger('Initialize instance.', ['instance' => $instance->hash, 'status' => $instance->status]);

        CreateInstanceRequestJob::dispatch($instance);

        return response()->json($instance, Response::HTTP_ACCEPTED);
    }

    public function update(UpdateRequest $request, Instance $instance): Response
    {
        if ($request->isMethod('put')) {
            abort(404);
        }

        if ($request->has('name')) {
            $instance->updateName($request->input('name') ?? '');
        }

        if ($request->has('status')) {
            switch ($request->get('status')) {
                case 'halted':
                    $instance->halt();
                    HaltInstanceRequestJob::dispatch($instance);
                    break;
                case 'running':
                    $instance->restart();
                    RestartInstanceRequestJob::dispatch($instance);
                default:
                    // do nothing.
            }

            return response()->json($instance, Response::HTTP_ACCEPTED);
        }

        return response()->noContent();
    }

    public function show(Instance $instance): JsonResponse
    {
        return response()->json($instance);
    }

    public function destroy(Instance $instance): JsonResponse
    {
        $instance->terminate();

        TerminateInstanceRequestJob::dispatch($instance);

        return response()->json($instance, Response::HTTP_ACCEPTED);
    }
}
