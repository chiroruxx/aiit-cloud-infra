<?php

declare(strict_types=1);

namespace App\OpenApi\Responses;

use App\OpenApi\Schemas\InstanceSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class InstanceDestroyResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create()->statusCode(HttpResponse::HTTP_ACCEPTED)->description('Successful response')->content(
            MediaType::json()->schema(InstanceSchema::ref())
        );
    }
}
