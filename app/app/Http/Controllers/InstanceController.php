<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\JsonResponse;

class InstanceController extends Controller
{
    public function index(): JsonResponse
    {
        $instances = Instance::all();

        return response()->json($instances);
    }
}
