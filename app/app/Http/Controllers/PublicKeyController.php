<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PublicKeyRequest;
use App\Models\PublicKey;
use App\OpenApi\Parameters\PublicKeySaveParameters;
use App\OpenApi\Responses\PublicKeyDestroyResponse;
use App\OpenApi\Responses\PublicKeyIndexResponse;
use App\OpenApi\Responses\PublicKeySaveValidationErrorResponse;
use App\OpenApi\Responses\PublicKeyShowResponse;
use App\OpenApi\Responses\PublicKeyStoreResponse;
use App\OpenApi\Responses\PublicKeyUpdateResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Vyuldashev\LaravelOpenApi\Attributes\Operation;
use Vyuldashev\LaravelOpenApi\Attributes\Parameters;
use Vyuldashev\LaravelOpenApi\Attributes\PathItem;
use Vyuldashev\LaravelOpenApi\Attributes\Response as OpenAPIResponse;

#[PathItem]
class PublicKeyController extends Controller
{
    /**
     * 公開鍵一覧
     *
     * 登録されている公開鍵の一覧を返します。
     *
     * @return JsonResponse
     */
    #[Operation]
    #[OpenAPIResponse(factory: PublicKeyIndexResponse::class)]
    public function index(): JsonResponse
    {
        $keys = PublicKey::all();

        return response()->json($keys);
    }

    /**
     * 公開鍵取得
     *
     * 指定した公開鍵の情報を返します。
     *
     * @param PublicKey $key 公開鍵のハッシュ値
     * @return JsonResponse
     */
    #[Operation]
    #[OpenAPIResponse(factory: PublicKeyShowResponse::class)]
    public function show(PublicKey $key): JsonResponse
    {
        return response()->json($key);
    }

    /**
     * 公開鍵作成
     *
     * 公開鍵をサーバにアップロードします。
     *
     * @param PublicKeyRequest $request
     * @return JsonResponse
     */
    #[Operation]
    #[Parameters(factory: PublicKeySaveParameters::class)]
    #[OpenAPIResponse(factory: PublicKeyStoreResponse::class)]
    #[OpenAPIResponse(factory: PublicKeySaveValidationErrorResponse::class)]
    public function store(PublicKeyRequest $request): JsonResponse
    {
        $content = $request->get('key');

        $key = PublicKey::create(['content' => $content, 'hash' => PublicKey::generateActiveHash()]);

        return response()->json($key, Response::HTTP_CREATED);
    }

    /**
     * 公開鍵更新
     *
     * 公開鍵を更新します。
     *
     * @param PublicKey $key 公開鍵のハッシュ
     * @param PublicKeyRequest $request
     * @return Response
     */
    #[Operation]
    #[Parameters(factory: PublicKeySaveParameters::class)]
    #[OpenAPIResponse(factory: PublicKeyUpdateResponse::class)]
    #[OpenAPIResponse(factory: PublicKeySaveValidationErrorResponse::class)]
    public function update(PublicKey $key, PublicKeyRequest $request): Response
    {
        $content = $request->get('key');

        $key->fill(['content' => $content]);
        $key->save();

        return response()->noContent();
    }

    /**
     * 公開鍵削除
     *
     * 指定された公開鍵を削除します。
     *
     * @param PublicKey $key 公開鍵のハッシュ
     * @return Response
     */
    #[Operation]
    #[OpenAPIResponse(factory: PublicKeyDestroyResponse::class)]
    public function destroy(PublicKey $key): Response
    {
        $key->delete();

        return response()->noContent();
    }
}
