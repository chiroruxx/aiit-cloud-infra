<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PublicKeyRequest;
use App\Models\PublicKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PublicKeyController extends Controller
{
    public function index(): JsonResponse
    {
        $keys = PublicKey::all();

        return response()->json($keys);
    }

    public function show(PublicKey $key): JsonResponse
    {
        return response()->json($key);
    }

    public function store(PublicKeyRequest $request): JsonResponse
    {
        $content = $request->get('key');

        $key = PublicKey::create(['content' => $content, 'hash' => PublicKey::generateActiveHash()]);

        return response()->json($key, Response::HTTP_CREATED);
    }

    public function update(PublicKey $key, PublicKeyRequest $request): Response
    {
        $content = $request->get('key');

        $key->fill(['content' => $content]);
        $key->save();

        return response()->noContent();
    }

    public function destroy(PublicKey $key): Response
    {
        $key->delete();

        return response()->noContent();
    }
}
