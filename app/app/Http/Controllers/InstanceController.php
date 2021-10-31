<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\CreateInstanceRequest;
use App\Models\Instance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class InstanceController extends Controller
{
    public function index(): JsonResponse
    {
        $instances = Instance::all();

        return response()->json($instances);
    }

    public function store(): Response
    {
        CreateInstanceRequest::dispatch();

        return response('', Response::HTTP_ACCEPTED);
    }
}
