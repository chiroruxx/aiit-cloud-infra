<?php

namespace App\OpenApi\Responses;

use App\OpenApi\Schemas\PublicKeySchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class PublicKeyIndexResponse extends ResponseFactory
{
    public function build(): Response
    {
        $schema = Schema::array()->items(PublicKeySchema::ref());

        return Response::ok()->description('Successful response')->content(
            MediaType::json()->schema($schema)
        );
    }
}
