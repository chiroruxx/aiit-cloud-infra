<?php

namespace App\OpenApi\Responses;

use App\OpenApi\Schemas\PublicKeySchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class PublicKeyStoreResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::created()->description('Successful response')->content(
            MediaType::json()->schema(PublicKeySchema::ref())
        );
    }
}
