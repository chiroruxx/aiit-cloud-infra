<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class InstanceUpdateNoContentResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create()->statusCode(HttpResponse::HTTP_NO_CONTENT)->description('ステータスを更新しない場合');
    }
}
