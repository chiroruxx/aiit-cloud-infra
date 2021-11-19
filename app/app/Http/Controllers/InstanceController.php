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
use App\OpenApi\Parameters\InstanceStoreParameters;
use App\OpenApi\Parameters\InstanceUpdateParameters;
use App\OpenApi\Responses\InstanceDestroyInvalidResponse;
use App\OpenApi\Responses\InstanceDestroyResponse;
use App\OpenApi\Responses\InstanceIndexResponse;
use App\OpenApi\Responses\InstanceSaveValidationErrorResponse;
use App\OpenApi\Responses\InstanceShowResponse;
use App\OpenApi\Responses\InstanceStoreResponse;
use App\OpenApi\Responses\InstanceUpdateAcceptedResponse;
use App\OpenApi\Responses\InstanceUpdateNoContentResponse;
use App\Services\InstanceManager;
use Illuminate\Http\JsonResponse;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Vyuldashev\LaravelOpenApi\Attributes\Operation;
use Vyuldashev\LaravelOpenApi\Attributes\Parameters;
use Vyuldashev\LaravelOpenApi\Attributes\PathItem;
use Vyuldashev\LaravelOpenApi\Attributes\Response as OpenAPIResponse;

#[PathItem]
class InstanceController extends Controller
{
    public function __construct(private InstanceManager $manager)
    {
    }

    /**
     * インスタンス一覧
     *
     * 登録されているインスタンスの一覧を返します。
     *
     * @return JsonResponse
     */
    #[Operation]
    #[OpenAPIResponse(factory: InstanceIndexResponse::class)]
    public function index(): JsonResponse
    {
        $instances = Instance::all();

        return response()->json($instances);
    }

    /**
     * インスタンス作成
     *
     * インスタンスを作成の申請をします。
     * このAPIにリクエスト後しばらくするとインスタンスが作成されます。
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    #[Operation]
    #[Parameters(factory: InstanceStoreParameters::class)]
    #[OpenAPIResponse(factory: InstanceStoreResponse::class)]
    #[OpenAPIResponse(factory: InstanceSaveValidationErrorResponse::class)]
    public function store(StoreRequest $request): JsonResponse
    {
        $memorySize = $request->input('memory') ?? '4m';
        $storageSize = $request->input('storage') ?? '100g';

        $instance = $this->manager->initialize(
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

    /**
     * インスタンス更新
     *
     * インスタンスを更新します。
     * ステータスを更新する場合はこのAPIにリクエスト後しばらくするとインスタンスが更新されます。
     *
     * ステータス更新については以下をご確認ください。
     *
     * - `halted` を指定する場合、対象のインスタンスのステータスは `running` でないといけません。
     * - `running` を指定する場合、対象のインスタンスのステータスは `halted` でないといけません。
     *
     * @param UpdateRequest $request
     * @param Instance $instance インスタンスのハッシュ
     * @return Response
     */
    #[Operation]
    #[Parameters(factory: InstanceUpdateParameters::class)]
    #[OpenAPIResponse(factory: InstanceUpdateNoContentResponse::class)]
    #[OpenAPIResponse(factory: InstanceUpdateAcceptedResponse::class)]
    #[OpenAPIResponse(factory: InstanceSaveValidationErrorResponse::class)]
    public function update(UpdateRequest $request, Instance $instance): Response
    {
        if ($request->isMethod('put')) {
            abort(404);
        }

        if ($request->has('name')) {
            $this->manager->update($instance, $request->input('name') ?? '');
        }

        if ($request->has('status')) {
            switch ($request->get('status')) {
                case 'halted':
                    $this->manager->halt($instance);
                    HaltInstanceRequestJob::dispatch($instance);
                    break;
                case 'running':
                    $this->manager->restart($instance);
                    RestartInstanceRequestJob::dispatch($instance);
                    break;
                default:
                    throw new LogicException('Request status is invalid.');
            }

            return response()->json($instance, Response::HTTP_ACCEPTED);
        }

        return response()->noContent();
    }

    /**
     * インスタンス取得
     *
     * 指定した公開鍵の情報を返します。
     *
     * @param Instance $instance インスタンスのハッシュ
     * @return JsonResponse
     */
    #[Operation]
    #[OpenAPIResponse(factory: InstanceShowResponse::class)]
    public function show(Instance $instance): JsonResponse
    {
        return response()->json($instance);
    }

    /**
     * インスタンス削除
     *
     * 指定されたインスタンスを削除します。
     * このAPIにリクエスト後しばらくするとインスタンスが作成されます。
     *
     * ステータスが `running`, `halted` 以外のインスタンスは削除できません。
     *
     * @param Instance $instance インスタンスのハッシュ
     * @return JsonResponse
     */
    #[Operation]
    #[OpenAPIResponse(factory: InstanceDestroyResponse::class)]
    #[OpenAPIResponse(factory: InstanceDestroyInvalidResponse::class)]
    public function destroy(Instance $instance): JsonResponse
    {
        if (!$instance->canTerminate()) {
            abort(Response::HTTP_BAD_REQUEST, 'Cannot destroy this instance.');
        }

        $instance = $this->manager->terminate($instance);

        TerminateInstanceRequestJob::dispatch($instance);

        return response()->json($instance, Response::HTTP_ACCEPTED);
    }
}
