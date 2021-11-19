<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class PublicKeyDestroyResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create()->statusCode(HttpResponse::HTTP_NO_CONTENT)->description('Successful response');
    }
}
