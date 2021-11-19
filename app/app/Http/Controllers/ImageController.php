<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Image;
use App\OpenApi\Responses\ImageIndexResponse;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes\Operation;
use Vyuldashev\LaravelOpenApi\Attributes\PathItem;
use Vyuldashev\LaravelOpenApi\Attributes\Response;

#[PathItem]
class ImageController extends Controller
{
    /**
     * マシンイメージ一覧
     *
     * 利用可能なマシンイメージの一覧を取得します。
     *
     * @return JsonResponse
     */
    #[Operation]
    #[Response(factory: ImageIndexResponse::class)]
    public function index(): JsonResponse
    {
        $images = Image::pluck('name')->all();

        return response()->json($images);
    }
}
