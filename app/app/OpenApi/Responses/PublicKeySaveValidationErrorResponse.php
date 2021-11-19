<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class PublicKeySaveValidationErrorResponse extends ResponseFactory
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            Schema::string('message')->example('The given data was invalid.'),
            Schema::object('errors')
                ->additionalProperties(
                    Schema::array()->items(Schema::string())
                )
                ->example(['content' => ['The content must not be greater than 1024 characters.']])
        );

        return Response::create()
            ->statusCode(HttpResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->description('Validation errors')
            ->content(
                MediaType::json()->schema($response)
            );    }
}
