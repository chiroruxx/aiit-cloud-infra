<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    public function index(): JsonResponse
    {
        $images = Image::pluck('name')->all();

        return response()->json($images);
    }
}
