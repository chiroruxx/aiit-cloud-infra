<?php

namespace App\OpenApi\Responses;

use App\OpenApi\Schemas\InstanceSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class InstanceUpdateAcceptedResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create()->statusCode(HttpResponse::HTTP_ACCEPTED)->description('ステータスを更新する場合')->content(
            MediaType::json()->schema(InstanceSchema::ref())
        );
    }
}
